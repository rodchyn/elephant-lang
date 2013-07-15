<?php

namespace ElephantLang\Parser;

use \ArrayAccess as ArrayAccess;


/* Driver template for the PHP_EL_rGenerator parser generator. (PHP port of LEMON)
*/

/**
 * This can be used to store both the string representation of
 * a token, and any useful meta-data associated with the token.
 *
 * meta-data should be stored as an array
 */
class yyToken implements ArrayAccess
{
    public $string = '';
    public $metadata = array();

    function __construct($s, $m = array())
    {
        if ($s instanceof yyToken) {
            $this->string = $s->string;
            $this->metadata = $s->metadata;
        } else {
            $this->string = (string) $s;
            if ($m instanceof yyToken) {
                $this->metadata = $m->metadata;
            } elseif (is_array($m)) {
                $this->metadata = $m;
            }
        }
    }

    function __toString()
    {
        return $this->string;
    }

    function offsetExists($offset)
    {
        return isset($this->metadata[$offset]);
    }

    function offsetGet($offset)
    {
        return $this->metadata[$offset];
    }

    function offsetSet($offset, $value)
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

    function offsetUnset($offset)
    {
        unset($this->metadata[$offset]);
    }
}

/** The following structure represents a single element of the
 * parser's stack.  Information stored includes:
 *
 *   +  The state number for the parser at this level of the stack.
 *
 *   +  The value of the token stored at this level of the stack.
 *      (In other words, the "major" token.)
 *
 *   +  The semantic value stored at this level of the stack.  This is
 *      the information used by the action routines in the grammar.
 *      It is sometimes called the "minor" token.
 */
class yyStackEntry
{
    public $stateno;       /* The state-number */
    public $major;         /* The major token value.  This is the code
                     ** number for the token at this stack level */
    public $minor; /* The user-supplied minor token value.  This
                   ** is the value of the token  */
};

// code external to the class is included here

