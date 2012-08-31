<?php
// ----------------------------------------------------------------------------------
// Class: RDF_StatementIterator
// ----------------------------------------------------------------------------------
/**
 * Iterator for traversing models.
 * This class can be used for iterating forward and backward trough Model_Memorys.
 * It should be instanced using the getIterator() method of a Model_Memory.
 *
 * @version V0.7
 * @author Chris Bizer <chris@bizer.de>
 * @package util
 * @access public
 */
class RDF_StatementIterator extends RDF_Object
{
    /**
     * Reference to the Model_Memory
     *
     * @var object Model_Memory
     * @access private
     */
    var $model;

    /**
     * Current position
     * StatementIterator does not use the build in PHP array iterator,
     * so you can use serveral iterators on a single Model_Memory.
     *
     * @var integer
     * @access private
     */
    var $position;

    /**
     * @param object Model_Memory
     * @access public
     */
    function RDF_StatementIterator(&$model)
    {
        $this->model = $model;
        $this->position = -1;
    }

    /**
     * Returns TRUE if there are more statements.
     *
     * @return boolean
     * @access public
     */
    function hasNext()
    {
        if ($this->position < count($this->model->triples) - 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns TRUE if the first statement has not been reached.
     *
     * @return boolean
     * @access public
     */
    function hasPrevious()
    {
        if ($this->position > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns the next statement.
     *
     * @return statement or null if there is no next statement.
     * @access public
     */
    function next()
    {
        if ($this->position < count($this->model->triples) - 1) {
            $this->position++;
            return $this->model->triples[$this->position];
        } else {
            return null;
        }
    }

    /**
     * Returns the previous statement.
     *
     * @return statement or null if there is no previous statement.
     * @access public
     */
    function previous()
    {
        if ($this->position > 0) {
            $this->position--;
            return $this->model->triples[$this->position];
        } else {
            return null;
        }
    }

    /**
     * Returns the current statement.
     *
     * @return statement or null if there is no current statement.
     * @access public
     */
    function current()
    {
        if (($this->position >= 0) && ($this->position < count($this->model->triples))) {
            return $this->model->triples[$this->position];
        } else {
            return null;
        }
    }

    /**
     * Moves the pointer to the first statement.
     *
     * @return void
     * @access public
     */
    function moveFirst()
    {
        $this->position = 0;
    }

    /**
     * Moves the pointer to the last statement.
     *
     * @return void
     * @access public
     */
    function moveLast()
    {
        $this->position = count($this->model->triples) - 1;
    }

    /**
     * Moves the pointer to a specific statement.
     * If you set an off-bounds value, next(), previous() and current() will return null
     *
     * @return void
     * @access public
     */
    function moveTo($position)
    {
        $this->position = $position;
    }

    /**
     * Returns the current position of the iterator.
     *
     * @return integer
     * @access public
     */
    function getCurrentPosition()
    {
        return $this->position;
    }
}

?>