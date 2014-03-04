<?php
// ---------------------------------------------
// Class: SparqlParser
// ---------------------------------------------
require_once RDFAPI_INCLUDE_DIR . 'model/Literal.php';
require_once RDFAPI_INCLUDE_DIR . 'model/Resource.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/Constraint.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/Query.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/QueryTriple.php';
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlParserException.php';

/**
* Parses a SPARQL Query string and returns a Query Object.
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @author   Christian Weiske <cweiske@cweiske.de>
* @version     $Id$
* @license http://www.gnu.org/licenses/lgpl.html LGPL
*
* @package sparql
*/
class SparqlParser extends Object
{

    /**
    * The query Object
    * @var Query
    */
    protected $query;

    /**
    * The Querystring
    * @var string
    */
    protected $queryString;

    /**
    * The tokenized Query
    * @var array
    */
    protected $tokens = array();

    /**
    * Last parsed graphPattern
    * @var int
    */
    protected $tmp;

    /**
    * Operators introduced by sparql
    * @var array
    */
    protected static $sops = array(
        'regex',
        'bound',
        'isuri',
        'isblank',
        'isliteral',
        'str',
        'lang',
        'datatype',
        'langmatches'
    );

    /**
    *   Which order operators are to be treated.
    *   (11.3 Operator Mapping)
    *   @var array
    */
    protected static $operatorPrecedence = array(
        '||'    => 0,
        '&&'    => 1,
        '='     => 2,
        '!='    => 3,
        '<'     => 4,
        '>'     => 5,
        '<='    => 6,
        '>='    => 7,
        '*'     => 0,
        '/'     => 0,
        '+'     => 0,
        '-'     => 0,
    );



    /**
    * Constructor of SparqlParser
    */
    public function SparqlParser()
    {
    }



    /**
    * Main function of SparqlParser. Parses a query string.
    *
    * @param  String $queryString The SPARQL query
    * @return Query  The query object
    * @throws SparqlParserException
    */
    public function parse($queryString = false)
    {
        $this->prepare();

        if ($queryString) {
            $this->query->setQueryString($queryString);
            $uncommentedQuery  = $this->uncomment($queryString);
            $this->queryString = $uncommentedQuery;
            $this->tokens      = self::tokenize($uncommentedQuery);
            $this->parseQuery();
            if (!$this->query->isComplete()) {
                throw new SparqlParserException(
                    "Query is incomplete.",
                    null,
                    $queryString
                );
            }
        } else {
            throw new SparqlParserException(
                "Querystring is empty.",
                null,
                key($this->tokens)
            );
            $this->query->isEmpty = true;
        }
        return $this->query;
    }//public function parse($queryString = false)



    /**
    *   Set all internal variables to a clear state
    *   before we start parsing.
    */
    protected function prepare()
    {
        $this->query          = new Query();
        $this->queryString    = null;
        $this->tokens         = array();
        $this->tmp            = null;
        // add the default prefixes defined in constants.php
        global $default_prefixes;
        $this->query->prefixes = $default_prefixes;
    }//protected function prepare()



    /**
    * Tokenizes the query string into $tokens.
    * The query may not contain any comments.
    *
    * @param  string $queryString Query to split into tokens
    *
    * @return array Tokens
    */
    public static function tokenize($queryString)
    {
        $queryString  = trim($queryString);
        $specialChars = array(' ', "\t", "\r", "\n", ',', '\\', '(', ')','{','}','"',"'",';','[',']');
        $len          = strlen($queryString);
        $tokens       = array('');
        $n            = 0;

        for ($i = 0; $i < $len; ++$i) {
            if (!in_array($queryString{$i}, $specialChars)) {
                $tokens[$n] .= $queryString{$i};
            } else {
                if ($tokens[$n] != '') {
                    ++$n;
                    if (!isset($tokens[$n])) {
                        $tokens[$n] = '';
                    }
                }
                if ($queryString{$i} == "'" && $n > 1
                  && $tokens[$n - 2] == "'" && $tokens[$n - 1] == "'"
                ) {
                    //special ''' quotation
                    $tokens[$n - 2] = "'''";
                    $tokens[$n - 1] = '';
                    unset($tokens[$n]);
                    --$n;
                    continue;
                } else if ($queryString{$i} == '"' && $n > 1
                  && $tokens[$n - 2] == '"' && $tokens[$n - 1] == '"'
                ) {
                    //special """ quotation
                    $tokens[$n - 2] = '"""';
                    $tokens[$n - 1] = '';
                    unset($tokens[$n]);
                    --$n;
                    continue;
                } else if ($queryString{$i} == '\\') {
                    $tokens[$n] .= substr($queryString, $i, 2);
                    ++$i;
                    continue;
                }
                $tokens[$n] = $queryString{$i};
                $tokens[++$n] = '';
            }
        }
//var_dump($tokens);
        return $tokens;
    }//public static function tokenize($queryString)



    /**
    * Removes comments in the query string. Comments are
    * indicated by '#'.
    *
    * @param  String $queryString
    * @return String The uncommented query string
    */
    protected function uncomment($queryString)
    {
        $regex ="/((\"[^\"]*\")|(\'[^\']*\')|(\<[^\>]*\>))|(#.*)/";
        return preg_replace($regex,'\1',$queryString);
    }//protected function uncomment($queryString)



