<?php

// ----------------------------------------------------------------------------------
// Class: OntProperty
// ----------------------------------------------------------------------------------


/**
* Class encapsulating a property in an ontology.
*
* @version  $Id: OntProperty.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	ontModel
* @access	public
**/

class OntProperty extends OntResource    
{
	/**
    * Constructor.
	* You can supply a URI.
    *
    * @param string $uri 
	* @access	public
    */
	function OntProperty($uri = null)
	{
		parent::OntResource($uri);
	}
	
	/**
	* Add a resource representing the domain of this property.
	*
   	* @param	object ResResource	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function addDomain($resResource)
	{
		return $this->addProperty($this->vocabulary->DOMAIN(),$resResource);
	}
	
	/**
	* Add a resource representing the range of this property.
	*
   	* @param	object ResResource	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function addRange($resResource)
	{
		return $this->addProperty($this->vocabulary->RANGE(),$resResource);
	}
	
	/**
	* Add a sub-property of this property.
	*
   	* @param	object ResProperty	$resProperty
   	* @return	boolean 
   	* @access	public
   	*/	
	function addSubProperty($resProperty)
	{
		return $resProperty->addProperty($this->vocabulary->SUB_PROPERTY_OF(),$this);
	}
	
	/**
	* Add a super-property of this property.
	*
   	* @param	object ResProperty	$resProperty
   	* @return	boolean 
   	* @access	public
   	*/
	function addSuperProperty($resProperty)
	{
		return $this->addProperty($this->vocabulary->SUB_PROPERTY_OF(),$resProperty);
	}
	
	/**
	* Answer a OntClass that represents the domain class of this property. 
	* If there is more than one such resource, an arbitrary selection is made.
	*
   	* @return	object OntClass 
   	* @access	public
   	*/
	function getDomain()
	{
		return $this->getPropertyValue($this->vocabulary->DOMAIN(),'OntClass');
	}

	/**
	* Answer a OntClass that represents the range class of this property. 
	* If there is more than one such resource, an arbitrary selection is made.
	*
   	* @return	object OntClass 
   	* @access	public
   	*/
	function getRange()
	{
		return $this->getPropertyValue($this->vocabulary->RANGE(),'OntClass');
	}
	
	/**
	* Answer a property that is the sub-property of this property. 
	* If there is more than one such property, an arbitrary selection is made.
	*
   	* @return	object OntProperty 
   	* @access	public
   	*/
	function getSubProperty()
	{
		$statement = $this->model->findFirstMatchingStatement(null,$this->vocabulary->SUB_PROPERTY_OF(),$this);
		if ($statement !== null)
			return new OntProperty($statement->getLabelSubject());
		
		return null;
	}
	
	/**
	* Answer a property that is the super-property of this property. 
	* If there is more than one such property, an arbitrary selection is made.
	*
   	* @return	object OntProperty 
   	* @access	public
   	*/
	function getSuperProperty()
	{
		return $this->getPropertyValue($this->vocabulary->SUB_PROPERTY_OF(),'OntProperty');
	}
	
	/**
	* Answer true if the given resource a class specifying the domain of this property.
	*
   	* @param	object ResResource	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function hasDomain($resResource)
	{
		return $this->hasProperty($this->vocabulary->DOMAIN(),$resResource);
	}
	
	/**
	* Answer true if the given resource a class specifying the range of this property.
	*
   	* @param	object ResResource	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function hasRange($resResource)
	{
		return $this->hasProperty($this->vocabulary->RANGE(),$resResource);
	}
	
	/**
	* Answer true if the given property is a sub-property of this property.
	* If $direct is set to true, only consider the direcly adjacent 
	* properties in the property hierarchy
	*
   	* @param	object ResResource	$resProperty
   	* @param	boolean	$direct
   	* @return	boolean 
   	* @access	public
   	*/
	function hasSubProperty($resProperty, $direct = true)
	{
		if ($direct)
			return $resProperty->hasProperty($this->vocabulary->SUB_PROPERTY_OF(),$this);
		
		$index=array();
		return ($this->_getSubAttributeStatementsRec($this,$this->vocabulary->SUB_PROPERTY_OF(),$index,$resProperty) === true);
	}
	
	/**
	* Answer true if the given property is a super-property of this property.
	* If $direct is set to true, only consider the direcly adjacent 
	* properties in the property hierarchy
	*
   	* @param	object ResResource	$resProperty
   	* @param	boolean	$direct
   	* @return	boolean 
   	* @access	public
   	*/
	function hasSuperProperty($resProperty, $direct = true)
	{
		if ($direct)
			return $this->hasProperty($this->vocabulary->SUB_PROPERTY_OF(),$resProperty);
		
		$index=array();
		return ($this->_getSuperAttributeStatementsRec($this,$this->vocabulary->SUB_PROPERTY_OF(),$index,$resProperty) === true);
	}
	
	/**
	* Answer an array of all of the declared domain classes of this property. 
	* Each element of the iterator will be an OntClass.
	*
   	* @return	array of OntClasses
   	* @access	public
   	*/
	function listDomain()
	{
		return $this->listProperty($this->vocabulary->DOMAIN(),'OntClass');
	}
	
