<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for Chisimba for the module Timeline	
* which uses the Simile Timeline Javascript library to 
* create a simple timeline
*
* @author Derek Keats
* @package timeline
*
*/
class timeline extends controller
{
    /**
    * @var $objConfig String object property for holding the 
    * configuration object
    */
    public $objConfig;
    
    /**
    * @var $objLanguage String object property for holding the 
    * language object
    */
    public $objLanguage;
    
    /**
    * @var $objLog String object property for holding the 
    * logger object for logging user activity
    */
    public $objLog;

    /**
     * Intialiser for the stories controller
     *
     * @param byref $ string $engine the engine object
     */
    public function init()
    {
        //$this->objUser = $this->getObject('user', 'security');
        //Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('altconfig', 'config');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the _MODULECODE module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
    	//Get the action parameter from the querystring
    	$action = $this->getParam('action', 'viewdemo');
    	//Add the onload function to the body
        $this->_addBodyParams();
    	//We have to suppress the XML directive in the page otherwise it breaks the JS
		$this->setVar('pageSuppressXML', TRUE);
		//add the call to the Timeline JS to the head
        $this->_addScriptToHead();
        //Instantiaate the object for creating timelines
        $objTl = $this->getObject("createtimeline", "timeline");
        //Add the local script to the page header
        $this->appendArrayVar('headerParams',$objTl->getScript());
    	$str = $objTl->show();
    	$this->setVarByRef("str", $str);
    	return "demo_tpl.php";
    	
    	/******* NOT USING THIS YET
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'view');
        // retrieve the mode (edit/add/translate) from the querystring
        $mode = $this->getParam("mode", null);
        // retrieve the sort order from the querystring
        $order = $this->getParam("order", null);
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        /
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting 
        * from action
        /
        return $this->$method();*/
    }
    
    /*------------- BEGIN : Methods for writing to the page body and header ---------------*/

	/**
	 * 
	 * Method to add the required body parameters to the body tag of
	 * the page containing the timeline
	 * 
	 */
    private function _addBodyParams()
    {
    	$bodyParams = "onload=\"onLoad();\" onresize=\"onResize();\"";
        $this->setVarByRef('bodyParams',$bodyParams);
        return TRUE;
    }

	private function _addScriptToHead()
	{
		//$jsLib = $this->objConfig->getValue('mod_timeline_jslocation', 'timeline');
		$jsLib = "libraries/timeline/timeline-api.js";
	    $scriptTag = "<script src=\"" . $jsLib . "\" type=\"text/javascript\"></script>";
        $this->appendArrayVar('headerParams', $scriptTag);
	}
    /*------------- END : Methods for writing to the page body and header ---------------*/











    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    * 
    * Method corresponding to the view action. It fetches the stories
    * into an array and passes it to a main_tpl content template.
    * @access private
    * 
    */
    private function __viewdemo()
    {
        return "main_tpl.php";
    }
    
    /**
    * 
    * Method corresponding to the edit action. It sets the mode to 
    * edit and returns the edit template.
    * @access private
    * 
    */
    private function __edit()
    {
        $this->setvar('mode', "edit");
        return 'editform_tpl.php';
    }

    /**
    * 
    * Method corresponding to the add action. It sets the mode to 
    * add and returns the edit content template.
    * @access private
    * 
    */
    private function __add()
    {
        $this->setvar('mode', 'add');
        return 'editform_tpl.php';
    }
    
   
    /**
    * 
    * Method corresponding to the save action. It gets the mode from 
    * the querystring to and saves the data then sets nextAction to be 
    * null, which returns the {yourmodulename} module in view mode. 
    * 
    * @access private
    * 
    */
    private function __save()
    {
        $mode = $this->getParam("mode", NULL);
        $this->objDb{yourmodulename}->save($mode);
        return $this->nextAction(NULL);
    }
    
    /**
    * 
    * Method corresponding to the delete action. It requires a 
    * confirmation, and then delets the item, and then sets 
    * nextAction to be null, which returns the {yourmodulename} module 
    * in view mode. 
    * 
    * @access private
    * 
    */
    private function __delete()
    {
        // retrieve the confirmation code from the querystring
        $confirm=$this->getParam("confirm", "no");
        if ($confirm=="yes") {
            $this->deleteItem();
            return $this->nextAction(NULL);
        }
    }
    
    
    /**
    * 
    * Method to return an error when the action is not a valid 
    * action method
    * 
    * @access private
    * @return string The dump template populated with the error message
    * 
    */
    private function __actionError()
    {
        $this->setVar('str', "<h3>"
          . $this->objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $action . "</h3>");
        return 'dump_tpl.php';
    }
    
    /**
    * 
    * Method to check if a given action is a valid method
    * of this class preceded by double underscore (__). If it __action 
    * is not a valid method it returns FALSE, if it is a valid method
    * of this class it returns TRUE.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return boolean TRUE|FALSE
    * 
    */
    function __validAction(& $action)
    {
        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * 
    * Method to convert the action parameter into the name of 
    * a method of this class.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return stromg the name of the method
    * 
    */
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }
    
    /*------------- END: Set of methods to replace case selection ------------*/
    


    /**
    *
    * This is a method to determine if the user has to 
    * be logged in or not. Note that this is an example, 
    * and if you use it view will be visible to non-logged in 
    * users. Delete it if you do not want to allow annonymous access.
    * It overides that in the parent class
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin()
    {
        $action=$this->getParam('action','NULL');
        switch ($action)
        {
            case 'viewdemo':
            case NULL:
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
     }
}
?>
