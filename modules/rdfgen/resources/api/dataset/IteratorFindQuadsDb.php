<?php 
//----------------------------------------------------------------------------------
// Class: IteratorFindQuadsDb
// ----------------------------------------------------------------------------------


/**
* Implementation of a quad iterator.
*
* This Iterator should be used like:
* for($iterator = $dataset->findInNamedGraphs(null,null,null,null); $iterator->valid(); $iterator->next()) 
* {
*	$currentQuad=$iterator->current();
* };
*
*
* @version  $Id$
* @author Daniel Westphal (http://www.d-westphal.de)
*
*
* @package 	dataset
* @access	public
**/
class IteratorFindQuadsDb
{
	/**
	* Holds a reference to the associated DB resultSet.
	*
	* @var		$dbResultSets ADODB result
	* @access	private
	*/
	var $dbResultSet;
	
	/**
	* Holds a reference to the associated datasetDb.
	*
	* @var		$datasetDb datasetDb
	* @access	private
	*/
	var $datasetDb;

	/**
	* boolean value, if the results should be returned as triples.
	*
	* @var		boolean
	* @access	private
	*/
	var $returnAsTriples;
	
	/**
    * Constructor.
    *
    * @param dataset
	* @access	public
    */
	function IteratorFindQuadsDb(&$dbResultSet,&$datasetDb,$returnAsTriples=false)
	{
		$this->dbResultSet=& $dbResultSet;
		$this->datasetDb=& $datasetDb;
		$this->returnAsTriples=$returnAsTriples;
	}
	
	/**
    * Resets iterator list to start.
    *
	* @access public
    */
	function rewind()
	{
		//not supported
	}
	
	/**
    * Says if there are additional items left in the list.
    *
    * @return	boolean
	* @access	public
    */
	function valid()
	{
		if (($this->dbResultSet ===false) OR ($this->dbResultSet->EOF) )
			return false;
		
		return true;
	}
	
	/**
    * Moves Iterator to the next item in the list.
    *
	* @access	public
    */
	function next()
	{
		if ($this->dbResultSet!==false)
			$this->dbResultSet->moveNext();
	}
	
	/**
    * Returns the current item.
    *
    * @return	mixed
	* @access	public
    */
	function &current()
	{
		if ($this->dbResultSet===false)
			return null;
		// subject
		if ($this->dbResultSet->fields[5] == 'r')
		$sub = new Resource($this->dbResultSet->fields[0]);
		else
		$sub = new BlankNode($this->dbResultSet->fields[0]);

		// predicate
		$pred = new Resource($this->dbResultSet->fields[1]);

		// object
		if ($this->dbResultSet->fields[6] == 'r')
		$obj = new Resource($this->dbResultSet->fields[2]);
		elseif ($this->dbResultSet->fields[6] == 'b')
		$obj = new BlankNode($this->dbResultSet->fields[2]);
		else {
			$obj = new Literal($this->dbResultSet->fields[2], $this->dbResultSet->fields[3]);
			if ($this->dbResultSet->fields[4])
			$obj->setDatatype($this->dbResultSet->fields[4]);
		}

		if($this->returnAsTriples)
			return (new Statement($sub, $pred, $obj));

		return (new Quad(new Resource($this->dbResultSet->fields[7]),$sub,$pred,$obj));
	}
	
	/**
    * Returns the key of the current item.
    *
    * @return	integer
	* @access	public
    */
	function key()
	{
		return $this->dbResultSet->_currentRow;
	}
}
?>