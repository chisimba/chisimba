<?php

/**
 * Controller object
 *
 * Top level controller
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* -------------------- controller class ----------------*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * controller class (top level)
 *
 * Controller controls the business logic of the Chisimba applicatipon (The C in MVC)
 *
 * @category  Chisimba
 * @package   core
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
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
     * Description for public
     * @var    string
     * @access public
     */
    public $footerStr = NULL;

    /**
    * @var		object
    * @access 	protected
    */
    protected $_objLanguage;

    /**
     * Constructor for the controller class.
     *
     * @param object $objEngine  by reference from Engine
     * @param string $moduleName
     */
    public function __construct($objEngine, $moduleName)
    {
    	$this->controllerName = get_class($this);
        try {
        	parent::__construct($objEngine, $moduleName);
        	$version = $objEngine->version;
        	$this->footerStr = '<center>Powered by <a href="http://avoir.uwc.ac.za/">Chisimba</a> version ' .$version . "</center>";
        }
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }

    }

    /**
     * Method to initialise the controller object.
     * Override in subclasses.
     *
     * @param  void
     * @return void
     */
    public function init()
    {
    }

	/**
	* Method to return a language text element.
	*
    * @param  	string 	$itemName   The language code for the item to be looked up
    * @param  	string 	$modulename The module name that owns the string
    * @param 	bool	$default	Default text
    * @return 	string	The language element
	*/
	protected function l($itemName,$modulename=NULL,$default = false)
	{
	  if (!defined('CHISIMBA_CONTROLLER_OBJLANGUAGE_CREATED')) {
       	define('CHISIMBA_CONTROLLER_OBJLANGUAGE_CREATED',true);
        $this->_objLanguage = $this->getObject('language','language');
	  }
	   $module = is_null($modulename)?$this->moduleName:$modulename;
	   return $this->_objLanguage->languageText($itemName, $module, $default);
	}

    /**
     * Method to return current page content.
     * For use within layout templates.
     *
     * @param  void
     * @return string Content of rendered content script.
     */
    public function getContent()
    {
        return $this->objEngine->getContent();
    }

    /**
     * Method to return the content of the rendered layout template.
     *
     * @param  void
     * @return string Content of rendered layout script.
     */
    public function getLayoutContent()
    {
        return $this->objEngine->getLayoutContent();
    }

    /**
     * Method to be overridden if the controller doesn't require a user login for this request.
     *
     * @param  string $action The action for this request
     * @return bool   TRUE|FALSE Does this controller require login.
     */
    public function requiresLogin($action)
    {
        return TRUE;
    }

    /**
     * Method to be overridden if the controller doesn't require no-cache headers to be sent.
     *
     * @param  string $action The action for this request.
     * @return bool   TRUE|FALSE Does this controller want no-cache headers to be sent
     */
    public function sendNoCacheHeaders($action)
    {
        return TRUE;
    }

    /**
     * Method to return a template variable.
     * These are used to pass information from module to template.
     *
     * @param  string $name    The name of the variable.
     * @param  mixed  $default The value to return if the variable is unset (optional).
     * @return mixed  The value of the variable, or $default if unset.
     */
    public function getVar($name, $default = NULL)
    {
        return $this->objEngine->getVar($name, $default);
    }

    /**
     * Method to set a template variable.
     * These are used to pass information from module to template.
     *
     * @param  string $name The name of the variable.
     * @param  mixed  $val  The value to set the variable to.
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
     * @param  string $name The name of the reference variable.
     * @return mixed  The value of the reference variable, or NULL if unset.
     */
    public function getVarByRef($name)
    {
        return $this->objEngine->getVarByRef($name);
    }

    /**
     * Method to set a template reference variable.
     * These are used to pass objects from module to template.
     *
     * @param  string $name The name of the reference variable.
     * @param  mixed  $ref  A reference to the object to set the reference variable to.
     * @return void
     */
    public function setVarByRef($name, $ref)
    {
        $this->objEngine->setVarByRef($name, $ref);
    }

    /**
     * Method to append a value to a template variable holding an array.
     *   If the array does not exist, it is created.
     *
     * @param  string $name  The name of the variable holding an array.
     * @param  mixed  $value The value to append to the array.
     * @return void
     */
    public function appendArrayVar($name, $value)
    {
        $this->objEngine->appendArrayVar($name, $value);
    }

    /**
     * Method to set the name of the layout template to use.
     *
     * @param  string $templateName The name of the layout template to use.
     * @return void
     */
    public function setLayoutTemplate($templateName)
    {
        $this->objEngine->setLayoutTemplate($templateName);
    }

    /**
     * Method to set the name of the page template to use.
     *
     * @param  string $templateName The name of the page template to use.
     * @return void
     */
    public function setPageTemplate($templateName)
    {
        $this->objEngine->setPageTemplate($templateName);
    }

    /**
     * Method to call a further action within a module.
     *
     * @param  string $action Action to perform next.
     * @param  array  $params Parameters to pass to action.
     * @return NULL
     */
    public function nextAction($action, $params = array(), $module=NULL)
    {
        //list($template, $_) = $this->_dispatch($action, $this->_moduleName);
		$params['action'] = $action;
		header('Location: '.html_entity_decode($this->uri($params,$module)));
        return NULL;
    }

    /**
     * Method to add a global system message.
     *
     * @param  string $msg The message.
     * @return Global error message to engine
     */
    public function addMessage($msg)
    {
        return $this->objEngine->addMessage($msg);
    }

    /**
     * Method to set the global error message, and an error field if appropriate.
     *
     * @param  string $errormsg The error message.
     * @param  string $field    The name of the field the error applies to (optional).
     * @return bool   TRUE |FALSE
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
     * @param  void
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
     * @param  string $tpl        Name of template to call, including file extension but excluding path.
     * @param  string $moduleName The name of the module to search for the template (if empty, search core).
     * @param  string $type       The type of template to call: 'content' or 'layout'.
     * @param  TRUE   $           |FALSE $buffer If TRUE buffer output and return as string, else send to browser.
     * @return string |NULL If buffering returns output, else returns NULL.
     */
    public function callTemplate($_magic__tpl, $_magic__type, $_magic__buffer = FALSE)
    {
        // objects that almost every template will use
        $this->setVarByRef('objConfig', $this->getObject('altconfig', 'config'));
        $this->setVarByRef('objSkin', $this->getObject('skin', 'skin'));
        $this->setVarByRef('objUser', $this->getObject('user', 'security'));
        $this->setVarByRef('objLanguage', $this->getObject('language', 'language'));
        $_magic__path = $this->objEngine->_findTemplate($_magic__tpl, $this->moduleName, $_magic__type);
        // extract the template vars
        // TODO: think some more about the extract flags to use
        extract($this->objEngine->_templateVars, EXTR_SKIP);
        extract($this->objEngine->_templateRefs, EXTR_SKIP | EXTR_REFS);

        if ($_magic__buffer) {
            ob_start();
        }
        include $_magic__path; //was require
        if ($_magic__buffer) {
            $_magic__pageContent = ob_get_contents();
            ob_end_clean();

            /*
            // Tidy
            //call on tidy to clean up...
            // Specify tidy configuration
            $config = array(
                'indent'        => true,
                'output-xhtml'  => true,
                'wrap'          => 200);
            $tidy = new tidy;
            $tidy->parseString($pageContent, $config, 'utf8');
            $tidy->cleanRepair();
            //return $tidy;
            */

            return $_magic__pageContent;
        } else {
            return NULL; // just to be explicit
        }
    }
}
?>