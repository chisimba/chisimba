<?php 
// ----------------------------------------------------------------------------------
// Class: OntResource
// ----------------------------------------------------------------------------------


/**
* Provides a common super-type for all of the abstractions in 
* this ontology representation package.
*
* @version  $Id: OntResource.php 268 2006-05-15 05:28:09Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	ontModel
* @access	public
**/
class OntResource extends ResResource 
{
	/**
	* Holds a reference to the assoiated vocabulary
	* @var		object
	* @access	private
	*/
	var $vocabulary;
	
	/**
	* Holds a resResource of the type, which is this ontResource of.
	* If this value is set, the ontModel will add an additional 
    * statement about this resource and the fiven rdf:type
	* @var		object
	* @access	private
	*/
	var $rdfType;
	
	
	/**
    * Constructor
	* You can supply a uri
    *
    * @param string $uri 
	* @access	public
    */
	function OntResource($uri = null)
	{
		$this->rdfType=false;
		parent::ResResource($uri);
	}
	
	/**
	* Sets the reference to the assoiated vocabulary
	*
   	* @param	object OntVocabulary	$vocabulary
   	* @access	public
   	*/
	function setVocabulary(& $vocabulary)
	{
		$this->vocabulary = & $vocabulary;
	}
	
	/**
	* Add the given comment to this resource.
	*
   	* @param	object ResLiteral	$comment
   	* @return	boolean 
   	* @access	public
   	*/
	function addComment($comment)
	{
		return $this->addProperty($this->vocabulary->COMMENT(),$comment);
	}
	
	/**
	* Answer the comment string for this object. If there is more than one such resource, an arbitrary selection is made.
	*
   	* @return	object ResLiteral or NULL 
   	* @access	public
   	*/
	function getComment()
	{
		return $this->getPropertyValue($this->vocabulary->COMMENT());
	}
	
	/**
	* Add a resource that is declared to provide a definition of this resource.
	*
   	* @param	object ResResource	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function addIsDefinedBy($resResource)
	{
		return $this->addProperty($this->vocabulary->IS_DEFINED_BY(),$resResource);
	}
	
	/**
	* Answer a resource that is declared to provide a definition of this resource. 
	* If there is more than one such resource, an arbitrary selection is made.
	*
   	* @return	object ResResource 
   	* @access	public
   	*/
	function getIsDefinedBy()
	{
		return $this->getPropertyValue($this->vocabulary->IS_DEFINED_BY());
	}
	
	/**
	* Add the given Label to this resource
	*
   	* @param	object ResLiteral	$resLiteral
   	* @return	boolean 
   	* @access	public
   	*/
	function addLabelProperty($resLiteral)
	{
		return $this->addProperty($this->vocabulary->LABEL(),$resLiteral);
	}
	
	/**
	* Answer the label ResLiteral for this object. 
	* If there is more than one such resource, an arbitrary selection is made.
	*
   	* @param	string	$uri
   	* @return	object ResResource
   	* @access	public
   	*/
	function getLabelProperty()
	{
		return $this->getPropertyValue($this->vocabulary->LABEL());
	}
	
	/**
	* Add the given class as one of the rdf:type's for this resource.
	*
   	* @param	object ResResource	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function addRDFType($resResource)
	{
		return $this->addProperty($this->vocabulary->TYPE(),$resResource);
	}
	
	/**
	* Answer the rdf:type (ie the class) of this resource. 
	* If there is more than one type for this resource, the return value will 
	* be one of the values, but it is not specified which one 
	* (nor that it will consistently be the same one each time).
	*
   	* @return	object ResResource 
   	* @access	public
   	*/
	function getRDFType()
	{
		return $this->getPropertyValue($this->vocabulary->TYPE());
	}
	
	/**
	* Add a resource that is declared to provided additional 
	* information about the definition of this resource.
	*
   	* @param	object ResResource	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function addSeeAlso($resResource)
	{
		return $this->addProperty($this->vocabulary->SEE_ALSO(),$resResource);
	}
	
	/**
	* Answer a resource that provides additional information about this resource. 
	* If more than one such resource is defined, make an arbitrary choice.
	*
   	* @return	object ResResource 
   	* @access	public
   	*/
	function getSeeAlso()
	{
		return $this->getPropertyValue($this->vocabulary->SEE_ALSO());
	}
	
	/**
	* Answer a view of this resource as a class
	*
   	* @return	object OntClass 
   	* @access	public
   	*/
	function asClass()
	{
		return  $this->model->createOntClass($this->uri);
	}
	
	/**
	* Answer a view of this resource as an Individual
	*
   	* @return	object Individual 
   	* @access	public
   	*/
	function asIndividual()
	{
		return  $this->model->createIndividual($this->uri);
	}
	
