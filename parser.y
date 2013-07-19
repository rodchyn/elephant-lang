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
    throw new \Exception('Unexpected ' . $this->tokenName($yymajor) . '(' . $TOKEN. '), expected one of: ' . implode(',', $expect));
}

%right PLUS MINUS.
%left MULTIPLICATION DIVISION.
%right AND_LITERAL.
%right WHITESPACE NEWLINE CONCAT.
%right ASSIGN ARRAY_LEFT.
%nonassoc UNTIL.

start(res)       ::=                 . { res = yy('Block'); }

start(res)       ::= expression(expr). { res = expr . ';'; }

/* Unary minus or plus */

expression(A) ::= value(B)        . { A = B; }

statement(res)  ::= identifier(i) PLUS identifier(i2). { res = i . ' + ' . i2; }
statement(res)  ::= NUMBER(i) PLUS identifier(i2). { res = i . ' + ' . i2; }
statement(res)  ::= identifier(i) PLUS NUMBER(i2). { res = i . ' + ' . i2; }
statement(res)  ::= NUMBER(i) PLUS NUMBER(i2). { res = i . ' + ' . i2; }

statement(res)  ::= identifier(i) MINUS identifier(i2). { res = i . ' - ' . i2; }
statement(res)  ::= NUMBER(i) MINUS identifier(i2). { res = i . ' - ' . i2; }
statement(res)  ::= identifier(i) MINUS NUMBER(i2). { res = i . ' - ' . i2; }
statement(res)  ::= NUMBER(i) MINUS NUMBER(i2). { res = i . ' - ' . i2; }
statement(res)  ::= statement(s1) PLUS identifier(s2). { res = s1 . ' + ' . s2; }

statement(res)  ::= param(A) CONCAT IDENTIFIER(helperFunction). { res = yy('Helper', A, helperFunction); }

expression(res)  ::= PLUS expression(e). { res = +e; }
expression(res)  ::= MINUS expression(e). { res = -e; }

expression(res)  ::= statement(s1) AND_LITERAL statement(s2). { res = s1 . ' && ' . s2; }
expression(res)  ::= expression(e1) AND_LITERAL expression(e2). { res = e1 . ' && ' . e2; }
expression(res)  ::= expression(e1) AND_LITERAL statement(e2). { res = e1 . ' && ' . e2; }


expression(res)  ::= assign(a). { res = a; }
expression(res)  ::= if(B)    . { res = B; }

if(res)          ::= expression(e1) IF statement(s). { res = 'if (' . s . ') { ' . e1 . ' }'; }
if(res)          ::= expression(e1) IF expression(e2). { res = 'if (' . e2 . ') { ' . e1 . ' } '; }

expression(res)  ::= expression(e) Q_ASSIGN expression(e2). { res = 'if ( empty(' . e . ') || !' . e . ' ) { ' . e . ' = ' . e2 .'; } '; }
expression(res)  ::= statement(e) Q_ASSIGN statement(e2). { res = 'if ( empty(' . e . ') || !' . e . ' ) { ' . e . ' = ' . e2 .'; } '; }
expression(res)  ::= expression(e) Q_ASSIGN statement(e2). { res = 'if ( empty(' . e . ') || !' . e . ' ) { ' . e . ' = ' . e2 .'; } '; }
expression(res)  ::= statement(e) Q_ASSIGN expression(e2). { res = 'if ( empty(' . e . ') || !' . e . ' ) { ' . e . ' = ' . e2 .'; } '; }

assign(A)        ::= assignable(B) ASSIGN expression(C)                       . { A = yy('Assign', B, C); }
assign(res)      ::= identifier(i) ASSIGN alphanumeric(n). { res = i . ' = ' . n; }
assign(res)      ::= identifier(i) ASSIGN statement(s). { res = i . ' = ' . s; }

/* The common stuff */
expression(res)  ::= term(t). { res = t; }
expression(res)  ::= expression(e1) PLUS term(t2). { res = e1 . ' + ' . t2; }
expression(res)  ::= expression(e1) MINUS term(t2). { res = e1 . ' - ' . t2; }

assignable(A) ::= simpleAssignable(B) . { A = B; }
assignable(A) ::= array(B)            . { A = yy('Value', B); }

value(A) ::= assignable(B)    . { A = B; }

simpleAssignable(A) ::= identifier(B) . { A = yy('Value', B); }

array(A) ::= ARRAY_LEFT ARRAY_RIGHT                      . { A = yy('Arr', array()); }
array(A) ::= ARRAY_LEFT argList(B) optComma ARRAY_RIGHT  . { A = yy('Arr', B); }

argList(A) ::= arg(B)                                                       . { A = array(B); }
argList(A) ::= argList(B) COMMA arg(C)                                   . { A = array_merge(B, array(C)); }

arg(A) ::= expression(B)  . { A = B; }

term(res)        ::= IDENTIFIER(function) paramList(params). { res = function . ' (' . join(', ', params) . ')'; }
term(res)        ::= factor(f) WHITESPACE. { res = f; }
term(res)        ::= factor(f). { res = f; }
term(res)        ::= term(t1) MULTIPLICATION factor(f2). { res = t1*f2; }
term(res)        ::= term(t1) DIVISION factor(f2). { res = t1/f2; }

optComma(A) ::=             . { A = ''; }
optComma(A) ::= COMMA(B)    . { A = B; }

paramList(A) ::=                                . { A = array(); }
paramList(A) ::= param(B)                       . { A = array(B); }
paramList(A) ::= paramList(B) COMMA param(C) . { A = array_merge(B, array(C)); }

param(A) ::= paramVar(B)                          . { A = B; } /*yy('Param', B); }
param(A) ::= paramVar(B) RANGE_EXCLUSIVE       . { A = yy('Param', B, NULL, TRUE); }
param(A) ::= paramVar(B) EQUALS expression(C)  . { A = yy('Param', B, C); }*/

paramVar(A) ::= identifier(B)   . { A = B; }
paramVar(A) ::= NUMBER(B) . { A = B; }
paramVar(A) ::= string(B) . { A = B; }


//factor(res)      ::= string(s). { res = s; }
factor(res)      ::= NUMBER(n). { res = n; }
factor(res)      ::= OPENP expression(e) CLOSEP. { res = e; }
factor(res)      ::= BRACKET_LEFT expression(e) BRACKET_RIGHT. { res = e; }




alphanumeric(A) ::= NUMBER(B)  .  { A = B; } // { A = yy('Literal', B); }
alphanumeric(A) ::= string(B)  .  { A = B; } //{ A = yy('Literal', B); }

statement(res)   ::= identifier(i). { res = i; }

identifier(res)  ::= IDENTIFIER(i). { res = '$' . i; }

string(res)      ::= STRING_SINGLE(s). { res = s; }
string(res)      ::= STRING_DOUBLE(s). { res = s; }
