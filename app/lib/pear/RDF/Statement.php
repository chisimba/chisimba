<?php
// ----------------------------------------------------------------------------------
// Class: RDF_Statement
// ----------------------------------------------------------------------------------
/**
 * An RDF statement.
 * In this implementation, a statement is not itself a resource.
 * If you want to use a a statement as subject or object of other statements,
 * you have to reify it first.
 *
 * @author Chris Bizer <chris@bizer.de>
 * @version V0.7
 * @package model
 */
class RDF_Statement extends RDF_Object
{
    /**
     * Subject of the statement
     *
     * @var object resource
     * @access private
     */
    var $subj;

    /**
     * Predicate of the statement
     *
     * @var object resource
     * @access private
     */
    var $pred;

    /**
     * Object of the statement
     *
     * @var object node
     * @access private
     */
    var $obj;

    /**
     * The parameters are instances of classes and not just strings
     *
     * @param object node $subj
     * @param object node $pred
     * @param object node $obj
     * @throws PhpError
     */
    function factory($subj, $pred, $obj)
    {
        $statement = new RDF_Statement;

        $statement->setSubject($subj);
        $statement->setPredicate($pred);
        $statement->setObject($obj);

        return $statement;
    }

    /**
     * Set the subject of the triple.
     *
     * @access public
     * @return object node
     */
    function setSubject($subj)
    {
        if (!is_a($subj, 'RDF_Resource')) {
            $errmsg = 'Resource expected as subject, got unexpected: '.
                (is_object($subj) ? get_class($subj) : gettype($subj));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }
        $this->subj = $subj;
    }

    /**
     * Returns the subject of the triple.
     *
     * @access public
     * @return object node
     */
    function getSubject()
    {
        return $this->subj;
    }

    /**
     * Set the predicate of the triple.
     *
     * @access public
     * @return object node
     */
    function setPredicate($pred)
    {
        if (!is_a($pred, 'RDF_Resource') || is_a($pred, 'RDF_BlankNode')) {
            $errmsg = 'Resource expected as predicate, no blank node allowed, got unexpected: '.
                (is_object($pred) ? get_class($pred) : gettype($pred));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }
        $this->pred = $pred;
    }

    /**
     * Returns the predicate of the triple.
     *
     * @access public
     * @return object node
     */
    function getPredicate()
    {
        return $this->pred;
    }

    /**
     * Set the object of the triple.
     *
     * @access public
     * @return object node
     */
    function setObject($obj)
    {
        if (!(is_a($obj, 'RDF_Resource') or is_a($obj, 'RDF_Literal'))) {
           $errmsg = 'Resource or Literal expected as object, got unexpected: '.
                (is_object($obj) ? get_class($obj) : gettype($obj));
            return RDF::raiseError(RDF_ERROR_UNEXPECTED, null, null, $errmsg);
        }
        $this->obj = $obj;
    }

    /**
     * Returns the object of the triple.
     *
     * @access public
     * @return object node
     */
    function getObject()
    {
        return $this->obj;
    }

    /**
     * Retruns the hash code of the triple.
     *
     * @access public
     * @return string
     */
    function hashCode()
    {
        return md5($this->subj->getLabel() . $this->pred->getLabel() . $this->obj->getLabel());
    }

    /**
     * Dumps the triple.
     *
     * @access public
     * @return string
     */

    function toString()
    {
        return 'Triple(' . $this->subj->toString() . ', ' . $this->pred->toString() . ', ' . $this->obj->toString() . ')';
    }

    /**
     * Returns a toString() serialization of the statements's subject.
     *
     * @access public
     * @return string
     */
    function toStringSubject()
    {
        return $this->subj->toString();
    }

    /**
     * Returns a toString() serialization of the statements's predicate.
     *
     * @access public
     * @return string
     */
    function toStringPredicate()
    {
        return $this->pred->toString();
    }

    /**
     * Reurns a toString() serialization of the statements's object.
     *
     * @access public
     * @return string
     */
    function toStringObject()
    {
        return $this->obj->toString();
    }