	/**
	* Answer a view of this resource as a property
	*
   	* @return	object OntProperty
   	* @access	public
   	*/
	function asOntProperty()
	{
		return  $this->model->createOntProperty($this->uri);
	}
	
	/**
	* Answer a reference to the ontology language profile that governs the 
	* ontology model to which this ontology resource is attached.
	*
   	* @param	string	$uri
   	* @return	object OntClass 
   	* @access	public
   	*/
	function getVocabulary()
	{
		return $this->vocabulary ;
	}
	
	/**
	* Answer the value of a given RDF property for this resource as $returnType, or null 
	* if it doesn't have one. If there is more than one RDF statement with 
	* the given property for the current value, it is not defined which of 
	* the values will be returned.
	* The following return Types are supported: 'OntClass', 'OntProperty', 'Individual', and 'ResResource'
	* Default is 'ResResource'
	*
   	* @param	object ResResource	$property
   	* @param	string	$returnType
   	* @return	object OntClass 
   	* @access	public
   	*/
	function getPropertyValue($property, $returnType = 'ResResource')
	{
		$statement=$this->getProperty($property);
		if ($statement===null)
			return null;
			
			switch ($returnType) 
			{
				case 'OntClass':
					return $this->model->createOntClass($statement->getLabelObject());		
					break;
					
				case 'OntProperty':
					return $this->model->createOntProperty($statement->getLabelObject());		
					break;
				
				case 'Individual':
					return $this->model->createIndividual($statement->getLabelObject());		
					break;
			
				default:
					return $statement->getObject();
					break;	
			}	
	}
	
	/**
	* Answer true if this resource has the given comment.
	*
   	* @param	object ResLiteral	$resLiteral
   	* @return	boolean 
   	* @access	public
   	*/
	function hasComment($resLiteral)
	{
		return $this->hasProperty($this->vocabulary->COMMENT(),$resLiteral);
	}
	
	/**
	* Answer true if this resource has the given label.
	*
   	* @param	object ResLiteral	$resLiteral
   	* @return	boolean 
   	* @access	public
   	*/
	function hasLabelProperty($resLiteral)
	{
		return $this->hasProperty($this->vocabulary->LABEL(),$resLiteral);
	}
	
	/**
	* Answer true if this resource has the given rdf:type.
	*
   	* @param	object ResResource	$ontClass
   	* @return	boolean 
   	* @access	public
   	*/
	function hasRDFType($ontClass)
	{
		return $this->hasProperty($this->vocabulary->TYPE(),$ontClass);
	}
	
	/**
	* Answer true if this resource has the given resource as a source 
	* of additional information.
	*
   	* @param	object ResResource	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function hasSeeAlso($resResource)
	{
		return $this->hasProperty($this->vocabulary->SEE_ALSO(),$resResource);
	}
	
	/**
	* Answer true if this resource is defined by the given resource.
	*
   	* @param	object ResResource	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function isDefinedBy($resResource)
	{
		return $this->hasProperty($this->vocabulary->IS_DEFINED_BY(),$resResource);
	}
	
	/**
	* Answer an array of all of the comment literals for this resource.
	*
   	* @param	string	$language
   	* @return	array
   	* @access	public
   	*/
	function listComments($language)
	{
		$return=$this->listProperty($this->vocabulary->COMMENT());
		if ($language === false)
			return $return;
					
		foreach ($return as $key => $resLiteral) 
		{
			if (!is_a($resLiteral,'ResLiteral') || $resLiteral->getLanguage() != $language)
				unset ($return[$key]);	
		}
		return $return;	
	}
	
	/**
	* Answer an array of all of the resources that are declared to define this resource.
	*
   	* @return	array
   	* @access	public
   	*/
	function listIsDefinedBy()
	{
		return $this->listProperty($this->vocabulary->IS_DEFINED_BY());
	}
	
	/**
	* Answer an array of all of the label literals for this resource, with the given
	* language, if $language is set.
	*
   	* @return	array
   	* @access	public
   	*/
	function listLabelProperties($language = false)
	{
		$return=$this->listProperty($this->vocabulary->LABEL());
		if ($language === false)
			return $return;
					
		foreach ($return as $key => $resLiteral) 
		{
			if (!is_a($resLiteral,'ResLiteral') || $resLiteral->getLanguage() != $language)
				unset ($return[$key]);	
		}
		return $return;	
	
	}
	
	/**
	* Answer an array of the RDF classes to which this resource belongs.
	* If $direct is true, only answer those resources that are direct types of 
	* this resource, not the super-classes of the class etc.
	* 
   	* @param	boolean	$direct
   	* @return	array Array of ResResources
   	* @access	public
   	*/
	function listRDFTypes($direct = true)
	{
		return $this->listProperty($this->vocabulary->TYPE());		
	}
	
