<?php
$ret = "";

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objWashout = $this->getObject('washout', 'utilities');
$objIcon->setIcon('edit');
$editIcon = $objIcon->show();

$objIcon->setIcon('delete');
$deleteIcon = $objIcon->show();

$header = new htmlHeading();
$header->str = $assignment['name'];

if ($this->isValid('edit')) {
    $editLink = new link($this->uri(array('action' => 'edit', 'id' => $assignment['id'])));
    $editLink->link = $editIcon;

    $deleteLink = new link($this->uri(array('action' => 'delete', 'id' => $assignment['id'], 'return' => 'view')));
    $deleteLink->link = $deleteIcon;

    $header->str .= ' ' . $editLink->show() . '&nbsp;' . $deleteLink->show();
}

$header->type = 1;

$objDateTime = $this->getObject('dateandtime', 'utilities');
$objTrimStr = $this->getObject('trimstr', 'strings');

$ret .= $header->show();

$table = $this->newObject('htmltable', 'htmlelements');

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('word_description', 'system', 'Description') . '</strong>', 130);
$table->addCell($objWashout->parseText($assignment['description']), NULL, NULL, NULL, NULL, ' colspan="3"');
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . ucfirst($this->objLanguage->code2Txt('mod_assignment_lecturer', 'assignment', NULL, '[-author-]')) . '</strong>', 130);
$table->addCell($this->objUser->fullName($assignment['userid']));
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_totalmark', 'assignment') . '</strong>', 130);
$table->addCell($assignment['mark']);
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_openingdate', 'assignment', 'Opening Date') . '</strong>', 130);
$table->addCell($objDateTime->formatDate($assignment['opening_date']));
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_percentyrmark', 'assignment', 'Percentage of year mark') . '</strong>', 200, NULL, NULL, 'nowrap');
$table->addCell($assignment['percentage'] . '%');
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_closingdate', 'assignment', 'Closing Date') . '</strong>', 130);
$table->addCell($objDateTime->formatDate($assignment['closing_date']));
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_assignmenttype', 'assignment', 'Assignment Type') . '</strong>', 130);
if ($assignment['format'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_assignment_online', 'assignment', 'Online'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_assignment_upload', 'assignment', 'Upload'));
}
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_emailalerttostudents1', 'assignment', 'Email on creation') . '</strong>', 130);
if ($assignment['email_alert'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_assignment_emailalertoff', 'assignment', 'Off'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_assignment_emailalerton', 'assignment', 'On'));
}


$table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_emailalertfromstudents1', 'assignment', 'Email on submission') . '</strong>', 130);
if ($assignment['email_alert_onsubmit'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_assignment_emailalertoff', 'assignment', 'Off'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_assignment_emailalerton', 'assignment', 'On'));
}
$table->endRow();

if ($assignment['format'] == '1') {
    $table->startRow();
    $filetypes = $this->objAssignmentUploadablefiletypes->getFiletypes($assignment['id']);
    if (empty($filetypes)) {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $allowedFilesString = $objSysConfig->getValue('FILETYPES_ALLOWED', 'assignment');
        $allowedFileTypes = explode(',', $allowedFilesString);
    } else {
        $allowedFileTypes = array();
        foreach ($filetypes as $filetype) {
            $allowedFileTypes[] = $filetype['filetype'];
        }
    }
    if (empty($allowedFileTypes)) {
        $str = $this->objLanguage->languageText('word_none', 'assignment');
    } else {
        $str = '';
        $separator = '';
        foreach ($allowedFileTypes as $filetype) {
            $str .= $separator . $filetype;
            $separator = '&nbsp;';
        }
    }
    $table->addCell('<strong>' . $this->objLanguage->languageText('mod_assignment_uploadablefiletypes', 'assignment') . '</strong>&nbsp;' . $str, NULL, NULL, NULL, NULL, 'colspan="4"');
    $table->endRow();

    $table->startRow();
    $table->addCell('<br/>', NULL, NULL, NULL, NULL, 'colspan="2"');
    $table->endRow();
}
if ($assignment['usegoals'] == '1') {

    $fieldset = new fieldset();
    $fieldset->setLegend('<b>' . $this->objLanguage->languageText('mod_assignment_learningoutcomes', 'assignment', 'Learning outcomes') . ':</b>');
    $fieldset->addContent($goals);

    $table->startRow();
    $table->addCell($fieldset->show(), NULL, NULL, NULL, NULL, 'colspan="4"');
    $table->endRow();
}
if ($assignment['usegroups'] == '1') {

    $gfieldset = new fieldset();
    $gfieldset->setLegend('<b>'.$this->objLanguage->languageText('mod_assignment_groups', 'assignment', 'Groups').'</b>');
    $gfieldset->addContent($groups);

    $table->startRow();
    $table->addCell($gfieldset->show(), NULL, NULL, NULL, NULL, 'colspan="4"');
    $table->endRow();
}
$ret .= $table->show();

$htmlHeader = new htmlHeading();
$htmlHeader->type = 1;
$htmlHeader->str = $this->objLanguage->languageText('mod_assignment_submittedassignments', 'assignment', 'Submitted Assignments');
$ret .= '<hr />' . $htmlHeader->show();