// declare_class is output here
#line 7 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
 class Parser #line 102 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
{
  static $LINE = 0;
  static $FILE = 'unknown';

/* First off, code is included which follows the "include_class" declaration
** in the input file. */
#line 9 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"

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
#line 127 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"

/* Next is all token values, as class constants
*/
/* 
** These constants (all generated automatically by the parser generator)
** specify the various kinds of tokens (terminals) that the parser
** understands. 
**
** Each symbol here is a terminal symbol in the grammar.
*/
    const EL_PLUS                           =  1;
    const EL_MINUS                          =  2;
    const EL_MULTIPLICATION                 =  3;
    const EL_DIVISION                       =  4;
    const EL_AND_LITERAL                    =  5;
    const EL_WHITESPACE                     =  6;
    const EL_NEWLINE                        =  7;
    const EL_CONCAT                         =  8;
    const EL_NUMBER                         =  9;
    const EL_IF                             = 10;
    const EL_Q_ASSIGN                       = 11;
    const EL_ASSIGN                         = 12;
    const EL_OPENP                          = 13;
    const EL_CLOSEP                         = 14;
    const EL_BRACKET_LEFT                   = 15;
    const EL_BRACKET_RIGHT                  = 16;
    const EL_IDENTIFIER                     = 17;
    const EL_STRING_SINGLE                  = 18;
    const EL_STRING_DOUBLE                  = 19;
    const YY_NO_ACTION = 108;
    const YY_ACCEPT_ACTION = 107;
    const YY_ERROR_ACTION = 106;

/* Next are that tables used to determine what action to take based on the
** current state and lookahead token.  These tables are used to implement
** functions that take a state number and lookahead value and return an
** action integer.  
**
** Suppose the action integer is N.  Then the action is determined as
** follows
**
**   0 <= N < self::YYNSTATE                              Shift N.  That is,
**                                                        push the lookahead
**                                                        token onto the stack
**                                                        and goto state N.
**
**   self::YYNSTATE <= N < self::YYNSTATE+self::YYNRULE   Reduce by rule N-YYNSTATE.
**
**   N == self::YYNSTATE+self::YYNRULE                    A syntax error has occurred.
**
**   N == self::YYNSTATE+self::YYNRULE+1                  The parser accepts its
**                                                        input. (and concludes parsing)
**
**   N == self::YYNSTATE+self::YYNRULE+2                  No such action.  Denotes unused
**                                                        slots in the yy_action[] table.
**
** The action table is constructed as a single large static array $yy_action.
** Given state S and lookahead X, the action is computed as
**
**      self::$yy_action[self::$yy_shift_ofst[S] + X ]
**
** If the index value self::$yy_shift_ofst[S]+X is out of range or if the value
** self::$yy_lookahead[self::$yy_shift_ofst[S]+X] is not equal to X or if
** self::$yy_shift_ofst[S] is equal to self::YY_SHIFT_USE_DFLT, it means that
** the action is not in the table and that self::$yy_default[S] should be used instead.  
**
** The formula above is for computing the action when the lookahead is
** a terminal symbol.  If the lookahead is a non-terminal (as occurs after
** a reduce action) then the static $yy_reduce_ofst array is used in place of
** the static $yy_shift_ofst array and self::YY_REDUCE_USE_DFLT is used in place of
** self::YY_SHIFT_USE_DFLT.
**
** The following are the tables generated in this section:
**
**  self::$yy_action        A single table containing all actions.
**  self::$yy_lookahead     A table containing the lookahead for each entry in
**                          yy_action.  Used to detect hash collisions.
**  self::$yy_shift_ofst    For each state, the offset into self::$yy_action for
**                          shifting terminals.
**  self::$yy_reduce_ofst   For each state, the offset into self::$yy_action for
**                          shifting non-terminals after a reduce.
**  self::$yy_default       Default action for each state.
*/
    const YY_SZ_ACTTAB = 136;
static public $yy_action = array(
 /*     0 */   107,   24,   34,   25,   42,   45,   46,   33,   41,   26,
 /*    10 */    34,   25,   42,   45,   49,   33,   41,   21,   32,   25,
 /*    20 */    42,   45,   63,   33,   41,   27,   34,   25,   42,   45,
 /*    30 */    49,   33,   41,   23,   29,   25,   42,   45,   48,   33,
 /*    40 */    41,   20,   34,   25,   42,   45,   49,   33,   41,   28,
 /*    50 */    31,   25,   42,   45,   61,   33,   41,   22,   36,   25,
 /*    60 */    42,   45,   49,   33,   41,   19,   34,   25,   42,   45,
 /*    70 */    37,   33,   41,    3,    5,   18,   15,   62,   49,   56,
 /*    80 */    57,   39,   18,   15,   53,    2,    9,    7,    2,   49,
 /*    90 */     7,   11,   10,   11,   10,    8,   52,    8,   14,   13,
 /*   100 */     4,    1,    4,    1,   51,   11,   10,    8,   43,    8,
 /*   110 */    17,   16,    4,    1,    4,    1,   58,   38,   12,   74,
 /*   120 */    59,   60,   38,   54,    6,   40,   55,   30,   41,   35,
 /*   130 */    41,   44,   50,   49,   47,   64,
    );
    static public $yy_lookahead = array(
 /*     0 */    21,   22,   23,   24,   25,   26,    9,   28,   29,   22,
 /*    10 */    23,   24,   25,   26,   17,   28,   29,   22,   23,   24,
 /*    20 */    25,   26,    9,   28,   29,   22,   23,   24,   25,   26,
 /*    30 */    17,   28,   29,   22,   23,   24,   25,   26,    9,   28,
 /*    40 */    29,   22,   23,   24,   25,   26,   17,   28,   29,   22,
 /*    50 */    23,   24,   25,   26,    9,   28,   29,   22,   23,   24,
 /*    60 */    25,   26,   17,   28,   29,   22,   23,   24,   25,   26,
 /*    70 */     9,   28,   29,    1,    2,    1,    2,   24,   17,   18,
 /*    80 */    19,    9,    1,    2,    9,   13,   12,   15,   13,   17,
 /*    90 */    15,    1,    2,    1,    2,    5,   29,    5,    3,    4,
 /*   100 */    10,   11,   10,   11,   14,    1,    2,    5,   16,    5,
 /*   110 */     1,    2,   10,   11,   10,   11,   23,   24,    5,   31,
 /*   120 */    27,   23,   24,   30,   11,    9,   29,   28,   29,   28,
 /*   130 */    29,    6,   24,   17,   24,   24,
);
    const YY_SHIFT_USE_DFLT = -4;
    const YY_SHIFT_MAX = 41;
    static public $yy_shift_ofst = array(
 /*     0 */    72,   72,   72,   72,   72,   72,   72,   72,   72,   61,
 /*    10 */    75,   75,  116,   75,   75,   -3,   13,   45,   29,   90,
 /*    20 */    92,  104,  104,  104,  104,   74,  102,  102,  102,  113,
 /*    30 */    95,  113,  113,   95,  113,   95,  113,  109,   81,  109,
 /*    40 */   109,  125,
);
    const YY_REDUCE_USE_DFLT = -22;
    const YY_REDUCE_MAX = 18;
    static public $yy_reduce_ofst = array(
 /*     0 */   -21,   35,   43,  -13,   -5,    3,   11,   19,   27,   93,
 /*    10 */    99,  101,   98,   67,   97,  108,   53,  111,  110,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(1, 2, 9, 13, 15, 17, ),
        /* 1 */ array(1, 2, 9, 13, 15, 17, ),
        /* 2 */ array(1, 2, 9, 13, 15, 17, ),
        /* 3 */ array(1, 2, 9, 13, 15, 17, ),
        /* 4 */ array(1, 2, 9, 13, 15, 17, ),
        /* 5 */ array(1, 2, 9, 13, 15, 17, ),
        /* 6 */ array(1, 2, 9, 13, 15, 17, ),
        /* 7 */ array(1, 2, 9, 13, 15, 17, ),
        /* 8 */ array(1, 2, 9, 13, 15, 17, ),
        /* 9 */ array(9, 17, 18, 19, ),
        /* 10 */ array(9, 13, 15, ),
        /* 11 */ array(9, 13, 15, ),
        /* 12 */ array(9, 17, ),
        /* 13 */ array(9, 13, 15, ),
        /* 14 */ array(9, 13, 15, ),
        /* 15 */ array(9, 17, ),
        /* 16 */ array(9, 17, ),
        /* 17 */ array(9, 17, ),
        /* 18 */ array(9, 17, ),
        /* 19 */ array(1, 2, 5, 10, 11, 14, ),
        /* 20 */ array(1, 2, 5, 10, 11, 16, ),
        /* 21 */ array(1, 2, 5, 10, 11, ),
        /* 22 */ array(1, 2, 5, 10, 11, ),
        /* 23 */ array(1, 2, 5, 10, 11, ),
        /* 24 */ array(1, 2, 5, 10, 11, ),
        /* 25 */ array(1, 2, 12, ),
        /* 26 */ array(5, 10, 11, ),
        /* 27 */ array(5, 10, 11, ),
        /* 28 */ array(5, 10, 11, ),
        /* 29 */ array(5, 11, ),
        /* 30 */ array(3, 4, ),
        /* 31 */ array(5, 11, ),
        /* 32 */ array(5, 11, ),
        /* 33 */ array(3, 4, ),
        /* 34 */ array(5, 11, ),
        /* 35 */ array(3, 4, ),
        /* 36 */ array(5, 11, ),
        /* 37 */ array(1, 2, ),
        /* 38 */ array(1, 2, ),
        /* 39 */ array(1, 2, ),
        /* 40 */ array(1, 2, ),
        /* 41 */ array(6, ),
        /* 42 */ array(),
        /* 43 */ array(),
        /* 44 */ array(),
        /* 45 */ array(),
        /* 46 */ array(),
        /* 47 */ array(),
        /* 48 */ array(),
        /* 49 */ array(),
        /* 50 */ array(),
        /* 51 */ array(),
        /* 52 */ array(),
        /* 53 */ array(),
        /* 54 */ array(),
        /* 55 */ array(),
        /* 56 */ array(),
        /* 57 */ array(),
        /* 58 */ array(),
        /* 59 */ array(),
        /* 60 */ array(),
        /* 61 */ array(),
        /* 62 */ array(),
        /* 63 */ array(),
        /* 64 */ array(),
);
    static public $yy_default = array(
 /*     0 */    65,  106,  106,  106,  106,  106,  106,  106,  106,  106,
 /*    10 */   106,  106,  106,  106,  106,  106,  106,  106,  106,  106,
 /*    20 */   106,   83,   84,   87,   66,  102,   75,   76,   78,   85,
 /*    30 */    92,   79,   82,   90,  106,   91,   86,  100,  102,   97,
 /*    40 */   106,   94,   80,   99,   93,   81,   73,   67,   69,  103,
 /*    50 */    71,   98,   96,   97,  101,   95,  104,  105,   89,   88,
 /*    60 */    77,   70,   72,   74,   68,
);
/* The next thing included is series of defines which control
** various aspects of the generated parser.
**    self::YYNOCODE      is a number which corresponds
**                        to no legal terminal or nonterminal number.  This
**                        number is used to fill in empty slots of the hash 
**                        table.
**    self::YYFALLBACK    If defined, this indicates that one or more tokens
**                        have fall-back values which should be used if the
**                        original value of the token will not parse.
**    self::YYSTACKDEPTH  is the maximum depth of the parser's stack.
**    self::YYNSTATE      the combined number of states.
**    self::YYNRULE       the number of rules in the grammar
**    self::YYERRORSYMBOL is the code number of the error symbol.  If not
**                        defined, then do no error processing.
*/
    const YYNOCODE = 32;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 65;
    const YYNRULE = 41;
    const YYERRORSYMBOL = 20;
    const YYERRSYMDT = 'yy0';
    const YYFALLBACK = 0;
    /** The next table maps tokens into fallback tokens.  If a construct
     * like the following:
     * 
     *      %fallback ID X Y Z.
     *
     * appears in the grammer, then ID becomes a fallback token for X, Y,
     * and Z.  Whenever one of the tokens X, Y, or Z is input to the parser
     * but it does not parse, the type of the token is changed to ID and
     * the parse is retried before an error is thrown.
     */
    static public $yyFallback = array(
    );
    /**
     * Turn parser tracing on by giving a stream to which to write the trace
     * and a prompt to preface each trace message.  Tracing is turned off
     * by making either argument NULL 
     *
     * Inputs:
     * 
     * - A stream resource to which trace output should be written.
     *   If NULL, then tracing is turned off.
     * - A prefix string written at the beginning of every
     *   line of trace output.  If NULL, then tracing is
     *   turned off.
     *
     * Outputs:
     * 
     * - None.
     * @param resource
     * @param string
     */
    static function Trace($TraceFILE, $zTracePrompt)
    {
        if (!$TraceFILE) {
            $zTracePrompt = 0;
        } elseif (!$zTracePrompt) {
            $TraceFILE = 0;
        }
        self::$yyTraceFILE = $TraceFILE;
        self::$yyTracePrompt = $zTracePrompt;
    }

