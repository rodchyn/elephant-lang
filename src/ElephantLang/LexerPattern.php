<?php

namespace ElephantLang;

class LexerPattern extends BaseLexerPattern
{
    function __construct()
    {
        parent::__construct();
        $this->initializeRules();
    }

    function initializeRules()
    {
        //general definitions
        $this->addDefinition('ANY', '*');
        $this->addDefinition('SOME', '+');
        $this->addDefinition('IS_OPTIONAL', '?');
        $this->addDefinition('WHITESPACE', '\s'); //existing macro
        $this->addDefinition('WITH_NAME', '?P');
        $this->addDefinition('MATCHING', '?P=');
        $this->addDefinition('ANY_CHAR_LAZY', '.*?');
        $this->addDefinition('SLASH_FORWARD', '\/');
        $this->addDefinition('BACKSLASH', '\\\\\\'); //disastrous escaping
        $this->addDefinition('ANY_CHAR_LAZY', '.*?');
        $this->addDefinition('ANY_CHAR_LAZY_NO_NEWLINE', '[^\n]*?');
        $this->addDefinition('ANY_CHAR_LAZY_NO_SLASH_FORWARD', '[^\/]*?');
        $this->addDefinition('ANY_CHAR_NO_NEWLINE', '[^\n]*');
        $this->addDefinition('FOLLOWED_BY', '?=');
        $this->addDefinition('PRECEDED_BY', '?<=');
        $this->addDefinition('NOT_FOLLOWED_BY', '?!');
        $this->addDefinition('NOT_PRECEDED_BY', '?<!');
        $this->addDefinition('NEWLINE', '\n');
        $this->addDefinition('STAR', '\*');
        $this->addDefinition('DOT', '\.');
        $this->addDefinition('MINUS', '-');
        $this->addDefinition('PLUS', '\+');
        $this->addDefinition('QUOTE_SINGLE', "'");
        $this->addDefinition('QUOTE_DOUBLE', '"');
        $this->addDefinition('SEMICOLON', ';');
        $this->addDefinition('DIGIT', '\d'); //existing macro
        $this->addDefinition('IDENTIFIER', '[a-zA-Z_$][a-zA-Z0-9_$]*');
        $this->addDefinition('DOC_START', "\<\<\<");
        $this->addDefinition('ONE_TIME_ONLY', "{1}");
        $this->addDefinition('FOUR_TIMES', "{4}");
        $this->addDefinition('SCRIPT_START', "#!");
        $this->addDefinition('INDENT', '\s{4}');
        $this->addDefinition('Q', '\?');



        //$this->addRule('REGEX', '{SLASH_FORWARD}{ANY_CHAR_LAZY_NO_SLASH_FORWARD}{SLASH_FORWARD}[gism]{IS_OPTIONAL}');
        $this->addRule('INDENT', '{NEWLINE}{WHITESPACE}{2,}');
        $this->addRule('NEWLINE', '{WHITESPACE}{ANY}{NEWLINE}{SOME}');

        $this->addRule('WHITESPACE', '{WHITESPACE}{SOME}');
        $this->addRule('USE', 'use{WHITESPACE}{IDENTIFIER}({SLASH_FORWARD}{IDENTIFIER}){ANY}');
        $this->addRule('NAMESPACE', 'namespace{WHITESPACE}{IDENTIFIER}({SLASH_FORWARD}{IDENTIFIER}){ANY}');



        //whitespace

        $this->addDefinition('CLASS', 'class');
        $this->addRule('CLASS', '{CLASS}{WHITESPACE}{IDENTIFIER}');
        //$this->addRule('INDENT', '{INDENT}');

        //it does affect PCRE's match results in what order the alternative patterns are presented

        //comments: must be matched in priority
        $this->addRule('COMMENT1', '{SLASH_FORWARD}{SLASH_FORWARD}{ANY_CHAR_LAZY_NO_NEWLINE}({FOLLOWED_BY}{NEWLINE})');
        $this->addRule('COMMENT2', '{SLASH_FORWARD}{STAR}{ANY_CHAR_LAZY}{STAR}{SLASH_FORWARD}');

        //directives: SCRIPT START
        $this->addRule('SCRIPT_START', '{SCRIPT_START}{ANY_CHAR_NO_NEWLINE}');

        //comments: but not take priority over script start
        $this->addRule('COMMENT3', '#{ANY_CHAR_NO_NEWLINE}');

        //constants: STRING
        $this->addRule('STRING_DOUBLE', '{QUOTE_DOUBLE}{ANY_CHAR_LAZY}({NOT_PRECEDED_BY}{BACKSLASH}){QUOTE_DOUBLE}');
        $this->addRule('STRING_SINGLE', '{QUOTE_SINGLE}{ANY_CHAR_LAZY}({NOT_PRECEDED_BY}{BACKSLASH}){QUOTE_SINGLE}');
        $this->addRule('STRING', '{STRING_SINGLE}{OR}{STRING_DOUBLE}');

        $this->addRule('SIMPLE_ASSIGMENT', '{IDENTIFIER}{ASSIGN}{STRING_SINGLE}{NEWLINE}');

        $this->addRule('DOC_DOUBLE', '{DOC_START}{WHITESPACE}{ANY}' .
            '{QUOTE_DOUBLE}({WITH_NAME}<id1>{IDENTIFIER}){QUOTE_DOUBLE}{NEWLINE}' .
            '{ANY_CHAR_LAZY}' .
            '({PRECEDED_BY}{NEWLINE})({MATCHING}id1){SEMICOLON}({FOLLOWED_BY}{NEWLINE})');

        $this->addRule('DOC_SINGLE', '{DOC_START}{WHITESPACE}{ANY}' .
            '{QUOTE_SINGLE}({WITH_NAME}<id2>{IDENTIFIER}){QUOTE_SINGLE}{NEWLINE}' .
            '{ANY_CHAR_LAZY}' .
            '({PRECEDED_BY}{NEWLINE})({MATCHING}id2){SEMICOLON}({FOLLOWED_BY}{NEWLINE})');

        //directives: DIRECTIVE
        $this->addDefinition('ANY_CHAR_LAZY_NO_SEMICOLON', '[^;]*?');
        //$this->addRule('DIRECTIVE','@{ANY_CHAR_LAZY_NO_SEMICOLON}{SEMICOLON}');

        //punctuation: SEMICOLON COLON COMMA
        $this->addRule('SEMICOLON', '{SEMICOLON}');
        $this->addRule('COLON', '\:');
        $this->addRule('COMMA', ',');

        //brackets: BRACKET_LEFT BRACKET_RIGHT BLOCK_START BLOCK_END ARRAY_LEFT ARRAY_RIGHT
        $this->addRule('BRACKET_LEFT', '\(');
        $this->addRule('BRACKET_RIGHT', '\)');
        $this->addRule('BLOCK_START', '\{');
        $this->addRule('BLOCK_END', '\}');
        $this->addRule('ARRAY_LEFT', '\[');
        $this->addRule('ARRAY_RIGHT', '\]');

        //keywords: IF WHILE FUNCTION RETURN NEW
        $this->addRule('IF', 'if');
        $this->addRule('WHILE', 'while');
        $this->addRule('FUNCTION', 'function');
        $this->addRule('RETURN', 'return');
        $this->addRule('NEW', 'new');
        $this->addRule('UNTIL', 'until');

        //deref
        $this->addRule('DEREF', '-\>');

        $this->addRule('EQ', '==');
        //operators: ASSIGN PLUS MINUS MULTIPLY DIVIDE EXPONENTIATE MODULO
        $this->addRule('ASSIGN', '=');
        $this->addRule('PLUS', '{PLUS}');
        $this->addRule('MINUS', '{MINUS}');
        $this->addRule('MULTIPLY', '{STAR}');
        $this->addRule('DIVIDE', '{SLASH_FORWARD}');
        $this->addRule('EXPONENTIATE', '\^');
        $this->addRule('MODULO', '%');
        $this->addRule('CONCAT', '{DOT}');


        //condition operators: EQ NE LT GT LE GE NOT

        $this->addRule('NE', '!=');
        $this->addRule('LT', '\<');
        $this->addRule('GT', '\>');
        $this->addRule('LE', '\<=');
        $this->addRule('GE', '\>=');
        $this->addRule('NOT', '!');
        $this->addRule('Q_ASSIGN', '{Q}=');
        $this->addRule('Q', '{Q}');

        //condition operators: AND OR
        $this->addRule('AND', '&&');
        $this->addRule('AND_LITERAL', 'and');
        $this->addRule('OR', '\|\|');

        //constants: TRUE FALSE NULL
        $this->addRule('TRUE', 'true');
        $this->addRule('TRUE1', 'on');
        $this->addRule('TRUE2', 'yes');
        $this->addRule('FALSE', 'false');
        $this->addRule('FALSE1', 'off');
        $this->addRule('FALSE2', 'no');
        $this->addRule('NULL', 'null');

        //constants: IDENTIFIER NUMBER
        $this->addRule('IDENTIFIER_CATCHER', '{IDENTIFIER}\.\.\.');
        $this->addRule('IDENTIFIER', '{IDENTIFIER}');
        $this->addRule('NUMBER', '{MINUS}{IS_OPTIONAL}{DIGIT}{SOME}({DOT}{DIGIT}{SOME}){IS_OPTIONAL}');

    }
}