    /**
     * Returns the URI or bNode identifier of the statements's subject.
     *
     * @access public
     * @return string
     */
    function getLabelSubject()
    {
        return $this->subj->getLabel();
    }

    /**
     * Returns the URI of the statements's predicate.
     *
     * @access public
     * @return string
     */
    function getLabelPredicate()
    {
        return $this->pred->getLabel();
    }

    /**
     * Reurns the URI, text or bNode identifier of the statements's object.
     *
     * @access public
     * @return string
     */
    function getLabelObject()
    {
        return $this->obj->getLabel();
    }

    /**
     * Checks if two statements are equal.
     * Two statements are considered to be equal if they have the
     * same subject, predicate and object. A statement can only be equal
     * to another statement object.
     *
     * @access public
     * @param object statement $that
     * @return boolean
     */
    function equals($that)
    {
        if ($this == $that) {
            return true;
        }

        if ($that == null || !(is_a($that, 'RDF_Statement'))) {
            return false;
        }

        return $this->subj->equals($that->getSubject())
            && $this->pred->equals($that->getPredicate())
            && $this->obj->equals($that->getObject());
    }

    /**
     * Compares two statements and returns integer less than, equal to, or greater than zero.
     * Can be used for writing sorting function for models or with the PHP function usort().
     *
     * @access public
     * @param object statement &$compare_with
     * @return boolean
     */

    function compare(&$compare_with)
    {
        return RDF_statementsorter($this, $compare_with);
        // statementsorter function see below
    }

    /**
     * Reifies a statement.
     * Returns a new Model_Memory that is the reification of the statement.
     * For naming the statement's bNode a Model or bNodeID must be passed to the method.
     *
     * @access public
     * @param mixed &$model_or_bNodeID
     * @return object model
     */
    function &reify(&$model_or_bNodeID)
    {
        if (is_a($model_or_bNodeID, 'RDF_Model_Memory')) {
            // parameter is model
            $statementModel =& new RDF_Model_Memory($model_or_bNodeID->getBaseURI());
            $thisStatement =& RDF_BlankNode::factory($model_or_bNodeID);
        } else {
            // parameter is bNodeID
            $statementModel =& new RDF_Model_Memory();
            $thisStatement =& RDF_BlankNode::factory($model_or_bNodeID);
        }

        $RDFstatement =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_STATEMENT);
        $RDFtype =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_TYPE);
        $RDFsubject =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_SUBJECT);
        $RDFpredicate =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_PREDICATE);
        $RDFobject =& RDF_Resource::factory(RDF_NAMESPACE_URI . RDF_OBJECT);

        $statementModel->add(RDF_Statement::factory($thisStatement, $RDFtype, $RDFstatement));
        $statementModel->add(RDF_Statement::factory($thisStatement, $RDFsubject, $this->getSubject()));
        $statementModel->add(RDF_Statement::factory($thisStatement, $RDFpredicate, $this->getPredicate()));
        $statementModel->add(RDF_Statement::factory($thisStatement, $RDFobject, $this->getObject()));
        return $statementModel;
    }
} // end: Statement

/**
 * Comparison function for comparing two statements.
 * RDF_statementsorter() is used by the PHP function usort ( array array, callback cmp_function)
 *
 * @access private
 * @param object Statement    $a
 * @param object Statement    $b
 * @return integer less than, equal to, or greater than zero
 * @throws phpErrpr
 */
function RDF_statementsorter($a, $b)
{
    // Compare subjects
    $x = $a->getSubject();
    $y = $b->getSubject();
    $r = strcmp($x->getLabel(), $y->getLabel());
    if ($r != 0) {
        return $r;
    }

    // Compare predicates
    $x = $a->getPredicate();
    $y = $b->getPredicate();
    $r = strcmp($x->getURI(), $y->getURI());
    if ($r != 0) {
        return $r;
    }

    // Final resort, compare objects
    $x = $a->getObject();
    $y = $b->getObject();

    return strcmp($x->toString(), $y->toString());
}
?>