    /**
     * Output debug information to output (php://output stream)
     */
    static function PrintTrace()
    {
        self::$yyTraceFILE = fopen('php://output', 'w');
        self::$yyTracePrompt = '';
    }

    /**
     * @var resource|0
     */
    static public $yyTraceFILE;
    /**
     * String to prepend to debug output
     * @var string|0
     */
    static public $yyTracePrompt;
    /**
     * @var int
     */
    public $yyidx = -1;                    /* Index of top element in stack */
    /**
     * @var int
     */
    public $yyerrcnt;                 /* Shifts left before out of the error */
    /**
     * @var array
     */
    public $yystack = array();  /* The parser's stack */

    /**
     * For tracing shifts, the names of all terminals and nonterminals
     * are required.  The following table supplies these names
     * @var array
     */
    static public $yyTokenName = array( 
  '$',             'PLUS',          'MINUS',         'MULTIPLICATION',
  'DIVISION',      'AND_LITERAL',   'WHITESPACE',    'NEWLINE',     
  'CONCAT',        'NUMBER',        'IF',            'Q_ASSIGN',    
  'ASSIGN',        'OPENP',         'CLOSEP',        'BRACKET_LEFT',
  'BRACKET_RIGHT',  'IDENTIFIER',    'STRING_SINGLE',  'STRING_DOUBLE',
  'error',         'start',         'expression',    'statement',   
  'identifier',    'assign',        'if',            'alphanumeric',
  'term',          'factor',        'string',      
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::=",
 /*   1 */ "start ::= expression",
 /*   2 */ "statement ::= identifier PLUS identifier",
 /*   3 */ "statement ::= NUMBER PLUS identifier",
 /*   4 */ "statement ::= identifier PLUS NUMBER",
 /*   5 */ "statement ::= NUMBER PLUS NUMBER",
 /*   6 */ "statement ::= identifier MINUS identifier",
 /*   7 */ "statement ::= NUMBER MINUS identifier",
 /*   8 */ "statement ::= identifier MINUS NUMBER",
 /*   9 */ "statement ::= NUMBER MINUS NUMBER",
 /*  10 */ "expression ::= PLUS expression",
 /*  11 */ "expression ::= MINUS expression",
 /*  12 */ "expression ::= statement AND_LITERAL statement",
 /*  13 */ "expression ::= expression AND_LITERAL expression",
 /*  14 */ "expression ::= expression AND_LITERAL statement",
 /*  15 */ "expression ::= assign",
 /*  16 */ "expression ::= if",
 /*  17 */ "if ::= expression IF statement",
 /*  18 */ "if ::= expression IF expression",
 /*  19 */ "expression ::= expression Q_ASSIGN expression",
 /*  20 */ "expression ::= statement Q_ASSIGN statement",
 /*  21 */ "expression ::= expression Q_ASSIGN statement",
 /*  22 */ "expression ::= statement Q_ASSIGN expression",
 /*  23 */ "assign ::= identifier ASSIGN alphanumeric",
 /*  24 */ "assign ::= identifier ASSIGN statement",
 /*  25 */ "expression ::= term",
 /*  26 */ "expression ::= expression PLUS term",
 /*  27 */ "expression ::= expression MINUS term",
 /*  28 */ "term ::= factor WHITESPACE",
 /*  29 */ "term ::= factor",
 /*  30 */ "term ::= term MULTIPLICATION factor",
 /*  31 */ "term ::= term DIVISION factor",
 /*  32 */ "factor ::= NUMBER",
 /*  33 */ "factor ::= OPENP expression CLOSEP",
 /*  34 */ "factor ::= BRACKET_LEFT expression BRACKET_RIGHT",
 /*  35 */ "alphanumeric ::= NUMBER",
 /*  36 */ "alphanumeric ::= string",
 /*  37 */ "statement ::= identifier",
 /*  38 */ "identifier ::= IDENTIFIER",
 /*  39 */ "string ::= STRING_SINGLE",
 /*  40 */ "string ::= STRING_DOUBLE",
    );