    /**
    * Starts parsing the tokenized SPARQL Query.
    *
    * @return void
    */
    protected function parseQuery()
    {
        do {
            switch (strtolower(current($this->tokens))) {
                case "base":
                    $this->parseBase();
                    break;
                case "prefix":
                    $this->parsePrefix();
                    break;
                case "select":
                    $this->parseSelect();
                    break;
                case "describe":
                    $this->parseDescribe();
                    break;
                case "ask":
                    $this->parseAsk('ask');
                    break;
                case "count":
                    $this->parseAsk('count');
                    break;
                case "from":
                    $this->parseFrom();
                    break;
                case "construct":
                    $this->parseConstruct();
                    break;
                case "where":
                    $this->parseWhere();
                    $this->parseModifier();
                    break;
                case "{":
                    prev($this->tokens);
                    $this->parseWhere();
                    $this->parseModifier();
                    break;
            }
        } while (next($this->tokens));

    }//protected function parseQuery()



    /**
    * Parses the BASE part of the query.
    *
    * @return void
    * @throws SparqlParserException
    */
    protected function parseBase()
    {
        $this->_fastForward();
        if ($this->iriCheck(current($this->tokens))) {
            $this->query->setBase(current($this->tokens));
        } else {
            $msg = current($this->tokens);
            $msg = preg_replace('/</', '&lt;', $msg);
            throw new SparqlParserException(
                "IRI expected",
                null,
                key($this->tokens)
            );
        }
    }



    /**
    * Adds a new namespace prefix to the query object.
    *
    * @return void
    * @throws SparqlParserException
    */
    protected function parsePrefix()
    {
        $this->_fastForward();
        $prefix = substr(current($this->tokens), 0, -1);
        $this->_fastForward();
        if ($this->iriCheck(current($this->tokens))) {
            $uri = substr(current($this->tokens), 1, -1);
            $this->query->addPrefix($prefix, $uri);
        } else {
            $msg = current($this->tokens);
            $msg = preg_replace('/</', '&lt;', $msg);
            throw new SparqlParserException(
                "IRI expected",
                null,
                key($this->tokens)
            );
        }
    }



    /**
    * Parses the SELECT part of a query.
    *
    * @return void
    * @throws SparqlParserException
    */
    protected function parseSelect()
    {
        $this->_fastForward();
        $curLow = strtolower(current($this->tokens));
        prev($this->tokens);
        if ($curLow == 'distinct') {
            $this->query->setResultForm('select distinct');
        } else {
            $this->query->setResultForm('select');
        }

        $currentVar = null;
        $currentFunc = null;
        $bWaitForRenaming = false;
        while ($curLow != 'from' && $curLow != 'where' &&
               $curLow != "{"
        ){
            $this->_fastForward();
            $curTok = current($this->tokens);
            $curLow = strtolower($curTok);

            if ($this->varCheck($curTok) || $curLow == '*') {
                if ($bWaitForRenaming) {
                    $bWaitForRenaming = false;
                    $currentVar->setAlias($curTok);
                    if ($currentFunc != null) {
                        $currentVar->setFunc($currentFunc);
                    }
                    $this->query->addResultVar($currentVar);
                    $currentVar = null;
                } else {
                    if ($currentVar != null) {
                        $this->query->addResultVar($currentVar);
                        $currentVar = null;
                    }
                    $currentVar = new Query_ResultVariable($curTok);
                    if ($currentFunc != null) {
                        $currentVar->setFunc($currentFunc);
                    }
                }
                $currentFunc = null;
            } else if ($curLow == 'as') {
                if ($currentVar === null) {
                    throw new SparqlParserException(
                        'AS requires a variable left and right',
                        null,
                        key($this->tokens)
                    );
                }
                $bWaitForRenaming = true;
            } else if (in_array($curLow, self::$sops)) {
                $currentFunc = $curLow;
            }

            if (!current($this->tokens)) {
                throw new SparqlParserException(
                    "Unexpected end of File.",
                    null,
                    key($this->tokens)
                );
            }
        }

        if ($currentVar != null) {
            $this->query->addResultVar($currentVar);
        }
        prev($this->tokens);

        if (count($this->query->getResultVars()) == 0) {
            throw new SparqlParserException(
                "Variable or '*' expected.",
                null,
                key($this->tokens)
            );
        }
    }//protected function parseSelect()


    /**
    * Adds a new variable to the query and sets result form to 'DESCRIBE'.
    *
    * @return void
    */
    protected function parseDescribe()
    {
        while(strtolower(current($this->tokens))!='from'& strtolower(current($this->tokens))!='where'){
            $this->_fastForward();
            if($this->varCheck(current($this->tokens))|$this->iriCheck(current($this->tokens))){
                $this->query->addResultVar(current($this->tokens));
                if(!$this->query->getResultForm())
                    $this->query->setResultForm('describe');
            }
            if(!current($this->tokens))
            break;
        }
        prev($this->tokens);
    }

