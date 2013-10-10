<?php
// ----------------------------------------------------------------------------------
// Class: OntClass
// ----------------------------------------------------------------------------------



/**
* Class that represents an ontology node characterising a class description.
*
* @version  $Id: OntClass.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	ontModel
* @access	public
**/
class OntClass extends OntResource 
{
	/**
    * Constructor
	* You can supply a uri
    *
    * @param string $uri 
	* @access	public
    */		
	function OntClass($uri = null)
	{
		parent::OntResource($uri);
	}

	/**
	* Add a sub-class of this class.
	*
   	* @param	object ResResource		$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function addSubClass($resResource)
	{
		return $resResource->addProperty($this->vocabulary->SUB_CLASS_OF(),$this);
	}
	
	/**
	* Add a super-class of this class.
	*
   	* @param	object ResResource		$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function addSuperClass($resResource)
	{
		return $this->addProperty($this->vocabulary->SUB_CLASS_OF(),$resResource);
	}
	
	/**
	* Answer a class that is the sub-class of this class. 
	* If there is more than one such class, an arbitrary selection is made.
	*
   	* @return	object OntClass or NULL
   	* @access	public
   	*/
	function getSubClass()
	{
		$statement = $this->model->findFirstMatchingStatement(null,$this->vocabulary->SUB_CLASS_OF(),$this);
		if ($statement !== null)
			return $this->model->createOntClass($statement->getLabelSubject());
		
		return null;
	}
	
	/**
	* Answer a class that is the super-class of this class. 
	* If there is more than one such class, an arbitrary selection is made.
	*
   	* @return	object OntClass or NULL
   	* @access	public
   	*/
	function getSuperClass()
	{
		return $this->getPropertyValue($this->vocabulary->SUB_CLASS_OF(),'OntClass');
	}
	
	/**
	* Answer true if the given class is a sub-class of this class.
	* $direct - If true, only search the classes that are directly 
	* adjacent to this class in the class hierarchy. 
	*
   	* @param	object ResResource		$resResource
  	* @param	boolean			$direct
   	* @return	boolean 
   	* @access	public
   	*/
	function hasSubclass($resResource, $direct = true)
	{
		if ($direct)
			return $resResource->hasProperty($this->vocabulary->SUB_CLASS_OF(),$this);
		
		$index=array();
		return ($this->_getSubAttributeStatementsRec($this,$this->vocabulary->SUB_CLASS_OF(),$index,$resResource) === true);
	}
	
	/**
	* Answer true if the given class is a super-class of this class.
	* $direct - If true, only search the classes that are directly 
	* adjacent to this class in the class hierarchy. 
	*
   	* @param	object ResResource		$resResource
  	* @param	boolean			$direct
   	* @return	boolean 
   	* @access	public
   	*/
	function hasSuperClass($resResource, $direct = true)
	{
		if ($direct)
			return $this->hasProperty($this->vocabulary->SUB_CLASS_OF(),$resResource);
		
		$index=array();
		return ($this->_getSuperAttributeStatementsRec($this,$this->vocabulary->SUB_CLASS_OF(),$index,$resResource) === true);
	}
	
	/**
	* Answer an ResIterator over the individuals in the model that have this class 
	* among their types.
	*
   	* @return	object ResIterator 
   	* @access	public
   	*/
	function listInstances()
	{
		/*
		$statements= $this->model->find(null,$this->vocabulary->TYPE(),$this);
		$return = array();
		$returnIndex=array();
		foreach ($statements as $statement) 
		{
			$subjectLabel=$statement->getLabelSubject();
			if (!in_array($subjectLabel,$returnIndex))
			{
				$returnIndex[]=$subjectLabel;
				$return[]=$statement->getSubject();
			}	
		}	
		return $return;	
		*/
		return new ResIterator(null,$this->vocabulary->TYPE(),$this,'s',$this->model,'Individual');
	
	}
	
