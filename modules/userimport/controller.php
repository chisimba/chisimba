<?php
/* -------------------- userimport class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* Controller class for the module load users from a file
* @copyright 2004 KEWL.NextGen
* @author James Scoble
*
* $Id: controller.php
*/

class userimport extends controller
{
    // Class properties
    var $userId;
    var $userPKId;
    var $contextCode;
    var $result;

    // Holders for objects that will be instantiated in the controller
    var $objUser;
    var $objUserAdmin;
    var $objUserImport;
    var $objUserBatch;
    var $objDBContext;
    var $objGroups;

    public function init()
    {
        // The user and useradmin objects
        $this->objUser= $this->getObject('user', 'security');
        //$this->objUserAdmin= $this->getObject('useradmin_model', 'security');

        // The config object
        //$this->objConfig=& $this->getObject('altconfig','config');
        $this->objConfig= $this->getObject('dbsysconfig','sysconfig');

        // The classes specific to this module
        $this->objUserImport= $this->getObject('importuserdata', 'userimport');
        $this->objUserBatch= $this->getObject('dbuserimport', 'userimport');

        // The language object
        $this->objLanguage=  $this->getObject('language','language');

        // The context and group objects used here to determine which context
        // the session is in, and whether the user has rights there.
        $this->objDBContext=$this->getObject('dbcontext','context');
        $this->objGroups=$this->getObject('groupadminmodel','groupadmin');

        // The properties are assigned values:
        // The user's primary key id is needed for looking up group membership.
        $this->userId=$this->objUser->userId();
        $this->userPKId=$this->objUser->PKId();
        $this->contextCode=$this->objDBContext->getContextCode();
        if ($this->contextCode==''){
            $this->contextCode='lobby';
        }
        $this->result='';

        // new function added for cron-based imports
        $this->noLoginNeeded=$this->checkCronImport();

        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }

    /**
    * Method to check for cron import
    */
    public function checkCronImport()
    {
        $cron=$this->getParam('cronimport','0');
        if ($cron=='1'){
            $pass=$this->getParam('importpass','0');
            if ($this->objConfig->getValue('autoimport','userimport') != sha1($pass)) { 
                return FALSE; 
            }
            $this->contextCode=$this->getParam('classmodule','lobby');
            return $this->objDBContext->valueExists('contextcode', $this->contextCode);
        } else {
            return FALSE;
        }
    }


    /**
    * This is the main method in the class
    * It calls other functions depending on the value of $action
    * @param string $action
    */
    public function dispatch($action=Null)
    {
        // To use this module you must be either a siteAdmin or
        // within a context in which you are a lecturer.
        if (!$this->allowUpload()){
            return 'error_tpl.php';
        }

        // Now the main 'switch' statement to parse values for "$action"
        switch ($action)
        {
        // Adding new users from a file
        case 'upload':
            $xmlbutton=$this->getParam('XML');
            $csvbutton=$this->getParam('CSV');
            if ($csvbutton!=''){
                $this->uploadData('CSV');
            } else if ($xmlbutton!=''){
                $this->uploadData('XML');
            }
            return 'main_tpl.php';
        // Removing an uploaded batch of users
        // It needs the batchcode passed as a param
        // and calls a method in the class for that table
        case 'delete':
            $batchCode=$this->getParam('batchCode');
            if ($batchCode!=''){
                $this->objUserBatch->deleteBatch($batchCode);
            }
            return 'main_tpl.php';
        // Display all the users in a batch
        case 'view':
            $batchCode=$this->getParam('batchCode');
            if ($batchCode!=''){
                $this->result=$this->objUserBatch->showBatch($batchCode);
            }
            return 'view_tpl.php';
        // Display all batches this user should "see"
        case 'list':
            return 'importlist_tpl.php';
        // Exporting a batch of users in CSV format
        case 'exportcsv':
            $batchCode=$this->getParam('batchCode');
            if ($batchCode!=''){
                $this->objUserBatch->exportCSV($batchCode);
                // A special page template is used, for download rather than display.
                $this->setPageTemplate('export_page_tpl.php');
                return 'export_tpl.php';
            }
            return 'main_tpl.php';
        // Exporting a batch of users in XML format
        case 'exportxml':
            $batchCode=$this->getParam('batchCode');
            if ($batchCode!=''){
                $this->objUserBatch->exportXML($batchCode);
                // A special page template is used, for download rather than display.
                $this->setPageTemplate('export_page_tpl.php');
                return 'export_tpl.php';
            }
            return 'main_tpl.php';
        // Exporting all students in a context in XML format
        case 'exportcontext':
            $contextCode=$this->getParam('contextCode',NULL);
            // Use the default context if none is supplied
            if ($contextCode==NULL){
                $contextCode=$this->contextCode;
            }
            $this->objUserBatch->exportClassXML($contextCode);
            // A special page template is used, for download rather than display.
            $this->setPageTemplate('export_page_tpl.php');
            return 'export_tpl.php';
        case 'remoteimport':
            $this->remoteImport($this->getParam('faculty'),$this->getParam('program'),$this->getParam('classmodule'));
            return 'remoteimport_tpl.php';
        case 'remotexml':
            $objRemoteImport=$this->getObject('remoteimport','userimport');
            $this->objUserBatch->exportName=$this->getParam('classmodule');
            $this->objUserBatch->export=$objRemoteImport->XMLexport($this->getParam('classmodule'));
            $this->setPageTemplate('export_page_tpl.php');
            return 'export_tpl.php';
        case 'remoteclassimport':
        // Case to import users from remote database - stores them as XML to use the standard method
            $classmodule=$this->getParam('classmodule',NULL);
            if ($classmodule!=NULL){
                $objRemoteImport=$this->getObject('remoteimport','userimport');
                $filename=$objRemoteImport->writeXML($classmodule);
                $this->uploadData('XML',$filename);
                unlink($filename);
                return 'main_tpl.php';
            }
        default:
            return ('main_tpl.php');
        }
    }


