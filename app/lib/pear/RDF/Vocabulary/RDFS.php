<?php 
// ----------------------------------------------------------------------------------
// RDF Vocabulary Description Language 1.0: RDF Schema (RDFS) Vocabulary
// ----------------------------------------------------------------------------------
// Version                   : 0.4
// Authors                   : Daniel Westphal (dawe@gmx.de)

// Description               : Wrapper, defining resources for all terms of the
// RDF Schema (RDFS).
// For details about RDF see: http://www.w3.org/TR/rdf-schema/.
// Using the wrapper allows you to define all aspects of
// the vocabulary in one spot, simplifing implementation and
// maintainence. Working with the vocabulary, you should use
// these resources as shortcuts in your code.
// 
// RDFS concepts
$RDFS_Resource =& RDF_Resource::factory(RDF_SCHEMA_URI . 'Resource');
$RDFS_Literal =& RDF_Resource::factory(RDF_SCHEMA_URI . 'Literal');
$RDFS_Class =& RDF_Resource::factory(RDF_SCHEMA_URI . 'Class');
$RDFS_Datatype =& RDF_Resource::factory(RDF_SCHEMA_URI . 'Datatype');
$RDFS_Container =& RDF_Resource::factory(RDF_SCHEMA_URI . 'Container');
$RDFS_ContainerMembershipProperty =& RDF_Resource::factory(RDF_SCHEMA_URI . 'ContainerMembershipProperty');
$RDFS_subClassOf =& RDF_Resource::factory(RDF_SCHEMA_URI . 'subClassOf');
$RDFS_subPropertyOf =& RDF_Resource::factory(RDF_SCHEMA_URI . 'subPropertyOf');
$RDFS_domain =& RDF_Resource::factory(RDF_SCHEMA_URI . 'domain');
$RDFS_range =& RDF_Resource::factory(RDF_SCHEMA_URI . 'range');
$RDFS_label =& RDF_Resource::factory(RDF_SCHEMA_URI . 'label');
$RDFS_comment =& RDF_Resource::factory(RDF_SCHEMA_URI . 'comment');
$RDFS_member =& RDF_Resource::factory(RDF_SCHEMA_URI . 'member');
$RDFS_seeAlso =& RDF_Resource::factory(RDF_SCHEMA_URI . 'seeAlso');
$RDFS_isDefinedBy =& RDF_Resource::factory(RDF_SCHEMA_URI . 'isDefinedBy');

?>