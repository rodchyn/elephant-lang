<?php

use ElephantLang\Lexer;
use ElephantLang\Parser\Parser;

class LexerTest extends \PHPUnit_Framework_TestCase
{

    public function testTokens()
    {	
        $example = file_get_contents(dirname(__FILE__) . '/test.elph');

        try {
            $lexer = new Lexer();
            $parser = new Parser($lexer);
            //Parser::Trace(fopen('php://output', 'w'), 'Trace: ');
            $lexer->tokenizeAll($example);

            foreach($lexer->tokens as $token) {
                echo "Parsing {$token->symbol} Token {$token->value} \n";
                $parser->parse($token);
            }

            $parser->parse(null);

        } catch (\Exception $e) {
            echo $e->getMessage();
            $lexer->debugPrintReport();
        }

        //die( "Returnvalue: ".$parser->retvalue."\n" );
    }
}
