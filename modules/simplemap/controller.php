<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for Chisimba for the module simplemap. It uses the Google Maps API
* to create one or more maps, including a demo map. The Google Maps JavaScript API lets 
* you embed Google Maps in your own web pages. To use the API, you need to sign up for 
* an API key and use System configuration to add it to the simplemap parameters.
*
* @author Derek Keats
* @package simplemap
*
*/
class simplemap extends controller
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
    * @var $objBuildMap String object property for holding the 
    * object that builds the maps (simplebuildmap class from classes)
    */
    public $objBuildMap;

    /**
     * Intialiser for the stories controller
     *
     * @param byref $ string $engine the engine object
     */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        //Instantiate the simplebuildmap class
        $this->objBuildMap = $this->getObject('simplebuildmap', 'simplemap');
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
        $action=$this->getParam('action', 'viewall');
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($action);
        //Add map script to page heading
        $this->_addMapScriptToPage();
        //Add the onunload method to the body
        $this->_addOnUnloadToBody();
        //Check if runnin in plain mode and disable banners etc
        $mode=$this->getParam('mode', NULL);
        if ($mode == "plain") {
            $this->setVar('pageSuppressContainer',TRUE);
	        $this->setVar('suppressFooter', TRUE); # suppress default page footer
	        $this->setVar('pageSuppressIM', TRUE);
	        $this->setVar('pageSuppressToolbar', TRUE);
	        $this->setVar('pageSuppressBanner', TRUE);
        }
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return $this->$method();
    }
    
    /*
    * 
    * This method gets the script call, including the API key and adds it to
    * the page header.
    * 
    * @return TRUE;
    *  
    */
    function _addMapScriptToPage()
    {
    	try {
	    	//Read the API key from sysconfig
	    	$apiKey = $this->objBuildMap->getApiKey();
	    	//ABQIAAAASzlWuBpqyHQoPD8OwyyFRhS9klZkf-a3YMqrNEgglGl8tlkEvBRUarouiwsLMxDlMc20SE2jC_GQmg
	        $hScript = "<script src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;key=" 
	           . $apiKey . "\" type=\"text/javascript\"></script>";
	        //Add the local script to the page header
	        $this->appendArrayVar('headerParams',$hScript);
	        return TRUE;
        }
        catch(customException $e) {
            //something went wrong, print it out and log it
            echo customException::cleanUp();
            //kill everything because we are dead anyway
            die();
        }
    }
    
    /*
    * 
    * Method to add the GUnload() Javascript call to the body tag. This is mainly
    * a method to prevent memory leaks which are apparently quite common in  
    * Internet Explorer.
    *
    */
    function _addOnUnloadToBody()
    {
        $bodyParams = "onunload=\"GUnload()\"";
        $this->setVarByRef('bodyParams',$bodyParams);
    }
    
    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    * 
    * Method corresponding to the showdemo action. It displays the 
    * default demo map when no action or showdemo is called.
    * 
    * @access private
    * 
    */
    private function __viewdemo()
    {
    	$this->setVar('pageSuppressXML', TRUE);
    	$str = $this->objBuildMap->show();
    	$this->setVarByRef('str', $str);
        return "demomap_tpl.php";
    }
    
    /**
     * 
     * Method to view a list of all simplemaps stored
     * @access private
     * @return String Template for viewing list of timelines
     */
    private function __viewall()
    {
        $objShowData = $this->getObject("simplemapinterface", "simplemap");
        $str = $objShowData->show();
        $this->setVarByRef("str", $str);
        return 'viewall_tpl.php';
    }
    
    /**
     * 
     * Method corresponding to the editmap action parameter
     * @access private
     * @return Template for editing or adding a timeline structure
     * 
     */
    private function __editmap()
    {
    	$id = $this->getParam("id", NULL);
    	$objDb = $this->getObject("dbmaps", "simplemap");
    	$ar = $objDb->getRow("id", $id);
        $this->setVar("ar", $ar);
        return "editadd_tpl.php";
    }
    
    /**
     * 
     * Method to add a map
     * @access private
     * @return string The edit add template
     * 
     */
    private function __addmap()
    {
        return "editadd_tpl.php";
    }
    
    /**
     * 
     * Method to save the map
     * @access private
     * @return The viewall action as next action
     * 
     */
    private function __save()
    {
    	$objDb = $this->getObject("dbmaps", "simplemap");
    	$mode = $this->getParam("mode", NULL);
    	$objDb->saveData($mode);
        return $this->nextAction("viewall");
    }
    
    private function __delete()
    {
        $id = $this->getParam('id', null);
        $objDb = $this->getObject("dbmaps", "simplemap");
        // Delete the record from the database
        $objDb->deleteRecord("id", $id);
        return $this->nextAction('viewall');
    }
    
    /**
    * 
    * Method corresponding to the viewmap action. It hands everything over to the
    * viewmap template.
    * 
    * @access private
    * 
    */
    private function __viewmap()
    {
    	$this->setVar('pageSuppressXML', TRUE);
    	$str = $this->objBuildMap->show();
    	$this->setVarByRef('str', $str);
        return "viewmap_tpl.php";
    }
    
    /**
    * 
    * Method corresponding to the testparser action. It catches the URL for the timeline
    * module and hands it over to the template. This is a unit test of sorts, and it not
    * really meant to have any end user functionality.
    * 
    * @access private
    * 
    */
    private function __testparser()
    {
    	$this->setVar('pageSuppressXML', TRUE);
    	$objParser = $this->getObject("smapparser", "simplemap");
    	$objRsConfig =  $this->getObject('altconfig', 'config');
    	$map = $filename =  "http://" . $_SERVER['SERVER_NAME'] 
    	   . $objRsConfig->getItem('MODULE_URI') . "simplemap/resources/jsmaps/madiba.smap";
    	$loc = $this->getParam("method", NULL);
        if ($loc !== "local") {
            $objParser->setMapUri($map);
        }
    	$str = $objParser->show();
    	$this->setVarByRef("str", $str);
        return "testparser_tpl.php";
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
    * be logged in or not. In this case, the showdemo which
    * shows the demo map can be shown without login
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin($action)
    {
        switch ($action)
        {
            case 'viewdemo':
            case 'viewmap':
            
                return FALSE;
                break;
            default:
            	case NULL:
                return TRUE;
                break;
        }
     }
}
?>