    /**
    * Sets result form to 'ASK' and 'COUNT'.
    *
    * @param string $form  if it's an ASK or COUNT query
    * @return void
    */
    protected function parseAsk($form){
        $this->query->setResultForm($form);
        $this->_fastForward();
        if(current($this->tokens)=="{")
            $this->_rewind();
        $this->parseWhere();
        $this->parseModifier();
    }

    /**
    * Parses the FROM clause.
    *
    * @return void
    * @throws SparqlParserException
    */
    protected function parseFrom(){
        $this->_fastForward();
        if(strtolower(current($this->tokens))!='named'){
            if($this->iriCheck(current($this->tokens))||$this->qnameCheck(current($this->tokens))){
                $this->query->addFrom(new Resource(substr(current($this->tokens),1,-1)));
            }else if($this->varCheck(current($this->tokens))){
                $this->query->addFrom(current($this->tokens));
            }else{
                throw new SparqlParserException("Variable, Iri or qname expected in FROM ",null,key($this->tokens));
            }
            $this->query->addFrom(current($this->tokens));
        }else{
            $this->_fastForward();
            if($this->iriCheck(current($this->tokens))||$this->qnameCheck(current($this->tokens))){
                $this->query->addFromNamed(new Resource(substr(current($this->tokens),1,-1)));
            }else if($this->varCheck(current($this->tokens))){
                $this->query->addFromNamed(current($this->tokens));
            }else{
                throw new SparqlParserException("Variable, Iri or qname expected in FROM NAMED ",null,key($this->tokens));
            }
        }
    }


    /**
    * Parses the CONSTRUCT clause.
    *
    * @return void
    * @throws SparqlParserException
    */
    protected function parseConstruct(){
        $this->_fastForward();
        $this->query->setResultForm('construct');
        if(current($this->tokens)=="{"){
            $this->parseGraphPattern(false,false,false,true);
        }else{
            throw new SparqlParserException("Unable to parse CONSTRUCT part. '{' expected. ",null,key($this->tokens));
        }
        $this->parseWhere();
        $this->parseModifier();
    }


    /**
    * Parses the WHERE clause.
    *
    * @return void
    * @throws SparqlParserException
    */
    protected function parseWhere(){
        $this->_fastForward();
        if(current($this->tokens)=="{"){
            $this->parseGraphPattern();
        }else{
            throw new SparqlParserException("Unable to parse WHERE part. '{' expected in Query. ",null,key($this->tokens));
        }
    }



    /**
    * Checks if $token is a variable.
    *
    * @param  String  $token The token
    * @return boolean TRUE if the token is a variable false if not
    */
    protected function varCheck($token)
    {
        if (isset($token[0]) && ($token{0} == '$' || $token{0} == '?')) {
            $this->query->addUsedVar($token);
            return true;
        }
        return false;
    }

    /**
    * Checks if $token is an IRI.
    *
    * @param  String  $token The token
    * @return boolean TRUE if the token is an IRI false if not
    */
    protected function iriCheck($token){
        $pattern="/^<[^>]*>\.?$/";
        if(preg_match($pattern,$token)>0)
        return true;
        return false;
    }


    /**
    * Checks if $token is a Blanknode.
    *
    * @param  String  $token The token
    * @return boolean TRUE if the token is BNode false if not
    */
    protected function bNodeCheck($token){
        if($token{0} == "_")
        return true;
        else
        return false;
    }


    /**
    * Checks if $token is a qname.
    *
    * @param  String  $token The token
    * @return boolean TRUE if the token is a qname false if not
    * @throws SparqlParserException
    */
    protected function qnameCheck($token)
    {
        $pattern="/^([^:^\<]*):([^:]*)$/";
        if (preg_match($pattern,$token,$hits)>0) {
            $prefs = $this->query->getPrefixes();
            if (isset($prefs{$hits{1}})) {
                return true;
            }
            if ($hits{1} == "_") {
                return true;
            }
            throw new SparqlParserException("Unbound Prefix: <i>".$hits{1}."</i>",null,key($this->tokens));
        } else {
            return false;
        }
    }



    /**
    * Checks if $token is a Literal.
    *
    * @param string $token The token
    *
    * @return boolean TRUE if the token is a Literal false if not
    */
    protected function literalCheck($token)
    {
        $pattern = "/^[\"\'].*$/";
        if (preg_match($pattern,$token) > 0) {
            return true;
        }
        return false;
    }//protected function literalCheck($token)



    /**
    * FastForward until next token which is not blank.
    *
    * @return void
    */
    protected function _fastForward()
    {
        next($this->tokens);
        while(current($this->tokens)==" "|current($this->tokens)==chr(10)|current($this->tokens)==chr(13)|current($this->tokens)==chr(9)){
            next($this->tokens);
        }
    }//protected function _fastForward()



    /**
    * Rewind until next token which is not blank.
    *
    * @return void
    */
    protected function _rewind()
    {
        prev($this->tokens);
        while(current($this->tokens)==" "|current($this->tokens)==chr(10)|current($this->tokens)==chr(13)|current($this->tokens)==chr(9)){
            prev($this->tokens);
        }
        return;
    }//protected function _rewind()



