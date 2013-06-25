<?php

namespace ElephantLang;

class LexerDefinition
{
    var $macro;
    var $replacement;

    function __construct($macro, $replacement)
    {
        $this->macro = $macro;
        $this->replacement = $replacement;
    }
}
