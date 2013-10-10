<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for Chisimba for the module facebookapps. This module allows
* Chisimba to be used to build applications that plugin to Facebook.
*
* @author Derek Keats
* @package facebookapps
*
*/
class facebookapps extends controller
{
    
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
     * Intialiser for the facebookapps controller
     *
     * @param byref $ string $engine the engine object
     */
    public function init()
    {
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
        $action=$this->getParam('action', 'default');
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
    * Method corresponding to the showdemo action. It displays the 
    * default demo map when no action or showdemo is called.
    * 
    * @access private
    * 
    */
    private function __default()
    {
        $this->setVar('pageSuppressContainer',TRUE);
        $this->setVar('suppressFooter', TRUE); # suppress default page footer
        $this->setVar('pageSuppressIM', TRUE);
        $this->setVar('pageSuppressToolbar', TRUE);
        $this->setVar('pageSuppressBanner', TRUE);
    	$this->setVar('pageSuppressXML', TRUE);
    	$str = "Working here";
    	$this->setVarByRef('str', $str);
        return "dump_tpl.php";
    }
    
    private function __getCommentsForAjax()
    {
        
    } 

    
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
        return FALSE;
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
}
?>