    /**
    * Parses a graph pattern.
    *
    * @param  int     $optional Optional graph pattern
    * @param  int     $union    Union graph pattern
    * @param  string  $graph    Graphname
    * @param  boolean $constr   TRUE if the pattern is a construct pattern
    * @param  boolean $external If the parsed pattern shall be returned
    * @param  int     $subpattern If the new pattern is subpattern of the
    *                               pattern with the given id
    * @return void
    */
    protected function parseGraphPattern(
      $optional = false, $union    = false, $graph = false,
      $constr   = false, $external = false, $subpattern = false
    ){
        $pattern = $this->query->getNewPattern($constr);
        if (is_int($optional)) {
            $pattern->setOptional($optional);
        } else {
            $this->tmp = $pattern->getId();
        }
        if (is_int($union)) {
            $pattern->setUnion($union);
        }
        if (is_int($subpattern)) {
            $pattern->setSubpatternOf($subpattern);
        }
        if ($graph != false) {
            $pattern->setGraphname($graph);
        }

        $this->_fastForward();

        do {
            switch (strtolower(current($this->tokens))) {
                case "graph":
                    $this->parseGraph();
                    break;
                case "union":
                    $this->_fastForward();
                    $this->parseGraphPattern(
                        false, $this->tmp, false, false, false, $subpattern
                    );
                    break;
                case "optional":
                    $this->_fastForward();
                    $this->parseGraphPattern(
                        $this->tmp, false, false, false, false, $subpattern
                    );
                    break;
                case "filter":
                    $this->parseConstraint(
                        $pattern, true, false, false, false, $subpattern
                    );
                    $this->_fastForward();
                    break;
                case ".":
                    $this->_fastForward();
                    break;
                case "{":
                    if (!is_int($subpattern)) {
                        $subpattern = $pattern->getId();
                    }

                    $this->parseGraphPattern(
                        false, false, false, false, false, $subpattern
                    );
                    break;
                case "}":
                    $pattern->open = false;
                    break;
                default:
                    $this->parseTriplePattern($pattern);
                    break;
            }
        } while ($pattern->open);

        if ($external) {
            return $pattern;
        }
        $this->_fastForward();
    }

    /**
    * Parses a triple pattern.
    *
    * @param  GraphPattern $pattern
    * @return void
    */
    protected function parseTriplePattern(&$pattern)
    {
        $trp      = array();
        $prev     = false;
        $prevPred = false;
        $cont     = true;
        $sub      = "";
        $pre      = "";
        $tmp      = "";
        $tmpPred  = "";
        $obj      = "";
        do {
//echo strtolower(current($this->tokens)) . "\n";
            switch (strtolower(current($this->tokens))) {
                case false:
                    $cont          = false;
                    $pattern->open = false;
                    break;
                case "filter":
                    $this->parseConstraint($pattern,false);
                    $this->_fastForward();
                    break;
                case "optional":
                    $this->_fastForward();
                    $this->parseGraphPattern($pattern->getId(),false);
                    $cont = false;
                    break;
                case "union":
                    $this->_fastForward();
                    $this->parseGraphPattern(
                        false, $this->tmp, false, false, false, $pattern->getId()
                    );
                    break;
                case ";":
                    $prev = true;
                    $this->_fastForward();
                    break;
                case ".":
                    $prev = false;
                    $this->_fastForward();
                    break;
                case "graph":
                    $this->parseGraph();
                    break;
                case ",":
                    $prev     = true;
                    $prevPred = true;
                    $this->_fastForward();
                    break;
                case "}":
                    $prev = false;
                    $pattern->open = false;
                    $cont = false;
                    break;
                case '{':
                    //subpatterns opens
                    $this->parseGraphPattern(
                        false, false, false, false, false, $pattern->getId()
                    );
                    break;
                case "[":
                    $prev = true;
                    $tmp  = $this->parseNode($this->query->getBlanknodeLabel());
                    $this->_fastForward();
                    break;
                case "]":
                    $prev = true;
                    $this->_fastForward();
                    break;
                case "(":
                    $prev = true;
                    $tmp = $this->parseCollection($trp);
                    $this->_fastForward();
                    break;
                case false:
                    $cont = false;
                    $pattern->open = false;
                    break;
                default:
                    if ($prev) {
                        $sub = $tmp;
                    } else {
                        $sub = $this->parseNode();
                        $this->_fastForward();
                        $tmp     = $sub;
                    }
                    if ($prevPred) {
                        $pre = $tmpPred;
                    } else {
                        $pre = $this->parseNode();
                        $this->_fastForward();
                        $tmpPred = $pre;
                    }
                    if (current($this->tokens)=="[") {
                        $tmp  = $this->parseNode($this->query->getBlanknodeLabel());
                        $prev = true;
                        $obj = $tmp;
                    } else if (current($this->tokens)=="(") {
                        $obj = $this->parseCollection($trp);
                    } else {
                        $obj = $this->parseNode();
                    }
                    $trp[] = new QueryTriple($sub,$pre,$obj);
                    $this->_fastForward();
                    break;

            }
        } while ($cont);

        if (count($trp) > 0) {
            $pattern->addTriplePatterns($trp);
        }
    }



