<?php

use ElephantLang\Rewriter;

class RewriterTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleAssign()
    {
        $rewriter = new Rewriter();
        $this->assertEquals('$a = "b"', $rewriter->rewrite('a = "b"'));
        $this->assertEquals('$c = $a', $rewriter->rewrite('c=a'));
    }
}
