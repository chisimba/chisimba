<?php
/**
* Sitestats controller class. This class presents 
* some site statistics, including contexts. users, etc.
* @author Derek Keats
* @copyright (c) 2004 UWC
* @license GNU GPL
* @package sitestats
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

class sitestats extends controller
{

    /**
    * @var $objLanguage String object property for holding the 
    * language object
    */
    var $objLanguage;
    /**
    * @var $objUser String object property for holding the user object
    */
    var $objUser;
    /**
    * @var String $action property for holding the action
    */
    var $action;
    /**
    * Initialise objects used in the module.
    */
    function init()
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
    function dispatch()
    {
        if ($this->objUser->isAdmin()) {
            //Get action from query string and set default to view
            $action = $this->getParam('action', 'viewsummarystats');
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
            $this->setVar('str', $this->objLanguage->languageText('mod_userstats_norights'));
            return "nopermission_tpl.php";
        }

    }
    
    /**
    * 
    * Method to produce all summary statistics
    * 
    */
    function __viewsummarystats()
    {
	
        //Instantiate the data object
        $objCtx = $this->getObject('dbcontextstats');
        //Get the number of contexts
        $tContexts = $objCtx->getTotalContexts();
        //Pass the total logins to the template
        $this->setVarByRef('tContexts', $tContexts);
        //Get the number of files in all contexts
        $tFiles = $objCtx->getTotalContextFiles();
        //Pass the total logins to the template
        $this->setVarByRef('tFiles', $tFiles);
        //Get the total size of all files in all contexts
        $tFlSp = $objCtx->getFileSpace();
        //Pass the total logins to the template
        $this->setVarByRef('tFlSp', $tFlSp);
        unset($tFlSp);
        
        //Instantiate the data object for user stuff
        $objUs = $this->getObject('dbuserstats');
        //Get the total number of users
        $users = $objUs->countUsers();
        //Pass the total users to the template
        $this->setVarByRef('users', $users);
        //Get the howcreated information
        $ar = $objUs->getHowCreated();
        $created="";
        foreach ($ar as $line) {
            $created .= $line['howCreated'] . " (" 
              . $line['count'] 
              . ")&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        $this->setVarByRef('created', $created);
        
        //Get males and females
        $females = $objUs->countFemales();
        $males = $objUs->countMales();
        $this->setVarByRef('males', $males);
        $this->setVarByRef('females', $females);
        //Get the total number of countries
        $tCntry = $objUs->getTotalCountries();
        //Pass the total logins to the template
        $this->setVarByRef('tCntry', $tCntry);
        //Get the flags
        $flags = $objUs->getFlags();
        //pass flags to templte
        $this->setVarByRef('flags', $flags);
        //Return the template populated
        return 'viewsummarystats_tpl.php';
	
    }
    
    function __getContextStats()
    {
        //Instantiate the data object
        $objCtx = $this->getObject('dbcontextstats');
        $ar = $objCtx->getContextsPages();
        //pass array to templte
        $this->setVarByRef('ar', $ar);
        return 'contextstats_tpl.php';
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
