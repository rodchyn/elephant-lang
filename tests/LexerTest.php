<?php

use ElephantLang\SimpleLangLexer;
use ElephantLang\Parser;

class LexerTest extends \PHPUnit_Framework_TestCase
{

    public function testTokens()
    {	
        $example = file_get_contents(dirname(__FILE__) . '/test.elph');

        $lexer = new SimpleLangLexer();
        $parser = new Parser($lexer);
        $lexer->tokenizeAll($example);

        foreach($lexer->tokens as $token) {
            echo "Parsing  {$token->symbol} Token {$token->value} \n";
            $parser->parse($token);
        }
        $parser->parse($token);

        die( "Returnvalue: ".$parser->retvalue."\n" );
    }
}
