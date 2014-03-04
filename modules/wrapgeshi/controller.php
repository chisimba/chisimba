<?php
/* ----------- controller class extends controller for podcast module------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
*
* Controller class for the the wrapgeshi module
* 
* @author Derek Keats
* @version $Id: controller.php 3853 2006-07-31 11:04:20Z tohir $
* @copyright 2005 GNU GPL
*
*/
class wrapgeshi extends controller
{

    /**
    * Standard constructor method 
    */
    public function init()
    {
        //Create an instance of the User object
        $this->objUser =  & $this->getObject("user", "security");
        //Create an instance of the language object
        $this->objLanguage = & $this->getObject("language", "language");
    }

    /**
    * Standard dispatch method 
    */
    public function dispatch()
    {
        $action = $this->getParam('action', NULL);
        switch ($action) {
            case null:
                return "input_tpl.php";
                break;
            case 'parse':
                return "view_tpl.php";
                break;
            default:
                $this->setVar('str', "Working here");
                return "dump_tpl.php";
                break;
        }
    } # dispatch

} #end of class
?>