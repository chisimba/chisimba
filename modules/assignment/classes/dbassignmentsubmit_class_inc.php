<?php

/**
 *
 * Assignments
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
 * @package   assignment2
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbassignmentsubmit_class_inc.php 24801 2012-12-09 12:00:21Z dkeats $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} // end security check

/**
 * Class to provide access to the table tbl_assignment_submit
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package assignment
 * @version 1
 */
class dbassignmentsubmit extends dbtable {

    /**
     * Method to construct the class
     */
    public function init() {
        $this->loadClass("link", "htmlelements");
        $this->objUser=$this->getObject("user","security");
        parent::init('tbl_assignment_submit');
    }

    /**
     * gets the submission
     * @param <type> $id
     * @return <type>
     */
    public function getSubmission($id) {
        return $this->getRow('id', $id);
    }

    /**
     * Gets the count of student submissions
     * @param string assignmentId
     * @return integer # rows in table
     */
    public function getCountStudentSubmissions($assignmentId) {
        return $this->getRecordCount("WHERE assignmentid='{$assignmentId}'");
    }

    /**
     * this gets student submissions
     * @param <type> $assignmentId
     * @param <type> $orderBy
     * @return <type>
     */
    public function getStudentSubmissions($assignmentId, $orderBy = 'firstname, datesubmitted') {
        $sql = ' SELECT tbl_assignment_submit.*, tbl_users.username, firstName, surname, staffnumber FROM tbl_assignment_submit
        INNER JOIN tbl_users ON tbl_assignment_submit.userid = tbl_users.userid  WHERE assignmentid=\'' . $assignmentId . '\' ORDER BY ' . $orderBy;

        return $this->getArray($sql);
    }

    /**
     * get assigment for a spfic student
     * @param <type> $studentId
     * @param <type> $assignmentId
     * @return <type>
     */
    public function getStudentAssignment($studentId, $assignmentId) {
        return $this->getAll(" WHERE assignmentid='{$assignmentId}' AND userid='{$studentId}' ORDER BY datesubmitted DESC");
    }

    /**
     * Method to check how many times a student submitted an assignment
     */
    public function numStudentAssignment($studentId, $assignmentId) {
        return $this->getRecordCount(" WHERE assignmentid='{$assignmentId}' AND userid='{$studentId}' ");
    }

