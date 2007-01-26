<?php

/**
* index.php ()
*
* The entry point into the KEWL application
*
* @author Paul Scott based on methods by Sean Legassick
*
*/
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