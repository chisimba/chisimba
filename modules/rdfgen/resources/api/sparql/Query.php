<?php
require_once RDFAPI_INCLUDE_DIR . 'sparql/GraphPattern.php';

/**
* The Class Query represents a SPARQL query.
*
*
* @author  Tobias Gauss <tobias.gauss@web.de>
* @version $Id$
* @license http://www.gnu.org/licenses/lgpl.html LGPL
*
* @package sparql
*/
class Query extends Object
{
    /**
    * The BASE part of the SPARQL query.
    * @var string
    */
    protected $base;

    /**
    * Original SPARQL query string
    * @var string
    */
    protected $queryString = null;

    /**
    * Array that contains used prefixes and namespaces.
    * Key is the prefix, value the namespace.
    *
    * @example
    * array(8) {
    *  ["rdf"] => string(43) "http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    *  ["rdfs"]=> string(37) "http://www.w3.org/2000/01/rdf-schema#"
    * }
    *
    * @var array
    */
    public $prefixes = array();

    /**
    * Array of result variables that shall be returned.
    * E.g. "?name", "?mbox"
    *
    * @var array
    */
    protected $resultVars = array();

    /**
    * What form/type of result should be returned.
    *
    * One of:
    * - "ask"
    * - "count"
    * - "construct"
    * - "describe"
    * - "select"
    * - "select distinct"
    *
    * @var string
    * @see http://www.w3.org/TR/rdf-sparql-query/#QueryForms
    */
    protected $resultForm = null;

    /**
    * Contains the result part of the SPARQL query.
    * Array of GraphPattern
    *
    * @var array
    */
    protected $resultPart = array();

    /**
    *  Contains the FROM part of the SPARQL query.
    * @var array
    */
    protected $fromPart = array();

    /**
    * Contains the FROM NAMED part of the SPARQL query.
    * @var array
    */
    protected $fromNamedPart = array();

    /**
    * Optional solution modifier of the query.
    * Array with three keys:
    *  "order by", "limit", "offset"
    * If they are not set, their value is null
    *
    * "order by" can be an array with subarrays, each of those
    *  subarrays having two keys: "val" and "type".
    *  "val" determines the variable ("?mbox"), "type" is
    *  "asc" or "desc"
    *
    * @var array
    */
    protected $solutionModifier = array();

    /**
    *  Blanknode counter.
    * How many blank nodes are in $resultPart
    *
    * @var int
    */
    protected $bnodeCounter = 0;

    /**
    * GraphPattern counter.
    * How many GraphPattern are in $resultPart
    *
    * @var int
    */
    public $graphPatternCounter = 0;

    /**
    * List of all vars used in the query.
    * Key is the variable (e.g. "?x"), value
    * is boolean true
    *
    * @var array
    */
    public $usedVars = array();

    /**
    * If the query type is CONSTRUCT this variable contains the
    * CONSTRUCT graph pattern.
    */
    protected $constructPattern = null;

    /**
    * TRUE if the query is empty FALSE if not.
    *
    * @var boolean
    */
    public $isEmpty = null;

    /**
    *   Language of variables. NULL if the variable has no
    *   language tag (e.g. @en) set.
    *   $varname => $language tag
    *   @var array
    */
    public $varLanguages = array();

    /**
    *   Datatype of variables. NULL if the variable has no
    *   data type (e.g. ^^xsd::integer) set.
    *   $varname => $datatype
    *   @var array
    */
    public $varDatatypes = array();



    /**
    * Constructor
    */
    public function Query(){
        $this->resultForm = null;
        $this->solutionModifier['order by'] = null;
        $this->solutionModifier['limit']    = null;
        $this->solutionModifier['offset']   = null;
        $this->bnodeCounter = 0;
        $this->graphPatternCounter = 0;

    }

    /**
    * Returns the BASE part of the query.
    *
    * @return String
    */
    public function getBase(){
        return $this->base;
    }

    /**
    * Returns the prefix map of the query.
    *
    * @return Array
    */
    public function getPrefixes(){
        return $this->prefixes;
    }
    
    /**
    * Returns a list containing the result vars.
    *
    * @return Array
    */
    public function getResultVar($strName) {
        foreach ($this->resultVars as $var) {
            if ($var->getVariable() == $strName) {
                return $var;
            }
        }
        return false;
    }

    /**
    * Returns a list containing the result vars.
    *
    * @return Array
    */
    public function getResultVars(){
        return $this->resultVars;
    }

