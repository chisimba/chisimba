<?php

/* --------------------------- engine class ------------------------*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
   die("You cannot view this page directly");
}
// end security check

/**
* The Engine class that acts as the interface between the UI scripts and
* the back-end (via the index.php script that instantiates the Engine object
* and calls approriate methods to kick the ball off)
*
* @author Paul Scott
*
* $Id$
*/

require_once 'classes/core/object_class_inc.php';
require_once 'classes/core/dbtable_class_inc.php';

require_once 'classes/core/access_class_inc.php';
require_once 'classes/core/controller_class_inc.php';

//require_once 'lib/logging.php';

function globalPearErrorCallback($error) {
    log_debug($error);
}

class engine
{
    public $objEngine;
    protected $_objDb;
    protected $_objUser;
    protected $_objLoggedInUsers;
    protected $_objConfig;
    protected $_objLanguage;
    protected $_objDbConfig;
    protected $_layoutTemplate;
    protected $_pageTemplate = 'default_page_tpl.php';
    protected $_hasError = FALSE;
    protected $_errorField = '';
    protected $_content = '';
    protected $_layoutContent = '';
    protected $_moduleName = NULL;
    protected $_objActiveController = NULL;
    protected $_errorMessage = '';
    protected $_messages = NULL;
    protected $_sessionStarted = FALSE;
    protected $_templateVars = NULL;
    protected $_templateRefs = NULL;
    protected $_cachedObjects = NULL;
    protected $_enableAccessControl = TRUE;

    /**
    * Constructor. For use by application entry point script (usually /index.php)
    */
    public function __construct()
    {
        // we only initiate session handling here if a session already exists;
        // the session is only created once a successful login has taken place.
        // this has the small security benefit (albeit an obscurity based one)
        // of concealing any information about the session id generator from
        // unauthenticated users. (see Engine->do_login for session creation)
        if (isset($_REQUEST[session_name()])) {
            $this->sessionStart();
        }
        // initialise member objects that *this object* is dependent on, and thus
        // must be created on every request
        $this->_objDbConfig = $this->getObject('dbconfig', 'config');
        $this->getDbObj();
        $this->_objConfig = $this->getObject('config', 'config');
        //$this->_objUser =& $this->getObject('user', 'security');
        //$this->_objLanguage =& $this->getObject('language', 'language');


        //if ($this->_objUser->isLoggedIn())
        //{
        //    $this->_objUser->updateLogin();
        //}

        // other fields
        $this->_messages = array();
        $this->_templateVars = array();
        $this->_templateRefs = array();
        $this->_cachedObjects = array();

        // Get Layout Template from Config file
        $this->_layoutTemplate = $this->_objConfig->defaultLayoutTemplate();

        // Get Page Template from Config file
        $this->_pageTemplate = $this->_objConfig->defaultPageTemplate();
        $this->objEngine = $this;

    }

    /**
    * This method is for use by the application entry point. It dispatches the
    * request to the appropriate module controller, and then renders the returned template
    * inside of the appropriate layout template.
    */
    public function run($presetModuleName = NULL, $presetAction = NULL)
    {
        if (empty($presetModuleName)) {
            $requestedModule = strtolower($this->getParam('module', '_default'));
        } else {
            $requestedModule = $presetModuleName;
        }
        if (empty($presetAction)) {
            $requestedAction = strtolower($this->getParam('action', ''));
        } else {
            $requestedAction = $presetAction;
        }
        list($template, $moduleName) = $this->_dispatch($requestedAction, $requestedModule);
        if ($template != NULL) {
            $this->_content = $this->_callTemplate($template, $moduleName, 'content', TRUE);
            if (!empty($this->_layoutTemplate)) {
                $this->_layoutContent = $this->_callTemplate($this->_layoutTemplate,
                                                             $moduleName,
                                                             'layout',
                                                             TRUE);
            }
            else {
                $this->_layoutContent = $this->_content;
            }
            if (!empty($this->_pageTemplate)) {
                $this->_callTemplate($this->_pageTemplate, $moduleName, 'page');
            }
            else {
                echo $this->_layoutContent;
            }
        }
        $this->_finish();
    }

