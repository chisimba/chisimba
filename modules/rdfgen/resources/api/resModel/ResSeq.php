<?php
// ----------------------------------------------------------------------------------
// Class: ResSeq
// ----------------------------------------------------------------------------------

/**
* This interface defines methods for accessing RDF Sequence resources. 
* These methods operate on the RDF statements contained in a model.
*
* @version  $Id: ResSeq.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
* @package 	resModel
* @access	public
**/
class ResSeq extends ResContainer 
{
	
	/**
    * Constructor
	* You can supply a URI
    *
    * @param string $uri 
	* @access	public
    */		
	function ResSeq($uri = null)
	{
		parent::ResContainer($uri);
		$this->containerType=new ResResource(RDF_NAMESPACE_URI.RDF_SEQ);
	}
	
	/**
	* Insert a new member into the sequence at the specified position.
	* The existing member at that position, and all others with higher indexes, 
	* have their index increased by one.
	*
	* @param 	integer	$index
   	* @param	object ResResource/ResLiteral	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function addAtIndex($index, $object)
	{
		//get a members index
		$memberIndex= $this->getMembers();
				
		//type this container, if it isn't already typed
		if(!$this->hasProperty(new ResResource(RDF_NAMESPACE_URI.RDF_TYPE)))
			$this->addProperty(new ResResource(RDF_NAMESPACE_URI.RDF_TYPE),$this->containerType);
		
		//renumber all higher members
		for ($i = count($memberIndex);$i >= $index ; $i--)
		{
			$this->removeAll($this->_getMembershipPropertyWithIndex($i));
			$this->addProperty($this->_getMembershipPropertyWithIndex($i+1),$memberIndex[$i]);
		}	
		//remove the old value at this position
		$this->removeAll($this->_getMembershipPropertyWithIndex($index));
		//add the new value
		$this->addProperty($this->_getMembershipPropertyWithIndex($index),$object);
				
		return $this;
	}
	
	/**
	* Get the member at a given index
	*
	* @param 	integer	$index
   	* @return	object ResResource/ResLiteral 
   	* @access	public
   	*/
	function getMember($index)
	{
		$result=$this->listProperties($this->_getMembershipPropertyWithIndex($index));
		if (isset($result[0]))
		{
			return $result[0];
		} 
		else 
		{
			return null;
		}
	}
	
	/**
	* Return the index of a given member of the sequence.
	* If the same value appears more than once in the sequence, it is undefined 
	* which of the indexes will be returned.
	* If the member is not found in this sequence, a value of 0 is returned.
	*
	* @param 	object ResResource/ResLiteral $object
   	* @return	integer
   	* @access	public
   	*/
	function indexOf($object)
	{
		//check all members, until $object is found
		foreach ($this->listProperties() as $statement)
		{
			$predicateLabel=$statement->getLabelPredicate();
			if ($this->_predicateLabelMatchesMembershipProperty($predicateLabel))
			{
				if($object->equals($statement->getObject()))
					//analyze the container membership property and return the index
					return $this->_getMemberIndexNrFromMembershipPropertyLabel($predicateLabel);
			}	
		}
		//return 0 if $object wasn't found
		return 0;
	}
	
	/**
	* Remove the member at the specified index.
	* All other members with a higher index will have their index reduced by one.
	*
	* @param 	integer	$index	 
   	* @access	public
   	*/
	function removeAtIndex($index)
	{
		$memberIndex= $this->getMembers();


		$this->removeAll($this->_getMembershipPropertyWithIndex($index));

		for ($i = $index;$i < count($memberIndex); $i++)
		{
			$this->removeAll($this->_getMembershipPropertyWithIndex($i+1));
			$this->addProperty($this->_getMembershipPropertyWithIndex($i),$memberIndex[$i+1]);
		}		
		return $this;
	}
	
	/**
	* Set the value at a given index in the sequence.
	*
	* If the index is not in the range of the sequence, false is returned
	*
	* @param 	integer	$index
   	* @return	boolean
   	* @access	public
   	*/
	function set($index, $object)
	{
		if (!$this->hasProperty($this->_getMembershipPropertyWithIndex($index)))
			return false;
			
		$this->removeAll($this->_getMembershipPropertyWithIndex($index));
		$this->addProperty($this->_getMembershipPropertyWithIndex($index),$object);
		return true;
	}
}
?>