    /**
    * Parses a value constraint.
    *
    * @param GraphPattern $pattern
    * @param boolean $outer     If the constraint is an outer one.
    * @return void
    */
    protected function parseConstraint(&$pattern, $outer)
    {
        $constraint = new Constraint();
        $constraint->setOuterFilter($outer);
        $this->_fastForward();
        $this->_rewind();
        $nBeginKey = key($this->tokens);
        $constraint->setTree(
            $t = $this->parseConstraintTree()
        );

        $nEndKey = key($this->tokens);
        if (current($this->tokens) == '}') {
            prev($this->tokens);
        }

        //for backwards compatibility with the normal sparql engine
        // which does not use the tree array currently
        $expression = trim(implode(
            '',
            array_slice(
                    $this->tokens,
                    $nBeginKey + 1,
                    $nEndKey - $nBeginKey - 1
            )
        ));
        if ($expression[0] == '(' && substr($expression, -1) == ')') {
            $expression = trim(substr($expression, 1, -1));
        }
        $constraint->addExpression($expression);

        $pattern->addConstraint($constraint);
    }//protected function parseConstraint(&$pattern, $outer)



    /**
    *   Parses a constraint string recursively.
    *
    *   The result array is one "element" which may contain subelements.
    *   All elements have one key "type" that determines which other
    *   array keys the element array has. Valid types are:
    *   - "value":
    *       Just a plain value with a value key, nothing else
    *   - "function"
    *       A function has a name and an array of parameter(s). Each parameter
    *       is an element.
    *   - "equation"
    *       An equation has an operator, and operand1 and operand2 which
    *       are elements themselves
    *   Any element may have the "negated" value set to true, which means
    *   that is is - negated (!).
    *
    *   @internal The functionality of this method is being unit-tested
    *   in testSparqlParserTests::testParseFilter()
    *   "equation'-elements have another key "level" which is to be used
    *   internally only.
    *
    *   @return array Nested tree array representing the filter
    */
    protected function parseConstraintTree($nLevel = 0, $bParameter = false)
    {
        $tree       = array();
        $part       = array();
        $chQuotes   = null;
        $litQuotes  = null;
        $strQuoted  = '';

        while ($tok = next($this->tokens)) {
//var_dump(array($tok, $tok[strlen($tok) - 1]));
            if ($chQuotes !== null && $tok != $chQuotes) {
                $strQuoted .= $tok;
                continue;
            } else if ($litQuotes !== null) {
                $strQuoted .= $tok;
                if ($tok[strlen($tok) - 1] == '>') {
                    $tok = '>';
                } else {
                    continue;
                }
            } else if ($tok == ')' || $tok == '}' || $tok == '.') {
                break;
            }

            switch ($tok) {
                case '"':
                case '\'':
                    if ($chQuotes === null) {
                        $chQuotes  = $tok;
                        $strQuoted = '';
                    } else {
                        $chQuotes = null;
                        $part[] = array(
                            'type'  => 'value',
                            'value' => $strQuoted,
                            'quoted'=> true
                        );
                    }
                    continue 2;
                    break;

                case '>':
                    $litQuotes = null;
                    $part[] = array(
                        'type'  => 'value',
                        'value' => $strQuoted,
                        'quoted'=> false
                    );
                    continue 2;
                    break;

                case '(':
                    $bFunc1 = isset($part[0]['type']) && $part[0]['type'] == 'value';
                    $bFunc2 = isset($tree['type'])    && $tree['type']    == 'equation'
                           && isset($tree['operand2']) && isset($tree['operand2']['value']);
                    $part[] = $this->parseConstraintTree(
                        $nLevel + 1,
                        $bFunc1 || $bFunc2
                    );

                    if ($bFunc1) {
                        $tree['type']       = 'function';
                        $tree['name']       = $part[0]['value'];
                        self::fixNegationInFuncName($tree);
                        if (isset($part[1]['type'])) {
                            $part[1] = array($part[1]);
                        }
                        $tree['parameter']  = $part[1];
                        $part = array();
                    } else if ($bFunc2) {
                        $tree['operand2']['type']       = 'function';
                        $tree['operand2']['name']       = $tree['operand2']['value'];
                        self::fixNegationInFuncName($tree['operand2']);
                        $tree['operand2']['parameter']  = $part[0];
                        unset($tree['operand2']['value']);
                        unset($tree['operand2']['quoted']);
                        $part = array();
                    }
                    continue 2;
                    break;

                case ' ':
                case "\t":
                    continue 2;

                case '=':
                case '>':
                case '<':
                case '<=':
                case '>=':
                case '!=':
                case '&&':
                case '||':
                    if (isset($tree['type']) && $tree['type'] == 'equation'
                        && isset($tree['operand2'])) {
                        //previous equation open
                        $part = array($tree);
                    } else if (isset($tree['type']) && $tree['type'] != 'equation') {
                        $part = array($tree);
                        $tree = array();
                    }
                    $tree['type']       = 'equation';
                    $tree['level']      = $nLevel;
                    $tree['operator']   = $tok;
                    $tree['operand1']   = $part[0];
                    unset($tree['operand2']);
                    $part = array();
                    continue 2;
                    break;

                case '!':
                    if ($tree != array()) {
                        throw new SparqlParserException(
                            'Unexpected "!" negation in constraint.'
                        );
                    }
                    $tree['negated'] = true;
                    continue 2;

                case ',':
                    //parameter separator
                    if (count($part) == 0 && !isset($tree['type'])) {
                        throw new SparqlParserException(
                            'Unexpected comma'
                        );
                    }
                    $bParameter = true;
                    if (count($part) == 0) {
                        $part[] = $tree;
                        $tree = array();
                    }
                    continue 2;

                default:
                    break;
            }

            if ($this->varCheck($tok)) {
                $part[] = array(
                    'type'      => 'value',
                    'value'     => $tok,
                    'quoted'    => false
                );
            } else if (substr($tok, 0, 2) == '^^') {
                $part[count($part) - 1]['datatype']
                    = $this->query->getFullUri(substr($tok, 2));
            } else if ($tok[0] == '@') {
                $part[count($part) - 1]['language'] = substr($tok, 1);
            } else if ($tok[0] == '<') {
                if ($tok[strlen($tok) - 1] == '>') {
                    //single-tokenized <> uris
                    $part[] = array(
                        'type'      => 'value',
                        'value'     => $tok,
                        'quoted'    => false
                    );
                } else {
                    //iris split over several tokens
                    $strQuoted = $tok;
                    $litQuotes = true;
                }
            } else if ($tok == 'true' || $tok == 'false') {
                $part[] = array(
                    'type'      => 'value',
                    'value'     => $tok,
                    'quoted'    => false,
                    'datatype'  => 'http://www.w3.org/2001/XMLSchema#boolean'
                );
            } else {
                $part[] = array(
                    'type'      => 'value',
                    'value'     => $tok,
                    'quoted'    => false
                );
            }

            if (isset($tree['type']) && $tree['type'] == 'equation' && isset($part[0])) {
                $tree['operand2'] = $part[0];
                self::balanceTree($tree);
                $part = array();
            }
        }

        if (!isset($tree['type']) && $bParameter) {
            return $part;
        } else if (isset($tree['type']) && $tree['type'] == 'equation'
            && isset($tree['operand1']) && !isset($tree['operand2'])
            && isset($part[0])) {
            $tree['operand2'] = $part[0];
            self::balanceTree($tree);
        }

        if (!isset($tree['type']) && isset($part[0])) {
            if (isset($tree['negated'])) {
                $part[0]['negated'] = true;
            }
            return $part[0];
        }

        return $tree;
    }//protected function parseConstraintTree($nLevel = 0, $bParameter = false)



