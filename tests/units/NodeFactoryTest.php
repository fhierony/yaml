<?php

namespace Test\Dallgoot\Yaml;

use Dallgoot\Yaml\NodeFactory;
use Dallgoot\Yaml\Nodes\Anchor;
use Dallgoot\Yaml\Nodes\Blank;
use Dallgoot\Yaml\Nodes\Comment;
use Dallgoot\Yaml\Nodes\CompactMapping;
use Dallgoot\Yaml\Nodes\CompactSequence;
use Dallgoot\Yaml\Nodes\Directive;
use Dallgoot\Yaml\Nodes\DocEnd;
use Dallgoot\Yaml\Nodes\DocStart;
use Dallgoot\Yaml\Nodes\Item;
use Dallgoot\Yaml\Nodes\JSON;
use Dallgoot\Yaml\Nodes\Key;
use Dallgoot\Yaml\Nodes\Literal;
use Dallgoot\Yaml\Nodes\LiteralFolded;
use Dallgoot\Yaml\Nodes\Partial;
use Dallgoot\Yaml\Nodes\Quoted;
use Dallgoot\Yaml\Nodes\Scalar;
use Dallgoot\Yaml\Nodes\SetKey;
use Dallgoot\Yaml\Nodes\SetValue;
use Dallgoot\Yaml\Nodes\Tag;
use Exception;
use ParseError;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class NodeFactoryTest.
 *
 * @author Stephane Rebai <stephane.rebai@gmail.com>.
 * @license https://opensource.org/licenses/MIT The MIT license.
 * @link https://github.com/dallgoot/yaml
 * @since File available since Release 1.0.0
 *
 * @covers \Dallgoot\Yaml\NodeFactory
 */
