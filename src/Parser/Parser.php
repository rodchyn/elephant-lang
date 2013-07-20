<?php

namespace Rodchyn\ElephantLang\Parser;

use \ArrayAccess as ArrayAccess;

use Rodchyn\ElephantLang\Init as Init;

Init::init();


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
    const EL_ASSIGN                         =  9;
    const EL_ARRAY_LEFT                     = 10;
    const EL_UNTIL                          = 11;
    const EL_NUMBER                         = 12;
    const EL_IDENTIFIER                     = 13;
    const EL_IF                             = 14;
    const EL_Q_ASSIGN                       = 15;
    const EL_ARRAY_RIGHT                    = 16;
    const EL_COMMA                          = 17;
    const EL_OPENP                          = 18;
    const EL_CLOSEP                         = 19;
    const EL_BRACKET_LEFT                   = 20;
    const EL_BRACKET_RIGHT                  = 21;
    const EL_STRING_SINGLE                  = 22;
    const EL_STRING_DOUBLE                  = 23;
    const YY_NO_ACTION = 161;
    const YY_ACCEPT_ACTION = 160;
    const YY_ERROR_ACTION = 159;

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
    const YY_SZ_ACTTAB = 348;
static public $yy_action = array(
 /*     0 */    31,   61,   43,   36,   56,   86,   87,   54,   46,   50,
 /*    10 */    94,   60,   25,   83,   64,   53,   53,   80,   73,   31,
 /*    20 */    61,   43,   36,   56,   86,   87,   54,   45,   50,   94,
 /*    30 */    60,    3,   10,   65,   53,   53,   80,   73,  160,   33,
 /*    40 */    61,   43,   36,   56,   86,   87,   54,   93,   50,   94,
 /*    50 */    60,   21,   23,   22,   24,   53,   80,   73,  147,   12,
 /*    60 */    48,   61,   43,   36,   56,   86,   87,   54,   91,   50,
 /*    70 */    94,   60,   88,   20,   19,   66,   53,   80,   73,   72,
 /*    80 */    39,   56,   28,   61,   43,   36,   56,   86,   87,   54,
 /*    90 */    74,   50,   94,   60,   80,   73,  149,   26,   53,   80,
 /*   100 */    73,   14,    2,   35,   61,   44,   36,   56,   86,   87,
 /*   110 */    54,    8,   50,   94,   60,   78,   92,   15,   51,   53,
 /*   120 */    80,   73,    9,   14,    6,   30,   61,   38,   36,   56,
 /*   130 */    86,   87,   54,    8,   50,   94,   60,   76,   75,   82,
 /*   140 */    75,   53,   80,   73,   62,   59,   40,   61,   49,   36,
 /*   150 */    56,   86,   87,   54,   68,   50,   94,   60,   80,   73,
 /*   160 */    22,   24,   53,   80,   73,   90,   75,  148,   32,   61,
 /*   170 */    43,   36,   56,   86,   87,   54,   84,   50,   94,   60,
 /*   180 */    21,   23,   71,   75,   53,   80,   73,  147,   58,   27,
 /*   190 */    61,   43,   36,   56,   86,   87,   54,   52,   50,   94,
 /*   200 */    60,   16,   92,    7,   75,   53,   80,   73,    9,  111,
 /*   210 */     6,   29,   61,   43,   36,   56,   86,   87,   54,  111,
 /*   220 */    50,   94,   60,  111,  111,  111,  111,   53,   80,   73,
 /*   230 */   111,  111,   34,   61,   41,   36,   56,   86,   87,   54,
 /*   240 */   111,   50,   94,   60,    5,    4,  111,  111,   53,   80,
 /*   250 */    73,  111,  111,    1,  111,   37,   13,   62,   69,   63,
 /*   260 */   111,    9,  111,    6,   11,   81,   77,    5,    4,   57,
 /*   270 */   111,   80,   73,    3,   10,  111,    1,  111,   37,   13,
 /*   280 */   111,   18,   17,  111,    9,   11,    6,  111,   81,   77,
 /*   290 */   111,  111,  111,  111,    3,   10,   18,   17,  156,  156,
 /*   300 */    11,   85,   70,   75,  111,   89,   39,   56,  111,    3,
 /*   310 */    10,   79,   81,   77,   67,   18,   17,  111,  111,   11,
 /*   320 */    80,   55,   70,   75,   47,   75,  111,  111,    3,   10,
 /*   330 */   111,  111,   81,   77,   81,   77,   42,   75,  111,  111,
 /*   340 */   111,  111,  111,  111,  111,  111,   81,   77,
    );
    static public $yy_lookahead = array(
 /*     0 */    26,   27,   28,   29,   30,   31,   32,   33,   35,   35,
 /*    10 */    36,   37,   38,   29,   40,   42,   42,   43,   44,   26,
 /*    20 */    27,   28,   29,   30,   31,   32,   33,   35,   35,   36,
 /*    30 */    37,   14,   15,   40,   42,   42,   43,   44,   25,   26,
 /*    40 */    27,   28,   29,   30,   31,   32,   33,   29,   35,   36,
 /*    50 */    37,    1,    2,    1,    2,   42,   43,   44,    8,    9,
 /*    60 */    26,   27,   28,   29,   30,   31,   32,   33,   42,   35,
 /*    70 */    36,   37,   42,    3,    4,   29,   42,   43,   44,   28,
 /*    80 */    29,   30,   26,   27,   28,   29,   30,   31,   32,   33,
 /*    90 */    29,   35,   36,   37,   43,   44,    8,    1,   42,   43,
 /*   100 */    44,    5,   17,   26,   27,   28,   29,   30,   31,   32,
 /*   110 */    33,   15,   35,   36,   37,   13,   12,   13,    8,   42,
 /*   120 */    43,   44,   18,    5,   20,   26,   27,   28,   29,   30,
 /*   130 */    31,   32,   33,   15,   35,   36,   37,   12,   13,   12,
 /*   140 */    13,   42,   43,   44,   29,   30,   26,   27,   28,   29,
 /*   150 */    30,   31,   32,   33,   16,   35,   36,   37,   43,   44,
 /*   160 */     1,    2,   42,   43,   44,   12,   13,    8,   26,   27,
 /*   170 */    28,   29,   30,   31,   32,   33,   29,   35,   36,   37,
 /*   180 */     1,    2,   12,   13,   42,   43,   44,    8,    6,   26,
 /*   190 */    27,   28,   29,   30,   31,   32,   33,   39,   35,   36,
 /*   200 */    37,   17,   12,    9,   13,   42,   43,   44,   18,   45,
 /*   210 */    20,   26,   27,   28,   29,   30,   31,   32,   33,   45,
 /*   220 */    35,   36,   37,   45,   45,   45,   45,   42,   43,   44,
 /*   230 */    45,   45,   26,   27,   28,   29,   30,   31,   32,   33,
 /*   240 */    45,   35,   36,   37,    1,    2,   45,   45,   42,   43,
 /*   250 */    44,   45,   45,   10,   45,   12,   13,   29,   30,   16,
 /*   260 */    45,   18,   45,   20,    5,   22,   23,    1,    2,   41,
 /*   270 */    45,   43,   44,   14,   15,   45,   10,   45,   12,   13,
 /*   280 */    45,    1,    2,   45,   18,    5,   20,   45,   22,   23,
 /*   290 */    45,   45,   45,   45,   14,   15,    1,    2,    8,    9,
 /*   300 */     5,   21,   12,   13,   45,   28,   29,   30,   45,   14,
 /*   310 */    15,   34,   22,   23,   19,    1,    2,   45,   45,    5,
 /*   320 */    43,   44,   12,   13,   12,   13,   45,   45,   14,   15,
 /*   330 */    45,   45,   22,   23,   22,   23,   12,   13,   45,   45,
 /*   340 */    45,   45,   45,   45,   45,   45,   22,   23,
);
    const YY_SHIFT_USE_DFLT = -1;
    const YY_SHIFT_MAX = 57;
    static public $yy_shift_ofst = array(
 /*     0 */   266,  243,  266,  266,  266,  266,  266,  266,  266,  266,
 /*    10 */   266,  266,  324,  290,  312,  310,  310,  104,  104,  190,
 /*    20 */   190,  127,  170,  153,  125,   85,  191,  295,  280,  314,
 /*    30 */   314,  314,  314,  314,  314,  314,   50,  159,   96,  179,
 /*    40 */   259,   96,  159,   96,   96,   70,   70,   52,   17,  118,
 /*    50 */    70,  102,  138,  182,  194,   88,  110,  184,
);
    const YY_REDUCE_USE_DFLT = -28;
    const YY_REDUCE_MAX = 26;
    static public $yy_reduce_ofst = array(
 /*     0 */    13,  -26,   -7,  206,  185,  142,   56,   34,   77,  163,
 /*    10 */    99,  120,  277,  228,   51,  228,  115,  -27,   -8,   30,
 /*    20 */    26,   18,   46,   61,  -16,  158,  147,
);
    static public $yyExpectedTokens = array(
        /* 0 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 1 */ array(1, 2, 10, 12, 13, 16, 18, 20, 22, 23, ),
        /* 2 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 3 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 4 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 5 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 6 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 7 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 8 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 9 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 10 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 11 */ array(1, 2, 10, 12, 13, 18, 20, 22, 23, ),
        /* 12 */ array(12, 13, 22, 23, ),
        /* 13 */ array(8, 9, 12, 13, 22, 23, ),
        /* 14 */ array(12, 13, 22, 23, ),
        /* 15 */ array(12, 13, 22, 23, ),
        /* 16 */ array(12, 13, 22, 23, ),
        /* 17 */ array(12, 13, 18, 20, ),
        /* 18 */ array(12, 13, 18, 20, ),
        /* 19 */ array(12, 18, 20, ),
        /* 20 */ array(12, 18, 20, ),
        /* 21 */ array(12, 13, ),
        /* 22 */ array(12, 13, ),
        /* 23 */ array(12, 13, ),
        /* 24 */ array(12, 13, ),
        /* 25 */ array(17, ),
        /* 26 */ array(13, ),
        /* 27 */ array(1, 2, 5, 14, 15, 19, ),
        /* 28 */ array(1, 2, 5, 14, 15, 21, ),
        /* 29 */ array(1, 2, 5, 14, 15, ),
        /* 30 */ array(1, 2, 5, 14, 15, ),
        /* 31 */ array(1, 2, 5, 14, 15, ),
        /* 32 */ array(1, 2, 5, 14, 15, ),
        /* 33 */ array(1, 2, 5, 14, 15, ),
        /* 34 */ array(1, 2, 5, 14, 15, ),
        /* 35 */ array(1, 2, 5, 14, 15, ),
        /* 36 */ array(1, 2, 8, 9, ),
        /* 37 */ array(1, 2, 8, ),
        /* 38 */ array(1, 5, 15, ),
        /* 39 */ array(1, 2, 8, ),
        /* 40 */ array(5, 14, 15, ),
        /* 41 */ array(1, 5, 15, ),
        /* 42 */ array(1, 2, 8, ),
        /* 43 */ array(1, 5, 15, ),
        /* 44 */ array(1, 5, 15, ),
        /* 45 */ array(3, 4, ),
        /* 46 */ array(3, 4, ),
        /* 47 */ array(1, 2, ),
        /* 48 */ array(14, 15, ),
        /* 49 */ array(5, 15, ),
        /* 50 */ array(3, 4, ),
        /* 51 */ array(13, ),
        /* 52 */ array(16, ),
        /* 53 */ array(6, ),
        /* 54 */ array(9, ),
        /* 55 */ array(8, ),
        /* 56 */ array(8, ),
        /* 57 */ array(17, ),
        /* 58 */ array(),
        /* 59 */ array(),
        /* 60 */ array(),
        /* 61 */ array(),
        /* 62 */ array(),
        /* 63 */ array(),
        /* 64 */ array(),
        /* 65 */ array(),
        /* 66 */ array(),
        /* 67 */ array(),
        /* 68 */ array(),
        /* 69 */ array(),
        /* 70 */ array(),
        /* 71 */ array(),
        /* 72 */ array(),
        /* 73 */ array(),
        /* 74 */ array(),
        /* 75 */ array(),
        /* 76 */ array(),
        /* 77 */ array(),
        /* 78 */ array(),
        /* 79 */ array(),
        /* 80 */ array(),
        /* 81 */ array(),
        /* 82 */ array(),
        /* 83 */ array(),
        /* 84 */ array(),
        /* 85 */ array(),
        /* 86 */ array(),
        /* 87 */ array(),
        /* 88 */ array(),
        /* 89 */ array(),
        /* 90 */ array(),
        /* 91 */ array(),
        /* 92 */ array(),
        /* 93 */ array(),
        /* 94 */ array(),
);
    static public $yy_default = array(
 /*     0 */    95,  159,  142,  159,  159,  159,  159,  159,  159,  159,
 /*    10 */   159,  159,  159,  143,  159,  143,  159,  159,  159,  159,
 /*    20 */   159,  159,  159,  159,  159,  141,  159,  159,  159,  109,
 /*    30 */   117,  135,  108,   96,  116,  120,  130,  150,  119,  155,
 /*    40 */   111,  115,  153,  159,  118,  125,  126,  148,  121,  112,
 /*    50 */   124,  159,  159,  138,  129,  154,  159,  136,  137,  145,
 /*    60 */   128,   97,  147,  131,  133,  134,   99,  151,  132,  144,
 /*    70 */   148,  101,  110,  149,  102,  156,  105,  158,  107,  122,
 /*    80 */   146,  157,  100,  103,  106,  152,  113,  114,  140,  123,
 /*    90 */   104,  139,  150,   98,  127,
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
    const YYNOCODE = 46;
    const YYSTACKDEPTH = 100;
    const YYNSTATE = 95;
    const YYNRULE = 64;
    const YYERRORSYMBOL = 24;
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
  'CONCAT',        'ASSIGN',        'ARRAY_LEFT',    'UNTIL',       
  'NUMBER',        'IDENTIFIER',    'IF',            'Q_ASSIGN',    
  'ARRAY_RIGHT',   'COMMA',         'OPENP',         'CLOSEP',      
  'BRACKET_LEFT',  'BRACKET_RIGHT',  'STRING_SINGLE',  'STRING_DOUBLE',
  'error',         'start',         'expression',    'value',       
  'statement',     'identifier',    'param',         'assign',      
  'if',            'assignable',    'alphanumeric',  'term',        
  'simpleAssignable',  'array',         'argList',       'optComma',    
  'arg',           'paramList',     'factor',        'paramVar',    
  'string',      
    );

    /**
     * For tracing reduce actions, the names of all rules are required.
     * @var array
     */
    static public $yyRuleName = array(
 /*   0 */ "start ::=",
 /*   1 */ "start ::= expression",
 /*   2 */ "expression ::= value",
 /*   3 */ "statement ::= identifier PLUS identifier",
 /*   4 */ "statement ::= NUMBER PLUS identifier",
 /*   5 */ "statement ::= identifier PLUS NUMBER",
 /*   6 */ "statement ::= NUMBER PLUS NUMBER",
 /*   7 */ "statement ::= identifier MINUS identifier",
 /*   8 */ "statement ::= NUMBER MINUS identifier",
 /*   9 */ "statement ::= identifier MINUS NUMBER",
 /*  10 */ "statement ::= NUMBER MINUS NUMBER",
 /*  11 */ "statement ::= statement PLUS identifier",
 /*  12 */ "statement ::= param CONCAT IDENTIFIER",
 /*  13 */ "expression ::= PLUS expression",
 /*  14 */ "expression ::= MINUS expression",
 /*  15 */ "expression ::= statement AND_LITERAL statement",
 /*  16 */ "expression ::= expression AND_LITERAL expression",
 /*  17 */ "expression ::= expression AND_LITERAL statement",
 /*  18 */ "expression ::= assign",
 /*  19 */ "expression ::= if",
 /*  20 */ "if ::= expression IF statement",
 /*  21 */ "if ::= expression IF expression",
 /*  22 */ "expression ::= expression Q_ASSIGN expression",
 /*  23 */ "expression ::= statement Q_ASSIGN statement",
 /*  24 */ "expression ::= expression Q_ASSIGN statement",
 /*  25 */ "expression ::= statement Q_ASSIGN expression",
 /*  26 */ "assign ::= assignable ASSIGN expression",
 /*  27 */ "assign ::= identifier ASSIGN alphanumeric",
 /*  28 */ "assign ::= identifier ASSIGN statement",
 /*  29 */ "expression ::= term",
 /*  30 */ "expression ::= expression PLUS term",
 /*  31 */ "expression ::= expression MINUS term",
 /*  32 */ "assignable ::= simpleAssignable",
 /*  33 */ "assignable ::= array",
 /*  34 */ "value ::= assignable",
 /*  35 */ "simpleAssignable ::= identifier",
 /*  36 */ "array ::= ARRAY_LEFT ARRAY_RIGHT",
 /*  37 */ "array ::= ARRAY_LEFT argList optComma ARRAY_RIGHT",
 /*  38 */ "argList ::= arg",
 /*  39 */ "argList ::= argList COMMA arg",
 /*  40 */ "arg ::= expression",
 /*  41 */ "term ::= IDENTIFIER paramList",
 /*  42 */ "term ::= factor WHITESPACE",
 /*  43 */ "term ::= factor",
 /*  44 */ "term ::= term MULTIPLICATION factor",
 /*  45 */ "term ::= term DIVISION factor",
 /*  46 */ "optComma ::=",
 /*  47 */ "optComma ::= COMMA",
 /*  48 */ "paramList ::=",
 /*  49 */ "paramList ::= param",
 /*  50 */ "paramList ::= paramList COMMA param",
 /*  51 */ "param ::= paramVar",
 /*  52 */ "paramVar ::= identifier",
 /*  53 */ "paramVar ::= NUMBER",
 /*  54 */ "paramVar ::= string",
 /*  55 */ "factor ::= NUMBER",
 /*  56 */ "factor ::= OPENP expression CLOSEP",
 /*  57 */ "factor ::= BRACKET_LEFT expression BRACKET_RIGHT",
 /*  58 */ "alphanumeric ::= NUMBER",
 /*  59 */ "alphanumeric ::= string",
 /*  60 */ "statement ::= identifier",
 /*  61 */ "identifier ::= IDENTIFIER",
 /*  62 */ "string ::= STRING_SINGLE",
 /*  63 */ "string ::= STRING_DOUBLE",
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
  array( 'lhs' => 25, 'rhs' => 0 ),
  array( 'lhs' => 25, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 28, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 2 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 32, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 31, 'rhs' => 3 ),
  array( 'lhs' => 31, 'rhs' => 3 ),
  array( 'lhs' => 31, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 1 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 26, 'rhs' => 3 ),
  array( 'lhs' => 33, 'rhs' => 1 ),
  array( 'lhs' => 33, 'rhs' => 1 ),
  array( 'lhs' => 27, 'rhs' => 1 ),
  array( 'lhs' => 36, 'rhs' => 1 ),
  array( 'lhs' => 37, 'rhs' => 2 ),
  array( 'lhs' => 37, 'rhs' => 4 ),
  array( 'lhs' => 38, 'rhs' => 1 ),
  array( 'lhs' => 38, 'rhs' => 3 ),
  array( 'lhs' => 40, 'rhs' => 1 ),
  array( 'lhs' => 35, 'rhs' => 2 ),
  array( 'lhs' => 35, 'rhs' => 2 ),
  array( 'lhs' => 35, 'rhs' => 1 ),
  array( 'lhs' => 35, 'rhs' => 3 ),
  array( 'lhs' => 35, 'rhs' => 3 ),
  array( 'lhs' => 39, 'rhs' => 0 ),
  array( 'lhs' => 39, 'rhs' => 1 ),
  array( 'lhs' => 41, 'rhs' => 0 ),
  array( 'lhs' => 41, 'rhs' => 1 ),
  array( 'lhs' => 41, 'rhs' => 3 ),
  array( 'lhs' => 30, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 43, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 1 ),
  array( 'lhs' => 42, 'rhs' => 3 ),
  array( 'lhs' => 42, 'rhs' => 3 ),
  array( 'lhs' => 34, 'rhs' => 1 ),
  array( 'lhs' => 34, 'rhs' => 1 ),
  array( 'lhs' => 28, 'rhs' => 1 ),
  array( 'lhs' => 29, 'rhs' => 1 ),
  array( 'lhs' => 44, 'rhs' => 1 ),
  array( 'lhs' => 44, 'rhs' => 1 ),
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
        2 => 2,
        18 => 2,
        19 => 2,
        29 => 2,
        32 => 2,
        34 => 2,
        40 => 2,
        43 => 2,
        47 => 2,
        51 => 2,
        52 => 2,
        53 => 2,
        54 => 2,
        55 => 2,
        58 => 2,
        59 => 2,
        60 => 2,
        62 => 2,
        63 => 2,
        3 => 3,
        4 => 3,
        5 => 3,
        6 => 3,
        11 => 3,
        30 => 3,
        7 => 7,
        8 => 7,
        9 => 7,
        10 => 7,
        31 => 7,
        12 => 12,
        13 => 13,
        14 => 14,
        15 => 15,
        16 => 15,
        17 => 15,
        20 => 20,
        21 => 21,
        22 => 22,
        23 => 22,
        24 => 22,
        25 => 22,
        26 => 26,
        27 => 27,
        28 => 27,
        33 => 33,
        35 => 33,
        36 => 36,
        37 => 37,
        38 => 38,
        49 => 38,
        39 => 39,
        50 => 39,
        41 => 41,
        42 => 42,
        56 => 42,
        57 => 42,
        44 => 44,
        45 => 45,
        46 => 46,
        48 => 48,
        61 => 61,
    );
    /* Beginning here are the reduction cases.  A typical example
    ** follows:
    **  #line <lineno> <grammarfile>
    **   function yy_r0($yymsp){ ... }           // User supplied code
    **  #line <lineno> <thisfile>
    */
