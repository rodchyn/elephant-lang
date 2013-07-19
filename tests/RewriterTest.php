<?php


class RewriterTest extends ElephantLangTest
{

    public function testSimpleAssign()
    {
        $this->rewriteTest('$a = "b";', 'a = "b"');
        $this->rewriteTest('$c = $a;', 'c=a');
        $this->rewriteTest('$c = $a + 2;', 'c=a+2');
        $this->rewriteTest('$c = 2 + $a;', 'c=2+a');
        $this->rewriteTest('$c = 2 + $a + $b;', 'c=2+a+b');
        $this->rewriteTest('$c = 2 + 1 + 3;', 'c=2+1+3');
    }

    public function testArithmeticOperations()
    {
        $this->rewriteTest('$a = 2 + 3;', 'a = 2 + 3');
    }

    public function testFunctionCall()
    {
        $this->rewriteTest('functionName(param1, param2);', 'fName param1, param2');
    }

    public function testHelpers()
    {
        $this->rewriteTest('$length = mb_strlen(\'text\')', "lenght = 'text'.lenght");
    }

}