	/**
	* Answer an array of all of the declared range classes of this property. 
	* Each element of the iterator will be an OntClass.
	*
   	* @return	array of OntClasses
   	* @access	public
   	*/
	function listRange()
	{
		return $this->listProperty($this->vocabulary->RANGE(),'OntClass');
	}
	
	/**
	* Answer an array of all the properties that are declared to be 
	* sub-properties of this property. Each element of the iterator will be an 
	* OntProperty.
	* If $direct is set to true, only consider the direcly adjacent 
	* properties in the property hierarchy
	*
   	* @param	boolean	$direct
   	* @return	array of OntProperties 
   	* @access	public
   	*/
	function listSubProperties($direct = true)
	{
		$return = array();
		if ($direct)
		{
			$statements = $this->model->find(null,$this->vocabulary->SUB_PROPERTY_OF(),$this);
		} else 
		{
			$index = array();
			$statements = $this->_getSubAttributeStatementsRec($this,$this->vocabulary->SUB_PROPERTY_OF(),$index);
		}
		
		$returnIndex=array();
		foreach ($statements as $statement) 
		{
			$objectLabel=$statement->getLabelSubject();
			if (!in_array($objectLabel,$returnIndex))
			{
				$returnIndex[]=$objectLabel;
				$return[]=new OntProperty($statement->getLabelSubject());
			}	
		}	
		return $return;	
	}
	
	/**
	* Answer an array of all the properties that are declared to be 
	* super-properties of this property. Each element of the iterator will be an 
	* OntProperty.
	* If $direct is set to true, only consider the direcly adjacent 
	* properties in the property hierarchy
	*
   	* @param	boolean	$direct
   	* @return	array of OntProperties 
   	* @access	public
   	*/
	function listSuperProperties($direct = true)
	{
		$return = array();
		if ($direct)
			return $this->listProperty($this->vocabulary->SUB_PROPERTY_OF(),'OntProperty');
			
		$index=array();	
		$statements = $this->_getSuperAttributeStatementsRec($this,$this->vocabulary->SUB_PROPERTY_OF(),$index);
		$returnIndex=array();
		foreach ($statements as $statement) 
		{
			$objectLabel=$statement->getLabelObject();
			if (!in_array($objectLabel,$returnIndex))
			{
				$returnIndex[]=$objectLabel;
				$return[]=new OntProperty($statement->getLabelObject());
			}	
		}	
		return $return;	
	}
	
	/**
	* Remove the given class from the stated domain(s) of this property.
	*
   	* @param	object ResResource $resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function removeDomain($resResource)
	{
		return $this->removeProperty($this->vocabulary->DOMAIN(),$resResource);
	}
	
	/**
	* Remove the given class from the stated range(es) of this property.
	*
   	* @param	object ResResource $resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function removeRange($resResource)
	{
		return $this->removeProperty($this->vocabulary->RANGE(),$resResource);
	}
	
	/**
	* Remove the given property from the sub-properties of this property.
	*
   	* @param	object ResProperty $resProperty
   	* @return	boolean 
   	* @access	public
   	*/
	function removeSubProperty($resProperty)
	{
		return	$this->model->remove(new Statement($resProperty,$this->vocabulary->SUB_PROPERTY_OF(),$this));
	}
	
	/**
	* Remove the given property from the super-properties of this property.
	*
   	* @param	object ResProperty $resProperty
   	* @return	boolean 
   	* @access	public
   	*/
	function removeSuperProperty($resProperty)
	{
		return $this->removeProperty($this->vocabulary->SUB_PROPERTY_OF(),$resProperty);
	}

	/**
	* Assert that the given resource represents the class of individuals 
	* that form the domain of this property. Any existing domain statements 
	* for this property are removed.
	*
   	* @param	object ResResource $resResource
   	* @access	public
   	*/
	function setDomain($resResource)
	{
		$this->setPropertyValue($this->vocabulary->DOMAIN(),$resResource);
	}
	
	/**
	* Assert that the given resource represents the class of individuals 
	* that form the range of this property. Any existing range statements 
	* for this property are removed.
	*
   	* @param	object ResResource $resResource
   	* @access	public
   	*/
	function setRange($resResource)
	{
		$this->setPropertyValue($this->vocabulary->RANGE(),$resResource);
	}
	
	/**
	* Assert that this property is super-property of the given property. 
	* Any existing statements for superPropertyOf on prop will be removed.
	*
   	* @param	object ResProperty $resProperty
   	* @access	public
   	*/
	function setSubProperty($resProperty)
	{
		foreach ($this->listSubProperties() as $oldResProperty) 
		{
			$this->removeSubProperty($oldResProperty);
		}
		
		$this->addSubProperty($resProperty);
	}
	
	/**
	* Assert that this property is sub-property of the given property. 
	* Any existing statements for subPropertyOf on prop will be removed.
	*
   	* @param	object ResProperty $resProperty
   	* @access	public
   	*/
	function setSuperProperty($resProperty)
	{
		$this->setPropertyValue($this->vocabulary->SUB_PROPERTY_OF(),$resProperty);
	}
} 
?>