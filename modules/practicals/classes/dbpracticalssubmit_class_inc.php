<?php

/**
 *
 * Practicals
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
 * @package   practical2
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbpracticalsubmit_class_inc.php 19292 2010-10-16 19:33:22Z davidwaf $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} // end security check

/**
 * Class to provide access to the table tbl_practicals_submit
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package practical
 * @version 1
 */
class dbpracticalssubmit extends dbtable {

    /**
     * Method to construct the class
     */
    public function init() {
        $this->loadClass("link", "htmlelements");
        $this->objUser=$this->getObject("user","security");
        parent::init('tbl_practicals_submit');
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
     * @param string practicalId
     * @return integer # rows in table
     */
    public function getCountStudentSubmissions($practicalId) {
        return $this->getRecordCount("WHERE practicalid='{$practicalId}'");
    }

    /**
     * this gets student submissions
     * @param <type> $practicalId
     * @param <type> $orderBy
     * @return <type>
     */
    public function getStudentSubmissions($practicalId, $orderBy = 'firstname, datesubmitted') {
        $sql = ' SELECT tbl_practicals_submit.*, firstName, surname, staffnumber FROM tbl_practicals_submit
        INNER JOIN tbl_users ON tbl_practicals_submit.userid = tbl_users.userid  WHERE practicalid=\'' . $practicalId . '\' ORDER BY ' . $orderBy;

        return $this->getArray($sql);
    }

    /**
     * get assigment for a spfic student
     * @param <type> $studentId
     * @param <type> $practicalId
     * @return <type>
     */
    public function getStudentPractical($studentId, $practicalId) {
        return $this->getAll(" WHERE practicalid='{$practicalId}' AND userid='{$studentId}' ORDER BY datesubmitted DESC");
    }

    /**
     * Method to check how many times a student submitted an practical
     */
    public function numStudentPractical($studentId, $practicalId) {
        return $this->getRecordCount(" WHERE practicalid='{$practicalId}' AND userid='{$studentId}' ");
    }

    /**
     * Method to check whether a user is allowed to submit an practical
     * @param string $studentId Record Id of User
     * @param string $practicalId Practical Id
     */
    public function checkOkToSubmit($studentId, $practicalId) {
        $objPractical = $this->getObject('dbpracticals');

        $practical = $objPractical->getPractical($practicalId);

        // If practical doesn't exist return FALSE;
        if ($practical == FALSE) {
            return FALSE;
        }

        // Check if pass closing date
        // If multiple submits allowed, return TRUE
        if ($practical['resubmit'] == '1') {
            return TRUE;
        }

        // Check if student has already submitted
        $numPracticals = $this->numStudentPractical($studentId, $practicalId);

        if ($numPracticals == 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * submit practical upload
     * @param <type> $practicalId
     * @param <type> $userId
     * @param <type> $fileId
     * @return <type>
     */
    public function submitPracticalUpload($practicalId, $userId, $fileId) {
        if (!$this->checkOkToSubmit($userId, $practicalId)) {
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

        $submitId = $this->addStudentPracticalUpload($practicalId, $userId, $fileId);

        if ($submitId == FALSE) {
            return 'unabletosave';
        } else {
            return $this->processfile($submitId, $filePath);
        }
    }

    /**
     * Save assignemnt uploaded by a student
     * @param <type> $practicalId
     * @param <type> $userId
     * @param <type> $fileId
     * @return <type>
     */
    private function addStudentPracticalUpload($practicalId, $userId, $fileId) {

       
        $submitid= $this->insert(array(
            'practicalid' => $practicalId,
            'userid' => $userId,
            'studentfileid' => $fileId,
            'datesubmitted' => date('Y-m-d H:i:s', time())
        ));
         $practical = $this->getPractical($practicalId);
        if ($practical[0]['email_alert_onsubmit'] == '1') {
            $this->prepareToSendEmail($submitid,$practical[0]['userid']);
        }
    }

    private function prepareToSendEmail($submitId,$instructorid) {
        $link = new link($this->uri(array("action" => "viewsubmission", "id" => $submitId)));
        $objUser = $this->getObject("user", "security");
        $names = $objUser->fullname($userId);
        $username = $objUser->username($userId);
        $title = "New practical submission from $username";
        $message = "New practical submission from $names ($username). To view submission, click on this link. " . $link->href;
        $recipient = $objUser->email($instructorid);
        $this->sendEmail($title, $message, $recipient);
    }

    /**
     * get the practical
     * @param <type> $id
     * @return <type>
     */
    private function getPractical($id) {
        $sql = "select * from tbl_practicals where id = '$id'";
        return $this->getArray($sql);
    }

    /**
     * Method to email an practical to users
     *
     * @param string $title Title of the practical
     * @param string $message The practical
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
        $savePath = $objConfig->getcontentBasePath() . '/practicals/submissions/' . $submitId;

        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        $savePath = $objCleanUrl->cleanUpUrl($savePath);

        $objMkdir = $this->getObject('mkdir', 'files');
        $objMkdir->mkdirs($savePath, 0777);

        copy($path, $savePath . '/' . basename($path));
    }

    /**
     * Submit online practical, but check if submited or not
     * @param <type> $practicalId
     * @param <type> $userId
     * @param <type> $text
     * @return <type>
     */
    public function submitPracticalOnline($practicalId, $userId, $text) {
        if (!$this->checkOkToSubmit($userId, $practicalId)) {
            return 'alreadysubmitted';
        }

        return $this->addStudentPracticalOnline($practicalId, $userId, $text);
    }

    /**
     * save the student online practical
     * @param <type> $practicalId
     * @param <type> $userId
     * @param <type> $text
     * @return <type>
     */
    private function addStudentPracticalOnline($practicalId, $userId, $text) {
        $practical = $this->getPractical($practicalId);
     
        $submitid= $this->insert(array(
            'practicalid' => $practicalId,
            'userid' => $userId,
            'online' => $text,
            'datesubmitted' => date('Y-m-d H:i:s', time())
        ));
        
        if ($practical[0]['email_alert_onsubmit'] == '1') {
            $this->prepareToSendEmail($submitid,$practical[0]['userid']);
        }
        return $submitid;
    }

    /**
     * get practical filename
     * @param <type> $submissionId
     * @param <type> $fileId
     * @return <type>
     */
    public function getPracticalFilename($submissionId, $fileId) {
        $objFile = $this->getObject('dbfile', 'filemanager');
        $file = $objFile->getFile($fileId);

        // Do own search if file not found
        if ($file == FALSE) {

        }

        //var_dump($file);

        $objConfig = $this->getObject('altconfig', 'config');
        $filePath = $objConfig->getcontentBasePath() . '/practicals/submissions/' . $submissionId . '/' . $file['filename'];

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
     * set practical as marked
     */
    function markPractical($id, $mark, $commentinfo) {
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
     * Method to get a list of practicals is the context.
     * Each practical shows number of submissions, number marked and closing date.
     * @param string $context The current context
     */
    public function getContextSubmissions($context) {
        $sql = 'SELECT assign.id, assign.name, assign.closing_date, submit.datesubmitted, submit.mark ';
        $sql .= 'FROM tbl_practicals_submit AS submit ';
        $sql .= 'LEFT JOIN tbl_practicals as assign ON assign.id = submit.practicalId ';
        $sql .= "WHERE context = '$context' ORDER BY assign.id";

        $data = $this->getArray($sql);
        return $data;
    }

}

?>