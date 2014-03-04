<?php

/* -------------------- helloworld class extends controller ---------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Workgroup module
 * @author Jeremy O'Connor, Juliet Mulindwa
 * @copyright 2004 University of the Western Cape
 * $Id: controller.php 23256 2012-01-31 09:54:18Z joconnor $
 * Modified by David Wafula on 2008-01-09
 */
class workgroup extends controller {

    var $objUser;
    var $objLanguage;
    var $objDbWorkgroup;
    var $objDbWorkgroupUsers;
    //var $objDbUsers;
    var $workgroupId;

    /**
     * The Init function
     */
    function init() {
        $this->objUser = & $this->getObject('user', 'security');
        $this->objFile = & $this->getObject('dbfile', 'filemanager');
        $this->objLanguage = & $this->getObject('language', 'language');
        $this->objDbWorkgroup = & $this->getObject('dbworkgroup', 'workgroup');
        $this->objDbFiles = & $this->getObject('dbworkgroupfiles', 'workgroup');
        $this->objOps = & $this->getObject('workgroupops', 'workgroup');
        $this->objDbWorkgroupUsers = & $this->getObject('dbworkgroupusers', 'workgroup');
        //$this->objHelp=& $this->getObject('helplink','help');
        //$this->objHelp->rootModule="helloworld";
        //Get the activity logger class
        $this->objLog = $this->newObject('logactivity', 'logger');
        //Set it to log once per session
        //$this->objLog->logOncePerSession = TRUE;
        //Log this module call
        $this->objLog->log();
    }