    /**
     * Method to load a class definition from the given module.
     * Used when you wish to instantiate objects of the class yourself.
     *
     * @param $name string The name of the class to load
     * @param $moduleName string The name of the module to load the class from (optional)
     */
    public function loadClass($name, $moduleName = '')
    {
        if ($name == 'config' && $moduleName == 'config' && $this->_objConfig) {
            // special case: skip if config and objConfig exists, this means config
            // class is already loaded using relative path, and an attempt to load with absolute
            // path will fail because the require_once feature matches filenames exactly.
            return;
        }

        if ($moduleName == '_core') {
            $filename = "classes/core/".strtolower($name)."_class_inc.php";
        } else {
            $filename = "modules/".$moduleName."/classes/".strtolower($name)."_class_inc.php";
        }
        // add the site root path to make an absolute path if the config object has
        // been loaded
        if ($this->_objConfig) {
            $filename = $this->_objConfig->siteRootPath() . $filename;
        }
        if (!file_exists($filename)) {
            die ("Could not load class $name from module $moduleName: filename $filename");
        }
        $engine =& $this;
        require_once($filename);
    }

    /**
    * Method to return the db object. Evaluates lazily,
    * so class file is not included nor object instantiated
    * until needed.
    *
    * @return string The config object
    */
    public function &getDbObj()
    {
        // I'm keeping $_globalObjDb as a global for now, as it's so convenient to
        // just pick it up wherever its needed. I'd like to thing of a better
        // approach that doesn't involve it being a global, but until I do,
        // it'll live here. I'll also have a member field _objDb for consistency
        // with the other objects [Sean]
        global $_globalObjDb;
        if ($this->_objDb == NULL || $_globalObjDb == NULL) {
            // I intend to subsume dbconfig into main config,
            // no particular reason for it to be separate,
            // at which point the next two lines will be
            // redundant
            $this->_objDbConfig = $this->getObject('dbconfig', 'config');
            // Connect to the database
            require_once 'MDB2.php';
            $_globalObjDb = MDB2::factory($this->_objDbConfig->dbConString());
            if (PEAR::isError($_globalObjDb)) {
                // manually call the callback function here,
                // as we haven't had a chance to install it as
                // the error handler
                $this->_pearErrorCallback($_globalObjDb);
                return $_globalObjDb;
            }
            // keep a copy as a field as well
            $this->_objDb =& $_globalObjDb;
            // install the error handler
            $this->_objDb->setErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, '_pearErrorCallback'));
            // set the default fetch mode for the MDB2 to assoc, as that's
            // a much nicer mode than the default MDB2_FETCHMODE_ORDERED
            $this->_objDb->setFetchMode(MDB2_FETCHMODE_ASSOC);
            // include the dbtable base class for future use
        }
        return $this->_objDb;
    }

    /**
     * Method to return current page content.
     * For use within layout templates.
     *
     * @return string Content of rendered content script
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
    * Method to return the currently selected layout template name.
    *
    * @return string Name of layout template
    */
    public function getLayoutTemplate()
    {
        return $this->_layoutContent;
    }

    /**
    * Method to set the name of the layout template to use.
    *
    * @param string $templateName The name of the layout template to use
    */
    public function setLayoutTemplate($templateName)
    {
        $this->_layoutTemplate = $templateName;
    }

    /**
    * Method to return the currently selected layout template name.
    *
    * @return string Name of layout template
    */
    public function getPageTemplate()
    {
        return $this->_pageTemplate;
    }

    /**
    * Method to set the name of the page template to use.
    *
    * @param string $templateName The name of the page template to use
    */
    public function setPageTemplate($templateName)
    {
        $this->_pageTemplate = $templateName;
    }

    /**
     * Method to get a new instance of a class from the given module.
     * Note that this relies on the naming convention for class files
     * being adhered to, e.g. class moduleAdmin should live in file:
     * 'moduleadmin_class_inc.php'.
     * This engine object is offered to the constructor as a parameter
     * when creating a new object although it need not be used.
     *
     * @param $name string The name of the class to load
     * @param $moduleName string The name of the module to load the class from
     * @return mixed The object asked for
     */
    public function &newObject($name, $moduleName)
    {
        $this->loadClass($name, $moduleName);
        $objNew = new $name($this, $moduleName);
        return $objNew;
    }

    /**
     * Method to get an instance of a class from the given module.
     * If this is the first call for that class a new instance will be created,
     * otherwise the existing instance will be returned.
     * Note that this relies on the naming convention for class files
     * being adhered to, e.g. class moduleAdmin should live in file:
     * 'moduleadmin_class_inc.php'.
     * This engine object is offered to the constructor as a parameter
     * when creating a new object although it need not be used.
     *
     * @param $name string The name of the class to load
     * @param $moduleName string The name of the module to load the class from
     * @return mixed The object asked for
     */
    public function &getObject($name, $moduleName)
    {
        $instance = NULL;
        if (isset($this->_cachedObjects[$moduleName][$name]))
        {
            // Changed to give link to object
            $instance = &$this->_cachedObjects[$moduleName][$name];
        }
        else
        {
            $this->loadClass($name, $moduleName);
            $instance = new $name($this, $moduleName);
            if (is_null($instance)) {
                die("Could not instantiate class $name from module $moduleName");
            }
            // first check that the map for the given module exists
            if (!isset($this->_cachedObjects[$moduleName]))
            {
                $this->_cachedObjects[$moduleName] = array();
            }
            // now store the instance in the map
            $this->_cachedObjects[$moduleName][$name] =& $instance;
        }
        return $instance;
    }

    /**
    * Method to return a template variable. These are used to pass
    * information from module to template.
    *
    * @param $name string The name of the variable
    * @param $default mixed The value to return if the variable is unset (optional)
    * @return mixed The value of the variable, or $default if unset
    */
    public function getVar($name, $default = NULL)
    {
        return isset($this->_templateVars[$name])
                   ? $this->_templateVars[$name]
                   : $default;
    }

    /**
    * Method to set a template variable. These are used to pass
    * information from module to template.
    *
    * @param $name string The name of the variable
    * @param $val mixed The value to set the variable to
    */
    public function setVar($name, $val)
    {
        $this->_templateVars[$name] = $val;
    }

    /**
    * Method to return a template reference variable. These are used to pass
    * objects from module to template.
    *
    * @param $name string The name of the reference variable
    * @return mixed The value of the reference variable, or NULL if unset
    */
    public function &getVarByRef($name)
    {
        return isset($this->_templateRefs[$name])
                   ? $this->_templateRefs[$name]
                   : NULL;
    }

    /**
    * Method to set a template refernce variable. These are used to pass
    * objects from module to template.
    *
    * @param $name string The name of the reference variable
    * @param $ref mixed A reference to the object to set the reference variable to
    */
    public function setVarByRef($name, &$ref)
    {
        $this->_templateRefs[$name] =& $ref;
    }

    /**
     * Method to append a value to a template variable holding an array. If the
     * array does not exist, it is created.
     * If the array contains the value, it is not added to prevent duplication.
     * @param string $name The name of the variable holding an array
     * @param mixed $value The value to append to the array
     */
    public function appendArrayVar($name, $value)
    {
        if (!isset($this->_templateVars[$name])) {
            $this->_templateVars[$name] = array();
        }
        if (!is_array($this->_templateVars[$name])) {
            die("Attempt to append to a non-array template variable $name");
        }
        if (!in_array($value, $this->_templateVars[$name])) {
            $this->_templateVars[$name][] = $value;
        }
    }

    /**
    * Method to return a request parameter (i.e. a URL query parameter,
    * a form field value or a cookie value).
    *
    * @param $name string The name of the parameter
    * @param $default mixed The value to return if the parameter is unset (optional)
    * @return mixed The value of the parameter, or $default if unset
    */
    public function getParam($name, $default = NULL)
    {
        return isset($_REQUEST[$name])
            ? is_string($_REQUEST[$name])
                ? trim($_REQUEST[$name])
                : $_REQUEST[$name]
            : $default;

    }

    /**
    * Method to return a request parameter (i.e. a URL query parameter,
    * a form field value or a cookie value).
    *
    * @param $name string The name of the parameter
    * @param $default mixed The value to return if the parameter is unset (optional)
    * @return mixed The value of the parameter, or $default if unset
    */
    public function getArrayParam($name, $default = NULL)
    {
        if ((isset($_REQUEST[$name]))&&(is_array($_REQUEST[$name]))){
            return $_REQUEST[$name];
        } else {
            return $default;
        }
    }

    /**
    * Method to set a session value.
    *
    * @param $name string The name of the session value
    * @param $val mixed The value to set the session value to
    */
    public function setSession($name, $val)
    {
        if (!$this->_sessionStarted) {
            $this->sessionStart();
        }
        $_SESSION[$name] = $val;
    }

    /**
    * Method to return a session value.
    *
    * @param $name string The name of the session value
    * @param $default mixed The value to return if the session value is unset (optional)
    * @return mixed the value of the parameter, or $default if unset
    */
    public function getSession($name, $default = NULL)
    {
        $val = $default;
        if (isset($_SESSION[$name])) {
            $val = $_SESSION[$name];
        }
        return $val;
    }

    /**
    * Method to unset a session parameter.
    *
    * @param $name string The name of the session parameter
    */
    public function unsetSession($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Method to kick off the session
     *
     * @return bool TRUE on success
     */
    public function sessionStart()
    {
        session_start();
        $this->_sessionStarted = TRUE;
    }

    /**
    * Method to set the global error message, and an error field if appropriate
    *
    * @param $errormsg string The error message
    * @param $field string The name of the field the error applies to (optional)
    * @return FALSE
    */
    public function setErrorMessage($errormsg, $field = NULL)
    {
        if (!$this->_hasError) {
            $this->_errorMessage = $errormsg;
            $this->_hasError = TRUE;
        }
        if ($field) {
            $this->_errorField = $field;
        }
        // error return code if needed by caller
        return FALSE;
    }

    /**
    * Method to add a global system message.
    *
    * @param $msg string The message
    */
    public function addMessage($msg)
    {
        $this->_messages[] = $msg;
    }

    /**
     * Method to call a further action within a module
     *
     * @param string $action Action to perform next
     * @param array $params Parameters to pass to action
     */
    public function nextAction($action, $params = array())
    {
        list($template, $_) = $this->_dispatch($action, $this->_moduleName);
        return $template;
    }

    /**
    * Method to return an application URI. All URIs pointing at the application
    * must be generated by this method. It is recommended that an action parameter
    * is used to indicate the action being performed.
    * The $mode parameter allows the use of a push/pop mechanism for storing
    * user context for return later. **This needs more work, both implementation
    * and documentation **
    *
    * @param array $params Associative array of parameter values
    * @param string $module Name of module to point to (blank for core actions)
    * @param string $mode The URI mode to use, must be one of 'push', 'pop', or 'preserve'
    * @param string $omitServerName flag to produce relative URLs
    * @returns string $uri the URL
    */
    public function uri($params = array(), $module = '', $mode = '', $omitServerName=FALSE)
    {
        if (!empty($action)) {
            $params['action'] = $action;
        }
        if ($omitServerName){
            $uri=$_SERVER['PHP_SELF'];
        } else {
            $uri = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        }
        if ($mode == 'push' && $this->getParam('_pushed_action')) {
            $mode = 'preserve';
        }
        if ($mode == 'pop') {
            $params['module'] = $this->getParam('_pushed_module', '');
            $params['action'] = $this->getParam('_pushed_action', '');
        }
        if (in_array($mode, array('push', 'pop', 'preserve'))) {
            $excluded = array('action', 'module');
            if ($mode == 'pop') {
                $excluded[] = '_pushed_action';
                $excluded[] = '_pushed_module';
            }
            foreach ($_GET as $key => $value) {
                if (!isset($params[$key]) && !in_array($key, $excluded)) {
                    // TODO: prefix on pushed values to protect them
                    $params[$key] = $value;
                }
            }
            if ($mode == 'push') {
                $params['_pushed_module'] = $this->_moduleName;
                $params['_pushed_action'] = $this->_action;
            }
        }
        elseif ($mode != '') {
            //log_objError('Incorrect URI mode in Engine::uri');
            die("Incorrect URI mode in Engine::uri");
        }
        if (count($params)>1){
            $params=array_reverse($params,TRUE);
        }
        $params['module'] = $module;
        $params=array_reverse($params,TRUE);
        if (!empty($params)) {
            $output = array();

            foreach ($params as $key => $item) {
                if ($item != NULL) {
                    $output[] = urlencode($key)."=".urlencode($item);
                }
            }
            $uri .= '?'.implode('&', $output);
        }

        // Replace & with &amp; to make it more standards compliant
        $uri = str_replace('&', '&amp;', $uri);
        return $uri;

    }

    /**
     * Method to generate a URI to a static resource stored in a module.
     * The resource should be stored within the 'resources' subdirectory of
     * the module directory.
     *
     * @param string $resourceFile The path to the file within the resources
     *                 subdirectory of the module
     * @param string $moduleName The name of the module the resource belongs to
     */
    public function getResourceUri($resourceFile, $moduleName)
    {
        return 'modules/' . $moduleName . '/resources/' . $resourceFile;
    }

    /**
     * Method that generates a URI to a static javascript
     * file that is stored in the resources folder in the subdirectory
     * in the modules directory
     *
     *  @param string $javascriptFile The javascript file name
     * @param string $moduleName The name of the module that the script is in
     */
    public function getJavaScriptFile($javascriptFile, $moduleName)
    {
        return '<script type="text/javascript" src="'
            . $this->getResourceUri($javascriptFile, $moduleName)
            . '"></script>';
    }

    /**
    * Method to output javascript that will display system error message and/or
    * system messages as set by setErrorMessage and addMessage
    */
    public function putMessages()
    {
        $str = '';
        if ($this->_hasError) {
            $str .= '<script language="JavaScript" type="text/javascript">'
                .'alert("'.$this->javascript_escape($this->_errorMessage).'");'
                .'</script>';
        }
        foreach ($this->_messages as $msg) {
            $str .= '<script language="JavaScript" type="text/javascript">'
                .'alert("'.$this->javascript_escape($msg).'");'
                .'</script>';
        }
        echo $str;
    }

    /**
     * Method to be called on PEAR error
     *
     * @param object $error
     */
    public function _pearErrorCallback($error)
    {
        // TODO: note $error->getMessage() returns a shorter and friendlier but
        //       less informative message, for production should use getMessage
        //       (make a config option?)
        $msg = "<p>The system crapped out with the following error</p><br>";
        $msg .= $error->getMessage();
        die($msg);
        $this->setErrorMessage($msg);

    }


    /**
     * ********************** Protected and Private Methods ***********************
     */

    /**
    * Main dispatch method. Called by run to dispatch this request
    * to the appropriate module, as specified by the 'module'
    * request parameter.
    *
    * @return list(string, string) Template name and module name
    */
    protected function _dispatch($action, $requestedModule)
    {
        $this->_action = $action;strtolower($this->getParam('action', ''));
        if (!$this->_loadModule($requestedModule)) {
            $this->_loadModule('_default');
            if (!$this->_objActiveController) {
                die('Default module not found!');
            }
            $this->setErrorMessage("Module {$requestedModule} not found");
        }

	if ($this->_objActiveController->sendNoCacheHeaders($this->_action)) {
	    // ensure no caching
	    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");              // Date in the past
	    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	    header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
	    header("Cache-Control: post-check=0, pre-check=0", false);
	    header("Pragma: no-cache");                                    // HTTP/1.0
	}

        if ((!$this->_objActiveController->requiresLogin($this->_action))
            || ($this->_objUser->isLoggedIn())) {
            return array($this->_dispatchToModule($this->_objActiveController, $this->_action),
                         $this->_moduleName);
        }
        else {
            if (!$this->_loadModule('security')) {
                die('Security module not found!');
            }
            $this->_moduleName = 'security';
            return array($this->_dispatchToModule($this->_objActiveController, 'showlogin'),
                         $this->_moduleName);
        }
    }

    /**
     * Method to load a module controller class and create a new
     * object of that class.
     * TODO: make main module an actual module, and if no module requested,
     * load that module (should be a configurable name)
     *
     * @param $moduleName string The name of the module to load
     * @return controller-subclass The new module controller object
    */
    protected function _loadModule($moduleName)
    {
        if ($moduleName == '_default') {
            $moduleName = $this->_objConfig->defaultModuleName();
        }

        $controllerFile = "modules/" . $moduleName . "/controller.php";
        $objActiveController = NULL;
        if (file_exists($controllerFile)
                && include_once $controllerFile) {
            $this->_objActiveController = new $moduleName($this, $moduleName);
        }
        if ($this->_objActiveController) {
            $this->_moduleName = $moduleName;
            return TRUE;
        }
        return FALSE;
    }

    /**
    * Method to dispatch request to given module, providing given action.
    * If no module object is provided, the main module is dispatched to.
    * TODO: eliminate main handling here when main becomes a module.
    *       Can probably eliminate this method altogether at that point.
    *
    *  @param $objActiveController controller-subclass The module controller to
    *                       dispatch to (or NULL for main)
    *  @param $action string The action parameter
    *  @return string Template name returned from dispatch method
    */
    protected function _dispatchToModule(&$module, $action)
    {
        if ($module) {
            $tpl = $this->_enableAccessControl
                ? $module->dispatchControl($module,$action) // with module access control
                : $module->dispatch($action); // without module access control
            return $tpl;
        }
        else {
            return $this->_getLoggedInTemplate();
        }
    }

    /**
    * Method to find the given template, either in the given module's template
    * subdir (if a module is specified) or in the core templates subdir.
    * Type must be 'content' or 'layout'
    *
    * @param $tpl string The name of the template to find,
    *                    including file extension but excluding path
    * @param $moduleName string The name of the module to search (can be empty to search only core)
    * @param $type string The type of template to load: 'content' or 'layout' are current options
    * @return string The full path to the found template
    */
    protected function _findTemplate($tpl, $moduleName, $type)
    {
        $path = '';
        if (!empty($moduleName)) {
            $path = $this->_objConfig->siteRootPath()
                . "modules/${moduleName}/templates/${type}/${tpl}";
        }
        if (empty($path) || !file_exists($path)) {
            $firstpath = $path;
            $path = $this->_objConfig->siteRootPath() . "templates/${type}/${tpl}";
            if (!file_exists($path))
            {
                die("Template $tpl not found (looked in $firstpath)!");
            }
        }
        return $path;
    }

    /**
    * Method to call the given template, looking first at the given modules templates
    * and then at the core templates (uses _findTemplate).
    * Output is either buffered ($buffer = TRUE) and returned as a string, or send directly
    * to browser.
    *
    * @param $tpl string Name of template to call, including file extension but excluding path
    * @param $moduleName string The name of the module to search for the template
    *                           (if empty, search core)
    * @param $type string The type of template to call: 'content' or 'layout'
    * @param $buffer TRUE|FALSE If TRUE buffer output and return as string, else send to browser
    * @return string|NULL If buffering returns output, else returns NULL
    */
    protected function _callTemplate($tpl, $moduleName, $type, $buffer = FALSE)
    {
        return $this->_objActiveController->callTemplate($tpl, $type, $buffer);
    }

    /**
    * Method to clean up at end of page rendering.
    */
    protected function _finish()
    {
        $this->_objDb->disconnect();
    }

    /**
     * Method that escapes a string suitable for inclusion as a JavaScript
     * string literal. Add's backslashes for
     *
     * @param $str string String to escape
     * @return string Escaped string
     */
    protected function javascript_escape($str)
    {
        return addcslashes($str, "\0..\37\"\'\177..\377");
    }
}
?>