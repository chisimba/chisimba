<?php
/* -------------------- object class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * * Baseclass for all data and helper module classes in the KNG framework
 *
 * @author Paul Scott based on methods by Sean Legassick
 * @package core
 */
class object
{
    /**
     * The reference to the Engine object
     *
     * @var object
     */
    public $objEngine;

    /**
     * The module name
     *
     * @var string
     */
    public $moduleName;

    /**
     * The config object
     *
     * @var object
     */
    public $objConfig;

    /**
     * Constructor for the object class
     *
     * @access public
     * @param mixed $ &$objEngine The reference to the engine object
     * @param mixed $moduleName The name of the module
     * @return object instantiation against the engine object
     */
    public function __construct(&$objEngine, $moduleName)
    {
        $this->objEngine = &$objEngine;
        $this->moduleName = $moduleName;
        $this->init();
    }

    /**
     * * Method to initialise the object.
     *
     * @access public
     * @abstract Override in subclasses.
     * @param void
     * @return void
     */
    public function init()
    {
    }

    /**
     * Method to retrieve a parameter from the request.
     * i.e. the _REQUEST superglobal which contains all URI query
     * parameters, all form data, and all cookies
     *
     * @access public
     * @param string $name The name of the parameter.
     * @param mixed $default The value to return if the parameter is unset. (optional)
     * @return mixed The value of the parameter, or $default if unset
     */
    public function getParam($name, $default = NULL)
    {
        return $this->objEngine->getParam($name, $default);
    }


    /**
    * Similar to getParam above, but for arrays.
    *
    * @access public
    * @param string $name The name of the parameter.
    * @param mixed $default The value to return if the parameter is unset. (optional)
    * @return mixed The value of the parameter, or $default if unset
    */
    public function getArrayParam($name,$default=NULL)
    {
        return $this->objEngine->getArrayParam($name,$default);
    }

    /**
     * Method to retrieve a value from the session.
     * Note that a module-specific prefix is added to $name to prevent conflict with other modules. As long
     * as setSession is used to store values, this will be invisible to the
     * module author.
     *
     * @access public
     * @param string $name Name of session parameter to retrieve
     * @param mixed $default Default value to return if session parameter unset
     * @param string $module default to _MODULE_ if unset
     * @return mixed The value of the named session parameter or $default if unset
     */
    public function getSession($name, $default = NULL,$module='_MODULE_')
    {
        if ($module=='_MODULE_'){
            $key=$this->moduleName."~".$name;
        } else {
            $key=$module."~".$name;
        }
        if (($module=='')||($module=='_site')){
            $key=$name;
        }
        $key=$this->sessionKey().$key;
        return $this->objEngine->getSession($key, $default);
    }

    /**
     * Stores a value in the session.
     * Note that a module-specific prefix is added to $name to prevent conflict with other
     * modules. As long as setSession is used to store values, this will be invisible to the
     * module author.
     *
     * @access public
     * @param string $name Name of session parameter to store
     * @param mixed $value Value to store in session parameter
     * @param string $module default to _MODULE_ if unset
     * @return set engine session property
     */
    public function setSession($name, $value,$module='_MODULE_')
    {
        if ($module=='_MODULE_'){
            $key=$this->moduleName."~".$name;
        } else {
            $key=$module."~".$name;
        }
        if (($module=='')||($module=='_site')){
            $key=$name;
        }
        $key=$this->sessionKey().$key;
        $this->objEngine->setSession($key, $value);
    }

    /**
     * Unsets a session parameter.
     * Note that a module-specific prefix is added to $name to prevent conflict with other
     * modules. As long as setSession is used to store values, this will be invisible to the
     * module author.
     *
     * @access public
     * @param string $name Name of session parameter to unset
     * @param string $module default to _MODULE_ if unset
     * @return session set to NULL in engine object
     */
    public function unsetSession($name,$module='_MODULE_')
    {
        if ($module=='_MODULE_'){
            $key=$this->moduleName."~".$name;
        } else {
            $key=$module."~".$name;
        }
        if (($module=='')||($module=='_site')){
            $key=$name;
        }
        $key=$this->sessionKey().$key;
        $this->objEngine->unsetSession($key);
    }

    /**
    * Method to create installation-specific key for Session variables
    *
    * @access public
    * @param void
    * @return string $key
    */
    public function sessionKey()
    {
	return "";
	// Code not executed, since it doesn't work on all platforms for some reason
        if (!isset($this->sessionkey)){
            $str=md5($_SERVER['SCRIPT_FILENAME']);
            $this->sessionkey=substr($str,0,5).'~';
        }
        return $this->sessionkey;
    }

