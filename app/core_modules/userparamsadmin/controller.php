<?php
/* ----------- controller class extends controller for tbl_userparamsadmin------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
*
* Controller class for the userparams
* 
* @version $Id: controller.php 17950 2010-06-04 21:23:51Z dkeats $
* @copyright 2005 GNU GPL
*
*/
class userparamsadmin extends controller
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
        $this->action = $this->getParam('action', Null);
        $this->objDbUserparamsadmin = & $this->getObject("dbuserparamsadmin");
        $this->objLanguage = & $this->getObject("language", "language");
        $this->objUser = & $this->getObject("user", "security");
    }

    /**
    * Standard dispatch method 
    */
    function dispatch()
    {
        switch ($this->action) {
            case null:
            case "view":
                  $ar = $this->objDbUserparamsadmin->readConfig();
                  $this->setVarByRef('ar',$ar->toArray());
                  return "main_tpl.php";
                  break;

            case 'edit':
                 $this->getForEdit('edit');
                 $this->setVar('mode', 'edit');
                 if ($this->getParam('suppressall', FALSE)) {
                     $this->setPageTemplate('page_template.php');
                 }
                 return "edit_tpl.php";
                 break;

            case 'delete':
                 // retrieve the confirmation code from the querystring
                 $confirm=$this->getParam("confirm", "no");
                 if ($confirm=="yes") {
                 	$key = $this->getParam('key');
                 	$ret = $this->objDbUserparamsadmin->delete($key);
                 	
                 	//$ar = $this->objDbUserparamsadmin->readConfig();
                    //$this->objDbUserparamsadmin->delete($ar->toArray(), $this->getParam('key', Null));
                    $this->nextAction(null,null,'userparamsadmin');
                     }
                  break;

            case 'add':
                $this->setVar('mode', 'add');
                return "edit_tpl.php";
                break;

            case 'save':
            	$pname = $this->getParam('pname');
            	$ptag = $this->getParam('ptag');
                $this->objDbUserparamsadmin->writeProperties($this->getParam('mode', Null), $this->objUser->userId(), $pname, $ptag);
	            $this->nextAction(null,null,'userparamsadmin');
                
                break;

            default:
             die("Action unknown");
             break;

        }#switch
    } # dispatch


    /**
    * Method to retrieve the data for edit and prepare 
    * the vars for the edit template.
    *    @param string $mode The edit or add mode @values edit | add
    */
    function getForEdit($mode)
    {
        $this->setvar('mode', $mode);
        // retrieve the PK value from the querystring
        $key=$this->getParam("key", NULL);
        $value =$this->getParam("value", NULL);
        if (!$key) {
            die($this->objLanguage->languageText("modules_badkey").": ".$key);
        }
        $this->setVar('keyEdit', $key);
        $this->setVar('valueEdit', $value);
    }#getForedit
    
    

} #end of class
?>