	/**
	* Answer an array over the classes that are declared to be sub-classes of this class. 
	* Each element of the array will be an OntClass.
	* $direct - If true, only search the classes that are directly 
	* adjacent to this class in the class hierarchy. 
	*
   	* @param	boolean		$direct
   	* @return	array 
   	* @access	public
   	*/
	function listSubClasses($direct = true)
	{
		$return = array();
		if ($direct)
		{
			$statements = $this->model->find(null,$this->vocabulary->SUB_CLASS_OF(),$this);
		} else 
		{
			$index = array();
			$statements = $this->_getSubAttributeStatementsRec($this,$this->vocabulary->SUB_CLASS_OF(),$index);
		}
		
		$returnIndex=array();
		foreach ($statements as $statement) 
		{
			$subjectLabel=$statement->getLabelSubject();
			if (!in_array($subjectLabel,$returnIndex))
			{
				$returnIndex[]=$subjectLabel;
				$return[]=$this->model->createOntClass($subjectLabel);
			}	
		}	
		return $return;	
	}
	
	/**
	* Answer an array over the classes that are declared to be super-classes of this class. 
	* Each element of the array will be an OntClass.
	* $direct - If true, only search the classes that are directly 
	* adjacent to this class in the class hierarchy. 
	*
   	* @param	boolean		$direct
   	* @return	array 
   	* @access	public
   	*/
	function listSuperClasses($direct = true)
	{
		$return = array();
		if ($direct)
			return $this->listProperty($this->vocabulary->SUB_CLASS_OF(),'OntClass');
			
		$index=array();	
		$statements = $this->_getSuperAttributeStatementsRec($this,$this->vocabulary->SUB_CLASS_OF(),$index);
		$returnIndex=array();
		foreach ($statements as $statement) 
		{
			$objectLabel=$statement->getLabelObject();
			if (!in_array($objectLabel,$returnIndex))
			{
				$returnIndex[]=$objectLabel;
				$return[]=$this->model->createOntClass($objectLabel);
			}	
		}	
		return $return;	
	}
	
	/**
	* Remove the given class from the sub-classes of this class.
	*
   	* @param	object ResResource	$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function removeSubClass($resResource)
	{
		return	$this->model->remove(new Statement($resResource,$this->vocabulary->SUB_CLASS_OF(),$this));
	}
	
	/**
	* Remove the given class from the super-classes of this class.
	*
   	* @param	object ResResource		$resResource
   	* @return	boolean 
   	* @access	public
   	*/
	function removeSuperClass($resResource)
	{
		return $this->removeProperty($this->vocabulary->SUB_CLASS_OF(),$resResource);
	}
	
	/**
	* Assert that this class is super-class of the given class. 
	* Any existing statements for subClassOf on prop will be removed.
	*
   	* @param	object ResResource		$resResource
   	* @access	public
   	*/
	function setSubClass($resResource)
	{
		foreach ($this->listSubClasses() as $oldRes) 
		{
			$this->removeSubClass($oldRes);
		}
		$this->addSubClass($resResource);
	}
	
	/**
	* Assert that this class is sub-class of the given class. 
	* Any existing statements for subClassOf on prop will be removed.
	*
   	* @param	object ResResource		$resResource
   	* @access	public
   	*/
	function setSuperClass($resResource)
	{
		$this->setPropertyValue($this->vocabulary->SUB_CLASS_OF(),$resResource);
	}
	
	/**
	* Answer a resource that represents an instance of this OntClass and Individual
	* node in this model.
	* If a resource with the given uri exists in the model, it will be re-used. 
	* If not, a new one is created in the updateable sub-model of the ontology model.
	*
   	* @param	string	$uri
   	* @return	object Individual 
   	* @access	public
   	*/	
	function createInstance($uri = null)
	{
		$instance = $this->model->createIndividual($uri);
		$instance->setInstanceRdfType($this);
		return $instance;
	}
} 
?>