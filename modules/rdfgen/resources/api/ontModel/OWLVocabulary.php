<?php

// ----------------------------------------------------------------------------------
// Class: OWLVocabulary
// ----------------------------------------------------------------------------------

/**
* OWL vocabulary items
*
* @version  $Id: OWLVocabulary.php 320 2006-11-21 09:38:51Z tgauss $
* @author Daniel Westphal <mail at d-westphal dot de>
*
*
* @package 	ontModel
* @access	public
**/
class OWLVocabulary extends OntVocabulary 
{

	/**
	* Answer the resource that represents the class 'class' in this vocabulary.
	*
   	* @return	object ResResource 
   	* @access	public
   	*/
	function ONTCLASS()
	{
		return new ResResource(OWL_NS.'Class');	
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
	
	
	
	function ANNOTATION_PROPERTY()
	{
		return new ResProperty(OWL_NS . 'AnnotationProperty');	
	}

	function ALL_DIFFERENT()
	{
		return new ResProperty(OWL_NS . 'AllDifferent');	
	}
	
	function ALL_VALUES_FROM()
	{
		return new ResProperty(OWL_NS . 'allValuesFrom');
	}

	function BACKWARD_COMPATIBLE_WITH()
	{
		return new ResProperty(OWL_NS . 'backwardCompatibleWith');
	}
	
	function CARDINALITY()
	{
		return new ResProperty(OWL_NS . 'cardinality');
	}

	function COMPLEMENT_OF()
	{
		return new ResProperty(OWL_NS . 'complementOf');
	}
	
	function DATATYPE()
	{
		return new ResProperty(OWL_NS . 'Datatype');
	}
	
	function DATATYPE_PROPERTY()
	{
		return new ResProperty(OWL_NS . 'DatatypeProperty');
	}

	function DATA_RANGE()
	{
		return new ResProperty(OWL_NS . 'DataRange');
	}

	function DATATYPE_RESTRICTION()
	{
		return new ResProperty(OWL_NS . 'DatatypeRestriction');
	}

	function DEPRECATED_CLASS()
	{
		return new ResProperty(OWL_NS . 'DeprecatedClass');
	}

	function DEPRECATED_PROPERTY()
	{
		return new ResProperty(OWL_NS . 'DeprecatedProperty');
	}

	function DISTINCT_MEMBERS()
	{
		return new ResProperty(OWL_NS . 'distinctMembers');
	}

	function DIFFERENT_FROM()
	{
		return new ResProperty(OWL_NS . 'differentFrom');
	}

	function DISJOINT_WITH()
	{
		return new ResProperty(OWL_NS . 'disjointWith');
	}

	function EQUIVALENT_CLASS()
	{
		return new ResProperty(OWL_NS . 'equivalentClass');
	}

	function EQUIVALENT_PROPERTY()
	{
		return new ResProperty(OWL_NS . 'equivalentProperty');
	}

	function FUNCTIONAL_PROPERTY()
	{
		return new ResProperty(OWL_NS . 'FunctionalProperty');
	}

	function HAS_VALUE()
	{
		return new ResProperty(OWL_NS . 'hasValue');
	}

	function INCOMPATIBLE_WITH()
	{
		return new ResProperty(OWL_NS . 'incompatibleWith');
	}

	function IMPORTS()
	{
		return new ResProperty(OWL_NS . 'imports');
	}

	function INTERSECTION_OF()
	{
		return new ResProperty(OWL_NS . 'intersectionOf');
	}

	function INVERSE_FUNCTIONAL_PROPERTY()
	{
		return new ResProperty(OWL_NS . 'InverseFunctionalProperty');
	}

	function INVERSE_OF()
	{
		return new ResProperty(OWL_NS . 'inverseOf');
	}

	function MAX_CARDINALITY()
	{
		return new ResProperty(OWL_NS . 'maxCardinality');
	}

	function MIN_CARDINALITY()
	{
		return new ResProperty(OWL_NS . 'minCardinality');
	}

	function NOTHING()
	{
		return new ResProperty(OWL_NS . 'Nothing');
	}

	function OBJECT_CLASS()
	{
		return new ResProperty(OWL_NS . 'ObjectClass');
	}

	function OBJECT_PROPERTY()
	{
		return new ResProperty(OWL_NS . 'ObjectProperty');
	}

	function OBJECT_RESTRICTION()
	{
		return new ResProperty(OWL_NS . 'ObjectRestriction');
	}

	function ONE_OF()
	{
		return new ResProperty(OWL_NS . 'oneOf');
	}

	function ON_PROPERTY()
	{
		return new ResProperty(OWL_NS . 'onProperty');
	}

	function ONTOLOGY()
	{
		return new ResProperty(OWL_NS . 'Ontology');
		
	}

	function PRIOR_VERSION()
	{
		return new ResProperty(OWL_NS . 'priorVersion');
	}

	function PROPERTY()
	{
		return new ResProperty(OWL_NS . 'Property');
	}

	function RESTRICTION()
	{
		return new ResProperty(OWL_NS . 'Restriction');
	}

	function SAME_AS()
	{
		return new ResProperty(OWL_NS . 'sameAs');
	}

	function SAME_CLASS_AS()
	{
		return new ResProperty(OWL_NS . 'sameClassAs');
	}

	function SAME_INDIVIDUAL_AS()
	{
		return new ResProperty(OWL_NS . 'sameIndividualAs');
	}

	function SAME_PROPERTY_AS()
	{
		return new ResProperty(OWL_NS . 'samePropertyAs');
	}

	function SOME_VALUES_FROM()
	{
		return new ResProperty(OWL_NS . 'someValuesFrom');
	}

	function SYMMETRIC_PROPERTY()
	{
		return new ResProperty(OWL_NS . 'SymmetricProperty');
	}

	function THING()
	{
		return new ResProperty(OWL_NS . 'Thing');
	}

	function TRANSITIVE_PROPERTY()
	{
		return new ResProperty(OWL_NS . 'TransitiveProperty');
	}

	function UNION_OF()
	{
		return new ResProperty(OWL_NS . 'unionOf');
	}

	function VERSION_INFO()
	{
		return new ResProperty(OWL_NS . 'versionInfo');
	}

	function NAMESPACE()
	{
		return OWL_NS;
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