    /**
    * Method to determine if the user has to be logged in or not
    */
    function requiresLogin() // overides that in parent class
    {
        if ($this->noLoginNeeded){ return FALSE; }
        return TRUE;
    }

    /**
    * This is a method to determine if the user is allowed to upload a list of users
    * it checks if the user is a lecturer in a context, or is a siteAdmin
    * @returns Boolean TRUE|FALSE
    */
    function allowUpload()
    {
        if (($this->objUser->isAdmin())||($this->objUser->isContextLecturer())){
            return TRUE;
        }
        if ($this->noLoginNeeded){ return TRUE; }
        // default option at the end - neither Admin nor Lecturer-in-context
        return FALSE;
    }

    /**
    * This method checks for the existance of an uploaded file.
    * if one exists, it passes the file location to the batchImport()
    * method in the userimport class, where the new users are added
    * by calls to the security module's useradmin class.
    * @param string $method the type of file
    */
    function uploadData($method='CSV',$location='upload')
    {
        if ($location=='upload'){
            // Calling the $_FILES superglobal to get the uploaded file details
            $file=$_FILES['upload'.$method];
            $location=$file['tmp_name'];
        }
        if ($location!=''){
            $this->result=$this->objUserImport->batchImport($location,$method);
            if (isset($this->result['student'])){
                $batch=$this->objUserBatch->addBatch($this->userId,$this->contextCode,$this->result['student'],$method,$this->result['batchCode']);
            }
        }
    }

    function remoteImport($faculty,$program,$module)
    {
        $objRemoteImport=$this->getObject('remoteimport','userimport');
        if ($module!=''){
            $classlist=$objRemoteImport->getClassList($module);
            $this->setVar('classlist',$classlist);
            $this->setVar('remoteCode',$module);
            $classNameData=$objRemoteImport->getModuleName($module);
            if (is_array($classNameData)){
                $classNameData=(array)$classNameData[0];
                $this->setVar('remoteDesc',$classNameData['desc']);
            } else {
                $this->setVar('remoteDesc',$module);
            }
        }
        if ($program!=''){
            $modulelist=$objRemoteImport->getModules($program);
            $progNameData= $objRemoteImport->getProgramName($program);
            $progNameData=(array) $progNameData[0];
            $this->setVar('remoteProg',$progNameData['desc']);
            $this->setVar('modules',$modulelist);
        }
        if ($faculty!=''){
            $programlist=$objRemoteImport->getProgrammes($faculty);
            $facNameData=$objRemoteImport->getFacultyName($faculty);
            $facNameData=$objRemoteImport->getFacultyName($faculty);
            $facNameData=(array) $facNameData[0];
            $this->setVar('remoteFac',$facNameData['desc']);
            $this->setVar('programs',$programlist);
        }

        $facultylist=$objRemoteImport->getFaculties();
        $this->setVar('faculties',$facultylist);

        // Check to see if we are in a context that matches a UWC one
        if (($module=='')&&($program=='')&&($faculty=='')&&($this->contextCode!='lobby')){
            $thisclass=$objRemoteImport->getClassList($this->contextCode);
            if (is_array($thisclass)){
                $this->setVar('classlist',$thisclass);
                $this->setVar('remoteCode',$this->contextCode);
                $classNameData=array($objRemoteImport->getModuleName($this->contextCode));
                if (is_array($classNameData[0])){
                    $this->setVar('remoteDesc',$classNameData[0]['desc']);
                }
            }
        }

    }

}
?>
