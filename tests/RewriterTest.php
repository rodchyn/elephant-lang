<?php

use ElephantLang\Rewriter;

class RewriterTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleAssign()
    {
        $rewriter = new Rewriter();
//        var_dump(get_declared_classes());die;
        $this->assertEquals('$a = "b"', $rewriter->rewrite('a = "b"'));
    }
}
