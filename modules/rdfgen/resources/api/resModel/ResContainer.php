<?php
// ----------------------------------------------------------------------------------
// Class: ResContainer
// ----------------------------------------------------------------------------------

/**
* An RDF Container.
* This Class defines methods for accessing RDF container resources. 
* These methods operate on the RDF statements contained in a model. 
*
* @version  $Id: ResContainer.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
* @package 	resModel
* @access	public
**/

class ResContainer extends ResResource 
{
	/**
	* Holds a ResResource of this container type rdf:Seq, rdf:Alt, or rdf:Bag
	* @var		ResResource
	* @access	private
	*/
	var $containerType;
	
	
	/**
    * Constructor
	* You can supply a URI
    *
    * @param string $uri 
	* @access	public
    */	
	function ResContainer($uri = null)
	{
		parent::ResResource($uri);
	}	
	
	/**
	* Add a new value to a container.
	* The new value is added as the last element of the container.
	*
   	* @param	object ResResource/ResLiteral	$object
   	* @access	public
   	*/
	function add($object)
	{
		//type this container, if it isn't already typed
		if(!$this->hasProperty(new ResResource(RDF_NAMESPACE_URI.RDF_TYPE)))
			$this->addProperty(new ResResource(RDF_NAMESPACE_URI.RDF_TYPE),$this->containerType);
		//get the current size
		$actualSize=$this->size();
		//add the object to the last position
		$this->addProperty(new ResResource(RDF_NAMESPACE_URI.'_'.($actualSize+1)),$object);
	}
	
	/**
	* Determine whether the container contains a value
	*
   	* @param	obejct ResResource/ResLiteral	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function contains($resResource)
	{
		//get all container's properties 
		foreach ($this->listProperties() as $statement)
		{
			//if the property matches a container membership property
			if ($this->_predicateLabelMatchesMembershipProperty($statement->getLabelPredicate()))
			{
				//check, if it's the value, we're looking for. 
				if ($resResource->equals($statement->getObject()))
					return true;
			}	
		}
		return false;
	}
	
	/**
	* Returns true, if this resource is a container from type rdf:Alt
	*
   	* @return	boolean 
   	* @access	public
   	*/	
	function isAlt()
	{
		return ($this->containerType->getURI()==RDF_NAMESPACE_URI.RDF_ALT);
	}
	
	/**
	* Returns true, if this resource is a container from type rdf:Bag
	*
   	* @return	boolean 
   	* @access	public
   	*/	
	function isBag()
	{
		return ($this->containerType->getURI()==RDF_NAMESPACE_URI.RDF_BAG);
	}

	/**
	* Returns true, if this resource is a container from type rdf:Seq
	*
   	* @return	boolean 
   	* @access	public
   	*/	
	function isSeq()
	{
		return ($this->containerType->getURI()==RDF_NAMESPACE_URI.RDF_SEQ);
	}
	
	/**
	* Get an array of all resources that are values of this container
	*
   	* @return	array 
   	* @access	public
   	*/	
	function getMembers()
	{
		$return=array();
		foreach ($this->listProperties() as $statement)
		{
			$predicateLabel=$statement->getLabelPredicate();
			if ($this->_predicateLabelMatchesMembershipProperty($predicateLabel))
			{
				$return[$this->_getMemberIndexNrFromMembershipPropertyLabel($predicateLabel)] = $statement->getObject();
			}	
		}
		return $return;
	}
	
	/**
	* Remove a value from the container.
	* 
	* Once removed, the values in the container with a higher ordinal value are renumbered. 
	* The renumbering algorithm depends on the type of container.
	*
   	* @param	obejct ResResource/ResLiteral	$resResource
   	* @access	public
   	*/
	function remove($object)
	{
		$deleteFromIndex=array();
		//get all container members
		$memberIndex=$this->getMembers();
		
		//check each container member if it equals the resoure to be removed
		foreach ($memberIndex as $key => $value)
		{
			//save the statements positio in the container
			if($object->equals($value))
					$deleteFromIndex[]=$key;	
		}

		//delete all found container members
		foreach ($deleteFromIndex as $index)
		{
			$this->removeAll($this->_getMembershipPropertyWithIndex($index));

			//renumber all members with higher ordinal numbers than the deleted one
			for ($i = $index;$i < count($memberIndex); $i++)
			{
				$this->removeAll($this->_getMembershipPropertyWithIndex($i+1));
				$this->addProperty($this->_getMembershipPropertyWithIndex($i),$memberIndex[$i+1]);
			}		
		}
		
	}
	
	/**
	* Returns the number values in the container.
	*
   	* @return	integer 
   	* @access	public
   	*/	
	function size()
	{
		return count($this->getMembers());
	}
	
	/**
	* Checks, if a predicate label fits a container membership property rdf:_n
	*
   	* @param	string	$predicateLabel
   	* @return	boolean 
   	* @access	private
   	*/
	function _predicateLabelMatchesMembershipProperty($predicateLabel)
	{
		return substr($predicateLabel,0,strlen(RDF_NAMESPACE_URI.'_')) == RDF_NAMESPACE_URI.'_';
	}
	
	/**
	* Get the ordinal number from a membership property rdf:_n
	*
   	* @param	string	$predicateLabel
   	* @return	integer 
   	* @access	private
   	*/
	function _getMemberIndexNrFromMembershipPropertyLabel($predicateLabel)
	{
		return (int)substr($predicateLabel,strlen(RDF_NAMESPACE_URI.'_'));
	}
	
	/**
	* Get a membership property rdf:_n with index $int 
	*
   	* @param	intger	$int
   	* @return	string 
   	* @access	private
   	*/
	function _getMembershipPropertyWithIndex($int)
	{
		return new ResResource(RDF_NAMESPACE_URI.'_'.$int);	
	}
}
?>