    /**
    * Returns the type the result shall have.
    * E.g. "select", "select distinct", "ask", ...
    *
    * @see $resultForm
    *
    * @return string
    */
    public function getResultForm(){
        return $this->resultForm;
    }

    /**
    * Returns a list containing the graph patterns of the query.
    *
    * @return Array
    */
    public function getResultPart(){
        return $this->resultPart;
    }

    /**
    * Returns the FROM clause of the query.
    *
    * @return String
    */
    public function getFromPart(){
        return $this->fromPart;
    }

    /**
    * Returns the FROM NAMED clause of the query.
    *
    * @return Array
    */
    public function getFromNamedPart(){
        return $this->fromNamedPart;
    }

    /**
    * Returns $isEmpty variable
    * @return boolean
    */
    public function isEmpty() {
        return $this->isEmpty;
    }

    /**
    * Returns an unused Bnode label.
    *
    * @return String
    */
    public function getBlanknodeLabel(){
        return "_:bN".$this->bnodeCounter++;
    }


    /**
    * Sets the base part.
    *
    * @param String $base
    * @return void
    */
    public function setBase($base){
        $this->base = $base;
    }


    /**
    * Adds a prefix to the list of prefixes.
    *
    * @param  String $prefix
    * @param  String $label
    * @return void
    */
    public function addPrefix($prefix, $label){
        $this->prefixes[$prefix]= $label;
    }

    /**
    * Adds a variable to the list of result variables.
    *
    * @param  String $var
    * @return void
    */
    public function addResultVar($var){
        $this->resultVars[] = $var;
        $var->setDatatype($this->getDatatype($var));

        $this->varLanguages[$var->getId()] = self::getLanguageTag($var);
        $this->varDatatypes[$var->getId()] = $this->getDatatype($var);
    }


    /**
    * Sets the result form.
    *
    * @param  String $form
    * @return void
    */
    public function setResultForm($form){
        $this->resultForm = strtolower($form);
    }


    /**
    * Sets the result part.
    *
    * @param  array Array of graph patterns
    */
    public function setResultPart($resultPart) {
        $this->resultPart = $resultPart;
    }

    /**
    * Adds a graph pattern to the result part.
    *
    * @param  GraphPattern $pattern
    * @return void
    */
    public function addGraphPattern($pattern){
        $pattern->setId($this->graphPatternCounter);
        $this->resultPart[] = $pattern;
        $this->graphPatternCounter++;
    }

    /**
    * Adds a construct graph pattern to the query.
    *
    * @param  GraphPattern $pattern
    * @return void
    */
    public function addConstructGraphPattern($pattern){
        $this->constructPattern = $pattern;
    }


    /**
    * Adds a graphuri to the from part.
    *
    * @param  String $graphURI
    * @return void
    */
    public function addFrom($graphURI){
        $this->fromPart[] = $graphURI;
    }

    /**
    * Adds a graphuri to the from named part.
    *
    * @param  String $graphURI
    * @return void
    */
    public function addFromNamed($graphURI){
        $this->fromNamedPart[] = $graphURI;
    }

    /**
    * Sets a solution modifier.
    *
    * @param  String $name
    * @param  Value  $value
    * @return void
    */
    public function setSolutionModifier($name, $value){
        $this->solutionModifier[$name] = $value;
    }


    /**
    * Generates a new GraphPattern. If it is a CONSTRUCT graph pattern
    * $constr has to set to TRUE, FALSE if not.
    *
    * @param  boolean $constr
    * @return GraphPattern
    */
    public function getNewPattern($constr = false){
        $pattern = new GraphPattern();
        if ($constr) {
            $this->addConstructGraphPattern($pattern);
        } else {
            $this->addGraphPattern($pattern);
        }
        return $pattern;
    }

    /**
    * Adds a new variable to the variable list.
    *
    * @param  String $var
    * @return void
    */
    public function addUsedVar($var){
        $this->usedVars[$var]=true;
    }

    /**
    * Returns a list with all used variables.
    *
    * @return Array
    */
    public function getAllVars(){
        return array_keys($this->usedVars);
    }

    /**
    * Gets the solution modifiers of the query.
    * $solutionModifier['order by'] = value
    *                  ['limit']    = vlaue
    *                  ['offset']   = value
    *
    *
    * @return Array
    */
    public function getSolutionModifier(){
        return $this->solutionModifier;
    }


    /**
    * Returns the construvtGraphPattern of the query if there is one.
    *
    * @return GraphPattern
    */
    public function getConstructPattern(){
        return $this->constructPattern;
    }