    /**
     * This function returns the symbolic name associated with a token
     * value.
     * @param int
     * @return string
     */
    static function tokenName($tokenType)
    {
        if ($tokenType === 0) {
            return 'End of Input';
        }
        if ($tokenType > 0 && $tokenType < count(self::$yyTokenName)) {
            return self::$yyTokenName[$tokenType];
        } else {
            return "Unknown";
        }
    }

    /**
     * The following function deletes the value associated with a
     * symbol.  The symbol can be either a terminal or nonterminal.
     * @param int the symbol code
     * @param mixed the symbol's value
     */
    static function yy_destructor($yymajor, $yypminor)
    {
        switch ($yymajor) {
        /* Here is inserted the actions which take place when a
        ** terminal or non-terminal is destroyed.  This can happen
        ** when the symbol is popped from the stack during a
        ** reduce or during error processing or when a parser is 
        ** being destroyed before it is finished parsing.
        **
        ** Note: during a reduce, the only symbols destroyed are those
        ** which appear on the RHS of the rule, but which are not used
        ** inside the C code.
        */
            default:  break;   /* If no destructor action specified: do nothing */
        }
    }

    /**
     * Pop the parser's stack once.
     *
     * If there is a destructor routine associated with the token which
     * is popped from the stack, then call it.
     *
     * Return the major token number for the symbol popped.
     * @param yyParser
     * @return int
     */
    function yy_pop_parser_stack()
    {
        if (!count($this->yystack)) {
            return;
        }
        $yytos = array_pop($this->yystack);
        if (self::$yyTraceFILE && $this->yyidx >= 0) {
            fwrite(self::$yyTraceFILE,
                self::$yyTracePrompt . 'Popping ' . self::tokenName($yytos->major) .
                    "\n");
        }
        $yymajor = $yytos->major;
        self::yy_destructor($yymajor, $yytos->minor);
        $this->yyidx--;
        return $yymajor;
    }

    /**
     * Deallocate and destroy a parser.  Destructors are all called for
     * all stack elements before shutting the parser down.
     */
    function __destruct()
    {
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        if (is_resource(self::$yyTraceFILE)) {
            fclose(self::$yyTraceFILE);
        }
    }