    /**
     * Method to check whether a user is allowed to submit an assignment
     * @param string $studentId Record Id of User
     * @param string $assignmentId Assignment Id
     */
    public function checkOkToSubmit($studentId, $assignmentId) {
        $objAssignment = $this->getObject('dbassignment');

        $assignment = $objAssignment->getAssignment($assignmentId);

        // If assignment doesn't exist return FALSE;
        if ($assignment == FALSE) {
            return FALSE;
        }

        // Check if pass closing date
        // If multiple submits allowed, return TRUE
        if ($assignment['resubmit'] == '1') {
            return TRUE;
        }

        // Check if student has already submitted
        $numAssignments = $this->numStudentAssignment($studentId, $assignmentId);

        if ($numAssignments == 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * submit assignment upload
     * @param <type> $assignmentId
     * @param <type> $userId
     * @param <type> $fileId
     * @return <type>
     */
    public function submitAssignmentUpload($assignmentId, $userId, $fileId) {
        if (!$this->checkOkToSubmit($userId, $assignmentId)) {
            return 'alreadysubmitted';
        }

        $objFile = $this->getObject('dbfile', 'filemanager');
        $filePath = $objFile->getFullFilePath($fileId);

        if ($filePath == FALSE) {
            return 'filedoesnotexist';
        }

        if (!file_exists($filePath)) {
            return 'filedoesnotexist';
        }

        $submitId = $this->addStudentAssignmentUpload($assignmentId, $userId, $fileId);

        if ($submitId == FALSE) {
            return 'unabletosave';
        } else {
            return $this->processfile($submitId, $filePath);
        }
    }

    /**
     * Save assignemnt uploaded by a student
     * @param <type> $assignmentId
     * @param <type> $userId
     * @param <type> $fileId
     * @return <type>
     */
    private function addStudentAssignmentUpload($assignmentId, $userId, $fileId) {
        $submitid= $this->insert(array(
            'assignmentid' => $assignmentId,
            'userid' => $userId,
            'studentfileid' => $fileId,
            'datesubmitted' => date('Y-m-d H:i:s', time())
        ));
        $assignment = $this->getAssignment($assignmentId);
        if ($assignment[0]['email_alert_onsubmit'] == '1') {
            $this->prepareToSendEmail($submitid,$assignment[0]['userid']);
        }
        return $submitid;
    }

    private function prepareToSendEmail($submitId,$instructorid) {
        $link = new link($this->uri(array("action" => "viewsubmission", "id" => $submitId)));
        $objUser = $this->getObject("user", "security");
        $names = $objUser->fullname($userId);
        $username = $objUser->username($userId);
        $title = "New assignment submission from $username";
        $message = "New assignment submission from $names ($username). To view submission, click on this link. " . $link->href;
        $recipient = $objUser->email($instructorid);
        $this->sendEmail($title, $message, $recipient);
    }

    /**
     * get the assignment
     * @param <type> $id
     * @return <type>
     */
    private function getAssignment($id) {
        $sql =
                "select * from tbl_assignment where id = '$id'";
        return $this->getArray($sql);
    }

    /**
     * Method to email an assignment to users
     *
     * @param string $title Title of the assignment
     * @param string $message The assignment
     * @param array $recipients List of Recipients (array of email addresses);
     */
    private function sendEmail($title, $message, $recipient) {

        $objMailer = $this->getObject('mailer', 'mail');
        $message = html_entity_decode($message);
        $message = strip_tags($message);
        $objMailer->setValue('to', array($recipient));
        $objMailer->setValue('from', $this->objUser->email());
        $objMailer->setValue('fromName', $this->objUser->fullname());
        $objMailer->setValue('subject', $title);
        $objMailer->setValue('body', $message);
        $objMailer->setValue('AltBody', $message);
        $objMailer->send();
    }

    /**
     * File processing util
     * @param <type> $submitId
     * @param <type> $path
     */
    private function processfile($submitId, $path) {
        $objConfig = $this->getObject('altconfig', 'config');
        $savePath = $objConfig->getcontentBasePath() . '/assignment/submissions/' . $submitId;

        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $savePath = $objCleanUrl->cleanUpUrl($savePath);

        $objMkdir = $this->getObject('mkdir', 'files');
        $objMkdir->mkdirs($savePath, 0777);

        copy($path, $savePath . '/' . basename($path));
    }

    /**
     * Submit online assignment, but check if submited or not
     * @param <type> $assignmentId
     * @param <type> $userId
     * @param <type> $text
     * @return <type>
     */
    public function submitAssignmentOnline($assignmentId, $userId, $text) {
        if (!$this->checkOkToSubmit($userId, $assignmentId)) {
            return 'alreadysubmitted';
        }

        return $this->addStudentAssignmentOnline($assignmentId, $userId, $text);
    }

    /**
     * save the student online assignment
     * @param <type> $assignmentId
     * @param <type> $userId
     * @param <type> $text
     * @return <type>
     */
    private function addStudentAssignmentOnline($assignmentId, $userId, $text) {
        $assignment = $this->getAssignment($assignmentId);

        $submitid= $this->insert(array(
            'assignmentid' => $assignmentId,
            'userid' => $userId,
            'online' => $text,
            'datesubmitted' => date('Y-m-d H:i:s', time())
        ));

        if ($assignment[0]['email_alert_onsubmit'] == '1') {
            $this->prepareToSendEmail($submitid,$assignment[0]['userid']);
        }
        return $submitid;
    }

    /**
     * get assignment filename
     * @param <type> $submissionId
     * @param <type> $fileId
     * @return <type>
     */
    public function getAssignmentFilename($submissionId, $fileId) {
        $objFile = $this->getObject('dbfile', 'filemanager');
        $file = $objFile->getFile($fileId);

        // Do own search if file not found
        if ($file == FALSE) {

        }

        //var_dump($file);

        $objConfig = $this->getObject('altconfig', 'config');
        $filePath = $objConfig->getcontentBasePath() . '/assignment/submissions/' . $submissionId . '/' . $file['filename'];

        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $filePath = $objCleanUrl->cleanUpUrl($filePath);

        if (!file_exists($filePath)) {
            $originalFile = $objConfig->getcontentBasePath() . '/' . $file['path'];
            $originalFile = $objCleanUrl->cleanUpUrl($originalFile);

            if (file_exists($originalFile)) {

                $objMkdir = $this->getObject('mkdir', 'files');
                $objMkdir->mkdirs(dirname($filePath), 0777);

                copy($originalFile, $filePath);
            }
        }

        return $filePath;
    }

    /**
     * set assignment as marked
     */
    function markAssignment($id, $mark, $commentinfo) {
        return $this->update('id', $id, array('mark' => $mark, 'commentinfo' => $commentinfo, 'updated' => date('Y-m-d H:i:s', time())));
    }

    /**
     * set lecturer mark file
     * @param <type> $id
     * @param <type> $fileId
     * @return <type>
     */
    public function setLecturerMarkFile($id, $fileId) {
        return $this->update('id', $id, array('lecturerfileid' => $fileId));
    }

    /**
     * Method to get a list of assignments is the context.
     * Each assignment shows number of submissions, number marked and closing date.
     * @param string $context The current context
     */
    public function getContextSubmissions($context) {
        $sql = 'SELECT assign.id, assign.name, assign.closing_date, submit.datesubmitted, submit.mark ';
        $sql .= 'FROM tbl_assignment_submit AS submit ';
        $sql .= 'LEFT JOIN tbl_assignment as assign ON assign.id = submit.assignmentId ';
        $sql .= "WHERE context = '$context' ORDER BY assign.id";

        $data = $this->getArray($sql);
        return $data;
    }

}

?>