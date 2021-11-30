<?php

namespace Test\Dallgoot\Yaml\Nodes;

use Dallgoot\Yaml\Nodes\Blank;
use Dallgoot\Yaml\Nodes\Partial;
use Dallgoot\Yaml\Nodes\Quoted;
use Dallgoot\Yaml\Nodes\Scalar;
use ParseError;
use PHPUnit\Framework\TestCase;

/**
 * Class PartialTest.
 *
 * @author Stephane Rebai <stephane.rebai@gmail.com>.
 * @license https://opensource.org/licenses/MIT The MIT license.
 * @link https://github.com/dallgoot/yaml
 * @since File available since Release 1.0.0
 *
 * @covers \Dallgoot\Yaml\Nodes\Partial
 */
class PartialTest extends TestCase
{
    /**
     * @var Partial $nodePartial An instance of "Nodes\Partial" to test.
     */
    private $nodePartial;

    /**
     * @covers \Dallgoot\Yaml\Nodes\Partial::specialProcess
     */
    public function testSpecialProcess(): void
    {
        $blankBuffer = [];
        $node = new Scalar(' end of quoting"', 2);
        $parent = new Scalar(' emptykey:', 1);
        $parent->add($this->nodePartial);
        $this->assertTrue($this->nodePartial->specialProcess($node, $blankBuffer));
        $this->assertTrue($parent->value instanceof Quoted);
        $this->assertEquals(" partially quoted end of quoting", $parent->value->build());
    }

    /**
     * @covers \Dallgoot\Yaml\Nodes\Partial::specialProcess
     */
    public function testSpecialProcessWithPreviousLineFeed(): void
    {
        $this->nodePartial = new Partial(" ' partially quoted\n");
        $blankBuffer = [];
        $node = new Scalar(" end of quoting'", 2);
        $parent = new Scalar(' emptykey:', 1);
        $parent->add($this->nodePartial);
        $this->assertTrue($this->nodePartial->specialProcess($node, $blankBuffer));
        $this->assertTrue($parent->value instanceof Quoted);
        $this->assertEquals(" partially quoted\nend of quoting", $parent->value->build());

    }

    /**
     * @covers \Dallgoot\Yaml\Nodes\Partial::specialProcess
     */
    public function testSpecialProcessWithNodeBlank(): void
    {
        $blankBuffer = [];
        $current = new Blank('', 2);
        $parent = new Scalar(' emptykey:', 1);
        $parent->add($this->nodePartial);
        $this->assertTrue($this->nodePartial->specialProcess($current, $blankBuffer));
        $this->assertTrue($parent->value instanceof Partial);
        $this->assertEquals(" \" partially quoted\n", $parent->value->raw);
    }

    /**
     * @covers \Dallgoot\Yaml\Nodes\Partial::build
     */
    public function testBuild(): void
    {
        $this->expectException(ParseError::class);
        $this->nodePartial->build();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->nodePartial = new Partial(' " partially quoted');
    }
}
