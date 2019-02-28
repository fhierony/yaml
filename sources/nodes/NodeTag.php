<?php

namespace Dallgoot\Yaml;

/**
 *
 * @author  Stéphane Rebai <stephane.rebai@gmail.com>
 * @license Apache 2.0
 * @link    TODO : url to specific online doc
 * @todo    handle tags like  <tag:clarkevans.com,2002:invoice>
 */
class NodeTag extends NodeActions
{
    public function isAwaitingChild(Node $node):bool
    {
        return is_null($this->value);
    }

    public function getTargetOnEqualIndent(Node &$node):Node
    {
        if (is_null($this->value)) {
            return $this;
        } else {
            return $this->getParent();
        }
    }

    /**
     * Builds a tag and its value (also built) and encapsulates them in a Tag object.
     *
     * @param array|object|null $parent The parent
     *
     * @return Tag|mixed The tag object of class Dallgoot\Yaml\Tag.
     */
    public function build(&$parent = null)
    {
        if ($this->getParent() instanceof NodeRoot && is_null($this->value)) {
            $this->getParent()->getYamlObject()->addTag($this->_tag);
            return;
        }
        $value = $this->value;
        if (is_null($parent) && isOneOf($value, ['NodeItem', 'NodeKey'])) {
            $value = new NodeList($value);
        }
        if (TagFactory::isKnown($this->_tag)) {
            if ($value instanceof NodeLiterals) {
                $value = $value->value;
            }
            $built = TagFactory::transform($this->_tag, $value);
            if ($built instanceof Node || $built instanceof NodeList) {
                return $built->build($parent);
            }
            return $built;
        } else {
            return new Tag($this->_tag, is_null($value) ? null : $value->build($parent));
        }
    }
}