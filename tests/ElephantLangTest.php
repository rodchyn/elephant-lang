<?php

use ElephantLang\Rewriter;

abstract class ElephantLangTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElephantLang\Rewriter
     */
    protected $rewriter;

    public function __construct()
    {
        $this->rewriter = new Rewriter();
    }

    public function rewriteTest($expected, $source)
    {
        $this->assertEquals($expected, $this->rewriter->rewrite($source));
    }
}
