<?php

namespace Dallgoot\Yaml;

use Dallgoot\Yaml\{Yaml as Y, Regex as R};

/**
 * Constructs the result (YamlObject or array) for specific Node Types
 *
 * @author  Stéphane Rebai <stephane.rebai@gmail.com>
 * @license Apache 2.0
 * @link    TODO : url to specific online doc
 */
final class TypesBuilder
{

    public static function buildReference($node, $parent)
    {
        $tmp = is_null($node->value) ? null : Builder::build($node->value, $parent);
        if ($node->type === Y::REF_DEF) Builder::$_root->addReference($node->identifier, $tmp);
        return Builder::$_root->getReference($node->identifier);
    }


    /**
     * Builds a key and set the property + value to the given parent
     *
     * @param Node $node       The node with type YAML::KEY
     * @param object|array $parent       The parent
     *
     * @throws \ParseError if Key has no name(identifier) Note: empty string is allowed
     * @return null
     */
    public static function buildKey(Node $node, &$parent=null)
    {
        extract((array) $node, EXTR_REFS);
        if (is_null($identifier)) {
            throw new \ParseError(sprintf(self::ERROR_NO_KEYNAME, $line));
        } else {
            if ($value instanceof Node) {
                if ($value->type & (Y::ITEM|Y::KEY)) {
                    $value = new NodeList($value);
                } else {
                    $result = Builder::build($value);
                }
            }
            if ($value instanceof NodeList) {
                $result = Builder::buildNodeList($value);
            }
            if (is_null($parent)) {
                return $result;
            } else {
                if (is_array($parent)) {
                    $parent[$identifier] = $result;
                } else {
                    $parent->{$identifier} = $result;
                }
            }
        }
    }

    /**
     * Builds an item. Adds the item value to the parent array|Iterator
     *
     * @param      Node        $node    The node with type YAML::ITEM
     * @param      array|\Iterator      $parent  The parent
     *
     * @throws     \Exception  if parent is another type than array or object Iterator
     * @return null
     */
    public static function buildItem(Node $node, &$parent)
    {
        extract((array) $node, EXTR_REFS);
        if (!is_array($parent) && !($parent instanceof \ArrayIterator)) {
            throw new \Exception("parent must be an Iterable not ".(is_object($parent) ? get_class($parent) : gettype($parent)), 1);
        }
        $ref = $parent instanceof \ArrayIterator ? $parent->getArrayCopy() : $parent;
        $numKeys = array_filter(array_keys($ref), 'is_int');
        $key = count($numKeys) > 0 ? max($numKeys) + 1 : 0;
        if ($value instanceof Node) {
            if($value->type & Y::KEY) {
                self::buildKey($node->value, $parent);
                return;
            } elseif ($value->type & Y::ITEM) {
                $a = [];
                $result = self::buildItem($value, $a);
            }
        }
        $result = Builder::build($value);
        $parent[$key] = $result;
    }


    /**
     * Builds a set key.
     *
     * @param      Node        $node    The node of type YAML::SET_KEY.
     * @param      object      $parent  The parent
     *
     * @throws     \Exception  if a problem occurs during serialisation (json format) of the key
     */
    public function buildSetKey(Node $node, &$parent)
    {
        $built = is_object($node->value) ? Builder::build($node->value) : null;
        $stringKey = is_string($built) && Regex::isProperlyQuoted($built) ? trim($built, '\'" '): $built;
        $key = json_encode($stringKey, JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_UNESCAPED_SLASHES);
        if (empty($key)) throw new \Exception("Cant serialize complex key: ".var_export($node->value, true), 1);
        $parent->{trim($key, '\'" ')} = null;
    }

    /**
     * Builds a set value.
     *
     * @param      Node    $node    The node of type YAML::SET_VALUE
     * @param      object  $parent  The parent (the document object or any previous object created through a mapping key)
     */
    public function buildSetValue(Node $node, &$parent)
    {
        $prop = array_keys(get_object_vars($parent));
        $key = end($prop);
        if ($node->value->type & (Y::ITEM|Y::KEY )) {
            $node->value = new NodeList($node->value);
        }
        $parent->{$key} = Builder::build($node->value);
    }

    /**
     * Builds a tag and its value (also built) and encapsulates them in a Tag object.
     *
     * @param      Node    $node    The node of type YAML::TAG
     * @param      mixed  $parent  The parent
     *
     * @return     Tag|null     The tag object of class Dallgoot\Yaml\Tag.
     * @todo       have somewhere a list of common tags and handle their treatment here
     */
    public static function buildTag(Node $node, &$parent)
    {
        $name = (string) $node->identifier;
        if ($parent === Builder::$_root && empty($node->value)) {
            $parent->addTag($name);
        } else {
            $target = $node->value;
            if ($node->value instanceof Node) {
                if ($node->value->type & (Y::KEY|Y::ITEM)) {
                    if (is_null($parent)) {
                        $target = new NodeList($node->value);
                    } else {
                        Builder::build($node->value, $parent);
                    }
                }
            }
            return new Tag($name, is_object($target) ? Builder::build($target) : null);
        }
    }

    /**
     * Builds a directive. NOT IMPLEMENTED YET
     *
     * @param      Node  $node    The node
     * @param      mixed  $parent  The parent
     * @todo implement if requested
     */
    public function buildDirective(Node $node, $parent)
    {
        // TODO : implement
    }
}