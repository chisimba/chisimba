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
 * @version   $Id: functions_assignment_class_inc.php 22493 2011-09-16 11:30:34Z joconnor $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * The assignment admin block class displays a block with an alert if students have handed in.
 * @author Jameel Adam
 */
class functions_assignment extends object {

    /**
     * Constructors
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $objDbContext = $this->getObject('dbcontext', 'context');
        $this->contextCode = $objDbContext->getContextCode();
        $this->objDate = $this->getObject('dateandtime', 'utilities');
        $this->dbAssignment = $this->getObject('dbassignment', 'assignment');
        $this->dbSubmit = $this->getObject('dbassignmentsubmit', 'assignment');
        $this->objCleaner = $this->getObject('htmlcleaner', 'utilities');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->objUser = $this->getObject('user', 'security');
        $objFile = $this->getObject('dbfile', 'filemanager');
        $this->userId = $this->objUser->userId();
        $this->loadClass('link', 'htmlelements');
        $objIcon = $this->getObject('geticon', 'htmlelements');
        $objFileIcon = $this->getObject('fileicons', 'files');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $objPopup = &$this->loadClass('windowpop', 'htmlelements');
        if ($this->objContext->isInContext()) {
            $this->contextCode = $this->objContext->getContextCode();
            $this->context = $this->objContext->getTitle();
        }
        $this->test = FALSE;
        $this->essay = FALSE;
        $this->ws = FALSE;
        $this->rubric = FALSE;
    }

    /**
     * Method to display a lecturers comment in a pop-up window
     */
    public function showComment() {
        $id = $this->getParam('id');
        $name = $this->getParam('name');
        $comment = $this->dbSubmit->getSubmit("id='$id'", 'comment');
        $comment[0]['name'] = $name;
        return $comment;
    }

    /**
     * Method to save a students submitted assignment in the database
     */
    public function submitAssign() {
        $id = $this->saveAssign();
        if (!($id === FALSE)) {
            $msg = $this->objLanguage->languageText('mod_assignment_confirmsubmit', 'assignment');
        }
        return $msg;
    }

    /**
     * Method to save a students submitted assignment in the database
     */
    public function saveAssign() {

        $fields = array();
        $fields['assignmentid'] = $this->getParam('id', '');
        $fields['userid'] = $this->userId;
        $fields['datesubmitted'] = date('Y-m-d H:i', time());

        $postFormat = $this->getParam('format');
        if ($postFormat && isset($_FILES['file'])) {
            $fileId = $this->getParam('fileid', NULL);
            $fileId = $this->objFile->uploadFile($_FILES['file'], 'file', $fileId);
            $fields['fileid'] = $fileId;
        } else {
            $text = $this->getParam('text', '');
            $cleanHtmltext = $this->objCleaner->cleanHtml($text);
            $fields['online'] = $cleanHtmltext;
        }

        $postSubmitId = $this->getParam('submitid', NULL);
        $id = $this->dbSubmit->addSubmit($fields, $postSubmitId);
        return $id;
    }

    /**
     * Method to display the assignment.
     * @param bool $var Allows the assignment to be resubmitted.
     * @return The template for displaying the assignment.
     */
    public function viewAssign($var = FALSE) {
        $id = $this->getParam('id');
        $data = $this->dbAssignment->getAssignment($this->contextCode, "id='$id'");

        if ($data[0]['resubmit'] || $var) {
            $submit = $this->dbSubmit->getSubmit("assignmentid='$id' AND userid='"
                            . $this->objUser->userId() . "'", 'id, online, studentfileid');
            if (!empty($submit)) {
                $data[0]['online'] = $submit[0]['online'];
                $data[0]['fileid'] = $submit[0]['fileid'];
                $data[0]['submitid'] = $submit[0]['id'];
            }
        }
        return $data;
    }