    /**
     * Based on the current state and parser stack, get a list of all
     * possible lookahead tokens
     * @param int
     * @return array
     */
    function yy_get_expected_tokens($token)
    {
        $state = $this->yystack[$this->yyidx]->stateno;
        $expected = self::$yyExpectedTokens[$state];
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return $expected;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return array_unique($expected);
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate])) {
                        $expected += self::$yyExpectedTokens[$nextstate];
                            if (in_array($token,
                                  self::$yyExpectedTokens[$nextstate], true)) {
                            $this->yyidx = $yyidx;
                            $this->yystack = $stack;
                            return array_unique($expected);
                        }
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return array_unique($expected);
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return $expected;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        return array_unique($expected);
    }

    /**
     * Based on the parser state and current parser stack, determine whether
     * the lookahead token is possible.
     * 
     * The parser will convert the token value to an error token if not.  This
     * catches some unusual edge cases where the parser would fail.
     * @param int
     * @return bool
     */
    function yy_is_expected_token($token)
    {
        if ($token === 0) {
            return true; // 0 is not part of this
        }
        $state = $this->yystack[$this->yyidx]->stateno;
        if (in_array($token, self::$yyExpectedTokens[$state], true)) {
            return true;
        }
        $stack = $this->yystack;
        $yyidx = $this->yyidx;
        do {
            $yyact = $this->yy_find_shift_action($token);
            if ($yyact >= self::YYNSTATE && $yyact < self::YYNSTATE + self::YYNRULE) {
                // reduce action
                $done = 0;
                do {
                    if ($done++ == 100) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // too much recursion prevents proper detection
                        // so give up
                        return true;
                    }
                    $yyruleno = $yyact - self::YYNSTATE;
                    $this->yyidx -= self::$yyRuleInfo[$yyruleno]['rhs'];
                    $nextstate = $this->yy_find_reduce_action(
                        $this->yystack[$this->yyidx]->stateno,
                        self::$yyRuleInfo[$yyruleno]['lhs']);
                    if (isset(self::$yyExpectedTokens[$nextstate]) &&
                          in_array($token, self::$yyExpectedTokens[$nextstate], true)) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        return true;
                    }
                    if ($nextstate < self::YYNSTATE) {
                        // we need to shift a non-terminal
                        $this->yyidx++;
                        $x = new yyStackEntry;
                        $x->stateno = $nextstate;
                        $x->major = self::$yyRuleInfo[$yyruleno]['lhs'];
                        $this->yystack[$this->yyidx] = $x;
                        continue 2;
                    } elseif ($nextstate == self::YYNSTATE + self::YYNRULE + 1) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        if (!$token) {
                            // end of input: this is valid
                            return true;
                        }
                        // the last token was just ignored, we can't accept
                        // by ignoring input, this is in essence ignoring a
                        // syntax error!
                        return false;
                    } elseif ($nextstate === self::YY_NO_ACTION) {
                        $this->yyidx = $yyidx;
                        $this->yystack = $stack;
                        // input accepted, but not shifted (I guess)
                        return true;
                    } else {
                        $yyact = $nextstate;
                    }
                } while (true);
            }
            break;
        } while (true);
        $this->yyidx = $yyidx;
        $this->yystack = $stack;
        return true;
    }

    /**
     * Find the appropriate action for a parser given the terminal
     * look-ahead token iLookAhead.
     *
     * If the look-ahead token is YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return YY_NO_ACTION.
     * @param int The look-ahead token
     */
    function yy_find_shift_action($iLookAhead)
    {
        $stateno = $this->yystack[$this->yyidx]->stateno;
     
        /* if ($this->yyidx < 0) return self::YY_NO_ACTION;  */
        if (!isset(self::$yy_shift_ofst[$stateno])) {
            // no shift actions
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_shift_ofst[$stateno];
        if ($i === self::YY_SHIFT_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            if (count(self::$yyFallback) && $iLookAhead < count(self::$yyFallback)
                   && ($iFallback = self::$yyFallback[$iLookAhead]) != 0) {
                if (self::$yyTraceFILE) {
                    fwrite(self::$yyTraceFILE, self::$yyTracePrompt . "FALLBACK " .
                        self::tokenName($iLookAhead) . " => " .
                        self::tokenName($iFallback) . "\n");
                }
                return $this->yy_find_shift_action($iFallback);
            }
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Find the appropriate action for a parser given the non-terminal
     * look-ahead token $iLookAhead.
     *
     * If the look-ahead token is self::YYNOCODE, then check to see if the action is
     * independent of the look-ahead.  If it is, return the action, otherwise
     * return self::YY_NO_ACTION.
     * @param int Current state number
     * @param int The look-ahead token
     */
    function yy_find_reduce_action($stateno, $iLookAhead)
    {
        /* $stateno = $this->yystack[$this->yyidx]->stateno; */

        if (!isset(self::$yy_reduce_ofst[$stateno])) {
            return self::$yy_default[$stateno];
        }
        $i = self::$yy_reduce_ofst[$stateno];
        if ($i == self::YY_REDUCE_USE_DFLT) {
            return self::$yy_default[$stateno];
        }
        if ($iLookAhead == self::YYNOCODE) {
            return self::YY_NO_ACTION;
        }
        $i += $iLookAhead;
        if ($i < 0 || $i >= self::YY_SZ_ACTTAB ||
              self::$yy_lookahead[$i] != $iLookAhead) {
            return self::$yy_default[$stateno];
        } else {
            return self::$yy_action[$i];
        }
    }

    /**
     * Perform a shift action.
     * @param int The new state to shift in
     * @param int The major token to shift in
     * @param mixed the minor token to shift in
     */
    function yy_shift($yyNewState, $yyMajor, $yypMinor)
    {
        $this->yyidx++;
        if ($this->yyidx >= self::YYSTACKDEPTH) {
            $this->yyidx--;
            if (self::$yyTraceFILE) {
                fprintf(self::$yyTraceFILE, "%sStack Overflow!\n", self::$yyTracePrompt);
            }
            while ($this->yyidx >= 0) {
                $this->yy_pop_parser_stack();
            }
            /* Here code is inserted which will execute if the parser
            ** stack ever overflows */
            return;
        }
        $yytos = new yyStackEntry;
        $yytos->stateno = $yyNewState;
        $yytos->major = $yyMajor;
        $yytos->minor = $yypMinor;
        array_push($this->yystack, $yytos);
        if (self::$yyTraceFILE && $this->yyidx > 0) {
            fprintf(self::$yyTraceFILE, "%sShift %d\n", self::$yyTracePrompt,
                $yyNewState);
            fprintf(self::$yyTraceFILE, "%sStack:", self::$yyTracePrompt);
            for ($i = 1; $i <= $this->yyidx; $i++) {
                fprintf(self::$yyTraceFILE, " %s",
                    self::tokenName($this->yystack[$i]->major));
            }
            fwrite(self::$yyTraceFILE,"\n");
        }
    }

    /**
     * The following table contains information about every rule that
     * is used during the reduce.
     *
     * <pre>
     * array(
     *  array(
     *   int $lhs;         Symbol on the left-hand side of the rule
     *   int $nrhs;     Number of right-hand side symbols in the rule
     *  ),...
     * );
     * </pre>
     */
    static public $yyRuleInfo = array(
  array( 'lhs' => 21, 'rhs' => 0 ),
  array( 'lhs' => 21, 'rhs' => 1 ),
  array( 'lhs' => 23, 'rhs' => 3 ),
  array( 'lhs' => 23, 'rhs' => 3 ),
  array( 'lhs' => 23, 'rhs' => 3 ),
  array( 'lhs' => 23, 'rhs' => 3 ),
  array( 'lhs' => 23, 'rhs' => 3 ),
  array( 'lhs' => 23, 'rhs' => 3 ),
  array( 'lhs' => 23, 'rhs' => 3 ),
  array( 'lhs' => 23, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 2 ),
  array( 'lhs' => 22, 'rhs' => 2 ),
  array( 'lhs' => 22, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 1 ),
  array( 'lhs' => 22, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 3 ),
  array( 'lhs' => 25, 'rhs' => 3 ),
  array( 'lhs' => 25, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 1 ),
  array( 'lhs' => 22, 'rhs' => 3 ),
  array( 'lhs' => 22, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 2 ),
  array( 'lhs' => 28, 'rhs' => 1 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 29, 'rhs' => 1 ),
  array( 'lhs' => 29, 'rhs' => 3 ),
  array( 'lhs' => 29, 'rhs' => 3 ),
  array( 'lhs' => 27, 'rhs' => 1 ),
  array( 'lhs' => 27, 'rhs' => 1 ),
  array( 'lhs' => 23, 'rhs' => 1 ),
  array( 'lhs' => 24, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
    );

    /**
     * The following table contains a mapping of reduce action to method name
     * that handles the reduction.
     * 
     * If a rule is not set, it has no handler.
     */
    static public $yyReduceMap = array(
        0 => 0,
        1 => 1,
        15 => 1,
        16 => 1,
        25 => 1,
        29 => 1,
        32 => 1,
        35 => 1,
        36 => 1,
        39 => 1,
        40 => 1,
        2 => 2,
        3 => 2,
        4 => 2,
        5 => 2,
        6 => 6,
        7 => 6,
        8 => 6,
        9 => 6,
        10 => 10,
        11 => 11,
        12 => 12,
        13 => 12,
        14 => 12,
        17 => 17,
        18 => 18,
        19 => 19,
        20 => 19,
        21 => 19,
        22 => 19,
        23 => 23,
        24 => 23,
        26 => 26,
        27 => 27,
        28 => 28,
        33 => 28,
        34 => 28,
        30 => 30,
        31 => 31,
        37 => 37,
        38 => 38,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 56 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r0(){ $this->_retvalue = yy('Block');     }
#line 958 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 58 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r1(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 961 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 62 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r2(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor . ' + ' . $this->yystack[$this->yyidx + 0]->minor;     }
#line 964 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 67 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r6(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor . ' - ' . $this->yystack[$this->yyidx + 0]->minor;     }
#line 967 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 72 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r10(){ $this->_retvalue = +$this->yystack[$this->yyidx + 0]->minor;     }
#line 970 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 73 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r11(){ $this->_retvalue = -$this->yystack[$this->yyidx + 0]->minor;     }
#line 973 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 75 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r12(){ $this->_retvalue = '( ' . $this->yystack[$this->yyidx + -2]->minor . ' || ' . $this->yystack[$this->yyidx + 0]->minor . ' )';     }
#line 976 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 83 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r17(){ $this->_retvalue = 'if (' . $this->yystack[$this->yyidx + 0]->minor . ') { ' . $this->yystack[$this->yyidx + -2]->minor . ' }';     }
#line 979 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 84 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r18(){ $this->_retvalue = 'if (' . $this->yystack[$this->yyidx + 0]->minor . ') { ' . $this->yystack[$this->yyidx + -2]->minor . ' } ';     }
#line 982 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 86 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r19(){ $this->_retvalue = 'if ( empty(' . $this->yystack[$this->yyidx + -2]->minor . ') || !' . $this->yystack[$this->yyidx + -2]->minor . ' ) { ' . $this->yystack[$this->yyidx + -2]->minor . ' = ' . $this->yystack[$this->yyidx + 0]->minor .'; } ';     }
#line 985 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 92 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r23(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor . ' = ' . $this->yystack[$this->yyidx + 0]->minor;     }
#line 988 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 97 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r26(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor+$this->yystack[$this->yyidx + 0]->minor;     }
#line 991 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 98 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r27(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor-$this->yystack[$this->yyidx + 0]->minor;     }
#line 994 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 100 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r28(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;     }
#line 997 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 102 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r30(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor*$this->yystack[$this->yyidx + 0]->minor;     }
#line 1000 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 103 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r31(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor/$this->yystack[$this->yyidx + 0]->minor;     }
#line 1003 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 116 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r37(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor . ';';     }
#line 1006 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 118 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r38(){ $this->_retvalue = '$' . $this->yystack[$this->yyidx + 0]->minor;     }
#line 1009 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"

    /**
     * placeholder for the left hand side in a reduce operation.
     * 
     * For a parser with a rule like this:
     * <pre>
     * rule(A) ::= B. { A = 1; }
     * </pre>
     * 
     * The parser will translate to something like:
     * 
     * <code>
     * function yy_r0(){$this->_retvalue = 1;}
     * </code>
     */
    private $_retvalue;

    /**
     * Perform a reduce action and the shift that must immediately
     * follow the reduce.
     * 
     * For a rule such as:
     * 
     * <pre>
     * A ::= B blah C. { dosomething(); }
     * </pre>
     * 
     * This function will first call the action, if any, ("dosomething();" in our
     * example), and then it will pop three states from the stack,
     * one for each entry on the right-hand side of the expression
     * (B, blah, and C in our example rule), and then push the result of the action
     * back on to the stack with the resulting state reduced to (as described in the .out
     * file)
     * @param int Number of the rule by which to reduce
     */
    function yy_reduce($yyruleno)
    {
        //int $yygoto;                     /* The next state */
        //int $yyact;                      /* The next action */
        //mixed $yygotominor;        /* The LHS of the rule reduced */
        //yyStackEntry $yymsp;            /* The top of the parser's stack */
        //int $yysize;                     /* Amount to pop the stack */
        $yymsp = $this->yystack[$this->yyidx];
        if (self::$yyTraceFILE && $yyruleno >= 0 
              && $yyruleno < count(self::$yyRuleName)) {
            fprintf(self::$yyTraceFILE, "%sReduce (%d) [%s].\n",
                self::$yyTracePrompt, $yyruleno,
                self::$yyRuleName[$yyruleno]);
        }

        $this->_retvalue = $yy_lefthand_side = null;
        if (array_key_exists($yyruleno, self::$yyReduceMap)) {
            // call the action
            $this->_retvalue = null;
            $this->{'yy_r' . self::$yyReduceMap[$yyruleno]}();
            $yy_lefthand_side = $this->_retvalue;
        }
        $yygoto = self::$yyRuleInfo[$yyruleno]['lhs'];
        $yysize = self::$yyRuleInfo[$yyruleno]['rhs'];
        $this->yyidx -= $yysize;
        for ($i = $yysize; $i; $i--) {
            // pop all of the right-hand side parameters
            array_pop($this->yystack);
        }
        $yyact = $this->yy_find_reduce_action($this->yystack[$this->yyidx]->stateno, $yygoto);
        if ($yyact < self::YYNSTATE) {
            /* If we are not debugging and the reduce action popped at least
            ** one element off the stack, then we can push the new element back
            ** onto the stack here, and skip the stack overflow test in yy_shift().
            ** That gives a significant speed improvement. */
            if (!self::$yyTraceFILE && $yysize) {
                $this->yyidx++;
                $x = new yyStackEntry;
                $x->stateno = $yyact;
                $x->major = $yygoto;
                $x->minor = $yy_lefthand_side;
                $this->yystack[$this->yyidx] = $x;
            } else {
                $this->yy_shift($yyact, $yygoto, $yy_lefthand_side);
            }
        } elseif ($yyact == self::YYNSTATE + self::YYNRULE + 1) {
            $this->yy_accept();
        }
    }

    /**
     * The following code executes when the parse fails
     * 
     * Code from %parse_fail is inserted here
     */
    function yy_parse_failed()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sFail!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser fails */
    }

    /**
     * The following code executes when a syntax error first occurs.
     * 
     * %syntax_error code is inserted here
     * @param int The major type of the error token
     * @param mixed The minor type of the error token
     */
    function yy_syntax_error($yymajor, $TOKEN)
    {
#line 37 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"


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
#line 1135 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
    }

    /**
     * The following is executed when the parser accepts
     * 
     * %parse_accept code is inserted here
     */
    function yy_accept()
    {
        if (self::$yyTraceFILE) {
            fprintf(self::$yyTraceFILE, "%sAccept!\n", self::$yyTracePrompt);
        }
        while ($this->yyidx >= 0) {
            $stack = $this->yy_pop_parser_stack();
        }
        /* Here code is inserted which will be executed whenever the
        ** parser accepts */
#line 30 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"

    $this->successful = !$this->internalError;
    $this->internalError = false;
    $this->retvalue = $this->_retvalue;
#line 1159 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
    }

    /**
     * The main parser program.
     * 
     * The first argument is the major token number.  The second is
     * the token value string as scanned from the input.
     *
     * @param int   $yymajor      the token number
     * @param mixed $yytokenvalue the token value
     * @param mixed ...           any extra arguments that should be passed to handlers
     *
     * @return void
     */
    function parse($token)
    {
        list($yymajor, $yytokenvalue, ) = $token ? $token : array(0, 0);
        self::$LINE = isset($token[2]) ? $token[2] : -1;

//        $yyact;            /* The parser action. */
//        $yyendofinput;     /* True if we are at the end of input */
        $yyerrorhit = 0;   /* True if yymajor has invoked an error */
        
        /* (re)initialize the parser, if necessary */
        if ($this->yyidx === null || $this->yyidx < 0) {
            /* if ($yymajor == 0) return; // not sure why this was here... */
            $this->yyidx = 0;
            $this->yyerrcnt = -1;
            $x = new yyStackEntry;
            $x->stateno = 0;
            $x->major = 0;
            $this->yystack = array();
            array_push($this->yystack, $x);
        }
        $yyendofinput = ($yymajor==0);
        
        if (self::$yyTraceFILE) {
            fprintf(
                self::$yyTraceFILE,
                "%sInput %s\n",
                self::$yyTracePrompt,
                self::tokenName($yymajor)
            );
        }
        
        do {
            $yyact = $this->yy_find_shift_action($yymajor);
            if ($yymajor < self::YYERRORSYMBOL
                && !$this->yy_is_expected_token($yymajor)
            ) {
                // force a syntax error
                $yyact = self::YY_ERROR_ACTION;
            }
            if ($yyact < self::YYNSTATE) {
                $this->yy_shift($yyact, $yymajor, $yytokenvalue);
                $this->yyerrcnt--;
                if ($yyendofinput && $this->yyidx >= 0) {
                    $yymajor = 0;
                } else {
                    $yymajor = self::YYNOCODE;
                }
            } elseif ($yyact < self::YYNSTATE + self::YYNRULE) {
                $this->yy_reduce($yyact - self::YYNSTATE);
            } elseif ($yyact == self::YY_ERROR_ACTION) {
                if (self::$yyTraceFILE) {
                    fprintf(
                        self::$yyTraceFILE,
                        "%sSyntax Error!\n",
                        self::$yyTracePrompt
                    );
                }
                if (self::YYERRORSYMBOL) {
                    /* A syntax error has occurred.
                    ** The response to an error depends upon whether or not the
                    ** grammar defines an error token "ERROR".  
                    **
                    ** This is what we do if the grammar does define ERROR:
                    **
                    **  * Call the %syntax_error function.
                    **
                    **  * Begin popping the stack until we enter a state where
                    **    it is legal to shift the error symbol, then shift
                    **    the error symbol.
                    **
                    **  * Set the error count to three.
                    **
                    **  * Begin accepting and shifting new tokens.  No new error
                    **    processing will occur until three tokens have been
                    **    shifted successfully.
                    **
                    */
                    if ($this->yyerrcnt < 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $yymx = $this->yystack[$this->yyidx]->major;
                    if ($yymx == self::YYERRORSYMBOL || $yyerrorhit ) {
                        if (self::$yyTraceFILE) {
                            fprintf(
                                self::$yyTraceFILE,
                                "%sDiscard input token %s\n",
                                self::$yyTracePrompt,
                                self::tokenName($yymajor)
                            );
                        }
                        $this->yy_destructor($yymajor, $yytokenvalue);
                        $yymajor = self::YYNOCODE;
                    } else {
                        while ($this->yyidx >= 0
                            && $yymx != self::YYERRORSYMBOL
                            && ($yyact = $this->yy_find_shift_action(self::YYERRORSYMBOL)) >= self::YYNSTATE
                        ) {
                            $this->yy_pop_parser_stack();
                        }
                        if ($this->yyidx < 0 || $yymajor==0) {
                            $this->yy_destructor($yymajor, $yytokenvalue);
                            $this->yy_parse_failed();
                            $yymajor = self::YYNOCODE;
                        } elseif ($yymx != self::YYERRORSYMBOL) {
                            $u2 = 0;
                            $this->yy_shift($yyact, self::YYERRORSYMBOL, $u2);
                        }
                    }
                    $this->yyerrcnt = 3;
                    $yyerrorhit = 1;
                } else {
                    /* YYERRORSYMBOL is not defined */
                    /* This is what we do if the grammar does not define ERROR:
                    **
                    **  * Report an error message, and throw away the input token.
                    **
                    **  * If the input token is $, then fail the parse.
                    **
                    ** As before, subsequent error messages are suppressed until
                    ** three input tokens have been successfully shifted.
                    */
                    if ($this->yyerrcnt <= 0) {
                        $this->yy_syntax_error($yymajor, $yytokenvalue);
                    }
                    $this->yyerrcnt = 3;
                    $this->yy_destructor($yymajor, $yytokenvalue);
                    if ($yyendofinput) {
                        $this->yy_parse_failed();
                    }
                    $yymajor = self::YYNOCODE;
                }
            } else {
                $this->yy_accept();
                $yymajor = self::YYNOCODE;
            }            
        } while ($yymajor != self::YYNOCODE && $this->yyidx >= 0);

        if ($token === NULL)
        {
          return $this->_retvalue;
        }
    }
}
