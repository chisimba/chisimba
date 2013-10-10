<?php
/* ------ normal controller class for forms ------*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
*
* Controller class for the forms module
*
* @author Charl Mert
*
*/
class forms extends controller
{

    /**
    * @var string object $objUser string to hold the user object
    */
    public $objUser;
    /**
    * @var string object $objLanguage string to hold the language object
    */
    public $objLanguage;
    /**
    * @var string object $objLog string to hold the logger object
    */
    public $objLog;
    
    /**
    * @var string object $objFile string to hold the file object
    */
    public $objFile;
    
    /**
    * Standard constructor method 
    */
    public function init()
    {

        // Supressing Prototype and Setting jQuery Version with Template Variables
        $this->setVar('SUPPRESS_PROTOTYPE', true);
        $this->setVar('SUPPRESS_JQUERY', false);
        $this->setVar('JQUERY_VERSION', '1.2.6');
    
        $this->jQuery =$this->newObject('jquery', 'jquery');
        $this->objUi =  & $this->getObject("ui", "forms");
        $this->objForms =  & $this->getObject("dbforms", "forms");
        $this->objFormRecords =  & $this->getObject("dbformrecords", "forms");
        $this->objFormSubRecords =  & $this->getObject("dbformsubrecords", "forms");
        $this->objExport =  & $this->getObject("export", "forms");
        $this->objUser =  & $this->getObject("user", "security");
        $this->objLanguage = &$this->getObject("language", "language");
        $this->objLog=$this->newObject('logactivity', 'logger');

        $this->objBox = $this->newObject('jqboxy', 'jquery');
        //$this->objConfig =$this->newObject('altconfig', 'config');

        //Live Query
        $this->jQuery->loadLiveQueryPlugin();
        //$this->jQuery->loadFormPlugin();

        // Log this module call
        $this->objLog->log();
        
        // Loading Common Styles
        $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('_common.css'.'">'));
        
        // Load the File Class from File Manager
        $this->objFile =& $this->getObject('dbfile', 'filemanager');
    }

   /**
    * Standard dispatch method 
    */
    public function dispatch()
    {
        $this->setLayoutTemplate('forms_layout_tpl.php');

        $action = $this->getParam('action', 'listforms');

        switch ($action) {
            case 'viewrecords':
                $formId = $this->getParam('id', NULL);
                $formDisplay = $this->objUi->getResultsForm($formId);
                $this->setVarByRef('formDisplay', $formDisplay);
                return 'form_viewrecords_tpl.php';
            break;

            case 'addform':
                $formId = $this->getParam('id', NULL);
                $formDisplay = $this->objUi->getAddEditForm($formId);
                $this->setVarByRef('formDisplay', $formDisplay);
                return 'form_add_tpl.php';
            break;

            case 'savedata':

                $formId = $this->getParam('form_id', NULL);

                //Adding the submitted record with all elements and respective values
                $this->objFormRecords->add($formId);

                //Retrieving the captured URL to return to
                $qryStr = $this->getParam('qry_str');

                //Processing Export Options
                //TODO: utilize export class here
                $formMsg = 'Form Sucessfully Submitted';

                header('Location: ?' . $qryStr . '&form_msg=' . $formMsg);
                return null;

                /*
                $qryParts = array();
                parse_str($qryStr, $qryParts);
                
                //Processing Export Options
                //TODO: utilize export class here
                $formMsg = 'Form Sucessfully Submitted';
                
                $qryParts['form_msg'] = $formMsg;

                if (is_array($qryParts)) {

                    if (isset($qryParts['action'])) {
                        $nAction = $qryParts['action']; 
                    } else {
                        $nAction = 'noaction'; 
                    }

                    if (isset($qryParts['module'])) {
                        $nModule = $qryParts['module']; 
                    } else {
                        $nModule = ''; 
                    }

                    return $this->nextAction($nAction, $qryParts, $nModule);
                }

                return $this->nextAction('', array(), '');
                */

            break;

            case 'createform':
                //Save the form / Edit if already exists
                $formId = $this->getParam('id', NULL);
                $this->objForms->add($formId);
                return 'form_list_tpl.php';
            break;

            case 'deleteform':
                //delete a form
                $formId = $this->getParam('id', NULL);
                $this->objForms->remove($formId);

                $formDisplay = $this->objUi->getAddEditForm($formId);
                $this->setVarByRef('formDisplay', $formDisplay);
                return 'form_list_tpl.php';
            break;

            case 'formpublish':
                $id = $this->getParam('id');
                $mode = $this->getParam('mode');
                $this->objForms->publish($id, $mode);
                return 'form_list_tpl.php';
            break;

            case 'export':
                $id = $this->getParam('id');
                $mode = $this->getParam('mode');

                switch($mode) {
                    case 'email':
                        
                        $to = $this->getParam('to');
                        $from = $this->getParam('from');
                        $subject = $this->getParam('subject', 'Chisimba Form Builder Report');
                        $message = $this->getParam('message');
                        
                        $recipients = '';
                        //$recipients = array('email1@something.com', 'email2@something.com');
                        
                        $this->objExport->sendEmail($to, $from, $subject, $message, $recipients);

                    break;

                }

                return 'form_list_tpl.php';
            break;

            default:
                return 'form_list_tpl.php';
            
            break;
        }
    }

    /**
    * Override the default requirement for login
    */
    public function requiresLogin()
    {
        $action = $this->getParam("action", NULL);
        //Allow any user to submit form data
        //TODO: Add permissions on form node level to allow restricted access to form creation and submissions
        if ($action == "savedata") {
            return FALSE;  
        } else {
            return TRUE;
        }
    }
}
?>