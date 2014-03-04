<?php 

// ----------------------------------------------------------------------------------
// Class: IterFind
// ----------------------------------------------------------------------------------


/**
* Implementation of a find-iterator which delivers statements or quads.
*
* This Iterator should be used in a for-loop like:
* for($iterator = $memmodel->iterFind(null,null,null,null); $iterator->valid(); $iterator->next()) 
* {
*	$statement=$iterator->current();
* };
*
* @version  $Id$
* @author Daniel Westphal (http://www.d-westphal.de)
*
*
* @package 	utility
* @access	public
**/
class IterFind
{
	
	/**
	* The current position
	* @var		integer
	* @access	private
	*/
	var $key;
	
	/**
	* boolean value, if the results should be returned as Quads
	* @var		boolean
	* @access	private
	*/
	var $returnAsQuads;
	
	/**
	* If the current resource is valid
	* @var		boolean
	* @access	private
	*/
	var $valid;
	
	/**
	* The current NamedGraph
	* @var obejct NamedGraph
	* @access	private
	*/
	var $current;
	
	/**
	* The graph to look in.
	* @var string 
	* @access	private
	*/	
	var $findGraph;
	
	/**
	* The subject Resource to search for
	* @var string 
	* @access	private
	*/	
	var $findSubject;
	
	/**
	* The predicate Resource to search for
	* @var string 
	* @access	private
	*/	
	var $findPredicate;
	
	/**
	* The object Resource to search for
	* @var string 
	* @access	private
	*/	
	var $findObject;
	
	
	
	/**
    * Constructor.
    *
	* $subject, $predicate, and $object are used like find().
	* $graph has to be a reference to the graph to search in.
	*
	* 
    *
    * @param $graph Resource
    * @param $subject Resource
    * @param $predicate Resource
    * @param $object Resource
    * @param $returnAsQuads boolean
	* @access	public
    */
	function IterFind($graph,$subject,$predicate,$object,$returnAsQuads=false)
	{
		if ($graph==NULL)
		{
			$this->valid=false;
			return;	
		}		
		$this->findGraph=&$graph;
		$this->findSubject=$subject;
		$this->findPredicate=$predicate;
		$this->findObject=$object;
		$this->rewind();
		$this->returnAsQuads=$returnAsQuads;
	}
	
	/**
    * Resets iterator list to start
    *
	* @access	public
    */
	function rewind()
	{
		$this->key = -1;
		$this->next();
	}
	
	/**
    * Says if there are additional items left in the list
    *
    * @return	boolean
	* @access	public
    */
	function valid()
	{
		return $this->valid;
	}
	
	/**
    * Moves Iterator to the next item in the list
    *
	* @access	public
    */
	function next()
	{
		$this->current = $this->findGraph->findFirstMatchingStatement($this->findSubject,$this->findPredicate,$this->findObject,++$this->key);
		$this->valid=($this->current!=NULL);
	}
	
	/**
    * Returns the current item
    *
    * @return	mixed
	* @access	public
    */
	function current()
	{
		if($this->returnAsQuads)
		return new Quad(new Resource($this->findGraph->getGraphName()),$this->current->getSubject(),$this->current->getPredicate(),$this->current->getObject());
		//else
		return $this->current;
	}
	
	
	/**
    * Returns the key of the current item
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