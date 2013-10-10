<?php
// ----------------------------------------------------------------------------------
// Class: Quad
// ----------------------------------------------------------------------------------

/**
*
* A Triple in a RDF dataset, consisting of four Jena Nodes: graphName,
* subject, predicate, and object.
*
* @version  $Id$
* @author Daniel Westphal (http://www.d-westphal.de)
*
* @package 	dataset
* @access	public
**/
class Quad  
{
	
	/**
	* Name of the NamedGraphMem.
	*
	* @var		Resource
	* @access	private
	*/
	var $graphName;
	
	/**
	* Statement
	*
	* @var		Statement
	* @access	private
	*/
	var $statement;
	
	/**
    * Constructor
	* Creates a Quad from four Nodes.
 	*
    * @param Resource 
    * @param Resource  
    * @param Resource  
    * @param Resource   
	* @access	public
    */		
	function Quad($graphName,$subject,$predicate,$object)
	{
		if (!is_a($graphName, 'Resource')) 
		{
			$errmsg = RDFAPI_ERROR . 
		          '(class: Quad; method: new): Resource expected as graphName.';
			trigger_error($errmsg, E_USER_ERROR); 
		}
		$this->statement=new Statement($subject,$predicate,$object);
		$this->graphName=$graphName;
	}
	
	/**
    * Sets the graph name.
    *
    * @param Resource  
	* @access	public
    */	
	function setGraphName($graphName)
	{
		$this->graphName=$graphName;
	}
	
	/**
    * Returns the graph name.
    *
    * @return Resource
	* @access	public
    */
	function getGraphName()
	{
		return $this->graphName;
	}
	
	/**
	 * Return a human-readable (sort of) string "graphname { s p o . }"
	 * describing the quad.
	 *
	 * @return string
	 */
	function toString() 
	{	
		return 'GraphName('.$this->graphName->getLabel().') '.$this->statement->toString();
	}
		
	/**
    * Returns the subject.
    *
    * @return Resource
	* @access	public
    */
	function getSubject()
	{
		return $this->statement->getSubject();	
	}
		
	/**
    * Returns the predicate.
    *
    * @return Resource
	* @access	public
    */
	function getPredicate()
	{
		return $this->statement->getPredicate();	
	}
		
	/**
    * Returns the object.
    *
    * @return Resource
	* @access	public
    */
	function getObject()
	{
		return $this->statement->getObject();	
	}
		
	/**
    * Returns the statement(subject,predicate,object).
    *
    * @return statement
	* @access	public
    */
	function getStatement()
	{
		return $this->statement;	
	}
		
	/**
    * Checks if two quads are equal.
    *
    * @param  Quad
    * @return boolean
	* @access	public
    */
	function equals($quad)
	{
		return ($this->graphName->equals($quad->getGraphName()) && $this->statement->equals($quad->getStatement()));	
	}
}
?>