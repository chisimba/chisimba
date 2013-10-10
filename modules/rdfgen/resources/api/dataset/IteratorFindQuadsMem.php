<?php
require_once RDFAPI_INCLUDE_DIR . 'dataset/Quad.php';
// ----------------------------------------------------------------------------------
// Class: IteratorFindQuadsMem
// ----------------------------------------------------------------------------------



/**
* Implementation of a quad iterator.
*
* This Iterator should be used like:
* for($iterator = $dataset->findInNamedGraphs(null,null,null,null); $iterator->valid(); $iterator->next())
* {
*	$currentQuad=$it->current();
* };
*
* @version  $Id$
* @author Daniel Westphal (http://d-westphal.de)
*
*
* @package 	dataset
* @access	public
**/
class IteratorFindQuadsMem
{
	/**
	* key value in the current graph.
	*
	* @var	 dataset
	* @access	private
	*/
	var $graphKey;

	/**
	* boolean value, if the results should be returned as triples.
	*
	* @var		boolean
	* @access	private
	*/
	var $returnAsTriples;

	/**
	* The current position.
	*
	* @var		integer
	* @access	private
	*/
	var $key;

	/**
	* If the current resource is valid.
	*
	* @var		boolean
	* @access	private
	*/
	var $valid;

	/**
	* The current NamedGraph.
	*
	* @var  NamedGraph
	* @access	private
	*/
	var $current;

	/**
	* The graphName Resource to search for.
	*
	* @var string
	* @access	private
	*/
	var $findGraphName;

	/**
	* The subject Resource to search for.
	*
	* @var string
	* @access	private
	*/
	var $findSubject;

	/**
	* The predicate Resource to search for.
	*
	* @var string
	* @access	private
	*/
	var $findPredicate;

	/**
	* The object Resource to search for.
	*
	* @var string
	* @access	private
	*/
	var $findObject;

	/**
	* Iterator over all graphs of the RDF dataset.
	*
	* @var string
	* @access	private
	*/
	var $graphIterator;


	/**
    * Constructor.
    *
	* $subject, $predicate, and $object are used like find().
	* $getSPO supports the strings 's', 'p', and 'o' to return
	* either the subject, predicate, or object of the result statements.
	*
    *
    * @param Resource
    * @param Resource
    * @param Resource
    * @param dataset
    * @param Boolean
	* @access	public
    */
	function IteratorFindQuadsMem($subject,$predicate,$object,&$graphIterator, $returnAsTriples=false)
	{
		$this->findSubject=$subject;
		$this->findPredicate=$predicate;
		$this->findObject=$object;
		$this->graphIterator=&$graphIterator;
		$this->rewind();
		$this->returnAsTriples=$returnAsTriples;
	}

	/**
    * Resets iterator list to start.
    *
	* @access	public
    */
	function rewind()
	{
		$this->graphIterator->rewind();
		$this->key = -1;
		$this->graphKey=-1;
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
		if($this->graphIterator->valid()===false)
		{
			$this->valid=false;
			return;
		}

		$currentGraph=&$this->graphIterator->current();
		$this->current= $currentGraph->findFirstMatchingStatement($this->findSubject,$this->findPredicate,$this->findObject,++$this->graphKey);
		if($this->current==null)
		{
			do
			{
				$this->graphIterator->next();
				if($this->graphIterator->valid()===false)
				{
					$this->valid=false;
					return;
				}
				$currentGraph=&$this->graphIterator->current();
				$this->graphKey=-1;
				$this->current= $currentGraph->findFirstMatchingStatement($this->findSubject,$this->findPredicate,$this->findObject,++$this->graphKey);

			} while ($this->current==null);
		}
		$this->key++;
		$this->valid=true;
	}

	/**
    * Returns the current item.
    *
    * @return	mixed
	* @access	public
    */
	function current()
	{
		if($this->returnAsTriples) return $this->current;

		$currentGraph=&$this->graphIterator->current();
		return new Quad(new Resource($currentGraph->getGraphName()),$this->current->getSubject(),$this->current->getPredicate(),$this->current->getObject());
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