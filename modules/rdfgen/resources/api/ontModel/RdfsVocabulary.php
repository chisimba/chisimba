<?php

// ----------------------------------------------------------------------------------
// Class: RdfsVocabulary
// ----------------------------------------------------------------------------------

/**
* RDFS vocabulary items
*
*
* @version  $Id: RdfsVocabulary.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	ontModel
* @access	public
**/
class RdfsVocabulary extends OntVocabulary 
{

	/**
	* Answer the resource that represents the class 'class' in this vocabulary.
	*
   	* @return	object ResResource 
   	* @access	public
   	*/
	function ONTCLASS()
	{
		return new ResResource(RDF_SCHEMA_URI.RDFS_CLASS);	
	}
	
	/**
	* Answer the predicate that denotes the domain of a property.
	*
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function DOMAIN()
	{
		return new ResProperty(RDF_SCHEMA_URI.RDFS_DOMAIN);	
	}
	
	 
 	/**
	* Answer the predicate that denotes comment annotation on an ontology element.
	*
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function COMMENT()
	{
		return new ResProperty(RDF_SCHEMA_URI.RDFS_COMMENT);	
	}
	
 	/**
	* Answer the predicate that denotes isDefinedBy annotation on an ontology element
	*
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function IS_DEFINED_BY()
	{
		return new ResProperty(RDF_SCHEMA_URI.RDFS_IS_DEFINED_BY);
	}
	
	/**
	* Answer the predicate that denotes label annotation on an ontology element
	*
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function LABEL()
	{
		return new ResProperty(RDF_SCHEMA_URI.RDFS_LABEL);
	}
	
	/**
	* Answer the predicate that denotes the domain of a property.
	*
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function RANGE()
	{
		return new ResProperty(RDF_SCHEMA_URI.RDFS_RANGE);
	}
	
	/**
	* Answer the predicate that denotes seeAlso annotation on an ontology element
	*
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function SEE_ALSO()
	{
		return new ResProperty(RDF_SCHEMA_URI.RDFS_SEE_ALSO);
	}
	
	/**
	* Answer the predicate that denotes that one class is a sub-class of another.
	*
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function SUB_CLASS_OF()
	{
		return new ResProperty(RDF_SCHEMA_URI.RDFS_SUBCLASSOF);
	}
	
	/**
	* Answer the predicate that denotes that one property is a sub-property of another.
	*
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function SUB_PROPERTY_OF()
	{
		return new ResProperty(RDF_SCHEMA_URI.RDFS_SUBPROPERTYOF);
	}

	/**
	* Answer the string that is the namespace prefix for this vocabulary
	*
   	* @return	string
   	* @access	public
   	*/
	function NAMESPACE()
	{
		return RDF_SCHEMA_URI;
	}
	
	/**
	* Answer the predicate that denotes the rdf:type property.
	*
   	* @return	object ResProperty 
   	* @access	public
   	*/
	function TYPE()
	{
		return new ResProperty(RDF_NAMESPACE_URI.RDF_TYPE);
	}
} 
?>