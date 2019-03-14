<?php

namespace Test\Dallgoot\Yaml;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Dallgoot\Yaml\NodeSetValue;
use Dallgoot\Yaml\Node;
use Dallgoot\Yaml\NodeBlank;
use Dallgoot\Yaml\NodeScalar;

/**
 * Class NodeSetValueTest.
 *
 * @author Stephane Rebai <stephane.rebai@gmail.com>.
 * @license https://opensource.org/licenses/MIT The MIT license.
 * @link https://github.com/john-doe/my-awesome-project
 * @since File available since Release 1.0.0
 *
 * @covers \Dallgoot\Yaml\NodeSetValue
 */
class NodeSetValueTest extends TestCase
{
    /**
     * @var NodeSetValue $nodeSetValue An instance of "NodeSetValue" to test.
     */
    private $nodeSetValue;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe check arguments of this constructor. */
        $this->nodeSetValue = new NodeSetValue("  :   a string to test", 42);
    }

    /**
     * @covers \Dallgoot\Yaml\NodeSetValue::__construct
     */
    public function testConstruct(): void
    {
        $this->assertTrue($this->nodeSetValue->value instanceof NodeScalar);
        $this->assertEquals('a string to test', $this->nodeSetValue->value->build());
    }

    /**
     * @covers \Dallgoot\Yaml\NodeSetValue::build
     */
    public function testBuild(): void
    {
        $parent = new \StdClass;
        $parent->lastKey = null;
        $this->nodeSetValue->build($parent);
        $this->assertTrue(property_exists($parent, 'lastKey'));
        $this->assertEquals('a string to test', $parent->lastKey);
    }

    /**
     * @covers \Dallgoot\Yaml\NodeSetValue::isAwaitingChild
     */
    public function testIsAwaitingChild(): void
    {
        $uselessNode = new NodeBlank('', 1);
        $this->assertFalse($this->nodeSetValue->isAwaitingChild($uselessNode));
        $this->nodeSetValue->value = null;
        $this->assertTrue($this->nodeSetValue->isAwaitingChild($uselessNode));
    }
}