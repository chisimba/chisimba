<?php

// ----------------------------------------------------------------------------------
// Class: OntModel
// ----------------------------------------------------------------------------------


/**
* Enhanced view of the model that is known to contain ontology data, under a
* given ontology vocabulary (such as RDFS). OntModel together with OntClass
* and OntResource provide ontology specific methods like addSubClass(),
* listSubClasses(), hasSuperProperty(), addDomain() and  listInstances(). This class does not by 
* itself compute the deductive extension of the graph under the semantic 
* rules of the language. Instead, we wrap an underlying model with this 
* ontology interface, that presents a convenience syntax for accessing the 
* language elements. Depending on the inference capability of the underlying 
* model, the OntModel will appear to contain more or less triples. 
* For example, if this class is used to wrap a MemModel or DBModel, only the 
* relationships asserted by the document will be reported through this 
* convenience API. 
* Alternatively, if the OntModel wraps an InfModel (InfModelF / InfModelB), 
* the inferred triples from the extension will be reported as well. 
*
*
* @version  $Id: OntModel.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	ontModel
* @access	public
**/
class OntModel extends ResModel 
{
	/**
	* Holds a reference to the assoiated vocabulary.
	* @var		object
	* @access	private
	*/
	var $vocabulary;
	
	
	/**
    * Constructor.
	* You have to supply a memmodel/dbmodel/infmodel to save the statements and a vocabulary
    *
    * @param object Model $model 
	* @access	public
    */	
	function OntModel(& $model,& $vocabulary)
	{
		parent::ResModel($model);
		$this->vocabulary = & $vocabulary;
	}
	
	/**
	* Answer a resource that represents a class description node in this model. 
	* If a resource with the given uri exists in the model, it will be re-used. 
	* If not, a new one is created in the updateable sub-model of the ontology model.
	*
   	* @param	string	$uri
   	* @return	object OntClass 
   	* @access	public
   	*/
	function createOntClass($uri = null)
	{
		$class = new OntClass($uri);
		$class->setAssociatedModel($this);
		$class->setVocabulary($this->vocabulary);
		$class->setInstanceRdfType($this->vocabulary->ONTCLASS());
		return $class;
	}
	
	/**
	* Answer a resource that represents an Individual node in this model.
	* If a resource with the given uri exists in the model, it will be re-used. 
	* If not, a new one is created in the updateable sub-model of the ontology model.
	*
   	* @param	string	$uri
   	* @return	object Individual 
   	* @access	public
   	*/
	function createIndividual($uri = null)
	{
		$individual = new Individual($uri);
		$individual->setAssociatedModel($this);
		$individual->setVocabulary($this->vocabulary);
		return $individual;
	}
	
	/**
	* Answer a resource that represents an OntProperty node in this model.
	* If a resource with the given uri exists in the model, it will be re-used. 
	* If not, a new one is created in the updateable sub-model of the ontology model.
	*
   	* @param	string	$uri
   	* @return	object OntProperty 
   	* @access	public
   	*/
	function createOntProperty($uri = null)
	{
		$ontProperty = new OntProperty($uri);
		$ontProperty->setAssociatedModel($this);
		$ontProperty->setVocabulary($this->vocabulary);
		$ontProperty->setInstanceRdfType($this->createResource(RDF_NAMESPACE_URI.RDF_PROPERTY));
		return $ontProperty;	
	}
	
	/**
	* Answer an array that ranges over all of the various forms of class 
	* description resource in this model. 
	* Class descriptions include  domain/range definitions, named  classes and subClass constructs.
	*
   	* @return	array of object ResResource 
   	* @access	public
   	*/
	function listClasses()
	{
		//get all statements, with an rdf:type as property
		$statements= $this->find(null,$this->vocabulary->TYPE(),null);
		$return = array();
		$returnIndex=array();
		foreach ($statements as $statement) 
		{
			$objectLabel=$statement->getLabelObject();
			//if it's about a typed Individual	
			if ($objectLabel!=RDF_SCHEMA_URI.RDFS_CLASS)
			{
				if (!in_array($objectLabel,$returnIndex))
				{
					$returnIndex[]=$objectLabel;
					$return[]=$statement->getObject();
				}
			} else 
			//if it's a "class1 rdf:type rdf:class" construct
			{
				$subjectLabel=$statement->getLabelSubject();
				if (!in_array($subjectLabel,$returnIndex))
				{
					$returnIndex[]=$subjectLabel;
					$return[]=$statement->getSubject();
				}
			}	
		}
		//find all statements about SubClassConstructs
		$statements= $this->find(null,$this->vocabulary->SUB_CLASS_OF(),null);
		foreach ($statements as $statement) 
		{
			//add the statements object to the result
			$objectLabel=$statement->getLabelObject();
			if (!in_array($objectLabel,$returnIndex))
			{
				$returnIndex[]=$objectLabel;
				$return[]=$statement->getObject();
			}	
		}	
		foreach ($statements as $statement) 
		{
			//add the statements subject to the result
			$objectLabel=$statement->getLabelSubject();
			if (!in_array($objectLabel,$returnIndex))
			{
				$returnIndex[]=$objectLabel;
				$return[]=$statement->getSubject();
			}	
		}
		return $return;	
	}
} 
?>
