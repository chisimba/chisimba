<?php 
//----------------------------------------------------------------------------------
// Class: IteratorAllGraphsDb
//----------------------------------------------------------------------------------


/**
* Implementation of a Graph iterator.
*
* This Iterator should be used in a for-loop like:
* for($iterator = $dataset->listGraphs(); $iterator->valid(); $iterator->next()) 
* {
*	$currentResource=$iterator->current();
* };
*
* @version  $Id$
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	dataset
* @access	public
**/
class IteratorAllGraphsDb
{
	/**
	* Holds a reference to the associated DB resultSet
	* @var		$dbResultSets ADODB result
	* @access	private
	*/
	var $dbResultSet;
	
	/**
	* Holds a reference to the associated datasetDb
	* @var		datasetDb
	* @access	private
	*/
	var $datasetDb;
	
	
	/**
	* The current position
	* @var		integer
	* @access	private
	*/
	var $key;
	
	
	/**
	* The current NamedGraph
	* @var obejct NamedGraph
	* @access	private
	*/
	var $current;
	
	
	
	/**
    * Constructor.
    *
    *
    * @param ADODBResultSet
    * @param DatasetDb
	* @access	public
    */
	function IteratorAllGraphsDb(&$dbResultSet,&$datasetDb)
	{
		$this->dbResultSet=& $dbResultSet;
		$this->datasetDb=& $datasetDb;
		$this->current = $this->dbResultSet->fields[0];
	}
	
	/**
    * Resets iterator list to start
    *
	* @access public
    */
	function rewind()
	{
		//not supported
	}
	
	/**
    * Says if there are additional items left in the list
    *
    * @return	boolean
	* @access	public
    */
	function valid()
	{
		return (!$this->dbResultSet->EOF);
	}
	
	/**
    * Moves Iterator to the next item in the list
    *
	* @access	public
    */
	function next()
	{
		$this->dbResultSet->moveNext();
		$this->current = $this->dbResultSet->fields[0];
	}
	
	/**
    * Returns the current item
    *
    * @return	mixed
	* @access	public
    */
	function &current()
	{
		return ($this->datasetDb->getNamedGraph($this->current));
	}
	
	
	/**
    * Returns the key of the current item
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