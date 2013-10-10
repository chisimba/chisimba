<?php 
// ----------------------------------------------------------------------------------
// Class: IteratorAllGraphsMem
// ----------------------------------------------------------------------------------


/**
* Implementation of a Graph iterator.
*
* This Iterator should be used in a for-loop like:
* for($iterator = $dataset->listGraphs(); $iterator->valid(); $iterator->next()) 
* {
*	$currentResource=$it->current();
* };
*
* @version  $Id$
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	dataset
* @access	public
**/
class IteratorAllGraphsMem
{
	/**
	* Holds a reference to the associated RDF dataset
	*
	* @var		object dataset
	* @access	private
	*/
	var $associatedGraphSet;
	
	/**
	* The current position
	*
	* @var		integer
	* @access	private
	*/
	var $key;
	
	/**
	* If the current resource is valid
	*
	* @var		boolean
	* @access	private
	*/
	var $valid;
	
	/**
	* The current NamedGraph
	*
	* @var obejct NamedGraph
	* @access	private
	*/
	var $current;
	
	
	/**
    * Constructor.
    *
    * @param dataset
	* @access	public
    */
	function IteratorAllGraphsMem(&$namedGraphSet)
	{
		$this->associatedGraphSet=&$namedGraphSet;
		$this->rewind();
	}
	
	/**
    * Resets iterator list to start
    *
	* @access public
    */
	function rewind()
	{
		$this->key = -1;
		$this->next();
	}
	
	/**
    * Says if there are additional items left in the list.
    *
    * @return	boolean
	* @access	public
    */
	function valid()
	{
		return $this->valid;
	}
	
	/**
    * Moves Iterator to the next item in the list.
    *
	* @access	public
    */
	function next()
	{
		$this->current = &$this->associatedGraphSet->getGraphWithOffset(++$this->key);
		$this->valid=($this->current!=NULL);
	}
	
	/**
    * Returns the current item.
    *
    * @return	mixed
	* @access	public
    */
	function &current()
	{
		return $this->current;
	}
	
	/**
    * Returns the key of the current item.
    *
    * @return	integer
	* @access	public
    */
	function key()
	{
		return $this->key;
	}
}
?>