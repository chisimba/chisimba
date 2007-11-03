<?php
/**
*
* Chisimba bridge is a singleton class for connecting non-chisimba
* code into chisimba.
*
* Chisimba bridge uses the singleton design pattern to ensure a class has only one instance
* when it is used outside the core framework itself, and to provide a global point of access
* to it. It achieves this by only creating a new instance the first time it is referenced,
* and thereafter it simply returns the handle to the existing instance. It ahould only only be
* used to instantiate core classes to provide access to Chsimba core outside of the framework.
* Normally it is only used to instantiate the engine, which gives access to all framework
* classes by default.
*
*
* Created on 02 Nov 2007
*
* @author Derek keats
* @package Chisimba
*
*/

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
require_once 'classes/core/engine_class_inc.php';

class bridge
{
    public function __construct() {}

    public function startBridge()
    {
        return $this->getInstance("engine");
    }

    /**
    *
    * Implements the 'singleton' design pattern to return the instance of
    * a class if one exists or instantiate it if not.
    *
    * @param string The class handle
    *
    */
    public function getInstance ($class)
    {
        //array of instance names
        static $instances = array();
        if (!array_key_exists($class, $instances)) {
            // instance does not exist, so create it
            $instances[$class] = new $class;
        }
        $instance =& $instances[$class];
        return $instance;
    }
}
?>