<?php
/* ----------- controller class extends controller for tbl_quotes------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
*
* Controller class for comment module. The comment module allows
* the associating of comments with any table in the database. It
* is used, for example, with the stories module.
*
* @author Derek Keats
* @package comment
*
* @version $Id: controller.php 10794 2008-10-03 14:02:55Z joconnor $
* @copyright 2005 GNU GPL
*
*/
class comment extends controller
{

    /**
    * 
    * @var string $action The action parameter from the querystring 
    * 
    */
    public $action;
    
    /**
    * 
    * @var object $objUser String to hold instance of the user object 
    * 
    */
    public $objUser;
    
    /**
    * 
    * @var $objLanguage $objUser String to hold instance of the language object 
    * 
    */
    public $objLanguage;

    /**
    * 
    * Standard constructor method to retrieve the action from the
    * querystring, and instantiate the user and lanaguage objects
    * 
    */
  public  function init()
    {
        //Retrieve the action parameter from the querystring
        $this->action = $this->getParam('action', Null);
        //Create an instance of the User object
        $this->objUser =  & $this->getObject("user", "security");
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
    }

    /**
    * 
    * Standard dispatch method to handle adding and saving
    * of comments
    * 
    */
 public  function dispatch()
    {
        switch ($this->action) {
            case null:
            case "add":
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressIM',TRUE);
                //Suppress footer in the page (keep it simple)
                $this->setVar('suppressFooter', TRUE);
                return "input_tpl.php";
                break;
            //------------
            //Added 2006/07/21 Serge Meunier to add editing functionality
            case "edit":
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressIM',TRUE);
                //Suppress footer in the page (keep it simple)
                $this->setVar('suppressFooter', TRUE);
                return "inputedit_tpl.php";
                break;
            case 'editsave':
                //Suppress all the banners and toolbar
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressIM', TRUE);
                //Suppress footer in the page (keep it simple)
                $this->setVar('suppressFooter', TRUE);
                //Create an instance of the database class for this module
                $this->objDbcomment = & $this->getObject('dbcomment','comment');
                $this->objDbcomment->saveRecord('edit', $this->objUser->userId());
                $this->setVar('comment', $this->getParam('comment', NULL));
		
                return "saved_tpl.php";
                break;
            case 'delete':
               // Suppress all the banners and toolbar
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressIM', TRUE);
                //Suppress footer in the page (keep it simple)
                $this->setVar('suppressFooter', TRUE);
                //Create an instance of the database class for this module
                $this->objDbcomment = & $this->getObject('dbcomment','comment');
                $this->objDbcomment->deleteRecord($this->getParam('id', NULL));
                $this->setVar('comment', $this->getParam('comment', NULL));
                              
				 return 'deleted_tpl.php';
		break;
            //-------------
            case 'save':
                //Suppress all the banners and toolbar
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressIM', TRUE);
                //Suppress footer in the page (keep it simple)
                $this->setVar('suppressFooter', TRUE);
                //Create an instance of the database class for this module

               $this->objDbcomment = & $this->getObject('dbcomment','comment');
               $this->objDbcomment->saveRecord('add', $this->objUser->userId());
                $this->setVar('comment', $this->getParam('comment', NULL));
               print_r($this->setVar('comment', $this->getParam('comment', NULL)));
		return "saved_tpl.php";
                break;
            case 'approve':
                //Suppress all the banners and toolbar
                $this->setVar('pageSuppressToolbar', TRUE);
                $this->setVar('pageSuppressBanner', TRUE);
                $this->setVar('pageSuppressIM', TRUE);
                //Suppress footer in the page (keep it simple)
                $this->setVar('suppressFooter', TRUE);
                //Create an instance of the database class for this module
                $this->objDbcomment = & $this->getObject('dbcomment','comment');
                $this->objDbcomment->setApproval($this->getParam('id'),$this->getParam('approved'));
                $this->setVar('approved', $this->getParam('approved', '1'));
                return "approved_tpl.php";
                break;
            default:
                $this->setVar('str', $this->objLanguage->languageText("phrase_actionunknown").": ".$this->action);
                return 'dump_tpl.php';

        }#switch
    } # dispatch
} #class
?>
