<?php
// ----------------------------------------------------------------------------------
// Dublin Core Vocabulary
// ----------------------------------------------------------------------------------
// @version                  : $Id: DC.php 268 2006-05-15 05:28:09Z tgauss $
// Authors                   : Chris Bizer (chris@bizer.de)
//
// Description               : Wrapper, defining resources for all terms of the Dublin 
//							   Core Vocabulary. For details about DC see: http://dublincore.org/
// 							   Using the wrapper allows you to define all aspects of 
//                             the vocabulary in one spot, simplifing implementation and 
//                             maintainence. Working with the vocabulary, you should use 
//                             these resources as shortcuts in your code. 
//							   



 
// DC concepts
$DC_contributor = new Resource(DC_NS . 'contributor');    
$DC_coverage = new Resource(DC_NS . 'coverage');  
$DC_creator = new Resource(DC_NS . 'creator');  
$DC_date = new Resource(DC_NS . 'date');  
$DC_description = new Resource(DC_NS . 'description');  
$DC_format = new Resource(DC_NS . 'format');  
$DC_identifier = new Resource(DC_NS . 'identifier');  
$DC_language = new Resource(DC_NS . 'language');  
$DC_publisher = new Resource(DC_NS . 'publisher');  
$DC_rights = new Resource(DC_NS . 'rights');  
$DC_source = new Resource(DC_NS . 'source');  
$DC_subject = new Resource(DC_NS . 'subject');  
$DC_title = new Resource(DC_NS . 'title');  
$DC_type = new Resource(DC_NS . 'type');  

// Dublin Core Metadata Element Set (DCMES) 1.1
$DCMES_name = array('contributor', 'coverage', 'creator', 'date',
		    'description', 'format', 'identifier', 'language', 
		    'publisher', 'relation', 'rights', 'source', 
		    'subject', 'title', 'type');

foreach ($DCMES_name as $name) {
    $DCMES[$name] = new Resource(DC_NS . $name);
    $GLOBALS['DC_' . $name] = $DCMES[$name];
}

// Other Elements and Element Refinements
$DCTERM_name = array('abstract', 'accessRights', 'alternative', 'audience', 
		     'available', 'bibliographicCitation', 'conformsTo',
		     'created', 'dateAccepted', 'dateCopyrighted', 
		     'dateSubmitted', 'educationLevel', 'extent',
		     'hasFormat', 'hasPart', 'hasVersion', 
		     'isFormatOf', 'isPartOf', 'isReferencedBy', 'isReplacedBy',
		     'isRequiredBy', 'issued', 'isVersionOf', 'license',
		     'mediator', 'medium', 'modified', 'references',
		     'replaces', 'requires', 'rightsHolder', 'spatial',
		     'tableOfContents', 'temporal', 'valid');
foreach ($DCTERM_name as $name) {
    $DCTERM[$name] = new Resource(DCTERM_NS . $name);
}

// Encoding schemes
$scheme_name = array('Box', 'DCMIType', 'IMT', 'ISO3166', 'ISO639-2',
		     'LCC', 'LCSH', 'MESH', 'Period', 'Point',
		     'RFC1766', 'RFC3066', 'TGN', 'UDC', 'URI',
		     'W3CDTF');

foreach ($scheme_name as $name) {
   $DCTERM[$name] = new Resource(DCTERM_NS . $name);
}

// DCMI Type Vocabulary
$DCMITYPE_names = array('Collection', 'Dataset', 'Event', 'Image',
			'InteractiveResource', 'MovingImage', 'PhysicalObject',
			'Service', 'Software', 'Sound', 'StillImage', 'Text');

foreach ($DCMITYPE_names as $name) {
    $DCMITYPE[$name] = new Resource(DCMITYPE_NS . $name);
}

?>