    /**
    *   "Balances" the filter tree in the way that operators on the same
    *   level are nested according to their precedence defined in
    *   $operatorPrecedence array.
    *
    *   @param array $tree  Tree to be modified
    */
    protected static function balanceTree(&$tree)
    {
        if (
            isset($tree['type']) && $tree['type'] == 'equation'
         && isset($tree['operand1']['type']) && $tree['operand1']['type'] == 'equation'
         && $tree['level'] == $tree['operand1']['level']
         && self::$operatorPrecedence[$tree['operator']] > self::$operatorPrecedence[$tree['operand1']['operator']]
        ) {
            $op2 = array(
                'type'      => 'equation',
                'level'     => $tree['level'],
                'operator'  => $tree['operator'],
                'operand1'  => $tree['operand1']['operand2'],
                'operand2'  => $tree['operand2']
            );
            $tree['operator']   = $tree['operand1']['operator'];
            $tree['operand1']   = $tree['operand1']['operand1'];
            $tree['operand2']   = $op2;
        }
    }//protected static function balanceTree(&$tree)



    protected static function fixNegationInFuncName(&$tree)
    {
        if ($tree['type'] == 'function' && $tree['name'][0] == '!') {
            $tree['name'] = substr($tree['name'], 1);
            if (!isset($tree['negated'])) {
                $tree['negated'] = true;
            } else {
                unset($tree['negated']);
            }
            //perhaps more !!
            self::fixNegationInFuncName($tree);
        }
    }//protected static function fixNegationInFuncName(&$tree)



    /**
    * Parses a bracketted expression.
    *
    * @param  Constraint $constraint
    * @return void
    * @throws SparqlParserException
    */
    protected function parseBrackettedExpression(&$constraint)
    {
        $open = 1;
        $exp = "";
        $this->_fastForward();
        while ($open != 0 && current($this->tokens)!= false) {
            switch (current($this->tokens)) {
                case "(":
                    $open++;
                    $exp = $exp . current($this->tokens);
                    break;
                case ")":
                    $open--;
                    if($open != 0){
                        $exp = $exp . current($this->tokens);
                    }
                    break;
                case false:
                    throw new SparqlParserException(
                        "Unexpected end of query.",
                        null,
                        key($this->tokens)
                    );
                default:
                    $exp = $exp . current($this->tokens);
                    break;
            }
            next($this->tokens);
        }
        $constraint->addExpression($exp);
    }


