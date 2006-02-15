<?php

/* --------------------------- engine class ------------------------*/

// security check - must be included in all scripts
//if (!$GLOBALS['kewl_entry_point_run'])
//{
//    die("You cannot view this page directly");
//}
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

//require_once 'classes/core/object_class_inc.php';
//require_once 'classes/core/access_class_inc.php';
//require_once 'classes/core/dbtable_class_inc.php';
//require_once 'classes/core/controller_class_inc.php';

//require_once 'lib/logging.php';
function globalPearErrorCallback($error) {
    log_debug($error);
}

class engine
{
    public $_objDb;

    public $_objUser;
    public $_objLoggedInUsers;
    public $_objConfig;
    public $_objLanguage;

    // deprecated objects (i.e. they will go soon)
    public $_objDbConfig;
    // end deprecated

    public $_layoutTemplate;
    public $_pageTemplate = 'default_page_tpl.php';
    public $_hasError = FALSE;
    public $_errorField = '';
    public $_content = '';
    public $_layoutContent = '';
    public $_moduleName = NULL;
    public $_objActiveController = NULL;
    public $_errorMessage = '';
    public $_messages = NULL;
    public $_sessionStarted = FALSE;
    public $_templateVars = NULL;
    public $_templateRefs = NULL;
    public $_cachedObjects = NULL;
    public $_enableAccessControl = TRUE;

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
        $this->_objUser =& $this->getObject('user', 'security');
        $this->_objLanguage =& $this->getObject('language', 'language');


        if ($this->_objUser->isLoggedIn())
        {
            $this->_objUser->updateLogin();
        }

        // other fields
        $this->_messages = array();
        $this->_templateVars = array();
        $this->_templateRefs = array();
        $this->_cachedObjects = array();

        // Get Layout Template from Config file
        $this->_layoutTemplate = $this->_objConfig->defaultLayoutTemplate();

        // Get Page Template from Config file
        $this->_pageTemplate = $this->_objConfig->defaultPageTemplate();

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
        // sbeen loaded
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
            $this->_objDbConfig =& $this->getObject('dbconfig', 'config');
            // Connect to the database
            require_once 'DB.php';
            $_globalObjDb = DB::connect($this->_objDbConfig->dbConString());
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
            $this->_objDb->setErrorHandling(PEAR_ERROR_CALLBACK,
                                            array(&$this, '_pearErrorCallback'));
            // set the default fetch mode for the DB to assoc, as that's
            // a much nicer mode than the default DB_FETCHMODE_ORDERED
            $this->_objDb->setFetchMode(DB_FETCHMODE_ASSOC);
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
        $objNew =& new $name($this, $moduleName);
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
            $instance =& new $name($this, $moduleName);
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
}
?>