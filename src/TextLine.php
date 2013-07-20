<?php

namespace Rodchyn\ElephantLang;

class TextLine
{
    var $lineNumber = null;
    var $start = null;
    var $end = null;
    var $line = null;

    function __construct($lineNumber, $start, $end, $line)
    {
        $this->lineNumber = $lineNumber;
        $this->start = $start;
        $this->end = $end;
        $this->line = $line;
    }
}
