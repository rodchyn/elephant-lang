<?php

namespace ElephantLang;

class LexerRule
{
    var $symbol;
    var $regex;

    function __construct($symbol, $regex)
    {
        $this->symbol = $symbol;
        $this->regex = $regex;
    }

    function __toString()
    {
        return "(?P<{$this->symbol}>{$this->regex})";
    }
}