    /**
     * The dispatch funtion
     * @param string $action The action
     * @return string The content template file
     */
    function dispatch($action=Null) {
        //$this->setLayoutTemplate('layout_tpl.php');


        $this->objConfig = &$this->getObject('altconfig', 'config');
        //$systemType = $this->objConfig->getValue("SYSTEM_TYPE", "contextabstract");
        //$isAlumni = ($systemType == "alumni");
        //$this->setVar('isAlumni',$isAlumni);
        // Set the layout template.

        $this->workgroupId = $this->objDbWorkgroup->getWorkgroupId();
        $userId = $this->objUser->userId();
        //$objfile = $this->objFile->userId();
        $userInWorkGroup = $this->objDbWorkgroupUsers->memberOfWorkGroup($userId, $this->workgroupId);

        if (is_null($this->workgroupId)) {
            $this->setLayoutTemplate("layout_tpl.php");
        } else {
            //$this->setLayoutTemplate("layout_tpl.php");
        }

        // 1. ignore action at moment as we only do one thing - say hello
        // 2. load the data object (calls the magical getObject which finds the
        //    appropriate file, includes it, and either instantiates the object,
        //    or returns the existing instance if there is one. In this case we
        //    are not actually getting a data object, just a helper to the
        //    controller.
        // 3. Pass variables to the template
        $this->setVarByRef('objUser', $this->objUser);
        $this->setVarByRef('objLanguage', $this->objLanguage);
        //$this->setVarByRef('objHelp', $this->objHelp);
        // return the name of the template to use  because it is a page content template
        // the file must live in the templates/content subdir of the module directory
        // Get context code.
        $objDbContext = &$this->getObject('dbcontext', 'context');
        $contextCode = $objDbContext->getContextCode();
        $workgroupId = '0';
        $userId = '0';
        $this->setVarByRef('contextCode', $contextCode);
        // Check if we are not in a context...
        if ($contextCode == null) {
            //if ($isAlumni) {
            //	$contextTitle = "Lobby";
            //	$this->setVarByRef('contextTitle', $contextTitle);
            //} else {
            return "error_tpl.php";
            //}
        } else {
            // ... else
            $contextTitle = $objDbContext->getTitle();
            $this->setVarByRef('contextTitle', $contextTitle);
        }
        switch ($action) {
            case 'joinworkgroup':
                $this->objDbWorkgroup->setWorkgroupId($this->getParam('workgroup'));
                return $this->nextAction(null, null);
            case 'leaveworkgroup':
                $this->objDbWorkgroup->unsetWorkgroupId();
                return $this->nextAction(null, null);
            case 'upload':
                $this->setVar('editMode', FALSE);
                $this->objDbWorkgroup->listAll($contextCode);
                return "uploadDocument_tpl.php";
            case 'uploadconfirm':
                $fields = array();
                $fields['workgroupid'] = $this->workgroupId;
                $fields['fileId'] = $this->getParam('fileupload');
                $fields['title'] = $this->getParam('title');
                $fields['description'] = $this->getParam('description');
                $fields['version'] = $this->getParam('version');


                $this->objDbFiles->insertFile($fields);
                return $this->nextAction(null, null);
                break;
            /* 	$this->objDbWorkgroup->uploadFile(
              //$contextCode,
              $workgroupId,
              $userId,
              $_POST['path'],
              $_POST['title'],
              $_POST['description'],
              $_POST['version']
              );
              $objfile->getFileName($this->getParam('fileupload'));
              //$objFile->getFileSize(($this->getParam('fileupload'));
              //$objFile->getFilePath($this->getParam('nameofforminput'));
              //$this->setLayoutTemplate("layout_tpl.php");

              //return 'upload_tpl.php';
              return "main_tpl.php"; */
            case 'ajaxgetfiles':

                echo $this->objOps->showFiles($this->getParam('workgroupid'));
                //echo $this->getParam('workgroupid');
                exit(0);
                break;
            case 'editfile':
                $this->setVar('editMode', TRUE);
                $this->setVar('fileId', $this->getParam('fileid'));
                $this->objDbWorkgroup->listAll($contextCode);
                return "uploadDocument_tpl.php";
            case 'editfileupload';
                $fields = array();
                $fields['workgroupid'] = $this->workgroupId;
                $fields['fileId'] = $this->getParam('fileupload');
                $fields['title'] = $this->getParam('title');
                $fields['description'] = $this->getParam('description');
                $fields['version'] = $this->getParam('version');


                $this->objDbFiles->editWorkgroupFiles($this->getParam('fileid'), $fields);
                return $this->nextAction(null, null);
            case 'deletefile':
                $this->objDbFiles->deleteWorkgroupFiles($this->getParam('fileid'));
            default:
                break;
        }
        $this->workgroupId = $this->objDbWorkgroup->getWorkgroupId();
        $this->setVarByRef('workgroupId', $this->workgroupId);
        if ($this->workgroupId == NULL) {
            // Check if a lecturer
            $objContextCondition = &$this->getObject('contextcondition', 'contextpermissions');
            $isContextLecturer = $objContextCondition->isContextMember('Lecturers');
            if ($isContextLecturer) {
                $workgroups = $this->objDbWorkgroup->getAll($contextCode);
                // var_dump($workgroups);
            } else {
                $workgroups = $this->objDbWorkgroup->getAllForUser($contextCode, $this->objUser->userId());
                // var_dump($workgroups);
            }
            $this->setVarByRef('workgroups', $workgroups);
            return "prelogin_tpl.php";
        }
        $this->workgroupDescription = $this->objDbWorkgroup->getDescription($this->workgroupId);
        $this->setVarByRef('workgroupDescription', $this->workgroupDescription);
        $members = $this->objDbWorkgroupUsers->listAll($this->workgroupId);
        $this->setVarByRef("members", $members);

        // Add workgroup description to breadcrumbs
        $objBreadcrumbs = & $this->getObject('tools', 'toolbar');
        $this->loadClass('link', 'htmlelements');
        $link = new link($this->uri(array(), 'workgroup'));
        $link->link = $this->workgroupDescription;
        $objBreadcrumbs->insertBreadCrumb(array($link->show()));

        // Get the groupAdminModel object.
        $groups = $this->getObject("groupAdminModel", "groupadmin");
        //if ($isAlumni) {
        // Get a list of students who are not already in a workgroup.
        //	$gid=$groups->getLeafId(array('Lecturers'));
        //}
        //else {
        // Get a list of students who are not already in a workgroup.
        $gid = $groups->getLeafId(array($contextCode, 'Lecturers'));
        //}
        //$lecturers = $groups->getGroupUsers($gid, array('userId',"'firstname' || ' ' || 'surname' AS fullname"), "ORDER BY fullname");
        //EDIT THIS ION MONDAY!!!!!!!!
        $lecturers_ = $groups->getGroupUsers($gid, array('userId', "'firstname' || ' ' || 'surname' AS fullname"), "ORDER BY fullname");
        $lecturers = array();
        foreach ($lecturers_ as $lecturer){
            $fullname = $lecturer['firstname'].' '.$lecturer['surname'];
            $lecturers[$fullname] = array(
                'userid'=>$lecturer['userid'],
                'fullname'=>$fullname
            );
        }
        ksort($lecturers);
        $this->setVar('lecturers', $lecturers);

        return "main_tpl.php";
    }

}

?>
