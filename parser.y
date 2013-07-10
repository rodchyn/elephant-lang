/* This is an example for a Parser in PHP */
%name EL_
%declare_class {class Parser}
%include_class
{
    // states whether the parse was successful or not
    public $successful = true;
    public $retvalue = 0;
    private $lex;
    private $internalError = false;

    function __construct($lex) {
        $this->lex = $lex;
    }
}

%token_prefix EL_

%parse_accept
{
    $this->successful = !$this->internalError;
    $this->internalError = false;
    $this->retvalue = $this->_retvalue;
    echo "WORKED!!\n\n";
}

%syntax_error
{
/*
    $this->internalError = true;
    echo "Syntax Error on line " . $this->lex->line . ": token '" . 
        $this->lex->value . "' count ".$this->lex->counter." while parsing rule: ";*/
    foreach ($this->yystack as $entry) {
        echo $this->tokenName($entry->major) . '->';
    }
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    echo "\n";
    throw new \Exception('Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN. '), expected one of: ' . implode(',', $expect));
}

%left PLUS MINUS.
%left MULTIPLICATION DIVISION.

start(res)       ::= expression(expr). { res = expr; }

/* Unary minus or plus */
expression(res)  ::= PLUS expression(e). { res = +e; }
expression(res)  ::= MINUS expression(e). { res = -e; }

/* The common stuff */
expression(res)  ::= term(t). { res = t; }
expression(res)  ::= expression(e1) PLUS term(t2). { res = e1+t2; }
expression(res)  ::= expression(e1) MINUS term(t2). { res = e1-t2; }

term(res)        ::= factor(f). { res = f; }
term(res)        ::= term(t1) MULTIPLICATION factor(f2). { res = t1*f2; }
term(res)        ::= term(t1) DIVISION factor(f2). { res = t1/f2; }

factor(res)      ::= NUMBER(n). { res = n; }
factor(res)      ::= OPENP expression(e) CLOSEP. { res = e; }
