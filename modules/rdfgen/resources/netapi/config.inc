<?php

// ----------------------------------------------------------------------------------
// RAP Net API Config
// ----------------------------------------------------------------------------------

/**
 * This file contains the configuration settings for the RAP Net API.
 *
 * @version  $Id: config.inc 268 2006-05-15 05:28:09Z tgauss $
 * @author Phil Dawes <pdawes@users.sf.net>
 *
 * @package netapi
 * @todo nothing
 * @access	public
 */

// ----------------------------------------------------------------------------------
// General
// ----------------------------------------------------------------------------------

// Defines the RAP include dirs
define("RDFAPI_INCLUDE_DIR", "../api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");

// Include RDQL Package
include_once( RDFAPI_INCLUDE_DIR . PACKAGE_RDQL);
include_once( RDFAPI_INCLUDE_DIR . PACKAGE_SPARQL);

// Allows clients to add data to models. 
define('NETAPI_ALLOW_ADD',FALSE);
// Allows clients to remove data from models. 
define('NETAPI_ALLOW_REMOVE',TRUE);

// ----------------------------------------------------------------------------------
// Model Map
// ----------------------------------------------------------------------------------

//$modelmap = array(
//	  "testmodel" => "db:http://sw.phildawes.net/2004/testmodel1",
//	  "testmodel2" => "db:http://sw.phildawes.net/2004/testmodel2"
//	  );

$modelmap = array(
	  "testmodel7" => "db:Manifest",
	  "testmodel" =>  "db:testmodel",
	  "testmodel4" => "db:manifest-extra",
	  "testmodel1" => "db:test1",
	  );	  


// ----------------------------------------------------------------------------------
// Database Configuration
// ----------------------------------------------------------------------------------

$NETAPI_DB_DRIVER = 'MySQL';
$NETAPI_DB_HOST = 'localhost';
$NETAPI_DB_DB = 'sparql_db';
$NETAPI_DB_USER = 'testuser';
$NETAPI_DB_PASS = '';

//$NETAPI_DB_DRIVER = 'ODBC';
//$NETAPI_DB_HOST = 'RDF_DB';
//$NETAPI_DB_DB = '';
//$NETAPI_DB_USER = '';
//$NETAPI_DB_PASS = '';
  
	  
?>