// If Lecturer, show list of assignments
if ($this->isValid('markassignments')) {
    //$submissions = $this->objAssignmentSubmit->getStudentSubmissions($assignment['id']);
    $table = $this->newObject('htmltable', 'htmlelements');
    $table->startHeaderRow();
    $table->addHeaderCell(ucfirst($this->objLanguage->code2Txt('mod_assignment_studname', 'assignment', NULL, '[-readonly-] name')));
    $table->addHeaderCell($this->objLanguage->languageText('mod_assignment_datesubmitted', 'assignment', 'Date Submitted'));
    $table->addHeaderCell($this->objLanguage->languageText('mod_assignment_mark', 'assignment', 'Mark'));
    $table->addHeaderCell($this->objLanguage->languageText('mod_assignment_comment', 'assignment', 'Comment'));
    $table->endHeaderRow();

    if (count($submissions) == 0) {
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_assignment_noassignmentssubmitted', 'assignment', 'No Assignments Submitted Yet'), NULL, NULL, NULL, 'noRecordsMessage', ' colspan="4"');
        $table->endRow();
    } else {

        foreach ($submissions as $submission) {
            $table->startRow();

            $link = new link($this->uri(array('action' => 'viewsubmission', 'id' => $submission['id'])));
            $link->link = $this->objUser->fullName($submission['userid']);

            $table->addCell($link->show());
            $table->addCell($objDateTime->formatDate($submission['datesubmitted']));

            if ($submission['mark'] == NULL) {
                $table->addCell('<em>' . $this->objLanguage->languageText('mod_assignment_notmarked', 'assignment', 'Not Marked') . '</em>');
                $table->addCell('<em>' . $this->objLanguage->languageText('mod_assignment_notmarked', 'assignment', 'Not Marked') . '</em>');
            } else {
                $table->addCell($submission['mark']);
                $table->addCell($objTrimStr->strTrim($submission['commentinfo'], 50));
            }

            $table->endRow();
        }
    }

    $ret .= $table->show();

} else {
    // Show Student Views

    //$submissions = $this->objAssignmentSubmit->getStudentAssignment($this->objUser->userId(), $assignment['id']);

//    if (count($submissions) == 0) {

//    } else if (count($submissions) == 0) {

//    } else {

    if (count($submissions) != 0) {

        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startHeaderRow();
        $table->addHeaderCell($this->objLanguage->languageText('mod_assignment_submissions', 'assignment', 'Submissions'));
        $table->addHeaderCell($this->objLanguage->languageText('mod_assignment_datesubmitted', 'assignment', 'Date Submitted'));
        $table->addHeaderCell($this->objLanguage->languageText('mod_assignment_mark', 'assignment', 'Mark'));
        //$table->addHeaderCell($this->objLanguage->languageText('mod_assignment_comment', 'assignment', 'Comment'));
        $table->endHeaderRow();

        $objFile = $this->getObject('dbfile', 'filemanager');
        /*
         * Creating the link to view assignment results
         */
        foreach ($submissions as $submission) {

            $isMarked = date('Y-m-d H:i:s') > $assignment['closing_date'];

            $table->startRow();
            /*
              if (!$isMarked) {
              $table->addCell('<em>'.$this->objLanguage->languageText('mod_assignment_notmarked', 'assignment', 'Not Marked').'</em>');
              }
              else {
             */
            $link = new link($this->uri(array('action' => 'viewsubmission', 'id' => $submission['id'])));
            $link->link = $this->objLanguage->languageText('mod_assignment_viewscoremark', 'assignment');
            $table->addCell($link->show());
            /*
              }
             */

            $table->addCell($objDateTime->formatDate($submission['datesubmitted']));

            if (!$isMarked) {
                $table->addCell('<em>' . $this->objLanguage->languageText('mod_assignment_notmarked', 'assignment', 'Not Marked') . '</em>');
                //$table->addCell('<em>'.$this->objLanguage->languageText('mod_assignment_notmarked', 'assignment', 'Not Marked').'</em>');
            } else {

                if ($submission['mark'] == NULL) {
                    $table->addCell('<em>' . $this->objLanguage->languageText('mod_assignment_notmarked', 'assignment', 'Not Marked') . '</em>');
                    //$table->addCell('<em>'.$this->objLanguage->languageText('mod_assignment_notmarked', 'assignment', 'Not Marked').'</em>');
                } else {
                    $table->addCell($submission['mark']);
                    /*
                     * The commented line prevents view of comments untill the assignment is opened for viewing the results
                     */
                    // $table->addCell($objTrimStr->strTrim($submission['commentinfo'], 50));
                }
            }

            $table->endRow();
        }

        $ret .= $table->show();
    }

    if ($this->objAssignmentSubmit->checkOkToSubmit($this->objUser->userId(), $assignment['id'])) {
        $hiddenInput = new hiddeninput('id', $assignment['id']);

        $header = new htmlHeading();
        $header->type = 1;
        $header->str = $this->objLanguage->languageText('mod_assignment_submitassignment', 'assignment', 'Submit Assignment');
        $ret .= '<hr />' . $header->show();

        // Display by Assignment Type
        $this->objCond = $this->newObject('contextCondition', 'contextpermissions');
        if ($assignment['closing_date'] < date('Y-m-d H:i')) {
            $ret .= '<div class="noRecordsMessage">' . $this->objLanguage->languageText('mod_assignment_assignmentclosed', 'assignment', 'Assignment Closed') . '</div>';
        } else if (!($this->objCond->isContextMember('Students'))) { 
            $ret .= '<div class="noRecordsMessage">' . $this->objLanguage->languageText('mod_assignment_notstudent', 'assignment', 'Not a Student') . '</div>';

        } else if ($assignment['format'] == '0') { // Online Assignment
            $form = new form('addassignment', $this->uri(array('action' => 'submitonlineassignment')));

            $htmlArea = $this->newObject('htmlarea', 'htmlelements');
            $htmlArea->name = 'text';
            $htmlArea->width = '100%';

            $button = new button('submitform', $this->objLanguage->languageText('mod_assignment_submitassignment', 'assignment', 'Submit Assignment'));
            $button->setToSubmit();
            ;

            $form->addToForm($hiddenInput->show() . $htmlArea->show() . '<br />' . $button->show());

            $ret .= $form->show();
        } else { // Upload Assignment
            $header = new htmlHeading();
            $header->str = $this->objLanguage->languageText('mod_assignment_uploadnewfile', 'assignment'); //$this->objLanguage->languageText('mod_filemanager_uploadnewfile', 'filemanager', 'Upload new file')
            $header->type = 4;

            $ret .= $header->show();

            $form = new form('addassignmentbyupload', $this->uri(array('action' => 'uploadassignment')));
            $form->extra = 'enctype="multipart/form-data"';

            $objUpload = $this->newObject('uploadinput', 'filemanager');
            $objUpload->targetDirMode = TARGETDIRMODE_USER;

            $filetypes = $this->objAssignmentUploadablefiletypes->getFiletypes($assignment['id']);
            if (empty($filetypes)) {
                $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $allowedFilesString = $this->objSysConfig->getValue('FILETYPES_ALLOWED', 'assignment');
                if (is_null($allowedFilesString)) {
                    $allowedFileTypes = array('doc', 'odt', 'rtf', 'txt', 'docx', 'mp3', 'ppt', 'pptx', 'mp3', 'pdf');
                } else {
                    $allowedFileTypes = explode(',', $allowedFilesString);
                }
            } else {
                $allowedFileTypes = array();
                foreach ($filetypes as $filetype) {
                    $allowedFileTypes[] = $filetype['filetype'];
                }
            }
            $objUpload->restrictFileList = $allowedFileTypes;

            $button = new button('submitform', $this->objLanguage->languageText('mod_assignment_uploadassignment', 'assignment', 'Upload Assignment'));
            $button->setToSubmit();

            $form->addToForm($hiddenInput->show() . $objUpload->show() . '<br />' . $button->show());
            $ret .= $form->show();

            $header = new htmlHeading();
            $header->str = $this->objLanguage->languageText('mod_filemanager_chooseexisting', 'filemanager', 'Choose existing file from file manager');
            $header->type = 4;

            $ret .= $header->show();

            $form = new form('submitassignment', $this->uri(array('action' => 'submitassignment')));
            $objSelectFile = $this->newObject('selectfile', 'filemanager');
            $objSelectFile->name = 'assignment';
            $objSelectFile->restrictFileList = $allowedFileTypes;
            $objSelectFile->setForceRestrictions(TRUE);

            $button = new button('submitform', 'Submit Assignment');
            $button->setToSubmit();

            $form->addToForm($hiddenInput->show() . $objSelectFile->show() . '<br />' . $button->show());

            $ret .= $form->show();
        }
    }
}

