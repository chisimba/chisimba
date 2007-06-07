<?php

/**
* index.php 
*
* The entry point into the KEWL application
*
* @author Paul Scott based on methods by Sean Legassick
*
*/

// URL Re-writing
// by Jeremy O'Connor

$uri = $_SERVER["REQUEST_URI"];
//echo "[$uri]";
//exit();

// Matches module name
// e.g. http://localhost:8080/chisimba_framework2/app/index.php/blog/

if (preg_match('/^(.*)\/index.php\/(.+?)\/$/i', $uri, $matches)) {
	header( 'Location: '.$matches[1].'/index.php?module='.$matches[2] );
}

// Matches module name and action
// e.g. http://localhost:8080/chisimba_framework2/app/index.php/blog/viewblog/

if (preg_match('/^(.*)\/index.php\/(.+?)\/(.+?)\/$/i', $uri, $matches)) {
	header( 'Location: '.$matches[1].'/index.php?module='.$matches[2].'&action='.$matches[3] );
}

// Matches module name and action and id
// e.g. http://localhost:8080/chisimba_framework2/app/index.php/blog/viewblog/

if (preg_match('/^(.*)\/index.php\/(.+?)\/(.+?)\/(.+?)\/$/i', $uri, $matches)) {
	header( 'Location: '.$matches[1].'/index.php?module='.$matches[2].'&action='.$matches[3].'&id='.$matches[4] );
}

// End of URL Re-writing

// checks for configuration file, if none found loads installation page
if ( !file_exists( 'config/config.xml' ) ) {
	header( 'Location: installer/index.php' );
	exit();
}
// this is a security measure, this can be checked by the included scripts and the script
// execution aborted if it is not set.
$GLOBALS['kewl_entry_point_run'] = true;

// initialise error handling
//require('lib/errorhandling/ErrorHandler.inc');
//$error =& new ErrorHandler();

// initialise the engine object
require_once 'classes/core/engine_class_inc.php';
$_globalObjEngine =& new engine;

//log_debug("page rendering started at ".strftime("%T"));

// engine object created by core_classes_inc.php
$_globalObjEngine->run();

//echo $undefined_variable;
//log_debug("page rendering finished at ".strftime("%T"));

?>