    /**
    *   Returns a list of variables used in the construct patterns.
    *
    *   @return array Array of variable names, unique.
    */
    public function getConstructPatternVariables()
    {
        $arVars = array();
/*
        if (count($this->resultPart) > 0) {
            foreach ($this->resultPart as $pattern) {
                $arVars = array_merge($arVars, $pattern->getVariables());
            }
        }
*/
        if ($this->constructPattern) {
            $arVars = array_merge($arVars, $this->constructPattern->getVariables());
        }

        return array_unique($arVars);
    }//public function getConstructPatternVariables()



    /**
    *   Returns the language of a variable if the tag is set (e.g. @en)
    *   Returns NULL if no language is set.
    *
    *   @param string Sparql variable name
    *   @return mixed NULL or language tag
    */
    public static function getLanguageTag($var)
    {
        $nAt = strpos($var, '@');
        if ($nAt === false) {
            return null;
        }
        //in case @ and ^^ are combined
        $nHatHat = strpos($var, '^^', $nAt + 1);
        if ($nHatHat === false) {
            $tag = substr($var, $nAt + 1);
        } else {
            $tag = substr($var, $nAt + 1, $nHatHat - $nAt - 1);
        }
        return $tag;
    }//public static function getLanguageTag($var)



    /**
    *   Returns the datatype of a variable if it is set.
    *
    *   @param string Sparql variable name
    *   @return mixed NULL or datatype
    */
    public function getDatatype($var)
    {
        $nHatHat = strpos($var, '^^');
        if ($nHatHat === false) {
            return null;
        }
        $nAt = strpos($var, '@', $nHatHat + 2);
        if ($nAt === false) {
            $type = substr($var, $nHatHat + 2);
        } else {
            $type = substr($var, $nHatHat + 2, $nAt - $nHatHat - 2);
        }

        $fullUri = $this->getFullUri($type);
        if ($fullUri === false) {
            $fullUri = $type;
            if ($fullUri[0] == '<' && substr($fullUri, -1) == '>') {
                $fullUri = substr($fullUri, 1, -1);
            }
        }

        return $fullUri;
    }//public function getDatatype($var)



    /**
    * Gets the full URI of a qname token.
    *
    * @param  string $token
    * @return string The complete URI of a given token, false if $token is not
    *                a qname or the prefix is not defined
    */
    public function getFullUri($token)
    {
        $pattern="/^([^:]*):([^:]*)$/";
        if (preg_match($pattern, $token, $hits) > 0) {

            if (isset($this->prefixes{$hits{1}})) {
                return substr($this->base, 1, -1)
                     . $this->prefixes{$hits{1}}
                     . $hits{2};
            }
            if ($hits{1}=='_') {
                return "_".$hits{2};
            }
        }

        return false;
    }



    /**
    *   Checks if the query is complete
    *   (so that querying is possible)
    *
    *   @return boolean true if the query is complete
    */
    public function isComplete()
    {
        if ($this->resultForm === null) {
            return false;
        }
        //TODO: maybe check selected vars and construct pattern depending
        // on the resultform
        return true;
    }//public function isIncomplete()



    /**
    * Sets the orignal query string
    *
    * @param string $queryString SPARQL query string
    */
    public function setQueryString($queryString)
    {
        $this->queryString = $queryString;
    }//public function setQueryString($queryString)



    /**
    * Returns the orignal query string
    *
    * @return string SPARQL query string
    */
    public function getQueryString()
    {
        return $this->queryString;
    }//public function getQueryString()

}// end class: Query.php



class Query_ResultVariable
{
    public $variable = null;
    public $datatype = null;
    public $language = null;
    public $alias    = null;
    public $func     = null;


    public function __construct($variable)
    {
        $this->variable = $variable;
        $this->language = Query::getLanguageTag($variable);
    }



    public function setAlias($alias)
    {
        $this->alias = $alias;
    }



    public function setFunc($func)
    {
        $this->func = $func;
    }



    public function setDatatype($datatype)
    {
        $this->datatype = $datatype;
    }



    public function getId()
    {
        //FIXME
        return $this->variable;
    }



    public function getFunc()
    {
        return $this->func;
    }



    public function getLanguage()
    {
        return $this->language;
    }



    public function getDatatype()
    {
        return $this->datatype;
    }



    public function getName()
    {
        if ($this->alias !== null) {
            return $this->alias;
        }
        //FIXME: support for nested(functions())
        return $this->variable;
    }



    public function getVariable()
    {
        return $this->variable;
    }



    public function __toString()
    {
        return $this->getName();
    }

}//class Query_ResultVariable

?>