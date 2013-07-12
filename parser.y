/****************************
 ElephantLang Parser in PHP
*****************************/

%name EL_

%declare_class { class Parser }
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

    public static function tokenNumberByName($name)
    {
        $arr = array_flip(self::$yyTokenName);
        return $arr[$name];
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

    $this->internalError = true;
    //echo "Syntax Error on line " . $this->lex->line . ": token '" . $this->lex->value . "' count ".$this->lex->counter." while parsing rule: ";
    foreach ($this->yystack as $entry) {
        echo $this->tokenName($entry->major) . '->';
    }
    foreach ($this->yy_get_expected_tokens($yymajor) as $token) {
        $expect[] = self::$yyTokenName[$token];
    }
    echo "\n";
    echo "yyMajor = ";
    var_dump($yymajor);
    echo "\n";
    throw new \Exception('Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN. '), expected one of: ' . implode(',', $expect));
}

%left PLUS MINUS.
%left MULTIPLICATION DIVISION.
%right AND_LITERAL.
%right WHITESPACE.

start(res)       ::=                 . { res = yy('Block'); }

start(res)       ::= expression(expr). { res = expr; }

start(A) ::= body(B)                 . { A = B; }
start(A) ::= block(B) NEWLINE  . { A = B; }

body(A) ::= line(B)                       . { A = yy_Block::wrap(array(B)); }
body(A) ::= body(B) NEWLINE line(C) . { A = B->push(C); }
body(A) ::= body(B) NEWLINE         . { A = B; }

block(A) ::= INDENT OUTDENT         . { A = yy('Block'); }
block(A) ::= INDENT body(B) OUTDENT . { A = B; }

line(A) ::= expression(B) . { A = B; }
line(A) ::= statement(B)  . { A = B; }

/* Unary minus or plus */
expression(res)  ::= PLUS expression(e). { res = +e; }
expression(res)  ::= MINUS expression(e). { res = -e; }

expression(res)  ::= statement(s1) AND_LITERAL statement(s2). { res = '( ' . s1 . ' || ' . s2 . ' )'; }
expression(res)  ::= expression(e1) AND_LITERAL expression(e2). { res = '( ' . e1 . ' || ' . e2 . ' )'; }
expression(res)  ::= expression(e1) AND_LITERAL statement(e2). { res = '( ' . e1 . ' || ' . e2 . ' )'; }


expression(res)  ::= assign(a). { res = a; }
expression(res)  ::= if(B)    . { res = B; }

if(res)          ::= expression(e1) IF statement(s). { res = 'if (' . s . ') { ' . e1 . '; }'; }
if(res)          ::= expression(e1) IF expression(e2). { res = 'if (' . e2 . ' ) { ' . e1 . '; } '; }

expression(res)  ::= expression(e) Q_ASSIGN expression(e2). { res = 'if ( empty(' . e . ') || !' . e . ' ) { ' . e . ' = ' . e2 .'; } '; }
expression(res)  ::= statement(e) Q_ASSIGN statement(e2). { res = 'if ( empty(' . e . ') || !' . e . ' ) { ' . e . ' = ' . e2 .'; } '; }
expression(res)  ::= expression(e) Q_ASSIGN statement(e2). { res = 'if ( empty(' . e . ') || !' . e . ' ) { ' . e . ' = ' . e2 .'; } '; }
expression(res)  ::= statement(e) Q_ASSIGN expression(e2). { res = 'if ( empty(' . e . ') || !' . e . ' ) { ' . e . ' = ' . e2 .'; } '; }



/* The common stuff */
expression(res)  ::= term(t). { res = t; }
expression(res)  ::= expression(e1) PLUS term(t2). { res = e1+t2; }
expression(res)  ::= expression(e1) MINUS term(t2). { res = e1-t2; }

term(res)        ::= factor(f) WHITESPACE. { res = f; }
term(res)        ::= factor(f). { res = f; }
term(res)        ::= term(t1) MULTIPLICATION factor(f2). { res = t1*f2; }
term(res)        ::= term(t1) DIVISION factor(f2). { res = t1/f2; }

factor(res)      ::= string(s). { res = s; }
factor(res)      ::= NUMBER(n). { res = n; }
factor(res)      ::= OPENP expression(e) CLOSEP. { res = e; }
factor(res)      ::= BRACKET_LEFT expression(e) BRACKET_RIGHT. { res = e; }

assign(res)      ::= identifier(i) ASSIGN NUMBER(n). { res = i . ' = ' . n; }
assign(res)      ::= identifier(i) ASSIGN string(s). { res = i . ' = ' . s; }



statement(res)   ::= identifier(i). { res = i; }

identifier(res)  ::= IDENTIFIER(i). { res = '$' . i; }

string(res)      ::= STRING_SINGLE(s). { res = s; }
string(res)      ::= STRING_DOUBLE(s). { res = s; }
