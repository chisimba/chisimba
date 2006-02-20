<?php
/* ----------- controller class extends controller for tbl_storycategory------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
*
* Controller class for the table tbl_storycategory
*
* @author Administrative User
*
*
* @version $Id$
* @copyright 2005 GNU GPL
*
*/
class storycategoryadmin extends controller
{

    /**
    * @var string $action The action parameter from the querystring 
    */
    var $action;

    /**
    * Standard constructor method 
    */
    function init()
    {
        //Retrieve the action parameter from the querystring
        $this->action = $this->getParam('action', Null);
        //Create an instance of the database class for this module
        $this->objDbstorycategory = & $this->getObject("dbstorycategory");
        //Create an instance of the User object
        $this->objUser =  & $this->getObject("user", "security");
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
    }

    /**
    * Standard dispatch method 
    */
    function dispatch()
    {
        switch ($this->action) {
            case null:
            case "view":
                  $ar = $this->objDbstorycategory->getAll();
                  $this->setVarByRef('ar', $ar);
                  return "main_tpl.php";
                  break;

            case 'edit':
                 $this->getForEdit('edit');
                 $this->setVar('mode', 'edit');
                 return "edit_tpl.php";
                 break;

            case 'delete':
                 // retrieve the confirmation code from the querystring
                 $confirm=$this->getParam("confirm", "no");
                 if ($confirm=="yes") {
                     $this->objDbstorycategory->delete("id", $this->getParam('id', Null));
                     $ar = $this->objDbstorycategory->getAll();
                     $this->setVarByRef('ar', $ar);
                     return "main_tpl.php";
                 }
                  break;

            case 'add':
                $this->setVar('mode', 'add');
                return "edit_tpl.php";
                break;

            case 'save':
                if ($this->objDbstorycategory->saveRecord($this->getParam('mode', Null), $this->objUser->userId())) {
                    $ar=array();
                    $ar = $this->objDbstorycategory->getAll();
                    $this->setVarByRef('ar', $ar);
                    return "main_tpl.php";
                } else {
                    $this->setVar('str', $this->objDbstorycategory->errCode);
                    return "dump_tpl.php";
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
    *    @param string $mode The edit or add mode @values edit | add
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
        $this->setVar('ar', $this->objDbstorycategory->getRow('id', $keyvalue));
    }#getForedit

} #end of class
?>