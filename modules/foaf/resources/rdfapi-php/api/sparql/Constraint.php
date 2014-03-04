<?php
require_once RDFAPI_INCLUDE_DIR . 'util/Object_rap.php';
// ---------------------------------------------
// class: Constraint.php
// ---------------------------------------------


/**
* Represents a constraint. A value constraint is a boolean- valued expression
* of variables and RDF Terms.
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 $Id: Constraint.php 7228 2007-09-27 06:24:51Z kudakwashe $
*
* @package sparql
*/
Class Constraint extends Object_rap{

    /**
    * The expression string.
    * @var string
    */
    protected $expression;

    /**
    *  True if it is an outer filter, false if not.
    * @var boolean
    */
    protected $outer;

    /**
    *   The expression tree
    *   @var array
    */
    protected $tree = null;

    /**
    * Adds an expression string.
    *
    * @param  String $exp the expression String
    * @return void
    */
    public function addExpression($exp)
    {
        $this->expression = $exp;
    }

    /**
    * Returns the expression string.
    *
    * @return String  the expression String
    */
    public function getExpression()
    {
        return $this->expression;
    }


    /**
    * Sets the filter type to outer or inner filter.
    * True for outer false for inner.
    *
    * @param  boolean $boolean
    * @return void
    */
    public function setOuterFilter($boolean)
    {
        $this->outer = $boolean;
    }

    /**
    * Returns true if this constraint is an outer filter- false if not.
    *
    * @return boolean
    */
    public function isOuterFilter()
    {
        return $this->outer;
    }


    public function getTree()
    {
        return $this->tree;
    }//public function getTree()

    public function setTree($tree)
    {
        $this->tree = $tree;
    }//public function setTree($tree)

}
// end class: Constraint.php
?>
