<?php
/**
*   RDF Vocabulary Description Language 1.0: RDF Schema (RDFS) Vocabulary
*
*   @version $Id: RDFS.php 431 2007-05-01 15:49:19Z cweiske $
*   @author Daniel Westphal (dawe@gmx.de)
*   @package vocabulary
*
*   Wrapper, defining resources for all terms of the
*   RDF Schema (RDFS).
*   For details about RDFS see: http://www.w3.org/TR/rdf-schema/.
*   Using the wrapper allows you to define all aspects of
*   the vocabulary in one spot, simplifing implementation and
*   maintainence.
*/
// RDFS concepts
$RDFS_Resource = new Resource(RDF_SCHEMA_URI . 'Resource');
$RDFS_Literal = new Resource(RDF_SCHEMA_URI . 'Literal');
$RDFS_Class = new Resource(RDF_SCHEMA_URI . 'Class');
$RDFS_Datatype = new Resource(RDF_SCHEMA_URI . 'Datatype');
$RDFS_Container = new Resource(RDF_SCHEMA_URI . 'Container');
$RDFS_ContainerMembershipProperty = new Resource(RDF_SCHEMA_URI . 'ContainerMembershipProperty');
$RDFS_subClassOf = new Resource(RDF_SCHEMA_URI . 'subClassOf');
$RDFS_subPropertyOf = new Resource(RDF_SCHEMA_URI . 'subPropertyOf');
$RDFS_domain = new Resource(RDF_SCHEMA_URI . 'domain');
$RDFS_range = new Resource(RDF_SCHEMA_URI . 'range');
$RDFS_label = new Resource(RDF_SCHEMA_URI . 'label');
$RDFS_comment = new Resource(RDF_SCHEMA_URI . 'comment');
$RDFS_member = new Resource(RDF_SCHEMA_URI . 'member');
$RDFS_seeAlso = new Resource(RDF_SCHEMA_URI . 'seeAlso');
$RDFS_isDefinedBy = new Resource(RDF_SCHEMA_URI . 'isDefinedBy');


?>