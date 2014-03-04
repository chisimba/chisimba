<?php
/**
* ----------------------------------------------------------------------------------
* Class: ResResource
* ----------------------------------------------------------------------------------
* @package resModel
**/

/**
* An RDF Resource.
* Resource instances, when created, are associated with a specific model. They support a 
* range of methods, such as getProperty() and addProperty() which will access or modify 
* that model. This enables the programmer to write code in a compact and easy style.
*
* @version  $Id: ResResource.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package resModel
* @access	public
**/
class ResResource extends Resource 
{
	/**
	* Holds a reference to the associated model
	* @var		ResModel
	* @access	private
	*/
	var $model;
	
	/**
	* Is true, if this resource is an anonymous node.
	* @var		boolean
	* @access	private
	*/
	var $isAnon;
	
	
	/**
    * Constructor
	* You can supply a uri
    *
    * @param string $uri 
	* @access	public
    */		
	function ResResource($uri)
	{
			parent::Resource($uri);
			$this->isAnon = ($uri === null);		
	}
	
	/**
    * Sets the reference to the assocoated model.
    *
	* @param	object Model	$model
	* @access public
    */	
	function setAssociatedModel(& $model)
	{
		$this->model=& $model;
		if ($this->isAnon)
			$this->uri=$this->model->getUniqueResourceURI(BNODE_PREFIX);
	}
	
	/**
    * Get the reference to the assocoated model.
    *
	* @return	object Model	$model
	* @access public
    */
	function getAssociatedModel()
	{
		return $this->model;
	}
	
	/**
    * Sets the URI of this resource
    *
	* @param	string $uri
	* @access public
    */	
	function setURI($uri)
	{
		$this->uri = $uri;
	}
	
	/**
	* Add a property to this resource.
	* A statement with this resource as the subject, p as the predicate and o 
	* as the object is added to the model associated with this resource.
	*
   	* @param	ResResource				$property
   	* @param	ResResource/ResLiteral	$object
   	* @return	object ResResource 
   	* @access	public
   	*/
	function addProperty($property,$object)
	{
		$this->model->add(new Statement($this,$property,$object));

		return $this;
	}
	
	/**
	* List all the values with the property p as statements in an array.
	*
   	* @param	ResResource		$property
   	* @return	ResIterator 
   	* @access	public
   	*/
	function listProperties($property = null)
	{
		return $this->model->find($this,$property,null);
	}
	
	/**
	* Answer some statement (this, p, O) in the associated model. 
	* If there are several such statements, any one of them may be returned. 
	* If no such statements exist, null is returned.
	*
   	* @param	ResResource				$property
   	* @return	object ResResource 
   	* @access	public
   	*/	
	function getProperty($property)
	{
		return $this->model->getProperty($this,$property);
	}
	
	/**
	* Determine whether this resource has any values for a given property.
	*
   	* @param	ResResource		$property
   	* @param	ResResource		$value
   	* @return	object ResResource 
   	* @access	public
   	*/	
	function hasProperty($property, $value = null)
	{
		$ret= $this->model->findFirstMatchingStatement($this,$property,$value);
		
		return ($ret!==null);	
	}
	
	/**
	* Determine whether this resource is an anonymous resource
	*
   	* @return	boolean
   	* @access	public
   	*/		
	function getIsAnon()
	{
		return $this->isAnon;	
	}
	
	/**
	* Set whether this resource is an anonymous resource
	*
   	* @param	boolean
   	* @access	public
   	*/	
	function setIsAnon($isAnon)
	{
		 $this->isAnon=$isAnon;	
	}
		
	/**
	* Checks if the resource equals another resource.
	* Two resources are equal, if they have the same URI
	*
	* @access	public 
	* @param	object	resource $that
	* @return	boolean 
	*/  
	function equals ($that) 
	{
		if (is_a($that,'ResLiteral'))
			return $that->equals($this);
			
	    return ($that!==null && ($this->getURI() == $that->getURI()));
	}
	
	/**
	* Delete all the statements with predicate p for this resource from 
	* its associated model.
	*
	* @access	public 
	* @param	object	resource $property
	* @return	object ResResource 
	*/ 
	function removeAll($property = null)
	{	
		foreach ($this->model->find($this,$property,null) as $statement)
		{
			$this->model->remove($statement);	
		}
		return $this;
	}
	
	/**
	* Delete all the properties for this resource from the associated model.
	*
	* @access	public 
	* @return	object ResResource 
	*/ 
	function removeProperties()
	{
		$this->removeAll();
		return $this;
	}
}
?>