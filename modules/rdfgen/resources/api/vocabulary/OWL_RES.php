<?php
/**
*   OWL Vocabulary (ResResource)
*
*   @version $Id: OWL_RES.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Daniel Westphal (dawe@gmx.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of theWeb
*   Ontology Language (OWL). For details about OWL see:
*   http://www.w3.org/TR/owl-ref/
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/
class OWL_RES{

	// OWL concepts
	function ANNOTATION_PROPERTY()
	{
		return  new ResResource(OWL_NS . 'AnnotationProperty');

	}

	function ALL_DIFFERENT()
	{
		return  new ResResource(OWL_NS . 'AllDifferent');

	}

	function ALL_VALUES_FROM()
	{
		return  new ResResource(OWL_NS . 'allValuesFrom');

	}

	function BACKWARD_COMPATIBLE_WITH()
	{
		return  new ResResource(OWL_NS . 'backwardCompatibleWith');

	}

	function CARDINALITY()
	{
		return  new ResResource(OWL_NS . 'cardinality');

	}

	function OWL_CLASS()
	{
		return  new ResResource(OWL_NS . 'Class');

	}

	function COMPLEMENT_OF()
	{
		return  new ResResource(OWL_NS . 'complementOf');

	}

	function DATATYPE()
	{
		return  new ResResource(OWL_NS . 'Datatype');

	}

	function DATATYPE_PROPERTY()
	{
		return  new ResResource(OWL_NS . 'DatatypeProperty');

	}

	function DATA_RANGE()
	{
		return  new ResResource(OWL_NS . 'DataRange');

	}

	function DATATYPE_RESTRICTION()
	{
		return  new ResResource(OWL_NS . 'DatatypeRestriction');

	}

	function DEPRECATED_CLASS()
	{
		return  new ResResource(OWL_NS . 'DeprecatedClass');

	}

	function DEPRECATED_PROPERTY()
	{
		return  new ResResource(OWL_NS . 'DeprecatedProperty');

	}

	function DISTINCT_MEMBERS()
	{
		return  new ResResource(OWL_NS . 'distinctMembers');

	}

	function DIFFERENT_FROM()
	{
		return  new ResResource(OWL_NS . 'differentFrom');

	}

	function DISJOINT_WITH()
	{
		return  new ResResource(OWL_NS . 'disjointWith');

	}

	function EQUIVALENT_CLASS()
	{
		return  new ResResource(OWL_NS . 'equivalentClass');

	}

	function EQUIVALENT_PROPERTY()
	{
		return  new ResResource(OWL_NS . 'equivalentProperty');

	}

	function FUNCTIONAL_PROPERTY()
	{
		return  new ResResource(OWL_NS . 'FunctionalProperty');

	}

	function HAS_VALUE()
	{
		return  new ResResource(OWL_NS . 'hasValue');

	}

	function INCOMPATIBLE_WITH()
	{
		return  new ResResource(OWL_NS . 'incompatibleWith');

	}

	function IMPORTS()
	{
		return  new ResResource(OWL_NS . 'imports');

	}

	function INTERSECTION_OF()
	{
		return  new ResResource(OWL_NS . 'intersectionOf');

	}

	function INVERSE_FUNCTIONAL_PROPERTY()
	{
		return  new ResResource(OWL_NS . 'InverseFunctionalProperty');

	}

	function INVERSE_OF()
	{
		return  new ResResource(OWL_NS . 'inverseOf');

	}

	function MAX_CARDINALITY()
	{
		return  new ResResource(OWL_NS . 'maxCardinality');

	}

	function MIN_CARDINALITY()
	{
		return  new ResResource(OWL_NS . 'minCardinality');

	}

	function NOTHING()
	{
		return  new ResResource(OWL_NS . 'Nothing');

	}

	function OBJECT_CLASS()
	{
		return  new ResResource(OWL_NS . 'ObjectClass');

	}

	function OBJECT_PROPERTY()
	{
		return  new ResResource(OWL_NS . 'ObjectProperty');

	}

	function OBJECT_RESTRICTION()
	{
		return  new ResResource(OWL_NS . 'ObjectRestriction');

	}

	function ONE_OF()
	{
		return  new ResResource(OWL_NS . 'oneOf');

	}

	function ON_PROPERTY()
	{
		return  new ResResource(OWL_NS . 'onProperty');

	}

	function ONTOLOGY()
	{
		return  new ResResource(OWL_NS . 'Ontology');

	}

	function PRIOR_VERSION()
	{
		return  new ResResource(OWL_NS . 'priorVersion');

	}

	function PROPERTY()
	{
		return  new ResResource(OWL_NS . 'Property');

	}

	function RESTRICTION()
	{
		return  new ResResource(OWL_NS . 'Restriction');

	}

	function SAME_AS()
	{
		return  new ResResource(OWL_NS . 'sameAs');

	}

	function SAME_CLASS_AS()
	{
		return  new ResResource(OWL_NS . 'sameClassAs');

	}

	function SAME_INDIVIDUAL_AS()
	{
		return  new ResResource(OWL_NS . 'sameIndividualAs');

	}

	function SAME_PROPERTY_AS()
	{
		return  new ResResource(OWL_NS . 'samePropertyAs');

	}

	function SOME_VALUES_FROM()
	{
		return  new ResResource(OWL_NS . 'someValuesFrom');

	}

	function SYMMETRIC_PROPERTY()
	{
		return  new ResResource(OWL_NS . 'SymmetricProperty');

	}

	function THING()
	{
		return  new ResResource(OWL_NS . 'Thing');

	}

	function TRANSITIVE_PROPERTY()
	{
		return  new ResResource(OWL_NS . 'TransitiveProperty');

	}

	function UNION_OF()
	{
		return  new ResResource(OWL_NS . 'unionOf');

	}

	function VERSION_INFO()
	{
		return  new ResResource(OWL_NS . 'versionInfo');
	}
}



?>