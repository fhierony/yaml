<?php

namespace Test\Dallgoot\Yaml\Nodes;

use Dallgoot\Yaml\Nodes\Comment;
use Dallgoot\Yaml\Nodes\Key;
use Dallgoot\Yaml\Nodes\Root;
use Dallgoot\Yaml\YamlObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Class CommentTest.
 *
 * @author Stephane Rebai <stephane.rebai@gmail.com>.
 * @license https://opensource.org/licenses/MIT The MIT license.
 * @link https://github.com/dallgoot/yaml
 * @since File available since Release 1.0.0
 *
 * @covers \Dallgoot\Yaml\Nodes\Comment
 */
class CommentTest extends TestCase
{
    /**
     * @var Comment $nodeComment An instance of "Nodes\Comment" to test.
     */
    private $nodeComment;

    private $commentLine = 5;

    /**
     * @covers \Dallgoot\Yaml\Nodes\Comment::specialProcess
     */
    public function testSpecialProcess(): void
    {
        $keyNode = new Key('  key: keyvalue', 1);
        $rootNode = new Root();
        $rootNode->add($keyNode);
        $blankBuffer = [];
        $this->assertTrue($this->nodeComment->specialProcess($keyNode, $blankBuffer));
    }

    /**
     * @covers \Dallgoot\Yaml\Nodes\Comment::build
     */
    public function testBuild(): void
    {
        $yamlObject = new YamlObject(0);
        $rootNode = new Root;
        $reflector = new ReflectionClass($rootNode);
        $method = $reflector->getMethod('buildFinal');
        $method->setAccessible(true);
        $method->invoke($rootNode, $yamlObject);
        $rootNode->add($this->nodeComment);
        $this->nodeComment->build();
        $this->assertEquals($yamlObject->getComment($this->commentLine), $this->nodeComment->raw);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        /** @todo Maybe add some arguments to this constructor */
        $this->nodeComment = new Comment('#this is a comment for test', $this->commentLine);
    }
}
