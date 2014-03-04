<?php
/* ----------- controller class extends controller for tbl_quotes------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
*
* Controller class for the table tbl_quotes
*
* @author Administrative User
*
*
* @version $Id: controller.php 9632 2008-06-16 17:33:42Z dkeats $
* @version $Id: controller.php,v 1.3 2006/09/14 Abdurahim Ported to PHP5
* @copyright 2005 GNU GPL
*
*/
class quotes extends controller
{

    /**
    * @var string $action The action parameter from the querystring 
    */
    public $action;

    /**
    * Standard constructor method 
    */
    public function init()
    {
        //Retrieve the action parameter from the querystring
        $this->action = $this->getParam('action', Null);
        //Create an instance of the database class for this module
        $this->objDbquotes = & $this->getObject("dbquotes");
        //Create an instance of the User object
        $this->objUser =  & $this->getObject("user", "security");
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
        
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }

    /**
    * Standard dispatch method 
    */
    public function dispatch()
    {
        switch ($this->action) {
            case null:
                $ar = $this->objDbquotes->getRandom();
                $this->setVarByRef('ar', $ar);
                $this->setVar('pageSuppressToolbar', TRUE);
                return "main_tpl.php";
                break;
            default:
                $this->setVar('str', $this->objLanguage->languageText("phrase_actionunknown","quotes").": ".$action);
                return 'dump_tpl.php';

        }//switch
    } // dispatch
    
    /**
    * Override the default requirement for login
    */
    public function requiresLogin()
    {
        return False;  
    }
}
?>