    /**
     * Method to display the Students home page.
     * @return The template for the students home page.
     */
    public function studentHome($msg) {
        // Get students assignments: worksheets, booked essays
        $wsData = array();
        $essay = array();
        $topic = array();
        $essayData = array();
        $assignData = array();
        $testData = array();

        if ($this->ws) {
            $wsData = $this->dbWorksheet->getWorksheetsInContext($this->contextCode);
            if (!empty($wsData)) {
                foreach ($wsData as $key => $line) {
                    $result = $this->dbWorksheetResults->getResults(NULL, "worksheet_id='"
                                    . $line['id'] . "' AND userid='" . $this->userId . "'");
                    $wsData[$key]['mark'] = $result[0]['mark'];
                    $wsData[$key]['completed'] = $result[0]['completed'];
                }
            }
        }
        if ($this->essay) {
            // get topic list for the context
            $topicFilter = "context='" . $this->contextCode . "'";
            $topicFields = 'id, name, closing_date, userid';
            $topics = $this->dbEssayTopics->getTopic(NULL, $topicFields, $topicFilter);

            // check booked topics and get booked essays
            if (!empty($topics)) {
                $i = 0;
                foreach ($topics as $item) {
                    $bookFilter = "where studentid='" . $this->userId . "' and topicid='" . $item['id'] . "'";
                    $booking = $this->dbEssayBook->getBooking($bookFilter);
                    if (!empty($booking)) {
                        $essay = $this->dbEssays->getEssay($booking[0]['essayid'], 'topic');
                        $booking[0]['essayName'] = $essay[0]['topic'];
                        $booking[0]['topicName'] = $item['name'];
                        $booking[0]['closing_date'] = $item['closing_date'];
                        $booking[0]['lecturer'] = $item['userid'];
                        $essayData[] = $booking[0];
                    } else {
                        $i++;
                    }
                }
                if ($i > 0) {
                    $essayData[]['unassigned'] = $i;
                }
            }
        }
        if ($this->test) {
            $filter =
                    $testData = $this->dbTestAdmin->getTests($this->contextCode);
            if (!empty($testData)) {
                foreach ($testData as $key => $line) {
                    $result = $this->dbTestResults->getResult($this->userId, $line['id']);
                    if (!empty($result)) {
                        $testData[$key]['mark'] = $result[0]['mark'];
                    } else {
                        $testData[$key]['mark'] = 'none';
                    }
                }
            }
        }
        $assignData = $this->dbAssignment->getAssignment($this->contextCode);
        if (!empty($assignData)) {
            foreach ($assignData as $key => $val) {
                $submitData = $this->dbSubmit->getSubmit("assignmentid='" . $val['id'] . "' AND
                userid='" . $this->objUser->userId() . "'", 'id AS submitid, mark AS studentmark, datesubmitted, studentfileid');

                if (!($submitData === FALSE)) {
                    $assignData[$key] = array_merge($val, $submitData[0]);
                }
            }
        }
        /* $msg = $this->getParam('confirm');
          if(!empty($msg)){
          $this->setVarByRef('msg',$msg);
          } */
        $mixed_arr = array();
        $mixed_arr[0] = $essayData;
        $mixed_arr[1] = $wsData;
        $mixed_arr[2] = $testData;
        $mixed_arr[3] = $assignData;
        $mixed_arr[4] = $msg;
        return $mixed_arr;
    }

    public function isValid($action) {
        $restrictedActions = array('add', 'edit', 'saveassignment', 'updateassignment', 'delete', 'markassignments', 'saveuploadmark', 'saveonlinemark');

        if (in_array($action, $restrictedActions)) {
            if ($this->objUser->isCourseAdmin($this->contextCode)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }

    /**
     * Method to display the Students assignments.
     * @return The template with assignments including marks.
     */
    function displayAssignment($contextCode = Null, $thisUserId = Null) {
        if (empty($contextCode)) {
            $contextCode = $this->contextCode;
        }
        if (empty($thisUserId)) {
            $thisUserId = $this->objUser->userId();
        }
        //$isLecturerRole = $this->objUser->isCourseAdmin($contextCode);
        $assignments = $this->dbAssignment->getAssignments($contextCode);
        $openLabel = $this->objLanguage->languageText('mod_assignment_open', 'assignment');
        $closedLabel = $this->objLanguage->languageText('mod_assignment_closed', 'assignment');
        $viewLabel = $this->objLanguage->languageText('mod_assignment_view', 'assignment');
        $uploadLabel = $this->objLanguage->languageText('mod_assignment_upload', 'assignment');
        $onlineLabel = $this->objLanguage->languageText('mod_assignment_online', 'assignment');

        // Set up html elements
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objTimeOut = $this->newObject('timeoutMessage', 'htmlelements');

        $objTrim = $this->getObject('trimstr', 'strings');
        /*
          $objHead = new htmlheading();
          $objHead->str=$this->objLanguage->languageText('mod_assignment_assignments', 'assignment', 'Assignments');
          $objHead->type=1;

          if ($this->isValid('add')) {

          $objIcon->setIcon('add');
          $link = new link ($this->uri(array('action'=>'add')));
          $link->link = $objIcon->show();

          $objHead->str .= ' '.$link->show();
          }

          echo $objHead->show();
         */
        $objTable = $this->newObject('htmltable', 'htmlelements');

        $objTable->startHeaderRow();
        $objTable->addHeaderCell($this->objLanguage->languageText('word_name', 'system', 'Name'), '20%');
        $objTable->addHeaderCell($this->objLanguage->languageText('mod_assignment_assignmenttype', 'assignment', 'Assignment Type'), '13%');
        $objTable->addHeaderCell($this->objLanguage->languageText('mod_assignment_closingdate', 'assignment', 'Closing Date'), '15%');
        $objTable->addHeaderCell($this->objLanguage->languageText('mod_assignment_datesubmitted', 'assignment', 'Date Submitted', '15%'));
        //$objTable->addHeaderCell($this->objLanguage->languageText('word_description', 'system', 'Description'));
        $objTable->addHeaderCell($this->objLanguage->languageText('mod_assignment_mark', 'assignment', 'Mark', '10%'));
        $objTable->addHeaderCell($viewLabel . " / " . $this->objLanguage->languageText('word_download', 'system', 'Status'), '13%');

        if ($this->isValid('edit') && count($assignments) > 0) {
            $objTable->addHeaderCell('&nbsp;', '60');
        }

        $objTable->endHeaderRow();

        if (count($assignments) == 0) {



            $objTable->startRow();
            $objTable->addCell($this->objLanguage->languageText('mod_assignment_noassignments', 'assignment', 'No Assignments'), '', '', '', 'noRecordsMessage', 'colspan="6"');
            $objTable->endRow();
        } else {

            $i = 0;
            $status = '';

            $objIcon->setIcon('edit');
            $editIcon = $objIcon->show();

            $objIcon->setIcon('delete');
            $deleteIcon = $objIcon->show();

            $counter = 0;


            foreach ($assignments as $assignment) {
                $submitData = $this->dbSubmit->getStudentAssignment($thisUserId, $assignment['id']);
                if (empty($submitData)) {
                    continue;
                }

                $class = ($i++ % 2 == 0) ? 'odd' : 'even';

                if ($assignment['closing_date'] > date('Y-m-d H:i')) {
                    if (($assignment['opening_date'] < date('Y-m-d H:i')) || $assignment['opening_date'] == NULL) {
                        $status = $openLabel;
                    } else {
                        $status = $this->objLanguage->languageText('mod_assignment_notopenforentry', 'assignment', 'Not Open for Entry');
                    }
                } else {
                    $status = $closedLabel;
                }

                // Display whether the assignment is online or uploadable
                if ($assignment['format'] == 1) {
                    $format = $uploadLabel;
                } else {
                    $format = $onlineLabel;
                }

                $okToShow = FALSE;

                if (($assignment['opening_date'] <= date('Y-m-d H:i:s')) || $assignment['opening_date'] == NULL) {
                    $okToShow = TRUE;
                }

                if ($this->isValid('edit')) {
                    $okToShow = TRUE;
                }

                if ($okToShow) {

                    $counter++;
                    // "userid='".$thisUserId."'"
                    //$submitData = $this->dbSubmit->getStudentSubmissions($assignment['id'], $orderBy = 'firstname, datesubmitted');
                    //$submitData = $this->dbSubmit->getStudentAssignment($thisUserId, $assignment['id']);
                    //var_dump($submitData);
                    // Check if before closing date
                    if (date('Y-m-d H:i:s') <= $assignment['closing_date']) {
                        $studentsMark = NULL;
                    } else if (!is_null($submitData[0]["mark"])) {
                        $studentsMark = number_format((($submitData[0]["mark"] / $assignment['mark']) * 100), 2);
                        //$assgnId = $submitData[0]['assignmentid'];
                    } else {
                        $studentsMark = NULL;
                    }
                    $objTable->startRow();
                    $objTable->addCell($assignment['name'], '20%', '', '', $class);
                    $objTable->addCell($format, '13%', '', '', $class);
                    //$objTable->addCell($objTrim->strTrim(strip_tags($assignment['description']), 50),'','','',$class);
                    $objTable->addCell($this->objDate->formatDate($assignment['closing_date']), '15%', '', '', $class);
                    $objTable->addCell($this->objDate->formatDate($submitData[0]['datesubmitted']), '15%', '', '', $class);
                    if (is_null($studentsMark)) {
                        $objTable->addCell($this->objLanguage->languageText('mod_assignment_notmarked', 'assignment'), '8%', '', '', $class);
                    } else {
                        $objTable->addCell($studentsMark . '%', '8%', '', '', $class);
                    }
                    if (is_null($studentsMark)) {
                        $objTable->addCell($this->objLanguage->languageText('mod_assignment_notmarked', 'assignment'), '8%', '', '', $class);
                    } else {
                        if ($assignment['format'] == 1) {
                            if (is_null($submitData[0]['lecturerfileid'])) {
                                $objTable->addCell($this->objLanguage->languageText('mod_assignment_nomarkedassignmentavailable', 'assignment'), '8%', '', '', $class);
                            } else {
                                $lecturerfileid = $submitData[0]['lecturerfileid'];
                                $objFile = $this->getObject('dbfile', 'filemanager');
                                $fileName = $objFile->getFileName($lecturerfileid);
                                $objIcon = $this->getObject('geticon', 'htmlelements');
                                $objFileIcon = $this->getObject('fileicons', 'files');

                                $downloadLink = new link($this->uri(array('action' => 'downloadfile', 'id' => $submitData[0]['id'], 'fileid' => $lecturerfileid)));
                                $downloadLink->link = $this->objLanguage->languageText('word_download', 'system', 'Download');

                                $objTable->addCell('<p>' . $objFileIcon->getFileIcon($fileName) . ' ' . $downloadLink->show() . '</p>', '8%', '', '', $class);
                            }
                        } else {
                            $this->objIcon->title = $viewLabel;
                            $this->objIcon->setIcon('comment_view');
                            $commentIcon = $this->objIcon->show();

                            $objPopup = new windowpop();
                            //$objPopup->set('location',$this->uri(array('action'=>'showcomment','id'=>$assignment['id'],'contextCode'=>$contextCode)));
                            $objPopup->set('location', $this->uri(array('action' => 'showcomment', 'id' => $assignment['id'], 'submissionid' => $submitData[0]['id'])));
                            $objPopup->set('linktext', $commentIcon);
                            $objPopup->set('width', '600');
                            $objPopup->set('height', '350');
                            $objPopup->set('left', '200');
                            $objPopup->set('top', '200');
                            $objPopup->putJs(); // you only need to do this once per page

                            $objTable->addCell('<p>' . $objPopup->show() . '</p>', '8%', '', '', $class);
                        }
                    }

                    if ($this->isValid('edit')) {
                        $editLink = new link($this->uri(array('action' => 'edit', 'id' => $assignment['id'])));
                        $editLink->link = $editIcon;

                        $deleteLink = new link($this->uri(array('action' => 'delete', 'id' => $assignment['id'])));
                        $deleteLink->link = $deleteIcon;

                        $objTable->addCell($editLink->show() . '&nbsp;' . $deleteLink->show(), '60');
                    }
                    $objTable->endRow();
                }
            }

            if ($counter == 0) {
                $objTable->startRow();
                $objTable->addCell($this->objLanguage->languageText('mod_assignment_noassignmentssubmitted', 'assignment'), '', '', '', 'noRecordsMessage', 'colspan="6"');
                $objTable->endRow();
            }
        }


        /*

          if ($this->isValid('add')) {
          $link = new link ($this->uri(array('action'=>'add')));
          $link->link = $this->objLanguage->languageText('mod_assignment_addassignment', 'assignment', 'Add Assignment');

          echo '<p>'.$link->show().'</p>';
          }
         */
        return $objTable->show();
    }

    /**
     * Method to display the Students assignments.
     * @return The template with assignments including marks.
     */
    function displayAssignmentFull($contextCode = Null, $thisUserId = Null) {
        if (empty($contextCode)) {
            $contextCode = $this->contextCode;
        }
        if (empty($thisUserId)) {
            $thisUserId = $this->objUser->userId();
        }
        $assignments = $this->dbAssignment->getAssignments($contextCode);
        $assignmentLabel = $this->objLanguage->languageText('mod_assignment_wordAssignment', 'assignment');
        $openLabel = $this->objLanguage->languageText('mod_assignment_open', 'assignment');
        $closedLabel = $this->objLanguage->languageText('mod_assignment_closed', 'assignment');
        $viewLabel = $this->objLanguage->languageText('mod_assignment_view', 'assignment');
        $uploadLabel = $this->objLanguage->languageText('mod_assignment_upload', 'assignment');
        $onlineLabel = $this->objLanguage->languageText('mod_assignment_online', 'assignment');

        // Set up html elements
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $objTimeOut = $this->newObject('timeoutMessage', 'htmlelements');

        $objTrim = $this->getObject('trimstr', 'strings');
        $objTable = $this->newObject('htmltable', 'htmlelements');
        $objTable->border = 1;
        $objTable->cellspacing = '1';
        $objTable->width = "100%";

        if (count($assignments) == 0) {
            $objTable->startRow();
            $objTable->addCell($this->objLanguage->languageText('mod_assignment_noassignments', 'assignment', 'No Assignments'), '', '', '', 'noRecordsMessage', 'colspan="6"');
            $objTable->endRow();
        } else {
            $i = 0;
            $status = '';
            $counter = 0;
            foreach ($assignments as $assignment) {
                $class = ($i++ % 2 == 0) ? 'odd' : 'even';

                if ($assignment['closing_date'] > date('Y-m-d H:i')) {
                    if (($assignment['opening_date'] < date('Y-m-d H:i')) || $assignment['opening_date'] == NULL) {
                        $status = $openLabel;
                    } else {
                        $status = $this->objLanguage->languageText('mod_assignment_notopenforentry', 'assignment', 'Not Open for Entry');
                    }
                } else {
                    $status = $closedLabel;
                }
                // Display whether the assignment is online or uploadable
                if ($assignment['format'] == 1) {
                    $format = $uploadLabel;
                } else {
                    $format = $onlineLabel;
                }
                $okToShow = FALSE;
                if (($assignment['opening_date'] < date('Y-m-d H:i')) || $assignment['opening_date'] == NULL) {
                    $okToShow = TRUE;
                }

                if ($this->isValid('edit')) {
                    $okToShow = TRUE;
                }

                if ($okToShow) {
                    $counter++;
                    // "userid='".$thisUserId."'"
                    //$submitData = $this->dbSubmit->getStudentSubmissions($assignment['id'], $orderBy = 'firstname, datesubmitted');
                    $submitData = $this->dbSubmit->getStudentAssignment($thisUserId, $assignment['id']);
                    //var_dump($submitData);
                    if (!empty($submitData[0]["mark"])) {
                        $studentsMark = (($submitData[0]["mark"] / $assignment['mark']) * 100);
                        $assgnId = $submitData[0]['assignmentid'];
                    } else {
                        $studentsMark = Null;
                    }
                    if ($counter > 1) {
                        $objTable->startRow();
                        $objTable->addCell('', '', '', '', $class, "bgcolor='#D3D3D3'");
                        $objTable->endRow();
                    }

                    $objTable->startRow();
                    $objTable->addCell('<b>' . $assignmentLabel . " " . $counter . "</b>", '', '', '', $class, "bgcolor='#FFFFFF'");
                    $objTable->endRow();

                    $objTable->startRow();
                    $objTable->addCell("<b>" . $this->objLanguage->languageText('word_name', 'system', 'Name') . ": </b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $objTable->endRow();
                    $objTable->startRow();
                    $objTable->addCell($assignment['name'], "", "", "", "", "bgcolor='#FFFFFF'");
                    $objTable->endRow();
                    $objTable->startRow();
                    $objTable->addCell("<b>" . $this->objLanguage->languageText('mod_assignment_assignmenttype', 'assignment', 'Assignment Type') . ": </b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $objTable->endRow();
                    $objTable->startRow();
                    $objTable->addCell($format, "", "", "", "", "bgcolor='#FFFFFF'");
                    $objTable->endRow();
                    $objTable->startRow();
                    //$objTable->addCell($objTrim->strTrim(strip_tags($assignment['description']), 50),'','','',$class);
                    $objTable->addCell("<b>" . $this->objLanguage->languageText('mod_assignment_closingdate', 'assignment', 'Closing Date') . ": </b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $objTable->endRow();
                    $objTable->startRow();
                    //$objTable->addCell($objTrim->strTrim(strip_tags($assignment['description']), 50),'','','',$class);
                    $objTable->addCell($this->objDate->formatDate($assignment['closing_date']), "", "", "", "", "bgcolor='#FFFFFF'");
                    $objTable->endRow();
                    $objTable->startRow();
                    $objTable->addCell("<b>" . $this->objLanguage->languageText('mod_assignment_datesubmitted', 'assignment', 'Date Submitted') . ": </b>", "", "", "", "", "bgcolor='#D3D3D3'");
                    $objTable->endRow();
                    $objTable->startRow();
                    $objTable->addCell($this->objDate->formatDate($assignment['last_modified']), "", "", "", "", "bgcolor='#FFFFFF'");
                    $objTable->endRow();
                    if (!empty($studentsMark)) {
                        $objTable->startRow();
                        $objTable->addCell("<b>" . $this->objLanguage->languageText('mod_assignment_mark', 'assignment', 'Mark') . ": </b>", "", "", "", "", "bgcolor='#D3D3D3'");
                        $objTable->endRow();
                        $objTable->startRow();
                        $objTable->addCell($studentsMark . '%', "", "", "", "", "bgcolor='#FFFFFF'");
                        $objTable->endRow();
                    }/*
                      else{
                      $objTable->startRow();
                      $objTable->addCell('&nbsp;','','','',$class,"bgcolor='#FFFFFF'");
                      $objTable->endRow();
                      } */
                    if ($assignment['format'] == 1) {
                        $objFile = $this->getObject('dbfile', 'filemanager');
                        $objIcon = $this->getObject('geticon', 'htmlelements');
                        $objFileIcon = $this->getObject('fileicons', 'files');
                        if (!empty($submitData[0]['studentfileid'])) {
                            $fileName = $objFile->getFileName($submitData[0]['studentfileid']);
                            $downloadLink = new link($this->uri(array('action' => 'downloadfile', 'id' => $submitData[0]['id'])));
                            $downloadLink->link = $this->objLanguage->languageText('word_download', 'system', 'Download');
                        }
                        //$objTable->addCell('<p>'.$objFileIcon->getFileIcon($fileName).' '.$downloadLink->show().'</p>','8%','','',$class);
                        $objTable->startRow();
                        $objTable->addCell('', '', '', '', $class, "bgcolor='#FFFFFF'");
                        $objTable->endRow();
                    } else {
                        if (!empty($assgnId)) {
                            $objTable->startRow();
                            $onlineSubmission = $this->dbAssignment->getAssignment($assgnId);
                            $objTable->addCell("<b>" . $viewLabel . ": </b>", "", "", "", "", "bgcolor='#D3D3D3'");
                            $objTable->endRow();
                            $objTable->startRow();
                            $onlineSubmission = $this->dbAssignment->getAssignment($assgnId);
                            $objTable->addCell($onlineSubmission['description'], "", "", "", "", "bgcolor='#FFFFFF'");
                            $objTable->endRow();
                        }
                    }
                }
            }
            if ($counter == 0) {
                $objTable->startRow();
                $objTable->addCell($this->objLanguage->languageText('mod_assignment_noassignments', 'assignment', 'No Assignments'), '', '', '', 'noRecordsMessage');
                $objTable->endRow();
                return false;
            } else {
                return $objTable->show();
            }
        }
        /*

          if ($this->isValid('add')) {
          $link = new link ($this->uri(array('action'=>'add')));
          $link->link = $this->objLanguage->languageText('mod_assignment_addassignment', 'assignment', 'Add Assignment');

          echo '<p>'.$link->show().'</p>';
          }
         */
        //return $objTable->show();
    }

    /**
     * allows the user to donwload the selected file
     * @param <type> $filename
     */
    function downloadSubmissionsFile($filename) {

        // Make sure we can't download files above the current directory location.
        if (eregi("\.\.", $filename))
            die("I'm sorry, you may not download that file.");
        $file = str_replace("..", "", $filename);

        // Make sure we can't download .ht control files.
        if (eregi("\.ht.+", $filename))
            die("I'm sorry, you may not download that file.");

        // Combine the download path and the filename to create the full path to the file.
        $file = $filename;
        // Test to ensure that the file exists.
        // if(!file_exists($file)) die("I'm sorry, the file doesn't seem to exist.");
        // Extract the type of file which will be sent to the browser as a header
        $type = filetype($file);

        // Get a date and timestamp
        $today = date("F j, Y, g:i a");
        $time = time();

        // Send file headers
        header("Content-type: $type");
        header("Content-Disposition: attachment;filename=" . $this->getFileName($filename));
        header('Pragma: no-cache');
        header('Expires: 0');

        // Send the file contents.
        readfile($file);
    }

    /**
     * returns filename with ext stripped
     */
    function getFileName($filepath) {
        preg_match('/[^?]*/', $filepath, $matches);
        $string = $matches[0];
        //split the string by the literal dot in the filename
        $pattern = preg_split('/\./', $string, -1, PREG_SPLIT_OFFSET_CAPTURE);
        //get the last dot position
        $lastdot = $pattern[count($pattern) - 1][1];
        //now extract the filename using the basename function
        $filename = basename(substr($string, 0, $lastdot - 1));
        $exts = split("[/\\.]", $filepath);
        $n = count($exts) - 1;
        $ext = $exts[$n];

        return $filename . '.' . $ext;
    }

    /**
     * Creates a zip file from the submissions.
     *
     * @param string $assignmentName Name of assignment
     * @param array $submissions Student submissions
     * @return string|bool Zip filename or FALSE on error
     */
    /*
    function createZipFromSubmissions_deprecated($assignmentName, $submissions) {
        $objConfig = $this->getObject('altconfig', 'config');
        $mkdir = $this->getObject('mkdir', 'files');
        $objFile = $this->getObject('dbfile', 'filemanager');
        //$objCreateZipFile = $this->getObject('createzipfile');
        $objWzip = $this->newObject('wzip', 'utilities');

        //$wzip = $this->getObject('wzip', 'utilities');
        //$zip = new ZipArchive();
        //$zip->open($zip_name, ZIPARCHIVE::CREATE);
        //$zip->addFile($filePath, $file['filename']);
        $contentBasePath = $objConfig->getcontentBasePath();
        //--$dirPath = $contentBasePath . 'assignment/submissions/' . $assignmentId;
        //--$mkdir->mkdirs($dirPath);
        $zipFN = $contentBasePath . 'assignment/submissions/' . preg_replace('/[^[:alnum:]_\s]/', '_', $assignmentName) . '.zip'; //$assignmentId
        if(file_exists($zipFN)){
            unlink($zipFN);
        }
        $files = array();
        foreach ($submissions as $submission) {
            $submissionId = $submission['id'];
            $userId = $submission['userid'];
            $fileId = $submission['studentfileid'];
            $file = $objFile->getFile($fileId);
            $filePath = $contentBasePath . 'assignment/submissions/' . $submissionId . '/' . $file['filename'];
            if (file_exists($filePath)) {
                //copy($filePath, $dirPath . '/' . $file['filename']);
                $files[] =
                    array(
                    'fn' => $filePath,
                    'local_fn' => $userId.'_'.basename($filePath)
                    );
            }
        }
        if (empty($files)) {
            trigger_error('There are no submission files!');
            return FALSE;
        }
        else {
            // Create the zip file.
            if (!extension_loaded('zip')) {
                throw new customException($this->objLanguage->languageText("mod_utilities_nozipext", "utilities"));
            }
            $zip = new ZipArchive();
            if ($zip->open($zipFN, ZIPARCHIVE::CREATE) !== TRUE) {
                log_debug("Assignment::Zip Error: cannot open [$zipFN]\n");
                throw new customException($this->objLanguage->languageText("mod_utilities_nozipcreate", "utilities"));
            } else {
                foreach ($files as $f) {
                    $FN = $f['fn'];
                    $localFN = $f['local_fn'];
                    $zip->addFile($FN, $localFN);
                }
                $zip->close();
                //return $zipFN;
            }
            return $zipFN;
            //$fn = $objWzip->packFilesZip($zipFN, $files, TRUE, FALSE);
            //return $fn;
        }
        //trigger_error('$dirPath:'.$dirPath);
        //trigger_error('$zipFN:'.$zipFN);
        //$objCreateZipFile->zipDirectory($dirPath, ''); //$zip_name
        //file_put_contents($zip_name, $objCreateZipFile->getZippedfile());
        //$objCreateZipFile->forceDownload($zip_name);
        //die();
        //$zip->close();
        //return; //$zip_name;
    }
    */

}

//end of class
?>
