<?php
// ----------------------------------------------------------------------------------
// dataset Package
// ----------------------------------------------------------------------------------
//
// Description               : dataset package
//
// Author: Daniel Westphal	<http://www.d-westphal.de>
//
// ----------------------------------------------------------------------------------

// Include ResModel classes
require_once( RDFAPI_INCLUDE_DIR . 'dataset/Dataset.php');
require_once( RDFAPI_INCLUDE_DIR . 'dataset/DatasetMem.php');
require_once( RDFAPI_INCLUDE_DIR . 'dataset/DatasetDb.php');
require_once( RDFAPI_INCLUDE_DIR . 'dataset/NamedGraphMem.php');
require_once( RDFAPI_INCLUDE_DIR . 'dataset/NamedGraphDb.php');
require_once( RDFAPI_INCLUDE_DIR . 'dataset/IteratorAllGraphsMem.php');
require_once( RDFAPI_INCLUDE_DIR . 'dataset/IteratorAllGraphsDb.php');
require_once( RDFAPI_INCLUDE_DIR . 'dataset/Quad.php');
require_once( RDFAPI_INCLUDE_DIR . 'dataset/IteratorFindQuadsMem.php');
require_once( RDFAPI_INCLUDE_DIR . 'dataset/IteratorFindQuadsDb.php');
require_once( RDFAPI_INCLUDE_DIR . 'syntax/TriXParser.php');
require_once( RDFAPI_INCLUDE_DIR . 'syntax/TriXSerializer.php');
?>