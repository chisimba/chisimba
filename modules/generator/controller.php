<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for Chisimba for the module {yourmodulename}
*
* @author Derek Keats
* @package generator
*
*/
class generator extends controller
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
     * 
     * Constructor for the generator controller
     *
     */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the {yourmodulename} module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'getui'); 
        // retrieve the mode (edit/add/translate) from the querystring
        $mode = $this->getParam("mode", null);
        // retrieve the sort order from the querystring
        $order = $this->getParam("order", null);
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return $this->$method();
    }
    
    
    /*------------- BEGIN: Set of methods to replace case selection ------------*/
    
    /**
    *
    * Method to get the User Interface for a particular generator
    * The user interface is built by parsing a file in the generators
    * directory under the object type (e.g controller/) called
    * OBJECTYPE_ui_form.xml.
    *
    * @param string $generator The name of the generator we are working with
    * @access Public
    *
    */
    public function __getui()
    {
    	$objUi = $this->getObject('uimanager');
    	//Get the type of object that we are generating
    	$objectType = $this->getParam('objecttype', 'start');
    	//Get the XML into memory for handling
    	$objUi->readFormXml($objectType);
    	//Generate the input form
    	$str=$objUi->generateForm();
    	//Send the formatted string to the template
    	$this->setVarByRef('str', $str);
    	return 'main_tpl.php';    	
    }
    
    /**
    *
    * Method to process the results of form input for a particular generator
    *
    * @param string $generator The name of the generator we are working with
    * @access Public
    *
    */
    public function __processresults()
    {
    	//Get the type of object that we are generating
    	$objectType = $this->getParam('objecttype', NULL);
        //Setup the appropriate generator class to use
        $rGen = 'gen' . $objectType;
        //Instantiate the appropriate generator
        $objGen = $this->getObject($rGen);
        //Get the output from the apporpriate generator
        $str = $objGen->generate();
        //Stick it in a textarea
        $str = $this->getOutputStr($str, $objectType);
        //Pass the output to the template
        $this->setVarByRef('str', $str);
        return 'main_tpl.php';
    }
    
    /**
     * 
     * Method to show all generator template tags as used
     * in the XML templates
     * 
     */
    function __showTemplateTags()
    {
        return 'showtemplatetags_tpl.php';
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
    * Method to render output inside a text area
    *
    * @param string $str The string to insert into the textarea
    * @param string $areaName The name for the textarea
    * @TODO replace this with a call to HTMLELEMENTS textarea
    *
    */
    private function getOutputStr($str, $areaName)
    {
        $js = $this->getJavascriptFile('codepress.js', 'codewriter');
        $this->appendArrayVar('headerParams', $js);
        $objRenderer = $this->getObject("cwrenderer", "codewriter");
        $objRenderer->code = $str;
        return $objRenderer->renderEditor("", $areaName);//still working here
    }
    
    
    
    
    
    
    
    
    ///----------------------old stuff remove ----------------------------
    
    
    
    
    /**
    * 
    * Method corresponding to the genedit action. It generates an
    * edit template and puts it into a text box.
    *  //////////WORK IN PROGRESS LEAVE IT FOR NOW
    * @access private
    * 
    */
    private function __genwrapper()
    {
        $this->setVar('page', 6);
        return "genwrapper_tpl.php";
    }
    
    
    /**
    * 
    * Method to get a database schema from the database //////////WORK IN PROGRESS LEAVE IT FOR NOW
    * 
    */
    private function __getxmlschema()
    {
    	$objSch = $this->getObject('getschema');
        $this->setVar('str', $objSch->getXmlSchema('tbl_users'));
        return "dump_tpl.php";
    } 
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    

    
    /*------------- END: Set of methods to replace case selection ------------*/
    

}
?>