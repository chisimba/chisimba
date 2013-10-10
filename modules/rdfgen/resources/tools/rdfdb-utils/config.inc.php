<?php

// error_reporting(E_ALL);

// ----------------------------------------------------------------------------------
// RDFDBUtils : Config file
// ----------------------------------------------------------------------------------

#
# Edit this to suit your setup! 
#

/** 
 * Configuration file
 * @version $Id: config.inc.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/


# Set this if you need to use a proxy for http access.

// $PROXYHOST="myproxy.com";
// $PROXYPORT="8080";

# Add each database you want to be available here.

$_DB=array();

// $i=0; 
// $_DB[$i]["type"]="mysql";
// $_DB[$i]["host"]="localhost";
// $_DB[$i]["port"]=""; 
// $_DB[$i]["dbName"]="rdf"; 
// $_DB[$i]["username"]="root";
// $_DB[$i]["password"]="";

//$i++

// $i=0; 
// $_DB[$i]["type"]="mysql";
// $_DB[$i]["host"]="localhost";
// $_DB[$i]["port"]=""; 
// $_DB[$i]["dbName"]="rdf"; 
// $_DB[$i]["username"]="root";
// $_DB[$i]["password"]="";


//etc...

##################################################
#
# You should not need to change anything below here.
#
##################################################


session_start();

define("RDFAPI_INCLUDE_DIR", "../../api/");

include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(RDFAPI_INCLUDE_DIR . PACKAGE_SYNTAX_RDF);
include(RDFAPI_INCLUDE_DIR . PACKAGE_SYNTAX_N3);


?>
