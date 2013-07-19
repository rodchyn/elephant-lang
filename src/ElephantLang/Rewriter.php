<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Yura Rodchyn (rodchyn) rodchyn@gmail.com
 * Date: 6/19/13
 * Time: 6:55 PM
 * To change this template use File | Settings | File Templates.
 */

namespace ElephantLang;

use ElephantLang\Parser\Parser;
use ElephantLang\Lexer;

class Rewriter {

    public function rewrite($content)
    {
        $lexer = new Lexer();
        $parser = new Parser($lexer);
        $lexer->tokenizeAll($content);

        foreach($lexer->tokens as $token) {
            $parser->parse($token);
        }

        $parser->parse(null);

        return $parser->retvalue;
    }
}
