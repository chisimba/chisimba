<?php
/* ----------- controller class extends controller for library------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
*
* Controller class for the table tbl_library
*
* @version $Id: controller.php 4687 2006-11-02 13:08:42Z jmulindwa $
* @copyright 2005 GNU GPL
*
*/
class library extends controller
{

    /**
    * @var string $action The action parameter from the querystring 
    */
    var $action;

    /**
    .0
    * Standard constructor method 
    */
    function init()
    {
        //Retrieve the action parameter from the querystring
        $this->action = $this->getParam('action', Null);
        //Create an instance of the database class for this module
        $this->objDbLibrary = & $this->getObject("dblibrary");
        //Create an instance of the language object
        $this->objLanguage =  &$this->getObject("language", "language");
        //Create an instance of the User object
        $this->objUser =  &$this->getObject("user", "security");
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }

    /**
    * Standard dispatch method 
    */
    function dispatch()
    {
        $adminErr="You are not admin!";
        switch ($this->action) {
            case null:
            
            case "view":
                  $ar = $this->objDbLibrary->getAll();
                  $this->setVarByRef('ar', $ar);
                  return "main_tpl.php";
                  break;
                  
            case "admin":
                if ( $this->objUser->isAdmin() || $this->isValid('admin') ) {
                    $ar = $this->objDbLibrary->getAll();
                    $this->setVarByRef('ar', $ar);
                    return "adminlist_tpl.php";
                } else {
                    $this->setVarByRef('str', $adminErr);
                    return 'dump_tpl.php';
                }
                break;            
                  
            case 'edit':
                if ( $this->objUser->isAdmin() || $this->isValid('edit') ) {
                     $this->getForEdit('edit');
                     $this->setVar('mode', 'edit');
                     return "edit_tpl.php";
                 } else {
                    $this->setVarByRef('str', $adminErr);
                    return 'dump_tpl.php';
                 }
                 break;

            case 'delete':
                if ( $this->objUser->isAdmin() || $this->isValid( 'delete' ) ) {
                    // retrieve the confirmation code from the querystring
                    $confirm=$this->getParam("confirm", "no");
                    if ($confirm=="yes") {
                        $this->objDbLibrary->delete("id", $this->getParam('id', Null));
                        return $this->nextAction('admin');
                    }
                } else {
                    $this->setVarByRef('str', $adminErr);
                    return 'dump_tpl.php';
                }
                break;

            case 'add':
                if ( $this->objUser->isAdmin() || $this->isValid('add') ){
                    $this->setVar('mode', 'add');
                    return "edit_tpl.php";
                } else {
                    $this->setVarByRef('str', $adminErr);
                    return 'dump_tpl.php';
                }
                break;

            case 'save':
                if ( $this->objUser->isAdmin() || $this->isValid('save') ) {
                    $this->objDbLibrary->saveRecord($this->getParam('mode', Null), $this->objUser->userId());
                    $ar=array();
                    return $this->nextAction('admin');
                } else {
                    $this->setVarByRef('str', $adminErr);
                    return 'dump_tpl.php';
                }
                break;
                  
            default:
                $this->setVar('str', $this->objLanguage->languageText("phrase_actionunknown").": ".$action);
                return 'dump_tpl.php';
                
        }#switch
    } # dispatch
 
 
    /**
    * 
    * Method to retrieve the data for edit and prepare 
    * the vars for the edit template.
    * @param string $mode The edit or add mode @values edit | add
    */
    function getForEdit($mode)
    {
        $this->setvar('mode', $mode);
        // retrieve the PK value from the querystring
        $keyvalue=$this->getParam("id", NULL);
        if (!$keyvalue) {
            die($this->objLanguage->languageText("modules_badkey").": ".$keyvalue);
        }
        // Get the data for edit
        $this->setVar('ar', $this->objDbLibrary->getRow('id', $keyvalue));
    }#getForedit

} #end of class
?>