	/**
	* Answer an array of all of the resources that are declared to 
	* provide addition information about this resource.
	*	
   	* @return	array Array of ResResources
   	* @access	public
   	*/
	function listSeeAlso()
	{
		return $this->listProperty($this->vocabulary->SEE_ALSO());	
	}
	
	/**
	* Answer an array of values of a given RDF property for this resource as $returnType, or null 
	* if it doesn't have one. 
	* The following return Types are supported: 'OntClass', 'OntProperty', 'Individual', and 'ResResource'
	* Default is 'ResResource'
	*
   	* @param	object ResResource	$property
   	* @param	string	$returnType
   	* @return	array of ResResources 
   	* @access	public
   	*/
	function listProperty($property, $returnType = 'OntResource')
	{
		$return=array();
		$resArray = $this->listProperties($property);
		foreach ($resArray as $statement) 
		{
			switch ($returnType) 
			{
				case 'OntClass':
					$return[]=$this->model->createOntClass($statement->getLabelObject());		
					break;
					
				case 'OntProperty':
					$return[]=$this->model->createOntProperty($statement->getLabelObject());		
					break;
				
				case 'Individual':
					$return[]=$this->model->createIndividual($statement->getLabelObject());		
					break;
			
				default:
					$return[]=$statement->getObject();
					break;
			}	
		}
		return $return;	
	}
	
	/**
	* Remove the statement that the given ResLiteral is a comment on this resource.
	* Returns true, if a statement was removed
	*
   	* @param	object ResLiteral $resLiteral
   	* @return	boolean
   	* @access	public
   	*/
	function removeComment($resLiteral)
	{
		return $this->removeProperty($this->vocabulary->COMMENT(),$resLiteral);
	}
	
	/**
	* Remove the statement that this resource is defined by the given resource.
	*
   	* @param	object ResResource $resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function removeDefinedBy($resResource)
	{
		return $this->removeProperty($this->vocabulary->IS_DEFINED_BY(),$resResource);
	}
	
	/**
	* Remove the statement that the given ResLiteral is a label on this resource.
	* Returns true, if a statement was removed
	*
   	* @param	object ResLiteral $resLiteral
   	* @return	boolean
   	* @access	public
   	*/
	function removeLabelProperty($resLiteral)
	{
		return $this->removeProperty($this->vocabulary->LABEL(),$resLiteral);
	}
	
	/**
	* Remove the specific property-value pair from this resource.
	*
   	* @param	object ResResource	$property
   	* @param	object ResResource	$value
   	* @return	boolean 
   	* @access	public
   	*/
	function removeProperty($property, $value)
	{
		return $this->model->remove(new Statement($this,$property,$value));
	}
	
	/**
	* Remove the statement that this resource is of the given RDF type.
	*
   	* @param	object ResResource $resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function removeRDFType($resResource)
	{
		return $this->removeProperty($this->vocabulary->TYPE(),$resResource);
	}
	
	/**
	* Remove the statement indicating the given resource as a source of 
	* additional information about this resource. 
	*
   	* @param	object ResResource $resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function removeSeeAlso($resResource)
	{
		return $this->removeProperty($this->vocabulary->SEE_ALSO(),$resResource);
	}
	
	/**
	* Assert that the given string is the comment on this resource. 
	* Any existing statements for comment will be removed.
	*
   	* @param	object ResLiteral	$resLiteral 
   	* @access	public
   	*/
	function setComment($resLiteral)
	{
		$this->setPropertyValue($this->vocabulary->COMMENT(),$resLiteral);
	}
	
	/**
	* Assert that the given resource provides a source of definitions about this resource. 
	* Any existing statements for isDefinedBy will be removed.
	*
   	* @param	object ResResource	$resResource 
   	* @access	public
   	*/
	function setIsDefinedBy($resResource)
	{
		$this->setPropertyValue($this->vocabulary->IS_DEFINED_BY(),$resResource);
	}
	
	/**
	* Assert that the given string is the label on this resource. 
	* Any existing statements for comment will be removed.
	*
   	* @param	object ResLiteral	$resLiteral 
   	* @access	public
   	*/
	function setLabelProperty($resLiteral)
	{
		$this->setPropertyValue($this->vocabulary->LABEL(),$resLiteral);
	}
	
	/**
	* Set the value of the given property of this ontology resource to the given value. 
	* Maintains the invariant that there is at most one value of the property for a 
	* given resource, so existing property values are first removed. 
	* To add multiple properties, use addProperty.
	*
   	* @param	object ResResource	$property
   	* @param	object ResResource	$value  
   	* @access	public
   	*/
	function setPropertyValue($property, $value)
	{
		$this->removeAll($property);
		$this->addProperty($property,$value);
	}
	
