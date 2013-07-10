<?php

namespace ElephantLang;

define('TOKEN_OUTPUT_FORMAT', "%-4s %-3s %-7s %-14s %-s\n");

class Token implements \ArrayAccess
{
    public $symbol;
    public $metadata = array();
    public $value = null;
    public $absolutePosition = null;
    public $lineNumber = null;
    public $columnNumber = null;

    public function __construct($symbol, $value, $absolutePosition, $lineNumber, $columnNumber)
    {
        $this->symbol = $symbol;
        $this->value = $value;
        $this->absolutePosition = $absolutePosition;
        $this->lineNumber = $lineNumber;
        $this->columnNumber = $columnNumber;

        $this[0] = $symbol;
        $this[1] = $value;
        $this[2] = $lineNumber;
    }

    public function getMatchSymbol($match)
    {
        foreach ($match as $key => $value) {
            if (is_string($key) && $value[0] != '') return $key;
        }
        return null;
    }

    public static function FromPCREMatch($match, $textLines)
    {
        $symbol = self::getMatchSymbol($match);
        $value = $match[0][0];
        $position = $match[0][1];
        $textLine = $textLines->textLineForAbsolutePosition($position);
        $lineNumber = $textLine->lineNumber;
        $columnNumber = $position - $textLine->start + 1;
        return new Token($symbol, $value, $position, $lineNumber, $columnNumber);
    }

    public static function debugEscapeNewlines($string)
    {
        return str_replace("\n", '\n', $string);
    }

    public static function debugPrintColumnHeadings()
    {
        printf(TOKEN_OUTPUT_FORMAT, 'pos', 'len', 'lin/col', '', '');
    }

    public function debugPrint()
    {
        $tokenLength = strlen($this->value);
        $value = self::debugEscapeNewlines($this->value);
        printf(TOKEN_OUTPUT_FORMAT,
            $this->absolutePosition,
            $tokenLength,
            $this->lineNumber . '/' . $this->columnNumber,
            $this->symbol,
            $value);
    }

    public function __toString()
    {
        return $this->symbol;
    }

    public function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            if (isset($value[0])) {
                $x = ($value instanceof yyToken) ?
                    $value->metadata : $value;
                $this->metadata = array_merge($this->metadata, $x);
                return;
            }
            $offset = count($this->metadata);
        }
        if ($value === null) {
            return;
        }
        if ($value instanceof yyToken) {
            if ($value->metadata) {
                $this->metadata[$offset] = $value->metadata;
            }
        } elseif ($value) {
            $this->metadata[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}
