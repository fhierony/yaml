<?php

namespace Test\Dallgoot\Yaml\Nodes;

use Dallgoot\Yaml\Nodes\Blank;
use Dallgoot\Yaml\Nodes\Scalar;
use Dallgoot\Yaml\Nodes\SetValue;
use PHPUnit\Framework\TestCase;
use StdClass;

/**
 * Class SetValueTest.
 *
 * @author Stephane Rebai <stephane.rebai@gmail.com>.
 * @license https://opensource.org/licenses/MIT The MIT license.
 * @link https://github.com/dallgoot/yaml
 * @since File available since Release 1.0.0
 *
 * @covers \Dallgoot\Yaml\Nodes\SetValue
 */
class SetValueTest extends TestCase
{
    /**
     * @var SetValue $nodeSetValue An instance of "Nodes\SetValue" to test.
     */
    private $nodeSetValue;

    /**
     * @covers \Dallgoot\Yaml\Nodes\SetValue::__construct
     */
    public function testConstruct(): void
    {
        $this->assertTrue($this->nodeSetValue->value instanceof Scalar);
        $this->assertEquals('a string to test', $this->nodeSetValue->value->build());
    }

    /**
     * @covers \Dallgoot\Yaml\Nodes\SetValue::build
     */
    public function testBuild(): void
    {
        $parent = new StdClass;
        $parent->lastKey = null;
        $this->nodeSetValue->build($parent);
        $this->assertTrue(property_exists($parent, 'lastKey'));
        $this->assertEquals('a string to test', $parent->lastKey);
    }

    /**
     * @covers \Dallgoot\Yaml\Nodes\SetValue::isAwaitingChild
     */
    public function testIsAwaitingChild(): void
    {
        $uselessNode = new Blank('', 1);
        $this->assertFalse($this->nodeSetValue->isAwaitingChild($uselessNode));
        $this->nodeSetValue->value = null;
        $this->assertTrue($this->nodeSetValue->isAwaitingChild($uselessNode));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe check arguments of this constructor. */
        $this->nodeSetValue = new SetValue("  :   a string to test", 42);
    }
}