    /**
    * Parses an expression.
    *
    * @param  Constraint  $constrain
    * @return void
    * @throws SparqlParserException
    */
    protected function parseExpression(&$constraint)
    {
        $exp = "";
        while (current($this->tokens) != false && current($this->tokens) != "}") {
            switch (current($this->tokens)) {
                case false:
                    throw new SparqlParserException(
                        "Unexpected end of query.",
                        null,
                        key($this->tokens)
                    );
                case ".":
                    break;
                    break;
                default:
                    $exp = $exp . current($this->tokens);
                    break;
            }
            next($this->tokens);
        }
        $constraint->addExpression($exp);
    }

    /**
    * Parses a GRAPH clause.
    *
    * @param  GraphPattern $pattern
    * @return void
    * @throws SparqlParserException
    */
    protected function parseGraph(){
        $this->_fastForward();
        $name = current($this->tokens);
        if(!$this->varCheck($name)&!$this->iriCheck($name)&&!$this->qnameCheck($name)){
            $msg = $name;
            $msg = preg_replace('/</', '&lt;', $msg);
            throw new SparqlParserException(" IRI or Var expected. ",null,key($this->tokens));
        }
        $this->_fastForward();

        if($this->iriCheck($name)){
            $name = new Resource(substr($name,1,-1));
        }else if($this->qnameCheck($name)){
            $name = new Resource($this->query->getFullUri($name));
        }
        $this->parseGraphPattern(false,false,$name);
        if(current($this->tokens)=='.')
        $this->_fastForward();
    }

    /**
    * Parses the solution modifiers of a query.
    *
    * @return void
    * @throws SparqlParserException
    */
    protected function parseModifier(){
        do{
            switch(strtolower(current($this->tokens))){
                case "order":
                $this->_fastForward();
                if(strtolower(current($this->tokens))=='by'){
                    $this->_fastForward();
                    $this->parseOrderCondition();
                }else{
                    throw new SparqlParserException("'BY' expected.",null,key($this->tokens));
                }
                break;
                case "limit":
                $this->_fastForward();
                $val = current($this->tokens);
                $this->query->setSolutionModifier('limit',$val);
                break;
                case "offset":
                $this->_fastForward();
                $val = current($this->tokens);
                $this->query->setSolutionModifier('offset',$val);
                break;
                default:
                break;
            }
        }while(next($this->tokens));
    }

    /**
    * Parses order conditions of a query.
    *
    * @return void
    * @throws SparqlParserException
    */
    protected function parseOrderCondition(){
        $valList = array();
        $val = array();
        while(strtolower(current($this->tokens))!='limit'
        & strtolower(current($this->tokens))!= false
        & strtolower(current($this->tokens))!= 'offset'){
            switch (strtolower(current($this->tokens))){
                case "desc":
                $this->_fastForward();
                $this->_fastForward();
                if($this->varCheck(current($this->tokens))){
                    $val['val'] = current($this->tokens);
                }else{
                    throw new SparqlParserException("Variable expected in ORDER BY clause. ",null,key($this->tokens));
                }
                $this->_fastForward();
                if(current($this->tokens)!=')')
                throw new SparqlParserException("missing ')' in ORDER BY clause.",null,key($this->tokens));
                $val['type'] = 'desc';
                $this->_fastForward();
                break;
                case "asc" :
                $this->_fastForward();
                $this->_fastForward();
                if($this->varCheck(current($this->tokens))){
                    $val['val'] = current($this->tokens);
                }else{
                    throw new SparqlParserException("Variable expected in ORDER BY clause. ",null,key($this->tokens));
                }
                $this->_fastForward();
                if(current($this->tokens)!=')')
                throw new SparqlParserException("missing ')' in ORDER BY clause.",null,key($this->tokens));
                $val['type'] = 'asc';
                $this->_fastForward();
                break;
                default:
                if($this->varCheck(current($this->tokens))){
                    $val['val'] = current($this->tokens);
                    $val['type'] = 'asc';
                }else{
                    throw new SparqlParserException("Variable expected in ORDER BY clause. ",null,key($this->tokens));
                }
                $this->_fastForward();
                break;
            }
            $valList[] = $val;
        }
        prev($this->tokens);
        $this->query->setSolutionModifier('order by',$valList);
    }