class NodeFactoryTest extends TestCase
{
    /**
     * @var NodeFactory $nodeFactory An instance of "NodeFactory" to test.
     */
    private $nodeFactory;

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::get
     */
    public function testGet(): void
    {
        $this->assertTrue($this->nodeFactory::get('', 1) instanceof Blank, 'Not a NodeBlank');
        $this->assertTrue($this->nodeFactory::get('    ...', 1) instanceof DocEnd, 'Not a NodeDocEnd');
        $this->assertTrue($this->nodeFactory::get('  key : value', 1) instanceof Key, 'Not a NodeKey');
        $this->assertTrue($this->nodeFactory::get('$qsd = 3213', 1) instanceof Scalar, 'Not a NodeScalar');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::get
     */
    public function testGetException(): void
    {
        $this->expectException(Exception::class);
        $this->nodeFactory::get('%INVALID_DIRECTIVE  xxx', 1);
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onSpecial
     */
    public function testOnSpecial(): void
    {
        $reflector = new ReflectionClass($this->nodeFactory);
        $method = $reflector->getMethod('onSpecial');
        $method->setAccessible(true);
        $nodeString = '#qsd = 3213';
        $this->assertTrue($method->invoke(null, $nodeString[0], $nodeString, 1) instanceof Comment, 'Not a NodeComment');
        $nodeString = '%YAML 1.2';
        $this->assertTrue($method->invoke(null, $nodeString[0], $nodeString, 1) instanceof Directive, 'Not a NodeDirective');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onSpecial
     */
    public function testOnSpecialParseError(): void
    {
        $this->expectException(ParseError::class);
        $reflector = new ReflectionClass($this->nodeFactory);
        $method = $reflector->getMethod('onSpecial');
        $method->setAccessible(true);
        $nodeString = '%INVALID_DIRECTIVE  xxx';
        $method->invoke(null, $nodeString[0], $nodeString, 1);
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onQuoted
     */
    public function testOnQuoted(): void
    {
        $reflector = new ReflectionClass($this->nodeFactory);
        $method = $reflector->getMethod('onQuoted');
        $method->setAccessible(true);
        $nodeString = '    "double quoted" ';
        $this->assertTrue($method->invoke(null, $nodeString[0], $nodeString, 1) instanceof Quoted,
            'Not a NodeQuoted');
        $nodeString = " 'simple quoted'   ";
        $this->assertTrue($method->invoke(null, $nodeString[0], $nodeString, 1) instanceof Quoted,
            'Not a NodeQuoted');
        $nodeString = " 'simple partial   ";
        $this->assertTrue($method->invoke(null, $nodeString[0], $nodeString, 1) instanceof Partial,
            'Not a NodePartial');
        $nodeString = '" double partial  ';
        $this->assertTrue($method->invoke(null, $nodeString[0], $nodeString, 1) instanceof Partial,
            'Not a NodePartial');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onSetElement
     */
    public function testOnSetElement(): void
    {
        $reflector = new ReflectionClass($this->nodeFactory);
        $method = $reflector->getMethod('onSetElement');
        $method->setAccessible(true);
        $nodeString = '    ? some setkey ';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof SetKey,
            'Not a NodeSetKey');
        $nodeString = '    : some setvalue ';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof SetValue,
            'Not a NodeSetValue');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onCompact
     */
    public function testOnCompactJSON(): void
    {
        $method = new ReflectionMethod($this->nodeFactory, 'onCompact');
        $method->setAccessible(true);
        $nodeString = '["a","b","c"]';
        $this->assertTrue($method->invoke(null, '', $nodeString, 1) instanceof JSON,
            'Not a NodeJSON');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onCompact
     */
    public function testOnCompactJSONMAPPING(): void
    {
        $method = new ReflectionMethod($this->nodeFactory, 'onCompact');
        $method->setAccessible(true);
        $nodeString = '{"key":"value","key1":"value1"}';
        $this->assertTrue($method->invoke(null, '', $nodeString, 1) instanceof JSON, 'Not a NodeJSON');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onCompact
     */
    public function testOnCompactMAPPING(): void
    {
        $method = new ReflectionMethod($this->nodeFactory, 'onCompact');
        $method->setAccessible(true);
        $nodeString = '{  key :  value ,  key1  : value1  }';
        $output = $method->invoke(null, '', $nodeString, 1);
        $this->assertTrue($output instanceof CompactMapping, get_class($output) . ' instead of a NodeCompactMapping');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onCompact
     */
    public function testOnCompactSEQUENCE(): void
    {
        $method = new ReflectionMethod($this->nodeFactory, 'onCompact');
        $method->setAccessible(true);
        $nodeString = '[a,b,c]';
        $output = $method->invoke(null, '', $nodeString, 1);
        $this->assertTrue($output instanceof CompactSequence, get_class($output) . ' instead of a NodeCompactSequence');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onCompact
     */
    public function testOnCompactPartialMapping(): void
    {
        $method = new ReflectionMethod($this->nodeFactory, 'onCompact');
        $method->setAccessible(true);
        $nodeString = ' { a: b, ';
        $output = $method->invoke(null, '', $nodeString, 1);
        $this->assertTrue($output instanceof Partial, get_class($output) . ' instead of a Partial');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onCompact
     */
    public function testOnCompactPartialSequence(): void
    {
        $method = new ReflectionMethod($this->nodeFactory, 'onCompact');
        $method->setAccessible(true);
        $nodeString = ' [ a, b, ';
        $output = $method->invoke(null, '', $nodeString, 1);
        $this->assertTrue($output instanceof Partial, get_class($output) . ' instead of a Partial');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onHyphen
     */
    public function testOnHyphen(): void
    {
        $reflector = new ReflectionClass($this->nodeFactory);
        $method = $reflector->getMethod('onHyphen');
        $method->setAccessible(true);
        $nodeString = '- ';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof Item,
            'Not a NodeItem');
        $nodeString = '-- ';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof Scalar,
            'Not a NodeScalar');
        $nodeString = '---';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof DocStart,
            'Not a NodeDocStart');
        $nodeString = '  - simpleitem  ';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof Item,
            'Not a NodeItem');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onNodeAction
     */
    public function testOnNodeAction(): void
    {
        $method = new ReflectionMethod($this->nodeFactory, 'onNodeAction');
        $method->setAccessible(true);
        $nodeString = '***';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof Scalar,
            'Not a NodeScalar');
        $nodeString = '&emptyanchor';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof Anchor,
            'Not a NodeAnchor' . get_class($method->invoke(null, trim($nodeString)[0], $nodeString, 1)));
        $nodeString = '&anchor [1,2,3]';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof Anchor,
            'Not a NodeAnchor');
        $nodeString = '*anchorcall ';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof Anchor,
            'Not a NodeAnchor');
        $nodeString = '!!emptytag';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof Tag,
            'Not a NodeTag');
        $nodeString = '!!str   345  ';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof Tag,
            'Not a NodeTag');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onLiteral
     */
    public function testOnLiteral(): void
    {
        $method = new ReflectionMethod($this->nodeFactory, 'onLiteral');
        $method->setAccessible(true);
        $nodeString = '  |-   ';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof Literal,
            'Not a NodeLit');
        $nodeString = '  >+   ';
        $this->assertTrue($method->invoke(null, trim($nodeString)[0], $nodeString, 1) instanceof LiteralFolded,
            'Not a NodeLitFolded');
    }

    /**
     * @covers \Dallgoot\Yaml\NodeFactory::onLiteral
     */
    public function testOnLiteralFail(): void
    {
        $method = new ReflectionMethod($this->nodeFactory, 'onLiteral');
        $method->setAccessible(true);
        $nodeString = ' x ';
        $this->expectException(ParseError::class);
        $method->invoke(null, trim($nodeString)[0], $nodeString, 1);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->nodeFactory = new NodeFactory();
    }
}
