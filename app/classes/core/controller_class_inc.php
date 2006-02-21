<?php
/* -------------------- controller class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * Baseclass for a module's controller class
 *
 * @author Paul Scott based on methods by Sean Legassick
 * @package core
 */
class controller extends access
{
    /**
     * Public variable to hold the derived controller name
     *
     * @var string
     */
    public $controllerName;

    /**
     * Constructor for the controller class.
     *
     * @param object $objEngine by reference from Engine
     * @param string $moduleName
     */
    public function __construct(&$objEngine, $moduleName)
    {
        $this->controllerName = get_class($this);
        parent::__construct($objEngine, $moduleName);
    }

    /**
     * Method to initialise the controller object.
     * Override in subclasses.
     *
     * @param void
     * @return void
     */
    public function init()
    {
    }

    /**
     * Method to return current page content.
     * For use within layout templates.
     *
     * @param void
     * @return string Content of rendered content script.
     */
    public function getContent()
    {
        return $this->objEngine->getContent();
    }

    /**
     * Method to return the content of the rendered layout template.
     *
     * @param void
     * @return string Content of rendered layout script.
     */
    public function getLayoutContent()
    {
        return $this->objEngine->getLayoutContent();
    }

    /**
     * Method to be overridden if the controller doesn't require a user login for this request.
     *
     * @param string $action The action for this request
     * @return bool TRUE|FALSE Does this controller require login.
     */
    public function requiresLogin($action)
    {
        return TRUE;
    }

    /**
     * Method to be overridden if the controller doesn't require no-cache headers to be sent.
     *
     * @param string $action The action for this request.
     * @return bool TRUE|FALSE Does this controller want no-cache headers to be sent
     */
    public function sendNoCacheHeaders($action)
    {
        return TRUE;
    }

    /**
     * Method to return a template variable.
     * These are used to pass information from module to template.
     *
     * @param string $name The name of the variable.
     * @param mixed $default The value to return if the variable is unset (optional).
     * @return mixed The value of the variable, or $default if unset.
     */
    public function getVar($name, $default = NULL)
    {
        return $this->objEngine->getVar($name, $default);
    }

    /**
     * Method to set a template variable.
     * These are used to pass information from module to template.
     *
     * @param string $name The name of the variable.
     * @param mixed $val The value to set the variable to.
     * @return void
     */
    public function setVar($name, $val)
    {
        $this->objEngine->setVar($name, $val);
    }

    /**
     * Method to return a template reference variable.
     * These are used to pass objects from module to template.
     *
     * @param string $name The name of the reference variable.
     * @return mixed The value of the reference variable, or NULL if unset.
     */
    public function &getVarByRef($name)
    {
        return $this->objEngine->getVarByRef($name);
    }

    /**
     * Method to set a template reference variable.
     * These are used to pass objects from module to template.
     *
     * @param string $name The name of the reference variable.
     * @param mixed $ref A reference to the object to set the reference variable to.
     * @return void
     */
    public function setVarByRef($name, &$ref)
    {
        $this->objEngine->setVarByRef($name, $ref);
    }

    /**
     * Method to append a value to a template variable holding an array.
     *   If the array does not exist, it is created.
     *
     * @param string $name The name of the variable holding an array.
     * @param mixed $value The value to append to the array.
     * @return void
     */
    public function appendArrayVar($name, $value)
    {
        $this->objEngine->appendArrayVar($name, $value);
    }

    /**
     * Method to set the name of the layout template to use.
     *
     * @param string $templateName The name of the layout template to use.
     * @return void
     */
    public function setLayoutTemplate($templateName)
    {
        $this->objEngine->setLayoutTemplate($templateName);
    }

    /**
     * Method to set the name of the page template to use.
     *
     * @param string $templateName The name of the page template to use.
     * @return void
     */
    public function setPageTemplate($templateName)
    {
        $this->objEngine->setPageTemplate($templateName);
    }

    /**
     * Method to call a further action within a module.
     *
     * @param string $action Action to perform next.
     * @param array $params Parameters to pass to action.
     * @return NULL
     */
    public function nextAction($action, $params = array(), $module=NULL)
    {
        //list($template, $_) = $this->_dispatch($action, $this->_moduleName);
		$params['action'] = $action;
        header('Location: '.$this->uri($params,$module));
        return NULL;
    }

    /**
     * Method to add a global system message.
     *
     * @param string $msg The message.
     * @return Global error message to engine
     */
    public function addMessage($msg)
    {
        return $this->objEngine->addMessage($msg);
    }

    /**
     * Method to set the global error message, and an error field if appropriate.
     *
     * @param string $errormsg The error message.
     * @param string $field The name of the field the error applies to (optional).
     * @return bool TRUE |FALSE
     */
    public function setErrorMessage($msg, $field = '')
    {
        return $this->objEngine->setErrorMessage($msg, $field);
    }

    /**
     * Method to output javascript.
     *   It will display system error message and/or system messages
     *   as set by setErrorMessage and addMessage.
     *
     * @param void
     * @return void
     */
    public function putMessages()
    {
        $this->objEngine->putMessages();
    }

    /**
     * Method to call the given template.
     * It looks first at the given modules templates and then at the core templates (uses _findTemplate).
     * Output is either buffered ($buffer = TRUE) and returned as a string, or send directly to browser.
     *
     * @param string $tpl Name of template to call, including file extension but excluding path.
     * @param string $moduleName The name of the module to search for the template (if empty, search core).
     * @param string $type The type of template to call: 'content' or 'layout'.
     * @param TRUE $ |FALSE $buffer If TRUE buffer output and return as string, else send to browser.
     * @return string |NULL If buffering returns output, else returns NULL.
     */
    public function callTemplate($tpl, $type, $buffer = FALSE)
    {
        // objects that almost every template will use
        $this->setVarByRef('objConfig', $this->getObject('config', 'config'));
        $this->setVarByRef('objSkin', $this->getObject('skin', 'skin'));
        $this->setVarByRef('objUser', $this->getObject('user', 'security'));
        $this->setVarByRef('objLanguage', $this->getObject('language', 'language'));
        $path = $this->objEngine->_findTemplate($tpl, $this->moduleName, $type);
        // extract the template vars
        // TODO: think some more about the extract flags to use
        extract($this->objEngine->_templateVars, EXTR_SKIP);
        extract($this->objEngine->_templateRefs, EXTR_SKIP | EXTR_REFS);

        if ($buffer) {
            ob_start();
        }
        require $path;
        if ($buffer) {
            $pageContent = ob_get_contents();
            ob_end_clean();

            //call on tidy to clean up...
            // Specify tidy configuration
            $config = array(
                'indent'        => true,
                'output-xhtml'  => true,
                'wrap'          => 200);

            // Tidy
            $tidy = new tidy;
            $tidy->parseString($pageContent, $config, 'utf8');
            $tidy->cleanRepair();

            //return $tidy;
            return $pageContent;
        } else {
            return NULL; // just to be explicit
        }
    }
}
?>