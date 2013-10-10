<?php
// ---------------------------------------------
// Class: QueryTriple
// ---------------------------------------------
require_once RDFAPI_INCLUDE_DIR . 'sparql/SparqlVariable.php';

/**
* Represents a query triple with subject, predicate and object.
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	$Id$
* @license http://www.gnu.org/licenses/lgpl.html LGPL
*
* @package sparql
*/
class QueryTriple extends Object
{

    /**
    * The QueryTriples Subject.
    * Can be a BlankNode or Resource, string in
    * case of a variable
    * @var Node/string
    */
    protected $subject;

    /**
    * The QueryTriples Predicate.
    * Normally only a Resource, string in
    * case of a variable
    * @var Node/string
    */
    protected $predicate;

    /**
    * The QueryTriples Object.
    * Can be BlankNode, Resource or Literal, string in
    * case of a variable
    * @var Node/string
    */
    protected $object;



    /**
    * Constructor
    *
    * @param Node $sub  Subject
    * @param Node $pred Predicate
    * @param Node $ob   Object
    */
    public function QueryTriple($sub,$pred,$ob)
    {
        $this->subject   = $sub;
        $this->predicate = $pred;
        $this->object    = $ob;
    }

    /**
    * Returns the Triples Subject.
    *
    * @return Node
    */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
    * Returns the Triples Predicate.
    *
    * @return Node
    */
    public function getPredicate()
    {
        return $this->predicate;
    }

    /**
    * Returns the Triples Object.
    *
    * @return Node
    */
    public function getObject()
    {
        return $this->object;
    }



    /**
    *   Returns an array of all variables in this triple.
    *
    *   @return array   Array of variable names
    */
    public function getVariables()
    {
        $arVars = array();

        foreach (array('subject', 'predicate', 'object') as $strVar) {
            if (SparqlVariable::isVariable($this->$strVar)) {
                $arVars[] = $this->$strVar;
            }
        }

        return $arVars;
    }//public function getVariables()



    public function __clone()
    {
        foreach (array('subject', 'predicate', 'object') as $strVar) {
            if (is_object($this->$strVar)) {
                $this->$strVar = clone $this->$strVar;
            }
        }
    }//public function __clone()

}//class QueryTriple extends Object

// end class: QueryTriple.php
?>