<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* This class creates the google analytics code to insert into
* a page. It does not provide any end user functionality
* 
* Google Analytics tells you everything you want to know about how 
* your visitors found you and how they interact with your site.
* 
* See http://www.google.com/analytics/index.html
* 
* The code snippet is for pear.uwc.ac.za
* <script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
* </script>
* <script type="text/javascript">
* _uacct = "UA-1632289-1";
* urchinTracker();
* </script>
*
* @author Derek Keats
* @category Chisimba
* @package timeline
* @copyright AVOIR and UWC
* @licence GNU/GPL
*
*/
class createanalytic extends object
{

    /**
    * 
    * @var $objConfig String object property for holding the 
    * configuration object
    * @access public
    * 
    */
    public $objConfig;
   
    
    /**
    *
    * Standard init method
    *
    * @access Public
    *
    */
    public function init()
    {
        //Create the configuration object
        $this->objConfig = $this->getObject('dbsysconfig', 'sysconfig');

    }
    
    /**
    * 
    * Render the google analytic code for the current module. 
    * First check if the module is enabled, then return the script
    * if so, else return a NULL string.
    * 
    * @return string The rendered dtimeline
    * @access public
    * 
    */
    public function show()
    {
    	$isEnabled = $this->objConfig->getValue("enabled", "googleanalytics");
    	//It is stored as string TRUE/FALSE in the database, not boolean
    	if ($isEnabled == "TRUE") {
	        $module=$this->getParam('module', '_default');
	        return $this->getScript($module);
    	} else {
    	    return NULL;
    	}
    }
    
    /**
    * 
    * Method to generate the dynamic script to track the module
    * that the user is accessing using google analytics
    * 
    */
    public  function getScript($module)
    {
        return "<script src=\"http://www.google-analytics.com/urchin.js\""
          . " type=\"text/javascript\">\n"
		  . "</script>\n"
		  . "<script type=\"text/javascript\">\n"
  		  . "_uacct=\"" . $this->getAccountCode() . "\";\n"
  		  . "urchinTracker('" . $module . "');\n"
		  . "</script>"; 
    }
    
    /**
    * 
    * Method to lookup the account code
    * 
    */
    public function getAccountCode()
    {
        return $this->objConfig->getValue("googleanalyticskey", "googleanalytics");
    }
    
}
?>
