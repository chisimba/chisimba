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
* @version $Id: controller.php 14552 2009-08-27 11:52:17Z jsc $
* @version $Id: controller.php,v 1.3 2006/09/14 Abdurahim Ported to PHP5
* @copyright 2005 GNU GPL
*
*/
class textblock extends controller
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
        $this->objDb = $this->getObject("dbtextblock");

        //Create an instance of the language object
        $this->objLanguage = $this->getObject("language", "language");
        // Load the module helper Javascript
        $this->appendArrayVar('headerParams',
        $this->getJavaScriptFile('textblock.js',
          'textblock'));
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
            case 'text':
            case 'view':
                // Set the layout template to compatible one
                if ($this->objUser->isAdmin()) {
                    $this->setLayoutTemplate('layout_tpl.php');
                    return 'narrowblockview_tpl.php';
                } else {
                    $this->setVar('str', 
                      "<br /><span class='error'>" 
                        . $this->objLanguage->languageText(
                          "phrase_nopermission",NULL,"You have no permission for"
                        ).": ".$this->action) . "</span>";
                    return 'dump_tpl.php';
                }
                break;
            case 'widetext':
                // Set the layout template to compatible one
                if ($this->objUser->isAdmin()) {
                    $this->setLayoutTemplate('layout_tpl.php');
                    return 'wideblockview_tpl.php';
                } else {
                    $this->setVar('str', 
                      "<br /><span class='error'>" 
                        . $this->objLanguage->languageText(
                          "phrase_nopermission",NULL,"You have no permission for"
                        ).": ".$this->action) . "</span>";
                    return 'dump_tpl.php';
                }
                break;
            case 'deleteajax':
                if ($this->objUser->isAdmin()) {
                    $this->objDb->delete("id", $this->getParam('id', Null));
                    die("RECORD_DELETED");
                } else {
                    die("ERR_NOPERMISSION");
                }
                break;
            case 'ajaxedit':
                if ($this->objUser->isAdmin()) {
                    // Set the layout template to compatible one
                    $this->setLayoutTemplate('layout_tpl.php');
                    return "editajax_tpl.php";
                } else {
                    $this->setVar('str', 
                      "<br /><span class='error'>" 
                        . $this->objLanguage->languageText(
                          "phrase_nopermission",NULL,"You have no permission for"
                        ).": ".$this->action) . "</span>";
                    return 'dump_tpl.php';
                }
                break;
            case 'save':
                if ($this->objUser->isAdmin()) {
                    $this->objDb->saveRecord($this->getParam('mode', Null), 
                      $this->objUser->userId());
                    $blockType = $this->getParam('blocktype', 'narrowblock');
                    return $this->nextAction($blockType);
                } else {
                    $this->setVar('str', 
                      "<br /><span class='error'>" 
                        . $this->objLanguage->languageText(
                          "phrase_nopermission",NULL,"You have no permission for"
                        ).": ".$this->action) . "</span>";
                    return 'dump_tpl.php';
                }
                break;
            default:
                $this->setVar('str', $this->objLanguage->languageText("phrase_actionunknown",NULL,"Unknown action").": ".$this->action);
                return 'dump_tpl.php';

        }
    }
    
    /**
    * Override the default requirement for login
    */
    public function requiresLogin()
    {
        return TRUE;
    }
}
?>
