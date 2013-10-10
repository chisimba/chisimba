<?php
/**
*   OWL Vocabulary
*
*   @version $Id: OWL.php 431 2007-05-01 15:49:19Z cweiske $
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

// OWL concepts
$OWL_AnnotationProperty = new Resource(OWL_NS . 'AnnotationProperty');
$OWL_AllDifferent = new Resource(OWL_NS . 'AllDifferent');
$OWL_allValuesFrom = new Resource(OWL_NS . 'allValuesFrom');
$OWL_backwardCompatibleWith = new Resource(OWL_NS . 'backwardCompatibleWith');
$OWL_cardinality = new Resource(OWL_NS . 'cardinality');
$OWL_Class = new Resource(OWL_NS . 'Class');
$OWL_complementOf = new Resource(OWL_NS . 'complementOf');
$OWL_Datatype = new Resource(OWL_NS . 'Datatype');
$OWL_DatatypeProperty = new Resource(OWL_NS . 'DatatypeProperty');
$OWL_DataRange = new Resource(OWL_NS . 'DataRange');
$OWL_DatatypeRestriction = new Resource(OWL_NS . 'DatatypeRestriction');
$OWL_DeprecatedClass = new Resource(OWL_NS . 'DeprecatedClass');
$OWL_DeprecatedProperty = new Resource(OWL_NS . 'DeprecatedProperty');
$OWL_distinctMembers = new Resource(OWL_NS . 'distinctMembers');
$OWL_differentFrom = new Resource(OWL_NS . 'differentFrom');
$OWL_disjointWith = new Resource(OWL_NS . 'disjointWith');
$OWL_equivalentClass = new Resource(OWL_NS . 'equivalentClass');
$OWL_equivalentProperty = new Resource(OWL_NS . 'equivalentProperty');
$OWL_FunctionalProperty = new Resource(OWL_NS . 'FunctionalProperty');
$OWL_hasValue = new Resource(OWL_NS . 'hasValue');
$OWL_incompatibleWith = new Resource(OWL_NS . 'incompatibleWith');
$OWL_imports = new Resource(OWL_NS . 'imports');
$OWL_intersectionOf = new Resource(OWL_NS . 'intersectionOf');
$OWL_InverseFunctionalProperty = new Resource(OWL_NS . 'InverseFunctionalProperty');
$OWL_inverseOf = new Resource(OWL_NS . 'inverseOf');
$OWL_maxCardinality = new Resource(OWL_NS . 'maxCardinality');
$OWL_minCardinality = new Resource(OWL_NS . 'minCardinality');
$OWL_Nothing = new Resource(OWL_NS . 'Nothing');
$OWL_ObjectClass = new Resource(OWL_NS . 'ObjectClass');
$OWL_ObjectProperty = new Resource(OWL_NS . 'ObjectProperty');
$OWL_ObjectRestriction = new Resource(OWL_NS . 'ObjectRestriction');
$OWL_oneOf = new Resource(OWL_NS . 'oneOf');
$OWL_onProperty = new Resource(OWL_NS . 'onProperty');
$OWL_Ontology = new Resource(OWL_NS . 'Ontology');
$OWL_priorVersion = new Resource(OWL_NS . 'priorVersion');
$OWL_Property = new Resource(OWL_NS . 'Property');
$OWL_Restriction = new Resource(OWL_NS . 'Restriction');
$OWL_sameAs = new Resource(OWL_NS . 'sameAs');
$OWL_sameClassAs = new Resource(OWL_NS . 'sameClassAs');
$OWL_sameIndividualAs = new Resource(OWL_NS . 'sameIndividualAs');
$OWL_samePropertyAs = new Resource(OWL_NS . 'samePropertyAs');
$OWL_someValuesFrom = new Resource(OWL_NS . 'someValuesFrom');
$OWL_SymmetricProperty = new Resource(OWL_NS . 'SymmetricProperty');
$OWL_Thing = new Resource(OWL_NS . 'Thing');
$OWL_TransitiveProperty = new Resource(OWL_NS . 'TransitiveProperty');
$OWL_unionOf = new Resource(OWL_NS . 'unionOf');
$OWL_versionInfo = new Resource(OWL_NS . 'versionInfo');

?>