<?php

/**
 *
 * Practicals
 *
 * Practicals enable students to view a list of booked practicals. The status is displayed indicating whether it is open, closed or if the student has submitted. The mark is shown once it has been marked.A new practical can be opened for answering. Students can complete the practical if its online and submit it. An uploadable or offline practical can be completed and then loaded into the database. A marked practical can be opened and the lecturer's comment can be viewed.
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
 * @version   $Id$
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
 * Controller class for Chisimba for the module practical2
 *
 * @author Tohir Solomons
 * @package practical2
 *
 */
class practicals extends controller {

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
     * Intialiser for the practical2 controller
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
        $this->objPractical = $this->getObject('dbpracticals', 'practicals');
        $this->objPracticalSubmit = $this->getObject('dbpracticalssubmit', 'practicals');
        $this->objPracticalFunctions = $this->getObject('functions_practicals', 'practicals');
        $this->objPracticalUploadablefiletypes = $this->getObject('dbpracticalsuploadablefiletypes', 'practicals');
        $this->objPracticalLearningOutcomes = $this->getObject('dbpracticalslearningoutcomes', 'practicals');
        $this->objPracticalGroups = $this->getObject('dbpracticalsworkgroups', 'practicals');

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
        $restrictedActions = array('add', 'edit', 'savepractical', 'updatepractical', 'delete', 'markpracticals', 'saveuploadmark', 'saveonlinemark');

        if (in_array($action, $restrictedActions)) {
            $valid = $this->objUser->isCourseAdmin($this->contextCode);
        } else {
            $valid = TRUE;
        }