	/**
	* Set the RDF type (ie the class) for this resource, 
	* replacing any existing rdf:type property. Any existing statements 
	* for the RDF type will first be removed.
	*
   	* @param	object ResResource	$resResource 
   	* @access	public
   	*/
	function setRDFType($resResource)
	{
		$this->setPropertyValue($this->vocabulary->TYPE(),$resResource);
	}
	
	/**
	* Add a resource that is declared to provided additional information 
	* about the definition of this resource
	*
   	* @param	object ResResource	$resResource 
   	* @access	public
   	*/
	function setSeeAlso($resResource)
	{
		$this->setPropertyValue($this->vocabulary->SEE_ALSO(),$resResource);
	}
	
	/**
	* Returns an array of ResResources that are recursively connected by $attribute 
	* in superProperty direction.
	* If $onlyFindThisResResource is set to a ResResource, this function returns boolean
	* if this distinct resource recursively is connected to the $startResource.
	*
   	* @param	object ResResource	$startResource
   	* @param	object ResResource	$attribute
	* @param	array				$attribute
	* @param	object ResResource	$onlyFindThisResResource	
   	* @return	array OR boolean 
   	* @access	private
   	*/
	function _getSuperAttributeStatementsRec(& $startResource,& $attribute,& $attributeIndex, $onlyFindThisResResource = false)
	{

		$return = $startResource->listProperties($attribute);
		
		if ($onlyFindThisResResource)
		{
			foreach ($return as $statement)
			{
				if ($onlyFindThisResResource->equals($statement->getObject()))
					return true;
			}	
		}
		
		foreach ($return as $statement)
		{
			$attributeLabel=$statement->getLabelObject();
			if (!in_array($attributeLabel,$attributeIndex))
			{
				$attributeIndex[]=$attributeLabel;
				$subReturn = $this->_getSuperAttributeStatementsRec($statement->getObject(), $attribute, $attributeIndex, $onlyFindThisResResource);
			} 
		}
		if (isset($subReturn))
		{
			if ($subReturn === true)
				return true;
			return array_merge($return,$subReturn);
		}	
		
		return $return;
	}
	
	/**
	* Returns an array of ResResources that are recursively connected by $attribute 
	* in subProperty direction.
	* If $onlyFindThisResResource is set to a ResResource, this function returns boolean
	* if this distinct resource recursively is connected to the $startResource.
	*
   	* @param	object ResResource	$startResource
   	* @param	object ResResource	$attribute
	* @param	array				$attribute
	* @param	object ResResource	$onlyFindThisResResource	
   	* @return	array OR boolean 
   	* @access	private
   	*/
	function _getSubAttributeStatementsRec(& $startResource,& $attribute,& $attributeIndex, $onlyFindThisResResource = false)
	{

		$return = $this->model->find(null,$attribute,$startResource);
		
		if ($onlyFindThisResResource)
		{
			foreach ($return as $statement)
			{
				if ($onlyFindThisResResource->equals($statement->getSubject()))
					return true;
			}	
		}
		
		foreach ($return as $statement)
		{
			$attributeLabel=$statement->getLabelSubject();
			if (!in_array($attributeLabel,$attributeIndex))
			{
				$attributeIndex[]=$attributeLabel;
				$subReturn = $this->_getSubAttributeStatementsRec($statement->getSubject(), $attribute, $attributeIndex, $onlyFindThisResResource);
			} 
		}
		if (isset($subReturn))
		{
			if ($subReturn === true)
				return true;
			return array_merge($return,$subReturn);
		}
			
		return $return;
	}
	
	/**
	* Add a property to this resource.
	* A statement with this resource as the subject, p as the predicate and o 
	* as the object is added to the model associated with this resource.
	* If $this->rdfType is set, an additional statement about it's type
	* is added.
	*
   	* @param	ResResource				$property
   	* @param	ResResource/ResLiteral	$object
   	* @return	object ResResource 
   	* @access	public
   	*/
	function addProperty($property,$object)
	{
		if ($this->rdfType !== false)
			if (!$this->hasRDFType($this->rdfType))
					$this->model->add(new Statement($this,$this->vocabulary->TYPE(),$this->rdfType));
	
		return parent:: addProperty($property,$object);
	}
	
	/**
    * Sets the rdf:type, that this distinct resource is instance of.
    * If this value is set, the ontModel will add an additional 
    * statement about this resource and the fiven rdf:type
    *
    * @param object ResResource $resResource 
	* @access	public
    */
	function setInstanceRdfType($resResource)
	{
		$this->rdfType = $resResource;
	}
	
	/**
    * returns the rdf:type, that this distinct resource is instance of.
    *
    * @return object ResResource $resResource 
	* @access	public
    */
	function getInstanceRdfType()
	{
		return $this->rdfType;
	}
} 
?>