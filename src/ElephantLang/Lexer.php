<?php

namespace ElephantLang;

class Lexer extends Regexer
{
    private $regex1 = null;

    public function __construct()
    {
        $pattern = new LexerPattern();
        $this->regex = $pattern->regex();
    }

    public function tokenizeAll($text)
    {
        parent::tokenizeAll($this->regex, $text);
        $this->renameSymbol('COMMENT1', 'COMMENT');
        $this->renameSymbol('COMMENT2', 'COMMENT');
        $this->renameSymbol('COMMENT3', 'COMMENT');
        $this->renameSymbol('TRUE1', 'TRUE');
        $this->renameSymbol('TRUE2', 'TRUE');
        $this->renameSymbol('FALSE1', 'FALSE');
        $this->renameSymbol('FALSE2', 'FALSE');
        $this->purgeUnimportantSymbol('COMMENT');
        $this->addAfterProcessing('STRING_SINGLE', function($token){ $token->value = trim($token->value, "'"); });
        $this->addAfterProcessing('STRING_DOUBLE', function($token){ $token->value = trim($token->value, '"'); });
        $this->addAfterProcessing('INDENT', function($token){ $token->value = preg_replace('/\n/', '', $token->value); });
        $this->addAfterProcessing('CLASS', function($token){$token->value = preg_replace('/^class\s+/', '', $token->value); });
        $this->addAfterProcessing('NAMESPACE', function($token){$token->value = preg_replace('/^namespace\s+/', '', $token->value); });
        $this->purgeUnimportantSymbol('WHITESPACE');
        $this->purgeUnimportantSymbol('NEWLINE');
    }



    private function addAfterProcessing($symbol, $callback)
    {
        foreach($this->tokens as $token) {
            $callback($token);
        }
    }
}
