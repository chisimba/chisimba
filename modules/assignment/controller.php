<?php

/**
 *
 * Assignments
 *
 * Assignments enable students to view a list of booked assignments. The status is displayed indicating whether it is open, closed or if the student has submitted. The mark is shown once it has been marked.A new assignment can be opened for answering. Students can complete the assignment if its online and submit it. An uploadable or offline assignment can be completed and then loaded into the database. A marked assignment can be opened and the lecturer's comment can be viewed.
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   helloforms
 * @author    Tohir Solomons tsolomons@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 24643 2012-09-21 13:21:27Z dkeats $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
        /**
         * The $GLOBALS is an array used to control access to certain constants.
         * Here it is used to check if the file is opening in engine, if not it
         * stops the file from running.
         *
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         *
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Controller class for Chisimba for the module assignment2
 *
 * @author Tohir Solomons
 * @package assignment2
 *
 */
class assignment extends controller {

    /**
     *
     * @var string $objConfig String object property for holding the
     * configuration object
     * @access public;
     *
     */
    public $objConfig;
    /**
     *
     * @var string $objLanguage String object property for holding the
     * language object
     * @access public
     *
     */
    public $objLanguage;
    /**
     *
     * @var string $objLog String object property for holding the
     * logger object for logging user activity
     * @access public
     *
     */
    public $objLog;
    public $contextCode;

    /**
     *
     * Intialiser for the assignment2 controller
     * @access public
     *
     */
    public function init() {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('altconfig', 'config');
        // Create an instance of the database class
        //database objects , provides access to the related tables.
        $this->objAssignment = $this->getObject('dbassignment');
        $this->objAssignmentSubmit = $this->getObject('dbassignmentsubmit');
        $this->objAssignmentFunctions = $this->getObject('functions_assignment', 'assignment');
        $this->objAssignmentUploadablefiletypes = $this->getObject('dbassignmentuploadablefiletypes');
        $this->objAssignmentLearningOutcomes = $this->getObject('dbassignmentlearningoutcomes');
        $this->objAssignmentGroups = $this->getObject('dbassignmentworkgroups');
        $this->objDate = $this->getObject('dateandtime', 'utilities');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->objContext = $this->getObject('dbcontext', 'context');
        if ($this->objContext->isInContext()) {
            $this->contextCode = $this->objContext->getContextCode();
            $this->context = $this->objContext->getTitle();
        }
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->loadclass('link', 'htmlelements');
        $this->objLink = new link();
        //Get the activity logger class
        $this->objLog = $this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
        //Load Module Catalogue Class
        $this->objModuleCatalogue = $this->getObject('modules', 'modulecatalogue');

        $this->objContextGroups = $this->getObject('managegroups', 'contextgroups');

        if ($this->objModuleCatalogue->checkIfRegistered('activitystreamer')) {
            $this->objActivityStreamer = $this->getObject('activityops', 'activitystreamer');
            $this->eventDispatcher->addObserver(array($this->objActivityStreamer, 'postmade'));
            $this->eventsEnabled = TRUE;
        } else {
            $this->eventsEnabled = FALSE;
        }
    }

    public function isValid($action) {
        $restrictedActions = array('add', 'edit', 'saveassignment', 'updateassignment', 'delete', 'markassignments', 'saveuploadmark', 'saveonlinemark');

        if (in_array($action, $restrictedActions)) {
            $valid = $this->objUser->isCourseAdmin($this->contextCode);
        } else {
            $valid = TRUE;
        }

        return $valid;
    }

