<?php 
// ----------------------------------------------------------------------------------
// Dublin Core Vocabulary
// ----------------------------------------------------------------------------------
// Version                   : 0.4
// Authors                   : Chris Bizer (chris@bizer.de)

// Description               : Wrapper, defining resources for all terms of the Dublin
// Core Vocabulary. For details about DC see: http://dublincore.org/
// Using the wrapper allows you to define all aspects of
// the vocabulary in one spot, simplifing implementation and
// maintainence. Working with the vocabulary, you should use
// these resources as shortcuts in your code.
// 
// define DC namespace
define('RDF_DC_NS', 'http://purl.org/dc/elements/1.0/');
// DC concepts
$DC_contributor =& RDF_Resource::factory(RDF_DC_NS . 'contributor');
$DC_coverage =& RDF_Resource::factory(RDF_DC_NS . 'coverage');
$DC_creator =& RDF_Resource::factory(RDF_DC_NS . 'creator');
$DC_date =& RDF_Resource::factory(RDF_DC_NS . 'date');
$DC_description =& RDF_Resource::factory(RDF_DC_NS . 'description');
$DC_format =& RDF_Resource::factory(RDF_DC_NS . 'format');
$DC_identifier =& RDF_Resource::factory(RDF_DC_NS . 'identifier');
$DC_language =& RDF_Resource::factory(RDF_DC_NS . 'language');
$DC_publisher =& RDF_Resource::factory(RDF_DC_NS . 'publisher');
$DC_rights =& RDF_Resource::factory(RDF_DC_NS . 'rights');
$DC_source =& RDF_Resource::factory(RDF_DC_NS . 'source');
$DC_subject =& RDF_Resource::factory(RDF_DC_NS . 'subject');
$DC_title =& RDF_Resource::factory(RDF_DC_NS . 'title');
$DC_type =& RDF_Resource::factory(RDF_DC_NS . 'type');

?>