#line 58 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r0(){ $this->_retvalue = yy('Block');     }
#line 1112 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 60 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r1(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor . ';';     }
#line 1115 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 64 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r2(){ $this->_retvalue = $this->yystack[$this->yyidx + 0]->minor;     }
#line 1118 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 66 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r3(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor . ' + ' . $this->yystack[$this->yyidx + 0]->minor;     }
#line 1121 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 71 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r7(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor . ' - ' . $this->yystack[$this->yyidx + 0]->minor;     }
#line 1124 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 77 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r12(){ $this->_retvalue = yy('Helper', $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1127 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 79 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r13(){ $this->_retvalue = +$this->yystack[$this->yyidx + 0]->minor;     }
#line 1130 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 80 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r14(){ $this->_retvalue = -$this->yystack[$this->yyidx + 0]->minor;     }
#line 1133 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 82 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r15(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor . ' && ' . $this->yystack[$this->yyidx + 0]->minor;     }
#line 1136 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 90 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r20(){ $this->_retvalue = 'if (' . $this->yystack[$this->yyidx + 0]->minor . ') { ' . $this->yystack[$this->yyidx + -2]->minor . ' }';     }
#line 1139 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 91 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r21(){ $this->_retvalue = 'if (' . $this->yystack[$this->yyidx + 0]->minor . ') { ' . $this->yystack[$this->yyidx + -2]->minor . ' } ';     }
#line 1142 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 93 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r22(){ $this->_retvalue = 'if ( empty(' . $this->yystack[$this->yyidx + -2]->minor . ') || !' . $this->yystack[$this->yyidx + -2]->minor . ' ) { ' . $this->yystack[$this->yyidx + -2]->minor . ' = ' . $this->yystack[$this->yyidx + 0]->minor .'; } ';     }
#line 1145 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 98 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r26(){ $this->_retvalue = yy('Assign', $this->yystack[$this->yyidx + -2]->minor, $this->yystack[$this->yyidx + 0]->minor);     }
#line 1148 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 99 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r27(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor . ' = ' . $this->yystack[$this->yyidx + 0]->minor;     }
#line 1151 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 108 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r33(){ $this->_retvalue = yy('Value', $this->yystack[$this->yyidx + 0]->minor);     }
#line 1154 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 114 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r36(){ $this->_retvalue = yy('Arr', array());     }
#line 1157 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 115 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r37(){ $this->_retvalue = yy('Arr', $this->yystack[$this->yyidx + -2]->minor);     }
#line 1160 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 117 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r38(){ $this->_retvalue = array($this->yystack[$this->yyidx + 0]->minor);     }
#line 1163 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 118 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r39(){ $this->_retvalue = array_merge($this->yystack[$this->yyidx + -2]->minor, array($this->yystack[$this->yyidx + 0]->minor));     }
#line 1166 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 122 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r41(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor . ' (' . join(', ', $this->yystack[$this->yyidx + 0]->minor) . ')';     }
#line 1169 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 123 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r42(){ $this->_retvalue = $this->yystack[$this->yyidx + -1]->minor;     }
#line 1172 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 125 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r44(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor*$this->yystack[$this->yyidx + 0]->minor;     }
#line 1175 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 126 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r45(){ $this->_retvalue = $this->yystack[$this->yyidx + -2]->minor/$this->yystack[$this->yyidx + 0]->minor;     }
#line 1178 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 128 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r46(){ $this->_retvalue = '';     }
#line 1181 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 131 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r48(){ $this->_retvalue = array();     }
#line 1184 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
#line 157 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.y"
    function yy_r61(){ $this->_retvalue = '$' . $this->yystack[$this->yyidx + 0]->minor;     }
#line 1187 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"

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
#line 1313 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
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
#line 1337 "/home/rodchyn/Documents/Github/rodchyn/elephant-lang/parser.php"
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