    /**
    * Parses a String to an RDF node.
    *
    * @param  String $node
    *
    * @return Node   The parsed RDF node
    * @throws SparqlParserException
    */
    protected function parseNode($node = false)
    {
        //$eon = false;
        if ($node) {
            $node = $node;
        } else {
            $node = current($this->tokens);
        }
        if ($node{strlen($node)-1} == '.') {
            $node = substr($node,0,-1);
        }
        if ($this->dtypeCheck($node)) {
            return $node;
        }
        if ($this->bNodeCheck($node)) {
            $node = '?'.$node;
            $this->query->addUsedVar($node);
            return $node;
        }
        if ($node == '[') {
            $node = '?' . substr($this->query->getBlanknodeLabel(), 1);
            $this->query->addUsedVar($node);
            $this->_fastForward();
            if(current($this->tokens)!=']') {
                prev($this->tokens);
            }
            return $node;
        }
        if ($this->iriCheck($node)){
            $base = $this->query->getBase();
            if ($base!=null) {
                $node = new Resource(substr(substr($base,0,-1).substr($node,1),1,-1));
            } else {
                $node = new Resource(substr($node,1,-1));
            }
            return $node;
        } else if ($this->qnameCheck($node)) {
            $node = $this->query->getFullUri($node);
            $node = new Resource($node);
            return $node;
        } else if ($this->literalCheck($node)) {
            $ch     = substr($node, 0, 1);
            $chLong = str_repeat($ch, 3);
            if (substr($node, 0, 3) == $chLong) {
                $ch = $chLong;
            }
            $this->parseLiteral($node, $ch);
        } else if ($this->varCheck($node)) {
            $pos = strpos($node,'.');
            if ($pos) {
                return substr($node,0,$pos);
            } else {
                return $node;
            }
        } else if ($node[0] == '<') {
            //partial IRI? loop tokens until we find a closing >
            while (next($this->tokens)) {
                $node .= current($this->tokens);
                if (substr($node, -1) == '>') {
                    break;
                }
            }
            if (substr($node, -1) != '>') {
                throw new SparqlParserException(
                    "Unclosed IRI: " . $node,
                    null,
                    key($this->tokens)
                );
            }
            return $this->parseNode($node);
        } else {
            throw new SparqlParserException(
                '"' . $node . '" is neither a valid rdf- node nor a variable.',
                null,
                key($this->tokens)
            );
        }
        return $node;
    }//protected function parseNode($node = false)



    /**
    * Checks if there is a datatype given and appends it to the node.
    *
    * @param string $node Node to check
    *
    * @return void
    */
    protected function checkDtypeLang(&$node, $nSubstrLength = 1)
    {
        $this->_fastForward();
        switch (substr(current($this->tokens), 0, 1)) {
            case '^':
                if (substr(current($this->tokens),0,2)=='^^') {
                    $node = new Literal(substr($node,1,-1));
                    $node->setDatatype(
                        $this->query->getFullUri(
                            substr(current($this->tokens), 2)
                        )
                    );
                }
                break;
            case '@':
                $node = new Literal(
                    substr($node, $nSubstrLength, -$nSubstrLength),
                    substr(current($this->tokens), $nSubstrLength)
                );
                break;
            default:
                prev($this->tokens);
                $node = new Literal(substr($node, $nSubstrLength, -$nSubstrLength));
                break;

        }
    }//protected function checkDtypeLang(&$node, $nSubstrLength = 1)



    /**
    * Parses a literal.
    *
    * @param String $node
    * @param String $sep used separator " or '
    *
    * @return void
    */
    protected function parseLiteral(&$node, $sep)
    {
        do {
            next($this->tokens);
            $node = $node.current($this->tokens);
        } while (current($this->tokens) != $sep);
        $this->checkDtypeLang($node, strlen($sep));
    }//protected function parseLiteral(&$node, $sep)



    /**
    * Checks if the Node is a typed Literal.
    *
    * @param String $node
    *
    * @return boolean TRUE if typed FALSE if not
    */
    protected function dtypeCheck(&$node)
    {
        $patternInt = "/^-?[0-9]+$/";
        $match = preg_match($patternInt,$node,$hits);
        if($match>0){
            $node = new Literal($hits[0]);
            $node->setDatatype(XML_SCHEMA.'integer');
            return true;
        }
        $patternBool = "/^(true|false)$/";
        $match = preg_match($patternBool,$node,$hits);
        if($match>0){
            $node = new Literal($hits[0]);
            $node->setDatatype(XML_SCHEMA.'boolean');
            return true;
        }
        $patternType = "/^a$/";
        $match = preg_match($patternType,$node,$hits);
        if($match>0){
            $node = new Resource(RDF_NAMESPACE_URI.'type');
            return true;
        }
        $patternDouble = "/^-?[0-9]+.[0-9]+[e|E]?-?[0-9]*/";
        $match = preg_match($patternDouble,$node,$hits);
        if($match>0){
            $node = new Literal($hits[0]);
            $node->setDatatype(XML_SCHEMA.'double');
            return true;
        }
        return false;
    }//protected function dtypeCheck(&$node)



    /**
    * Parses an RDF collection.
    *
    * @param  TriplePattern $trp
    *
    * @return Node          The first parsed label
    */
    protected function parseCollection(&$trp)
    {
        $tmpLabel = $this->query->getBlanknodeLabel();
        $firstLabel = $this->parseNode($tmpLabel);
        $this->_fastForward();
        $i = 0;
        while (current($this->tokens)!=")") {
            if($i>0)
            $trp[] = new QueryTriple($this->parseNode($tmpLabel),new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#rest"),$this->parseNode($tmpLabel = $this->query->getBlanknodeLabel()));
            $trp[] = new QueryTriple($this->parseNode($tmpLabel),new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#first"),$this->parseNode());
            $this->_fastForward();
            $i++;
        }
        $trp[] = new QueryTriple($this->parseNode($tmpLabel),new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#rest"),new Resource("http://www.w3.org/1999/02/22-rdf-syntax-ns#nil"));
        return $firstLabel;
    }//protected function parseCollection(&$trp)

}// end class: SparqlParser.php

?>