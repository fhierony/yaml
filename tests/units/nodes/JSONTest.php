<?php

namespace Test\Dallgoot\Yaml\Nodes;

use Dallgoot\Yaml\Nodes\JSON;
use PHPUnit\Framework\TestCase;

/**
 * Class JSONTest.
 *
 * @author Stephane Rebai <stephane.rebai@gmail.com>.
 * @license https://opensource.org/licenses/MIT The MIT license.
 * @link https://github.com/dallgoot/yaml
 * @since File available since Release 1.0.0
 *
 * @covers \Dallgoot\Yaml\Nodes\JSON
 */
class JSONTest extends TestCase
{
    /**
     * @var JSON $nodeJSON An instance of "Nodes\JSON" to test.
     */
    private $nodeJSON;

    /**
     * @covers \Dallgoot\Yaml\Nodes\JSON::build
     */
    public function testBuild(): void
    {
        $this->assertEquals([1, 2, 3], $this->nodeJSON->build());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->nodeJSON = new JSON('  [1,2,3]', 1);
    }
}