    /**
     *
     * The standard dispatch method for the assignment2 module.
     * The dispatch method uses methods determined from the action
     * parameter of the  querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     */
    public function dispatch($action) {
        if (!$this->objContext->isInContext()) {
            return "needtojoin_tpl.php";
        }
        if (!$this->isValid($action)) {
            return $this->nextAction(NULL, array('error' => 'nopermission'));
        }

        $this->setLayoutTemplate('assignment_layout_tpl.php');


        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $method = $this->__getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function __validAction(& $action) {
        if (method_exists($this, "__" . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return stromg the name of the method
     *
     */
    function __getMethod(& $action) {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__home";
        }
    }

    /* ------------- BEGIN: Set of methods to replace case selection ------------ */

    /**
     *
     * Method corresponding to the view action. It fetches the stories
     * into an array and passes it to a main_tpl content template.
     * @access private
     *
     */
    private function __home() {

        $assignments = $this->objAssignment->getAssignments($this->contextCode);
        $this->setVarByRef('assignments', $assignments);

        return 'assignment_home_tpl.php';
    }

    private function __displaylist() {

        $assignments = $this->objAssignment->getAssignments($this->contextCode);
        $this->setVarByRef('assignments', $assignments);

        return 'assignment_list_tpl.php';
    }

    private function __add() {

        $workgroupsinassignment = array();
        $learningoutcomesinassignment = array();
        $this->setVarByRef('workgroupsinassignment', $workgroupsinassignment);
        $this->setVarByRef('learningoutcomesinassignment', $learningoutcomesinassignment);
        $this->setVar('mode', 'add');

        return 'addedit_assignment_tpl.php';
    }

    private function __saveassignment() {
        //echo '<pre>';
        //var_dump($_POST);
        //echo '</pre>';
        //die("<br />----------------------------");
        $name = $this->getParam('name');
        $type = $this->getParam('type');
        $resubmit = $this->getParam('resubmit');
        $mark = $this->getParam('mark');
        $yearmark = $this->getParam('yearmark');
        $openingDate = $this->getParam('openingdate') . ' ' . $this->getParam('openingtime');
        $closingDate = $this->getParam('closingdate') . ' ' . $this->getParam('closingtime');
        $description = $this->getParam('description');
        $assesment_type = $this->getParam('assesment_type');
        $emailAlert = $this->getParam('emailalert');
        $filetypes = $this->getParam('filetypes', array());
        $filenameConversion = $this->getParam('filenameconversion','1');
        $visibility = $this->getParam('visibility');
        $emailalertonsubmit = $this->getParam('emailalertonsubmit');
        $groups = $this->getParam('groups');
        $goals = $this->getParam('goals');
        $usegroups = $this->getParam('groups_radio');
        $usegoals = $this->getParam('goals_radio');

        $result = $this->objAssignment->addAssignment($name, $this->contextCode, $description, $resubmit, $type, $mark, $yearmark, $openingDate, $closingDate, $assesment_type, $emailAlert, $filenameConversion, $visibility, $emailalertonsubmit,$usegroups,$usegoals);

        if ($result == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unabletosaveassignment'));
        } else {
            $this->objAssignmentUploadablefiletypes->addFiletypes($result, $filetypes);

            //add to activity streamer
            if ($this->eventsEnabled) {
                $message = $this->objUser->getsurname() . " " . $this->objLanguage->languageText('mod_assignment_addedassignment', 'assignment') . " " . $this->contextCode;
                $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title' => $message,
                    'link' => $this->uri(array()),
                    'contextcode' => $this->contextCode,
                    'author' => $this->objUser->fullname(),
                    'description' => $message));

                $this->eventDispatcher->post($this->objActivityStreamer, "context", array('title' => $message,
                    'link' => $this->uri(array()),
                    'contextcode' => null,
                    'author' => $this->objUser->fullname(),
                    'description' => $message));
            }

            $this->objAssignmentLearningOutcomes->deleteGoals($result);
            if (is_array($goals)) {

                if (count($goals) > 0) {
                    foreach ($goals as $goal) {
                        $this->objAssignmentLearningOutcomes->addGoal($result, $goal);
                    }
                }
            }

            $this->objAssignmentGroups->deleteWorkgroups($result);
            if (is_array($groups)) {

                if (count($groups) > 0) {
                    foreach ($groups as $group) {
                        $this->objAssignmentGroups->addWorkgroup($result, $group);
                    }

                    if (!empty($workgroupId)) {
                        $this->objAssignmentGroups->addWorkgroup($result, $workgroupId);
                        return $this->nextAction('view', array(
                            'id' => $result,
                            'workgroupId' => $workgroupId,
                            'fromworkgroup' => 1
                        ));
                    } else {
                        return $this->nextAction('view', array(
                            'id' => $result
                        ));
                    }
                }
            }

            return $this->nextAction('view', array('id' => $result));
        }
    }

    // display notes in a pop up window
    private function __showcomment() {
        $this->setLayoutTemplate('');
        $id = $this->getParam('id');
        $submissionId = $this->getParam('submissionid');
        $assignment = $this->objAssignment->getAssignment($id);
        $submission = $this->objAssignmentSubmit->getSubmission($submissionId);
        $this->setVarByRef('assignment', $assignment);
        $this->setVarByRef('submission', $submission);
        return 'onlineview_tpl.php';
    }

    private function __view() {
        $id = $this->getParam('id');

        $assignment = $this->objAssignment->getAssignment($id);

        if ($assignment == FALSE) {
            return $this->nextAction(NULL, array('error' => 'noassignment'));
        }

        if ($assignment['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }
        $learningoutcomesinassignment = $this->objAssignmentLearningOutcomes->getGoalsFormatted($id);
        $groups = $this->objAssignmentGroups->getGroupsFormatted($id);

        if ($this->isValid('markassignments')) {
            $submissions = $this->objAssignmentSubmit->getStudentSubmissions($assignment['id']);
        } else {
            $submissions = $this->objAssignmentSubmit->getStudentAssignment($this->objUser->userId(), $assignment['id']);
        }

        $this->setVarByRef('assignment', $assignment);
        $this->setVarByRef('goals', $learningoutcomesinassignment);
        $this->setVarByRef('groups', $groups);
        $this->setVarByRef('submissions', $submissions);

        return 'viewassignment_tpl.php';
    }

    function __edit() {
        $id = $this->getParam('id');

        $assignment = $this->objAssignment->getAssignment($id);
        $workgroupsinassignment = $this->objAssignmentGroups->getWorkgroups($id);
        $learningoutcomesinassignment = $this->objAssignmentLearningOutcomes->getGoals($id);

        if ($assignment == FALSE) {
            return $this->nextAction(NULL, array('error' => 'noassignment'));
        }

        if ($assignment['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }

        $this->setVarByRef('workgroupsinassignment', $workgroupsinassignment);
        $this->setVarByRef('learningoutcomesinassignment', $learningoutcomesinassignment);
        $this->setVarByRef('assignment', $assignment);
        $this->setVar('mode', 'edit');

        return 'addedit_assignment_tpl.php';
    }

    function __updateassignment() {
//        echo '<pre>';
//        var_dump($_POST);
//        echo '</pre>';
//        die;
        $id = $this->getParam('id');
        $name = $this->getParam('name');

        $resubmit = $this->getParam('resubmit');
        $type = $this->getParam('type');
        $mark = $this->getParam('mark');
        $yearmark = $this->getParam('yearmark');

        $openingDate = $this->getParam('openingdate') . ' ' . $this->getParam('openingtime');
        $closingDate = $this->getParam('closingdate') . ' ' . $this->getParam('closingtime');

        $description = $this->getParam('description');
        $assesment_type = $this->getParam('assesment_type');
        $emailAlert = $this->getParam('emailalert');
        $filetypes = $this->getParam('filetypes', array());
        $filenameConversion = $this->getParam('filenameconversion','1');
        $visibility = $this->getParam('visibility');
        $emailalertonsubmit = $this->getParam('emailalertonsubmit');
        $usegroups = $this->getParam('groups_radio');
        $usegoals = $this->getParam('goals_radio');

        $result = $this->objAssignment->updateAssignment($id, $name, $description, $resubmit, $type, $mark, $yearmark, $openingDate, $closingDate, $assesment_type, $emailAlert, $filenameConversion, $visibility, $emailalertonsubmit,$usegroups,$usegoals);
        $this->objAssignmentUploadablefiletypes->deleteFiletypes($id);
        $this->objAssignmentUploadablefiletypes->addFiletypes($id, $filetypes);
        $groups = $this->getParam('groups');
        $goals = $this->getParam('goals');
        $update = $result ? 'Y' : 'N';

        $this->objAssignmentLearningOutcomes->deleteGoals($id);
        if (is_array($goals)) {

            if (count($goals) > 0) {
                foreach ($goals as $goal) {
                    $this->objAssignmentLearningOutcomes->addGoal($id, $goal);
                }
            }
        }

        $this->objAssignmentGroups->deleteWorkgroups($id);
        if (is_array($groups)) {
            if (count($groups) > 0) {
                foreach ($groups as $group) {
                    $this->objAssignmentGroups->addWorkgroup($id, $group);
                }

                if (!empty($workgroupId)) {
                    $this->objAssignmentGroups->addWorkgroup($id, $workgroupId);
                    return $this->nextAction('view', array(
                        'id' => $id,
                        'workgroupId' => $workgroupId,
                        'fromworkgroup' => 1,
                        'update' => $update
                    ));
                } else {
                    return $this->nextAction('view', array(
                        'id' => $id, 'update' => $update
                    ));
                }
            }
        }

        return $this->nextAction('view', array('id' => $id, 'update' => $update));
    }

    function __uploadassignment() {
        $objFileUpload = $this->getObject('uploadinput', 'filemanager');
        $objFileUpload->enableOverwriteIncrement = TRUE;
        $results = $objFileUpload->handleUpload('fileupload');

        // Technically, FALSE can never be returned, this is just a precaution
        // FALSE means there is no fileinput with that name
        if ($results == FALSE) {
            return $this->nextAction('view', array('id' => $this->getParam('id'), 'error' => 'unabletoupload'));
        } else {
            // If successfully Uploaded
            if ($results['success']) {

                return $this->__submitassignment($results['fileid']);
            } else {
                // If not successfully uploaded
                return $this->nextAction('view', array('id' => $this->getParam('id'), 'error' => $results['reason']));
            }
        }
    }

    function __submitassignment($fileId=null) {
        if ($fileId == NULL) {
            $fileId = $this->getParam('assignment');
        }

        $result = $this->objAssignmentSubmit->submitAssignmentUpload($this->getParam('id'), $this->objUser->userId(), $fileId);

        return $this->nextAction('view', array('id' => $this->getParam('id'), 'message' => 'assignmentsubmitted'));
    }

    function __submitonlineassignment() {

        $result = $this->objAssignmentSubmit->submitAssignmentOnline($this->getParam('id'), $this->objUser->userId(), $this->getParam('text'));

        return $this->nextAction('view', array('id' => $this->getParam('id'), 'message' => 'assignmentsubmitted'));
    }

    function __viewsubmission() {
        $id = $this->getParam('id');

        $submission = $this->objAssignmentSubmit->getSubmission($id);

        if ($submission == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownsubmission'));
        }

        $assignment = $this->objAssignment->getAssignment($submission['assignmentid']);

        if ($assignment == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownassignment'));
        }

        if ($assignment['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }

        $this->setVarByRef('assignment', $assignment);
        $this->setVarByRef('submission', $submission);

        return 'viewsubmission_tpl.php';
    }

    function __downloadfile() {
        $id = $this->getParam('id');
        //$mode = $this->getParam('mode');
        $fileId = $this->getParam('fileid');

        $submission = $this->objAssignmentSubmit->getSubmission($id);

        if ($submission == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownsubmission'));
        }

        $assignment = $this->objAssignment->getAssignment($submission['assignmentid']);

        if ($assignment == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownassignment'));
        }

//        switch($mode){
//            case 'submitted':
//                $fileid = $submission['studentfileid'];
//                break;
//            case 'marked':
//                $fileid = $submission['lecturerfileid'];
//                break;
//            default:
//                return $this->nextAction(NULL, array('error'=>'unknownmode'));
//        }

        $filePath = $this->objAssignmentSubmit->getAssignmentFilename($submission['id'], $fileId);

        $objDateTime = $this->getObject('dateandtime', 'utilities');

        $objFile = $this->getObject('dbfile', 'filemanager');

        $file = $objFile->getFile($fileId);

        $extension = $file['datatype'];

        if ($assignment['filename_conversion'] == '0') {
            $filename = $file['filename'];
        } else {
            $filename = $this->objUser->fullName($submission['userid']) . ' ' . $objDateTime->formatDate($submission['datesubmitted']) . '.' . $extension;
        }

        $filename = str_replace(' ', '_', $filename);
        $filename = str_replace(':', '_', $filename);

        if (file_exists($filePath)) {
            // Set Mimetype
            header('Content-type: ' . $file['mimetype']);
            // Set filename and as download
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            // Load file
            readfile($filePath);
            exit;
        }
    }

    function __saveuploadmark() {
        $id = $this->getParam('id');
        $mark = $this->getParam('mark');
        $comment = $this->getParam('commentinfo');

        $this->objAssignmentSubmit->markAssignment($id, $mark, $comment);

        $submission = $this->objAssignmentSubmit->getSubmission($id);

        ///
        $filePath = $this->objConfig->getcontentPath() . '/assignment/submissions/' . $id;

        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $folderPath = $objCleanUrl->cleanUpUrl($filePath);

        $objFolder = $this->getObject('dbfolder', 'filemanager');

        $folderId = $objFolder->indexFolder($folderPath, FALSE);

        $objUpload = $this->getObject('upload', 'filemanager');
        $objUpload->setUploadFolder('/assignment/submissions/' . $id);
        $objUpload->enableOverwriteIncrement = TRUE;

        $restrictions = NULL;

        $fileUploadResultsArray = array();

        $fileResults = $objUpload->uploadFile('lectfile', $restrictions, $fileUploadResultsArray);

        if ($fileResults['success'] == TRUE) {
            $this->objAssignmentSubmit->setLecturerMarkFile($id, $fileResults['fileid']);
        }

        return $this->nextAction('view', array('id' => $submission['assignmentid'], 'message' => 'assignmentmarked', 'assignment' => $id));
    }

    function __saveonlinemark() {
        $id = $this->getParam('id');
        $mark = $this->getParam('mark');
        $comment = $this->getParam('commentinfo');

        $this->objAssignmentSubmit->markAssignment($id, $mark, $comment);

        $submission = $this->objAssignmentSubmit->getSubmission($id);

        return $this->nextAction('view', array('id' => $submission['assignmentid'], 'message' => 'assignmentmarked', 'assignment' => $id));
    }

    function __viewhtmlsubmission() {
        //die;
//        echo '<pre>';
//        var_dump($_GET);
//        echo '</pre>';
        $id = $this->getParam('id');
        $fileId = $this->getParam('fileid');
//        echo "Id==$id";
//        echo "FileId==$fileId";
//        die;
        $filePath = $this->objAssignmentSubmit->getAssignmentFilename($id, $fileId) . '.php';

        //$filePath = $this->objConfig->getcontentBasePath().'/assignment/submissions/'.$id.'/'.$filename;

        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $filePath = $objCleanUrl->cleanUpUrl($filePath);

        $permission = TRUE;

        include($filePath);
    }

    function __delete() {
        $id = $this->getParam('id');

        $assignment = $this->objAssignment->getAssignment($id);

        if ($assignment == FALSE) {
            return $this->nextAction(NULL, array('error' => 'noassignment'));
        }

        if ($assignment['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }

        $this->setVarByRef('assignment', $assignment);

        // Generate Random Number required for delete
        // This prevents delete by URL
        $randomNumber = rand(0, 1000);
        $this->setVar('randNumber', $randomNumber);
        $this->setSession($id, $randomNumber);

        return 'deleteassignment_tpl.php';
    }

    function __deleteconfirm() {
        $id = $this->getParam('id');

        $assignment = $this->objAssignment->getAssignment($id);

        if ($assignment == FALSE) {
            return $this->nextAction(NULL, array('error' => 'noassignment'));
        }

        if ($assignment['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }

        if ($this->getParam('confirm') == 'Y') {
            if ($this->getSession($id) == $this->getParam('randNumber')) {
                $result = $this->objAssignment->deleteAssignment($id);

                return $this->nextAction(NULL, array('id' => $id, 'message' => 'assignmentdeleted'));
            } else {
                return $this->nextAction('delete', array('id' => $id, 'error' => 'invaliddeletesession'));
            }
        } else {
            return $this->nextAction($this->getParam('return'), array('id' => $id, 'message' => 'deletecancelled'));
        }
    }

    function __exporttospreadsheet() {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $downloadfolder = $objSysConfig->getValue('DOWNLOAD_FOLDER', 'assignment');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $siteRoot = $this->objAltConfig->getsiteRoot();

        $assignmentid = $this->getParam('assignmentid');
        $assignment = $this->objAssignment->getAssignment($assignmentid);
        $submissions = $this->objAssignmentSubmit->getStudentSubmissions($assignmentid);
        $id = mktime() . rand();
        $filename = $assignmentid; //.'-'.$id;

        $objMkDir = $this->getObject('mkdir', 'files');
        $destinationDir = $this->objAltConfig->getcontentBasePath() . '/assignment/submissions/export';
        $objMkDir->mkdirs($destinationDir);
        $exportfile = $destinationDir . "/" . $filename . ".xls";

        if (file_exists($exportfile)) {
            unlink($exportfile);
        }
        $file = fopen($exportfile, "a");
        $objDateTime = $this->getObject('dateandtime', 'utilities');
        $objWashout = $this->getObject('washout', 'utilities');
        $type = $assignment['format'] == 0 ? "Online" : "Upload"; // JOC
        $name = $assignment['name'];
        $desc = strip_tags($assignment['description']);
        $percOfYear = $assignment['percentage'] . '%';

        $openingDate = $objDateTime->formatDate($assignment['opening_date']);
        $closingDate = $objDateTime->formatDate($assignment['closing_date']);
        fputs($file, "Title, $name\r\n");
        fputs($file, "Description, $desc\r\n");
        fputs($file, "Percentage of year mark, $percOfYear\r\n");
        fputs($file, "Opening Date, $openingDate\r\n");
        fputs($file, "Closing Date, $closingDate\r\n");
        fputs($file, "Type: $type\r\n");
        fputs($file, "\r\n");

        fputs($file, "\r\n");

        fputs($file, 'Student No,Name,Submision Date,Mark, Comment');
        fputs($file, "\r\n");
        foreach ($submissions as $submission) {

            $date = $objDateTime->formatDate($submission['datesubmitted']);
            $mark = "";
            $comment = "";
            $student = $this->objUser->fullname($submission['userid']);
            $username = $this->objUser->username($submission['userid']);
            if ($submission['mark'] == NULL) {
                $mark = $this->objLanguage->languageText('mod_assignment_notmarked', 'assignment', 'Not Marked');
            } else {
                $mark = $submission['mark'] . '%';
                $comment = $submission['commentinfo'];
            }

            $content = $username . ',' . $student . ',' . $date . ',' . $mark . ', ' . $comment . "\r\n";

            fputs($file, $content);
        }

        fclose($file);


        $this->nextAction('downloadassignmentfile', array("filename" => $filename));
    }

    function __downloadassignmentfile() {
        $filename = $this->getParam('filename');
        $this->setVarbyRef("filename", $filename);
        return "downloadsubmissionsfile_tpl.php";
    }
    /**
     * this downloads all student submissions as a zip file
     */
    function __downloadall() {
        //$objConfig = $this->getObject('altconfig', 'config');
        $objFilename = $this->newObject('filename', 'files');
        $objMkdir = $this->newObject('mkdir', 'files');
        $objDBFile = $this->newObject('dbfile', 'filemanager');
        $objWzip = $this->newObject('wzip', 'utilities');

        $assignmentId = $this->getParam("id");
        $assignment = $this->objAssignment->getAssignment($assignmentId);
        $assignmentName = $assignment['name'];
        $submissions = $this->objAssignmentSubmit->getStudentSubmissions($assignmentId);
        if (empty($submissions)) {
            trigger_error('There are no submissions!');
            return $this->nextAction(NULL, array());
        }
        //--$dirPath = $contentBasePath . 'assignment/submissions/' . $assignmentId;
        $contentBasePath = $this->objConfig->getcontentBasePath();
        //$contentPath = ;
        //$zipPath = ;
        $zipFullPath = $contentBasePath . 'assignment'.DIRECTORY_SEPARATOR.'submissions'.DIRECTORY_SEPARATOR;
        //$sysTemp = sys_get_temp_dir();
        //if ($sysTemp[strlen($sysTemp)-1] != DIRECTORY_SEPARATOR) {
        //    $sysTemp .= DIRECTORY_SEPARATOR;
        //}
        //$zipFullPath = $sysTemp.'chisimba'.DIRECTORY_SEPARATOR.$this->objConfig->serverName().DIRECTORY_SEPARATOR.'assignment'.DIRECTORY_SEPARATOR.'submissions'.DIRECTORY_SEPARATOR;
        //==preg_replace('/[^[:alnum:]_\s]/', '_', '\temp0_ \/:*?"<>|');
        $zipBaseName = $objFilename->makeFileName($this->context.' ('.$this->contextCode.')_'.$assignmentName.' ('.$assignmentId.').zip'); //preg_replace('/[^[:alnum:]_\s]/', '_', $assignmentName) . '.zip'; //$assignmentId //$contentBasePath . 'assignment/submissions/'
        $zipFN = $zipFullPath . $zipBaseName;
        //$zipURI = $this->objConfig->getsiteRoot() . $this->objConfig->getcontentPath() . $zipRelPath . rawurlencode($zipBaseName);
        $objMkdir->mkdirs($zipFullPath);
        if(file_exists($zipFN)){
            unlink($zipFN);
        }
        $files = array();
        foreach ($submissions as $submission) {
            $submissionId = $submission['id'];
            $userId = $submission['userid'];
            $userName = $submission['username'];
            $fileId = $submission['studentfileid'];
            $file = $objDBFile->getFile($fileId);
            $filePath = $contentBasePath . 'assignment/submissions/' . $submissionId . '/' . $file['filename'];
            if (!file_exists($filePath)) {
                //path
                $filePath = $contentBasePath . $file['path'];
                //trigger_error("Redirected:$filePath");
            }
            if (file_exists($filePath)) {
                //copy($filePath, $dirPath . '/' . $file['filename']);
                $files[] =
                    array(
                        $filePath,
                        $objFilename->makeFileName($userName.'_'.basename($filePath))
                    );
            }
        }
        if (empty($files)) {
            trigger_error('There are no submission files available!');
            log_debug("There are no submission files available!\n");
            throw new customException('There are no submission files available!');
            return $this->nextAction(NULL, array());
            //return FALSE;
        }
        else {
            // Create the zip file.
            if (!extension_loaded('zip')) {
                trigger_error('The ZIP extension is not loaded!');
                log_debug("The ZIP extension is not loaded!\n");
                throw new customException($this->objLanguage->languageText("mod_utilities_nozipext", "utilities"));
            }
            $zip = new ZipArchive();
            if ($zip->open($zipFN, ZIPARCHIVE::CREATE) !== TRUE) {
                trigger_error("Cannot open [$zipFN]!");
                log_debug("Cannot open [$zipFN]!\n");
                throw new customException($this->objLanguage->languageText("mod_utilities_nozipcreate", "utilities"));
            } else {
                foreach ($files as $f) {
                    $FN = $f[0];
                    $localFN = $f[1];
                    $zip->addFile($FN, $localFN);
                }
                $zip->close();
                //--return $zipFN;
            }
            //return $zipFN;
            //$fn = $objWzip->packFilesZip($zipFN, $files, TRUE, FALSE);
            //return $fn;
        }
        //$zipFN = $this->objAssignmentFunctions->createZipFromSubmissions($assignmentName, $submissions);
        //if (FALSE === $zipFN) {
            //trigger_error('No ZIP filename!');
            //return $this->nextAction(NULL, array());
        //}
        //else {
        if (!file_exists($zipFN)) {
            trigger_error('ZIP file does not exist!');
            log_debug("ZIP file does not exist!\n");
            throw new customException('ZIP file does not exist');
            //return $this->nextAction(NULL, array());
        }
        else {
            //header('Location: '.$zipURI);
            //return NULL;
            $output_compression = ini_get('zlib.output_compression');
            if ('On' == $output_compression || (bool)$output_compression) {
                ini_set('zlib.output_compression', 'Off');
            }
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: private", false);
            header("Content-Type: application/zip");
            header("Content-Disposition: attachment; filename=\"" . $zipBaseName . "\";");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . filesize($zipFN));
            readfile($zipFN);
            unlink($zipFN);
            return NULL;
            //exit(0);
        }
        //}
        /*
        if (file_exists($zipname)) {
// Set Mimetype
            header('Content-type: application/zip');
// Set filename and as download
            header('Content-Disposition: attachment; filename="' . $assignmentId . '.zip"');
// Load file
            readfile($zipname);
            exit;
        }
        */
    }
    /* ------------- END: Set of methods to replace case selection ------------ */
}

?>
