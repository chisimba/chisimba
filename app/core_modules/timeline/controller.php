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
    	$action = $this->getParam('action', 'viewall');
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($action);
    	//Add the onload function to the body
        $this->_addBodyParams();
    	//We have to suppress the XML directive in the page otherwise it breaks the JS
		$this->setVar('pageSuppressXML', TRUE);
		//add the call to the Timeline JS to the head
        $this->_addScriptToHead();
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
		$jsLib =  $this->objConfig->getItem('MODULE_URI') . "timeline/resources/timeline-api.js";
	    $scriptTag = "<script src=\"" . $jsLib . "\" type=\"text/javascript\"></script>";
        $this->appendArrayVar('headerParams', $scriptTag);
	}
	
    /**
     * @deprecated
     * Method to add the scriptaculous library to the page head
     * @access private
     * @return TRUE
     * 
    /* scriptaculous moved to default page template / no need to suppress XML        

     private function addScriptaculousToPage()
     {
     	//$scripts = '<script src="core_modules/htmlelements/resources/script.aculos.us/lib/prototype.js" type="text/javascript"></script>
          //<script src="core_modules/htmlelements/resources/script.aculos.us/src/scriptaculous.js" type="text/javascript"></script>
          //<script src="core_modules/htmlelements/resources/script.aculos.us/src/unittest.js" type="text/javascript"></script>';
        //$this->appendArrayVar('headerParams',$scripts);
        return TRUE;
     }
    /*------------- END : Methods for writing to the page body and header ---------------*/


    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    * 
    * Method corresponding to the viewdemo action. It hands everything over to the
    * demo template.
    * 
    * @access private
    * 
    */
    private function __viewdemo()
    {
    	//Instantiaate the object for creating timelines
        $objTl = $this->getObject("createtimeline", "timeline");
        //Add the local script to the page header
        $this->appendArrayVar('headerParams',$objTl->getScript());
    	$str = $objTl->show();
    	$this->setVarByRef("str", $str);
        return "demo_tpl.php";
    }
    
    /**
    * 
    * Method corresponding to the viewdemo action. It hands everything over to the
    * demo template.
    * 
    * @access private
    * 
    */
    private function __viewtimeline()
    {
    	//Instantiaate the object for creating timelines
        $objTl = $this->getObject("createtimeline", "timeline");
        //Add the local script to the page header
        $this->appendArrayVar('headerParams',$objTl->getScript());
    	$str = $objTl->show();
    	$this->setVarByRef("str", $str);
        return "viewtimeline_tpl.php";
    }
    
    /**
    * 
    * Method corresponding to the teststream action. It hands everything over to the
    * demo template.
    * 
    * @access private
    * 
    */
    private function __teststream()
    {
    	//Instantiaate the object for creating timelines
        $objTl = $this->getObject("createtimeline", "timeline");
    	$str = $objTl->teststream();
        //Add the local script to the page header
        $this->appendArrayVar('headerParams',$objTl->getScript());
    	$this->setVarByRef("str", $str);
        return "viewtimeline_tpl.php";
    }
    
    private function __sendnotfounderror() {
            $this->setVar('pageSuppressContainer',TRUE);
	        $this->setVar('suppressFooter', TRUE); # suppress default page footer
	        $this->setVar('pageSuppressIM', TRUE);
	        $this->setVar('pageSuppressToolbar', TRUE);
	        $this->setVar('pageSuppressBanner', TRUE);
	        $this->setVarByRef("str", $str);
	        $str = "<h1><div class=\"error\">" 
	          . $this->objLanguage->languageTExt("mod_timeline_error_localnotfound", "timeline")
	          . "</div></h1>";
        	return "dump_tpl.php";
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
    	$objParser = $this->getObject("timelineparser", "timeline");
        //Create the config reader and get the location of demo maps
        $objSconfig =  $this->getObject('altconfig', 'config');
        $demoData =  $objSconfig->getItem('MODULE_URI') . "timeline/resources/demodata/madiba.xml";
        $loc = $this->getParam("method", NULL);
        if ($loc !== "local") {
            $objParser->setTimelineUri($demoData);
        }
    	$str = $objParser->show();
    	$this->setVarByRef("str", $str);
        return "testparser_tpl.php";
    }
    
    /**
     * 
     * Method to return the form for making a single
     * timeline entry
     * 
     */
    private function __makesingle()
    {
    	$this->appendArrayVar('headerParams', $this->getJsForSimpleCreate());
        return "makesingle_tpl.php";
    }
    
    /**
     * 
     * Method to view a list of all timelines stored
     * @access private
     * @return String Template for viewing list of timelines
     */
    private function __viewall()
    {
        $objShowData = $this->getObject("structureinterface", "timeline");
        $str = $objShowData->show();
        $this->setVarByRef("str", $str);
        return 'viewall_tpl.php';
    }
    
    /**
     * 
     * Method corresponding to the editstructure action parameter
     * @access private
     * @return Template for editing or adding a timeline structure
     * 
     */
    private function __editstructure()
    {
    	$id = $this->getParam("id", NULL);
    	$objDb = $this->getObject("dbstructure", "timeline");
    	$ar = $objDb->getRow("id", $id);
        $this->setVar("ar", $ar);
        //$this->addScriptaculousToPage();
        return "editadd_tpl.php";
    }
    
    /**
    * 
    * Method to add a new timeline structure or 
    * configuration. It provides the edit/add template
    * for creating a new entry.
    * 
    * @access private
    * @return string The rendered edit/add template
    * 
    */
    private function __addstructure()
    {
    	//$this->addScriptaculousToPage();
        return "editadd_tpl.php";
    }
    
    /**
    * 
    * Method to save a timeline structure or 
    * configuration
    * @access private
    * @return The next action to view all
    * 
    */
    private function __savestructure()
    {
    	$objDb = $this->getObject("dbstructure", "timeline");
    	$mode = $this->getParam("mode", NULL);
    	$objDb->saveData($mode);
        return $this->nextAction("viewall");
    }
    
    /**
    * 
    * Method to delete a saved timeline structure or 
    * configuration
    * @access private
    * @return The next action to view all
    * 
    */
    private function __deletestructure()
    {
        $id = $this->getParam('id', null);
        $objDb = $this->getObject("dbstructure", "timeline");
        // Delete the record from the database
        $objDb->deleteRecord("id", $id);
        return $this->nextAction('viewall', array());
    }
    
    //THIS IS WORK IN PROGRESS
    private function __edittimeline()
    {
        $str="Working here";
        $this->setVarByRef("str", $str);
        return 'editaddtimeline_tpl.php';
    }
    
    /**
    * 
    * Method for adding a timeline 
    * //THIS IS WORK IN PROGRESS
    * 
    */
    private function __addtimeline()
    {
        $str="Working here for adding";
        $this->setVarByRef("str", $str);
        return 'editaddtimeline_tpl.php';
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
          .": " . $this->getParam("action", NULL) . "</h3>");
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
    function __validAction($action)
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
    function __getMethod($action)
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
	 * 
	 */
	function getJsForSimpleCreate()
	{
	    return "<script language=\"JavaScript\" type=\"text/javascript\">
			<!--
			function generateEventXML() {
				if (document.getElementById(\"estart\").value == '' )
				{
					alert('Event start date required!');
				} else {
					r = '<event \\nstart=\"' + document.getElementById(\"estart\").value + '\"';
				
					if (document.getElementById(\"eend\").value != '' )
					{
						r += ' \\nend=\"'+ document.getElementById(\"eend\").value + '\"';
					}
					if (document.getElementById(\"elink\").value != '' )
					{
						r += ' \\nlink=\"'+ document.getElementById(\"elink\").value + '\"';
					}
					if (document.getElementById(\"eimg\").value != '' )
					{
						r += ' \\nimage=\"'+ document.getElementById(\"eimg\").value + '\"';
					}
					if (document.getElementById(\"etitle\").value != '' )
					{
						r += ' \\ntitle=\"'+ document.getElementById(\"etitle\").value + '\"';
					}
					r += '>\\n';
					r += document.getElementById(\"edesc\").value;
					r += '\\n</event>';
					document.forms.eventdetails.results.value = r;
				}
			}
			-->
			</script>";
	}

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
    public function requiresLogin($action)
    {
        switch ($action)
        {
            case 'viewdemo':
            case 'viewall':
            case 'viewtimeline':
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