$links = '';

$backLink = new link($this->uri(array()));
$backLink->link = $this->objLanguage->languageText('mod_assignment_backtolist', 'assignment', 'Back to List of Assignments');
$links .= '<p class="assignment_link_return">' . $backLink->show() . '</p>';

if ($this->isValid('markassignments')) { //'edit'
    // [[ JOC OK
    if (!empty($submissions)) {
        $exportLink = new link($this->uri(array("action" => "exporttospreadsheet", "assignmentid" => $assignment['id'])));
        $exportLink->link = $this->objLanguage->languageText('mod_assignment_exporttospreadsheet', 'assignment');
        $links .= '<p class="assignment_link_export">' . $exportLink->show() . '</p>';
    }
    // ]] JOC OK
    // [[ JOC OK
    if ($assignment['format'] == '1' && !empty($submissions)) {
        $downloadalllink = new link($this->uri(array("action" => "downloadall", 'id' => $assignment['id'])));
        $downloadalllink->link = $this->objLanguage->languageText('mod_assignment_downloadall', 'assignment');
        $links .= '<p class="assignment_link_dnlall">' . $downloadalllink->show() . '</p>';
    }
    // ]] JOC OK
}
$ret .= $links;
echo "<div class='assignment_main'>$ret</div>";
?>
