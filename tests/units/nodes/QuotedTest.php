<?php

namespace Test\Dallgoot\Yaml\Nodes;

use Dallgoot\Yaml\Nodes\Quoted;
use PHPUnit\Framework\TestCase;

/**
 * Class QuotedTest.
 *
 * @author Stephane Rebai <stephane.rebai@gmail.com>.
 * @license https://opensource.org/licenses/MIT The MIT license.
 * @link https://github.com/dallgoot/yaml
 * @since File available since Release 1.0.0
 *
 * @covers \Dallgoot\Yaml\Nodes\Quoted
 */
class QuotedTest extends TestCase
{
    /**
     * @var Quoted $nodeQuoted An instance of "Nodes\Quoted" to test.
     */
    private $nodeQuoted;

    /**
     * @covers \Dallgoot\Yaml\Nodes\Quoted::build
     */
    public function testBuild(): void
    {
        $this->assertEquals("a quoted string", $this->nodeQuoted->build());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->nodeQuoted = new Quoted('"a quoted string"');
    }
}
