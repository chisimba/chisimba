<?php 
// ----------------------------------------------------------------------------------
// OWL Vocabulary
// ----------------------------------------------------------------------------------
// Version                   : 0.4
// Authors                   : Daniel Westphal (dawe@gmx.de)

// Description               : Wrapper, defining resources for all concepts of the Web
// Ontology Language (OWL). For details about OWL see:
// http://www.w3.org/TR/owl-ref/
// Using the wrapper allows you to define all aspects of
// the language in one spot, simplifing implementation and
// maintainence. Working with the vocabulary, you should use
// these resources as shortcuts in your code.
// 
// define OWL namespace
define('RDF_OWL_NS', 'http://www.w3.org/2002/07/owl');

// OWL concepts
$OWL_AnnotationProperty =& RDF_Resource::factory(RDF_OWL_NS . 'AnnotationProperty');
$OWL_allValuesFrom =& RDF_Resource::factory(RDF_OWL_NS . 'allValuesFrom');
$OWL_backwardCompatibleWith = RDF_Resource::factory(RDF_OWL_NS . 'backwardCompatibleWith');
$OWL_cardinality =& RDF_Resource::factory(RDF_OWL_NS . 'cardinality');
$OWL_Class =& RDF_Resource::factory(RDF_OWL_NS . 'Class');
$OWL_complementOf =& RDF_Resource::factory(RDF_OWL_NS . 'complementOf');
$OWL_Datatype =& RDF_Resource::factory(RDF_OWL_NS . 'Datatype');
$OWL_DatatypeProperty =& RDF_Resource::factory(RDF_OWL_NS . 'DatatypeProperty');
$OWL_DataRange =& RDF_Resource::factory(RDF_OWL_NS . 'DataRange');    
$OWL_DatatypeRestriction =& RDF_Resource::factory(RDF_OWL_NS . 'DatatypeRestriction');
$OWL_DeprecatedClass = RDF_Resource::factory(RDF_OWL_NS . 'DeprecatedClass');
$OWL_DeprecatedProperty = RDF_Resource::factory(RDF_OWL_NS . 'DeprecatedProperty');
$OWL_differentFrom =& RDF_Resource::factory(RDF_OWL_NS . 'differentFrom');
$OWL_disjointWith =& RDF_Resource::factory(RDF_OWL_NS . 'disjointWith');
$OWL_sameAs =& RDF_Resource::factory(RDF_OWL_NS . 'sameAs');
$OWL_FunctionalProperty =& RDF_Resource::factory(RDF_OWL_NS . 'FunctionalProperty');
$OWL_hasValue =& RDF_Resource::factory(RDF_OWL_NS . 'hasValue');
$OWL_incompatibleWith = RDF_Resource::factory(RDF_OWL_NS . 'incompatibleWith');
$OWL_imports =& RDF_Resource::factory(RDF_OWL_NS . 'imports');
$OWL_intersectionOf =& RDF_Resource::factory(RDF_OWL_NS . 'intersectionOf');
$OWL_InverseFunctionalProperty =& RDF_Resource::factory(RDF_OWL_NS . 'InverseFunctionalProperty');
$OWL_inverseOf =& RDF_Resource::factory(RDF_OWL_NS . 'inverseOf');
$OWL_maxCardinality =& RDF_Resource::factory(RDF_OWL_NS . 'maxCardinality');
$OWL_minCardinality =& RDF_Resource::factory(RDF_OWL_NS . 'minCardinality');
$OWL_ObjectClass =& RDF_Resource::factory(RDF_OWL_NS . 'ObjectClass');
$OWL_ObjectProperty =& RDF_Resource::factory(RDF_OWL_NS . 'ObjectProperty');
$OWL_ObjectRestriction =& RDF_Resource::factory(RDF_OWL_NS . 'ObjectRestriction');
$OWL_oneOf =& RDF_Resource::factory(RDF_OWL_NS . 'oneOf');
$OWL_onProperty =& RDF_Resource::factory(RDF_OWL_NS . 'onProperty');
$vOWL_Ontology =& RDF_Resource::factory(RDF_OWL_NS . 'Ontology');
$OWL_priorVersion = RDF_Resource::factory(RDF_OWL_NS . 'priorVersion');   
$OWL_Property =& RDF_Resource::factory(RDF_OWL_NS . 'Property');
$vOWL_Restriction =& RDF_Resource::factory(RDF_OWL_NS . 'Restriction');
$OWL_sameClassAs =& RDF_Resource::factory(RDF_OWL_NS . 'sameClassAs');
$OWL_sameIndividualAs =& RDF_Resource::factory(RDF_OWL_NS . 'sameIndividualAs');
$OWL_samePropertyAs =& RDF_Resource::factory(RDF_OWL_NS . 'samePropertyAs');
$OWL_someValuesFrom =& RDF_Resource::factory(RDF_OWL_NS . 'someValuesFrom');
$OWL_SymmetricProperty =& RDF_Resource::factory(RDF_OWL_NS . 'SymmetricProperty');
$OWL_TransitiveProperty =& RDF_Resource::factory(RDF_OWL_NS . 'TransitiveProperty');
$OWL_unionOf =& RDF_Resource::factory(RDF_OWL_NS . 'unionOf');
$OWL_versionInfo =& RDF_Resource::factory(RDF_OWL_NS . 'versionInfo');

?>