    /**
     * Method to return an application URI.
     * All URIs pointing at the application must be generated by this method. It is
     * recommended that an action parameter is used to indicate the action being
     * performed. The $moduleName parameter is optional - if omitted the URI will
     * point to the current module.
     * The $mode parameter allows the use of a push/pop mechanism for storing
     * user context for return later. **This needs more work, both implementation
     * and documentation **
     *
     * @access public
     * @param array $params Associative array of parameter values
     * @param string $moduleName Name of module to point to (blank for core actions)
     * @param string $uriMode string The URI mode to use, must be one of 'push', 'pop', or 'preserve'
     * @param string $omitServerName flag to produce relative URLs
     * @return mixed Returns the application URI
     */
    public function uri($params, $moduleName = '', $uriMode = '', $omitServerName=FALSE)
    {
        if (empty($moduleName)) {
            $moduleName = $this->moduleName;
        }
        return $this->objEngine->uri($params, $moduleName, $uriMode,$omitServerName);
    }

    /**
     * Method to load a class definition.
     * Use when you wish to instantiate the class yourself.
     * If module isn't given the class is loaded from the current module
     *
     * @access public
     * @param string $name The name of the class to load
     * @param string $moduleName The name of the module to load the class from (optional)
     * @return The class reference in the engine parent
     */
    public function loadClass($name, $moduleName = '')
    {
        if (empty($moduleName)) {
            $moduleName = $this->moduleName;
        }
        return $this->objEngine->loadClass($name, $moduleName);
    }

    /**
     * Method to get a new instance of a class from the given module.
     * Note that this relies on the naming convention for class files
     * being adhered to, e.g. class moduleAdmin should live in file:
     * 'moduleadmin_class_inc.php'.
     * This engine object is offered to the constructor as a parameter
     * when creating a new object although it need not be used.
     *
     * @access public
     * @param string $name The name of the class to load
     * @param string $moduleName The name of the module to load the class from
     * @return mixed The reference to the new object asked for
     */
    public function &newObject($name, $moduleName='')
    {
        if (empty($moduleName)) {
            $moduleName = $this->moduleName;
        }
        return $this->objEngine->newObject($name, $moduleName);
    }

    /**
     * Method to load a new instance of a class from the given module.
     * Note that this relies on the naming convention for class files
     * being adhered to, e.g. class moduleAdmin should live in file
     * 'moduleadmin_class_inc.php'. If module isn't given the class
     * is loaded from the current module
     *
     * @access public
     * @param string $name The name of the class to load
     * @param string $moduleName The name of the module to load the class from (optional)
     * @return mixed The object asked for
     */
    public function &getObject($name, $moduleName = '')
    {
        if (empty($moduleName)) {
            $moduleName = $this->moduleName;
        }
        return $this->objEngine->getObject($name, $moduleName);
    }

    /**
     * Method to generate a URI to a static resource stored in a module.
     * The resource should be stored within the 'resources' subdirectory of
     * the module directory.
     *
     * @access public
     * @param string $ The path to the file within the resources subdirectory of the module
     * @param string $ The name of the module the resource belongs to (optional)
     * @return mixed The URI of the resource asked for.
     */
    public function getResourceUri($resourcePath, $moduleName = '')
    {
        if (empty($moduleName)) {
            $moduleName = $this->moduleName;
        }
        return $this->objEngine->getResourceUri($resourcePath, $moduleName);
    }

	/**
	 * Method that generates a URI to a static javascrict
	 * file that is stored in the resources folder in the subdirectory
	 * in the modules directory
	 *
	 * @access public
	 * @param string $javascriptFile : The javascript file name and path
	 * @param string $moduleName : The name of the module that the script is in
	 * @return javascript file to engine::getJavaScriptFile()
	 */
	public function getJavascriptFile($javascriptFile,$moduleName='')
	{
		if(empty($moduleName)){
			$moduleName=$this->moduleName;
		}
		return $this->objEngine->getJavascriptFile($javascriptFile,$moduleName);

	}

    /**
    * Method to append a value to a template variable holding an array. If the
    * array does not exist, it is created
    *
    * @access public
    * @param string $name The name of the variable holding an array
    * @param mixed $value The value to append to the array
    * @return Engine::AppendarrayVar
    */
    public function appendArrayVar($name, $value)
    {
        return $this->objEngine->appendArrayVar($name, $value);
    }

    /**
    * Method to set a template variable. These are used to pass
    * information from module to template.
    *
    * @access public
    * @param $name string The name of the variable
    * @param $val mixed The value to set the variable to
    * @return string as associative array of template name
    */
    public function setVar($name, $value)
    {
        return $this->objEngine->setVar($name, $value);
    }
}
?>