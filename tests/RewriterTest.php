<?php

use ElephantLang\Rewriter;

class RewriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElephantLang\Rewriter
     */
    private $rewriter;

    public function __construct()
    {
        $this->rewriter = new Rewriter();
    }
    public function testSimpleAssign()
    {

        $this->assertEquals('$a = "b";', $this->rewriter->rewrite('a = "b"'));
        $this->assertEquals('$c = $a;', $this->rewriter->rewrite('c=a'));
    }

    public function testArithmeticOperations()
    {
        $this->assertEquals('$a = 2 + 3;', $this->rewriter->rewrite('a = 2 + 3'));
    }

    public function test()
    {
        $this->assertEquals('$length = mb_strlen(\'text\')', $this->rewriter->rewrite("lenght = 'text'.lenght"));
    }
}
