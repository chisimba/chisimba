<?php
/**
* index.php
*
* The entry point into the Chisimba application
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

// initialise the engine object
require_once 'classes/core/engine_class_inc.php';
$_globalObjEngine = new engine;

// engine object created by core_classes_inc.php
$_globalObjEngine->run();

?>