        return $valid;
    }

    /**
     *
     * The standard dispatch method for the practical2 module.
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

        $this->setLayoutTemplate('practical_layout_tpl.php');


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

        $practicals = $this->objPractical->getPracticals($this->contextCode);
        $this->setVarByRef('practicals', $practicals);

        return 'practical_home_tpl.php';
    }

    private function __displaylist() {

        $practicals = $this->objPractical->getPracticals($this->contextCode);
        $this->setVarByRef('practicals', $practicals);

        return 'practical_list_tpl.php';
    }

    private function __add() {
        $this->setVar('mode', 'add');

        return 'addedit_practical_tpl.php';
    }

    private function __savepractical() {
//        echo '<pre>';
//        var_dump($_POST);
//        echo '</pre>';
//        die;
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

        $result = $this->objPractical->addPractical($name, $this->contextCode, $description, $resubmit, $type, $mark, $yearmark, $openingDate, $closingDate, $assesment_type, $emailAlert, $filenameConversion, $visibility, $emailalertonsubmit,$usegroups,$usegoals);

        if ($result == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unabletosavepractical'));
        } else {
            $this->objPracticalUploadablefiletypes->addFiletypes($result, $filetypes);

            //add to activity streamer
            if ($this->eventsEnabled) {
                $message = $this->objUser->getsurname() . " " . $this->objLanguage->languageText('mod_practicals_addedpractical', 'practicals') . " " . $this->contextCode;
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

            $this->objPracticalLearningOutcomes->deleteGoals($result);
            if (is_array($goals)) {

                if (count($goals) > 0) {
                    foreach ($goals as $goal) {
                        $this->objPracticalLearningOutcomes->addGoal($result, $goal);
                    }
                }
            }

            $this->objPracticalGroups->deleteWorkgroups($result);
            if (is_array($groups)) {

                if (count($groups) > 0) {
                    foreach ($groups as $group) {
                        $this->objPracticalGroups->addWorkgroup($result, $group);
                    }

                    if (!empty($workgroupId)) {
                        $this->objPracticalGroups->addWorkgroup($result, $workgroupId);
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
        $practical = $this->objPractical->getPractical($id);
        $submission = $this->objPracticalSubmit->getSubmission($submissionId);
        $this->setVarByRef('practical', $practical);
        $this->setVarByRef('submission', $submission);
        return 'onlineview_tpl.php';
    }

    private function __view() {
        $id = $this->getParam('id');

        $practical = $this->objPractical->getPractical($id);

        if ($practical == FALSE) {
            return $this->nextAction(NULL, array('error' => 'nopractical'));
        }

        if ($practical['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }
        $learningoutcomesinpractical = $this->objPracticalLearningOutcomes->getGoalsFormatted($id);
        $groups = $this->objPracticalGroups->getGroupsFormatted($id);

        $this->setVarByRef('practical', $practical);
        $this->setVarByRef('goals', $learningoutcomesinpractical);
        $this->setVarByRef('groups', $groups);
        return 'viewpractical_tpl.php';
    }

    function __edit() {
        $id = $this->getParam('id');

        $practical = $this->objPractical->getPractical($id);
        $workgroupsinpractical = $this->objPracticalGroups->getWorkgroups($id);
        $learningoutcomesinpractical = $this->objPracticalLearningOutcomes->getGoals($id);

        if ($practical == FALSE) {
            return $this->nextAction(NULL, array('error' => 'nopractical'));
        }

        if ($practical['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }

        $this->setVarByRef('workgroupsinpractical', $workgroupsinpractical);
        $this->setVarByRef('learningoutcomesinpractical', $learningoutcomesinpractical);
        $this->setVarByRef('practical', $practical);
        $this->setVar('mode', 'edit');

        return 'addedit_practical_tpl.php';
    }

    function __updatepractical() {
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

        $result = $this->objPractical->updatePractical($id, $name, $description, $resubmit, $type, $mark, $yearmark, $openingDate, $closingDate, $assesment_type, $emailAlert, $filenameConversion, $visibility, $emailalertonsubmit,$usegroups,$usegoals);
        $this->objPracticalUploadablefiletypes->deleteFiletypes($id);
        $this->objPracticalUploadablefiletypes->addFiletypes($id, $filetypes);
        $groups = $this->getParam('groups');
        $goals = $this->getParam('goals');
        $update = $result ? 'Y' : 'N';

        $this->objPracticalLearningOutcomes->deleteGoals($id);
        if (is_array($goals)) {

            if (count($goals) > 0) {
                foreach ($goals as $goal) {
                    $this->objPracticalLearningOutcomes->addGoal($id, $goal);
                }
            }
        }

        $this->objPracticalGroups->deleteWorkgroups($id);
        if (is_array($groups)) {
            if (count($groups) > 0) {
                foreach ($groups as $group) {
                    $this->objPracticalGroups->addWorkgroup($id, $group);
                }

                if (!empty($workgroupId)) {
                    $this->objPracticalGroups->addWorkgroup($id, $workgroupId);
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

    function __uploadpractical() {
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

                return $this->__submitpractical($results['fileid']);
            } else {
                // If not successfully uploaded
                return $this->nextAction('view', array('id' => $this->getParam('id'), 'error' => $results['reason']));
            }
        }
    }

    function __submitpractical($fileId=null) {
        if ($fileId == NULL) {
            $fileId = $this->getParam('practical');
        }

        $result = $this->objPracticalSubmit->submitPracticalUpload($this->getParam('id'), $this->objUser->userId(), $fileId);

        return $this->nextAction('view', array('id' => $this->getParam('id'), 'message' => 'practicalsubmitted'));
    }

    function __submitonlinepractical() {

        $result = $this->objPracticalSubmit->submitPracticalOnline($this->getParam('id'), $this->objUser->userId(), $this->getParam('text'));

        return $this->nextAction('view', array('id' => $this->getParam('id'), 'message' => 'practicalsubmitted'));
    }

    function __viewsubmission() {
        $id = $this->getParam('id');

        $submission = $this->objPracticalSubmit->getSubmission($id);

        if ($submission == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownsubmission'));
        }

        $practical = $this->objPractical->getPractical($submission['practicalid']);

        if ($practical == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownpractical'));
        }

        if ($practical['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }

        $this->setVarByRef('practical', $practical);
        $this->setVarByRef('submission', $submission);

        return 'viewsubmission_tpl.php';
    }

    function __downloadfile() {
        $id = $this->getParam('id');
        //$mode = $this->getParam('mode');
        $fileId = $this->getParam('fileid');

        $submission = $this->objPracticalSubmit->getSubmission($id);

        if ($submission == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownsubmission'));
        }

        $practical = $this->objPractical->getPractical($submission['practicalid']);

        if ($practical == FALSE) {
            return $this->nextAction(NULL, array('error' => 'unknownpractical'));
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

        $filePath = $this->objPracticalSubmit->getPracticalFilename($submission['id'], $fileId);

        $objDateTime = $this->getObject('dateandtime', 'utilities');

        $objFile = $this->getObject('dbfile', 'filemanager');

        $file = $objFile->getFile($fileId);

        $extension = $file['datatype'];

        if ($practical['filename_conversion'] == '0') {
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

        $this->objPracticalSubmit->markPractical($id, $mark, $comment);

        $submission = $this->objPracticalSubmit->getSubmission($id);

        ///
        $filePath = $this->objConfig->getcontentPath() . '/practical/submissions/' . $id;

        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $folderPath = $objCleanUrl->cleanUpUrl($filePath);

        $objFolder = $this->getObject('dbfolder', 'filemanager');

        $folderId = $objFolder->indexFolder($folderPath, FALSE);

        $objUpload = $this->getObject('upload', 'filemanager');
        $objUpload->setUploadFolder('/practical/submissions/' . $id);
        $objUpload->enableOverwriteIncrement = TRUE;

        $restrictions = NULL;

        $fileUploadResultsArray = array();

        $fileResults = $objUpload->uploadFile('lectfile', $restrictions, $fileUploadResultsArray);

        if ($fileResults['success'] == TRUE) {
            $this->objPracticalSubmit->setLecturerMarkFile($id, $fileResults['fileid']);
        }

        return $this->nextAction('view', array('id' => $submission['practicalid'], 'message' => 'practicalmarked', 'practical' => $id));
    }

    function __saveonlinemark() {
        $id = $this->getParam('id');
        $mark = $this->getParam('mark');
        $comment = $this->getParam('commentinfo');

        $this->objPracticalSubmit->markPractical($id, $mark, $comment);

        $submission = $this->objPracticalSubmit->getSubmission($id);

        return $this->nextAction('view', array('id' => $submission['practicalid'], 'message' => 'practicalmarked', 'practical' => $id));
    }

    function __viewhtmlsubmission() {

        $id = $this->getParam('id');
        $fileId = $this->getParam('fileid');
        $filePath = $this->objPracticalSubmit->getPracticalFilename($id, $fileId) . '.php';

        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $filePath = $objCleanUrl->cleanUpUrl($filePath);

        $permission = TRUE;

        include($filePath);
    }

    function __delete() {
        $id = $this->getParam('id');

        $practical = $this->objPractical->getPractical($id);

        if ($practical == FALSE) {
            return $this->nextAction(NULL, array('error' => 'nopractical'));
        }

        if ($practical['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }

        $this->setVarByRef('practical', $practical);

        // Generate Random Number required for delete
        // This prevents delete by URL
        $randomNumber = rand(0, 1000);
        $this->setVar('randNumber', $randomNumber);
        $this->setSession($id, $randomNumber);

        return 'deletepractical_tpl.php';
    }

    function __deleteconfirm() {
        $id = $this->getParam('id');

        $practical = $this->objPractical->getPractical($id);

        if ($practical == FALSE) {
            return $this->nextAction(NULL, array('error' => 'nopractical'));
        }

        if ($practical['context'] != $this->contextCode) {
            return $this->nextAction(NULL, array('error' => 'wrongcontext'));
        }

        if ($this->getParam('confirm') == 'Y') {
            if ($this->getSession($id) == $this->getParam('randNumber')) {
                $result = $this->objPractical->deletePractical($id);

                return $this->nextAction(NULL, array('id' => $id, 'message' => 'practicaldeleted'));
            } else {
                return $this->nextAction('delete', array('id' => $id, 'error' => 'invaliddeletesession'));
            }
        } else {
            return $this->nextAction($this->getParam('return'), array('id' => $id, 'message' => 'deletecancelled'));
        }
    }

    function __exporttospreadsheet() {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $downloadfolder = $objSysConfig->getValue('DOWNLOAD_FOLDER', 'practical');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $siteRoot = $this->objAltConfig->getsiteRoot();

        $practicalid = $this->getParam('practicalid');
        $practical = $this->objPractical->getPractical($practicalid);
        $submissions = $this->objPracticalSubmit->getStudentSubmissions($practicalid);
        $id = mktime() . rand();
        $filename = $practicalid; //.'-'.$id;

        $objMkDir = $this->getObject('mkdir', 'files');
        $destinationDir = $this->objAltConfig->getcontentBasePath() . '/practical/submissions/export';
        $objMkDir->mkdirs($destinationDir);
        $exportfile = $destinationDir . "/" . $filename . ".xls";

        if (file_exists($exportfile)) {
            unlink($exportfile);
        }
        $file = fopen($exportfile, "a");
        $objDateTime = $this->getObject('dateandtime', 'utilities');
        $objWashout = $this->getObject('washout', 'utilities');
        $type = $submission['online'] == 0 ? "Online" : "Upload";
        $name = $practical['name'];
        $desc = strip_tags($practical['description']);
        $percOfYear = $practical['percentage'] . '%';

        $openingDate = $objDateTime->formatDate($practical['opening_date']);
        $closingDate = $objDateTime->formatDate($practical['closing_date']);
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
                $mark = $this->objLanguage->languageText('mod_practicals_notmarked', 'practicals', 'Not Marked');
            } else {
                $mark = $submission['mark'] . '%';
                $comment = $submission['commentinfo'];
            }

            $content = $username . ',' . $student . ',' . $date . ',' . $mark . ', ' . $comment . "\r\n";

            fputs($file, $content);
        }

        fclose($file);


        $this->nextAction('downloadpracticalfile', array("filename" => $filename));
    }

    function __downloadpracticalfile() {
        $filename = $this->getParam('filename');
        $this->setVarbyRef("filename", $filename);
        return "downloadsubmissionsfile_tpl.php";
    }
    /**
     * this downloads all student submissions as a zip file
     */
    function __downloadall() {

        $practicalId = $this->getParam("id");
        $submissions = $this->objPracticalSubmit->getStudentSubmissions($practicalId);

        $zipname = $this->objPracticalFunctions->createZipFromSubmissions($submissions, $practicalId);

        if (file_exists($zipname)) {
            // Set Mimetype
            header('Content-type: application/zip');
            // Set filename and as download
            header('Content-Disposition: attachment; filename="' . $practicalId . '.zip"');
            // Load file
            readfile($zipname);
            exit;
        }
    }
    /* ------------- END: Set of methods to replace case selection ------------ */
}

?>
