<?php
/**
* Userstats controller class. This class presents 
* some user statistics, including last login, etc.
* @author Derek Keats
* @copyright (c) 2004 UWC
* @license GNU GPL
* @package userstats
* @version 1
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} // end security check

 /**
 * Userstats controller class to present summary stats about
 * user logins to administrative users
 * @author Derek Keats
 * @copyright (c) 2004 UWC
 * @license GNU GPL 2
 * @package userstats
 * @version 1
 */

class userstats extends controller
{

    /**
    * @var $objLanguage String object property for holding the 
    * language object
    */
    public $objLanguage;
    /**
    * @var $objUser String object property for holding the user object
    */
    public $objUser;
    /**
    * @var String $action property for holding the action
    */
    public $action;
    /**
    * Initialise objects used in the module.
    */
    public function init()
    {
        //Instantiate the language object
        $this->objLanguage = & $this->getObject('language', 'language');
        //Instantiate the user object
        $this->objUser = & $this->getObject('user', 'security');
    }

    /**
    * The standard dispatch method for the module.
    * The dispatch() method must return the name of a page body template which will
    * render the module output (for more details see Modules and templating)
    * @return The template to display
    */
    public function dispatch()
    {
        if ($this->objUser->isAdmin()) {
            //Get action from query string and set default to view
            $action = $this->getParam('action', 'viewloginhistory');
            $this->action=$action;
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
        } else {
            $this->setVar('str', $this->objLanguage->languageText('mod_userstats_norights'),userstats);
            return "nopermission_tpl.php";
        }

    }
    
    function __viewloginhistory()
    {
        //Instantiate the data object
        $objHist = $this->getObject('dbloginhistory');
        //Get an array of the login history data
        $ar = $objHist->getLoginHistory();
        //Pass the resulting array to the template
        $this->setVarByRef('ar', $ar);
        //Get the total number of logins
        $totalLogins = $objHist->getTotalLogins();
        //Pass the total logins to the template
        $this->setVarByRef('totalLogins', $totalLogins);
        
	//get gender history
	$males = $objHist->getmales();
	//pass the male logins to the template
	$this->setvarByRef('males',$males);

	//get number of females
	$females = $objHist->getfemales();
	//pass the female logins to the template
	$this->setVarByRef('females',$females);
	
	
        //Get the number of unique logins (users)
        $uniqueLogins = $objHist->getUniqueLogins();
        //Pass the total logins to the template
        $this->setVarByRef('uniqueLogins', $uniqueLogins);
        //Return the template populated
        return 'loginhist_tpl.php';

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
    function __actionError()
    {
        $this->setVar('str', "<h3>"
          . $this->objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $this->action . "</h3>");
        return 'dump_tpl.php';
    }
    
    /**
    * 
    * Method to check if a given action is a valid method
    * of this class preceded by double underscore (__). If it __action 
    * is not a valit method it returns FALSE, if it is a valid method
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
    
}// end class assignment
?>
