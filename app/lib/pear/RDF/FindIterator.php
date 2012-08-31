<?php

// ----------------------------------------------------------------------------------
// Class: RDF_FindIterator
// ----------------------------------------------------------------------------------

/**
 * Iterator for traversing statements matching a searchpattern.
 * RDF_FindIterators are returned by model->findAsIterator()
 * Using a find iterator is significantly faster than using model->find() which returns
 * a new result model.
 * 
 * @version  V0.81
 * @author Tobias Gauﬂ <tobias.gauss@web.de>
 *
 * @package RDF
 * @access  public
 *
 */
class RDF_FindIterator extends RDF_Object
{
     /**
    * Reference to the Model_Memory
    * @var     object Model_Memory
    * @access  private
    */
    var $model;

     /**
    * Current position
    * RDF_FindIterator does not use the build in PHP array iterator,
    * so you can use serveral iterators on a single Model_Memory.
    * @var     integer
    * @access  private
    */
    var $position;

    /**
    * Searchpattern
    * 
    * @var     Object Subject, Predicate, Object
    * @access  private
    */
    var $subject;
    var $predicate;
    var $object;

   /**
    * Constructor
    *
    * @param    object  Model_Memory
    * @param    object  Subject
    * @param    object  Predicate
    * @param    object  Object
    * @access   public
    */
    function RDF_FindIterator(&$model, &$sub, &$pred, &$obj)
    {
        $this->model = &$model;
        $this->subject =& $sub;
        $this->predicate =& $pred;
        $this->object =& $obj;
        $this->position = -1;
    }

     /**
    * Returns true if there are more matching statements.
    * @return   boolean
    * @access   public
    */
    function hasNext()
    {
        if($this->model->findFirstMatchOff($this->subject, $this->predicate, $this->object, ($this->position+1)) > -1) {
            return true;
        } else {
            return false;
        }
    }

    /**
    * Returns the next matching statement.
    * @return  tatement or NULL if there is no next matching statement.
    * @access  public  
    */
    function next()
    {
        $res = $this->model->findFirstMatchOff($this->subject, $this->predicate, $this->object, ($this->position+1));
        if ($res > -1) {
            $this->position=$res;
            return $this->model->triples[$res];
        } else {
            return null;
        }
    }

    /**
    * Returns the current matching statement.
    * @return  statement or NULL if there is no current matching statement.
    * @access  public
    */
    function current()
    {
        if ($this->position >= -1 && isset($this->model->triples[$this->position])) {
            return $this->model->triples[$this->position];
        } else {
            return null;
        }
    }
}

?>