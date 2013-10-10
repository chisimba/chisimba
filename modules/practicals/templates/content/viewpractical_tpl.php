<?php

//var_dump($practical);

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
$header->str = $practical['name'];

if ($this->isValid('edit')) {
    $editLink = new link($this->uri(array('action' => 'edit', 'id' => $practical['id'])));
    $editLink->link = $editIcon;

    $deleteLink = new link($this->uri(array('action' => 'delete', 'id' => $practical['id'], 'return' => 'view')));
    $deleteLink->link = $deleteIcon;

    $header->str .= ' ' . $editLink->show() . '&nbsp;' . $deleteLink->show();
}

$header->type = 1;

$objDateTime = $this->getObject('dateandtime', 'utilities');
$objTrimStr = $this->getObject('trimstr', 'strings');

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('word_description', 'system', 'Description') . '</strong>', 130);
$table->addCell($objWashout->parseText($practical['description']), NULL, NULL, NULL, NULL, ' colspan="3"');
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . ucfirst($this->objLanguage->code2Txt('mod_practicals_lecturer', 'practicals', NULL, '[-author-]')) . '</strong>', 130);
$table->addCell($this->objUser->fullName($practical['userid']));
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_practicals_totalmark', 'practicals') . '</strong>', 130);
$table->addCell($practical['mark']);
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_practicals_openingdate', 'practicals', 'Opening Date') . '</strong>', 130);
$table->addCell($objDateTime->formatDate($practical['opening_date']));
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_practicals_percentyrmark', 'practicals', 'Percentage of year mark') . '</strong>', 200, NULL, NULL, 'nowrap');
$table->addCell($practical['percentage'] . '%');
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_practicals_closingdate', 'practicals', 'Closing Date') . '</strong>', 130);
$table->addCell($objDateTime->formatDate($practical['closing_date']));
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_practicals_practicaltype', 'practicals', 'Practical Type') . '</strong>', 130);
if ($practical['format'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_practicals_online', 'practicals', 'Online'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_practicals_upload', 'practicals', 'Upload'));
}
$table->endRow();

$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_practicals_emailalerttostudents1', 'practicals', 'Email on creation') . '</strong>', 130);
if ($practical['email_alert'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_practicals_emailalertoff', 'practicals', 'Off'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_practicals_emailalerton', 'practicals', 'On'));
}


$table->addCell('<strong>' . $this->objLanguage->languageText('mod_practicals_emailalertfromstudents1', 'practicals', 'Email on submission') . '</strong>', 130);
if ($practical['email_alert_onsubmit'] == '0') {
    $table->addCell($this->objLanguage->languageText('mod_practicals_emailalertoff', 'practicals', 'Off'));
} else {
    $table->addCell($this->objLanguage->languageText('mod_practicals_emailalerton', 'practicals', 'On'));
}
$table->endRow();

if ($practical['format'] == '1') {
    $table->startRow();
    $filetypes = $this->objPracticalUploadablefiletypes->getFiletypes($practical['id']);
    if (empty($filetypes)) {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $allowedFilesString = $objSysConfig->getValue('FILETYPES_ALLOWED', 'practicals');
        $allowedFileTypes = explode(',', $allowedFilesString);
    } else {
        $allowedFileTypes = array();
        foreach ($filetypes as $filetype) {
            $allowedFileTypes[] = $filetype['filetype'];
        }
    }
    if (empty($allowedFileTypes)) {
        $str = $this->objLanguage->languageText('word_none', 'practicals');
    } else {
        $str = '';
        $separator = '';
        foreach ($allowedFileTypes as $filetype) {
            $str .= $separator . $filetype;
            $separator = '&nbsp;';
        }
    }
    $table->addCell('<strong>' . $this->objLanguage->languageText('mod_practicals_uploadablefiletypes', 'practicals') . '</strong>&nbsp;' . $str, NULL, NULL, NULL, NULL, 'colspan="4"');
    $table->endRow();

    $table->startRow();
    $table->addCell('<br/>', NULL, NULL, NULL, NULL, 'colspan="2"');
    $table->endRow();
}
if ($practical['usegoals'] == '1') {

    $fieldset = new fieldset();
    $fieldset->setLegend('<b>' . $this->objLanguage->languageText('mod_practicals_learningoutcomes', 'practicals', 'Learning outcomes') . ':</b>');
    $fieldset->addContent($goals);

    $table->startRow();
    $table->addCell($fieldset->show(), NULL, NULL, NULL, NULL, 'colspan="4"');
    $table->endRow();
}
if ($practical['usegroups'] == '1') {

    $gfieldset = new fieldset();
    $gfieldset->setLegend('<b>'.$this->objLanguage->languageText('mod_practicals_groups', 'practicals', 'Groups').'</b>');
    $gfieldset->addContent($groups);

    $table->startRow();
    $table->addCell($gfieldset->show(), NULL, NULL, NULL, NULL, 'colspan="4"');
    $table->endRow();
}
echo $table->show();

$htmlHeader = new htmlHeading();
$htmlHeader->type = 1;
$htmlHeader->str = $this->objLanguage->languageText('mod_practicals_submittedpracticals', 'practicals', 'Submitted Practicals');
echo '<hr />' . $htmlHeader->show();

// If Lecturer, show list of practicals
if ($this->isValid('markpracticals')) {
    $submissions = $this->objPracticalSubmit->getStudentSubmissions($practical['id']);
    $table = $this->newObject('htmltable', 'htmlelements');
    $table->startHeaderRow();
    $table->addHeaderCell(ucfirst($this->objLanguage->code2Txt('mod_practicals_studname', 'practicals', NULL, '[-readonly-] name')));
    $table->addHeaderCell($this->objLanguage->languageText('mod_practicals_datesubmitted', 'practicals', 'Date Submitted'));
    $table->addHeaderCell($this->objLanguage->languageText('mod_practicals_mark', 'practicals', 'Mark'));
    $table->addHeaderCell($this->objLanguage->languageText('mod_practicals_comment', 'practicals', 'Comment'));
    $table->endHeaderRow();

    if (count($submissions) == 0) {
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_practicals_nopracticalssubmitted', 'practicals', 'No Practicals Submitted Yet'), NULL, NULL, NULL, 'noRecordsMessage', ' colspan="4"');
        $table->endRow();
    } else {

        foreach ($submissions as $submission) {
            $table->startRow();

            $link = new link($this->uri(array('action' => 'viewsubmission', 'id' => $submission['id'])));
            $link->link = $this->objUser->fullName($submission['userid']);

            $table->addCell($link->show());
            $table->addCell($objDateTime->formatDate($submission['datesubmitted']));

            if ($submission['mark'] == NULL) {
                $table->addCell('<em>' . $this->objLanguage->languageText('mod_practicals_notmarked', 'practicals', 'Not Marked') . '</em>');
                $table->addCell('<em>' . $this->objLanguage->languageText('mod_practicals_notmarked', 'practicals', 'Not Marked') . '</em>');
            } else {
                $table->addCell($submission['mark']);
                $table->addCell($objTrimStr->strTrim($submission['commentinfo'], 50));
            }

            $table->endRow();
        }
    }

    echo $table->show();

} else {
    // Show Student Views

    $submissions = $this->objPracticalSubmit->getStudentPractical($this->objUser->userId(), $practical['id']);

//    if (count($submissions) == 0) {

//    } else if (count($submissions) == 0) {

//    } else {
    if (count($submissions) != 0) {

        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startHeaderRow();
        $table->addHeaderCell($this->objLanguage->languageText('mod_practicals_submissions', 'practicals', 'Submissions'));
        $table->addHeaderCell($this->objLanguage->languageText('mod_practicals_datesubmitted', 'practicals', 'Date Submitted'));
        $table->addHeaderCell($this->objLanguage->languageText('mod_practicals_mark', 'practicals', 'Mark'));
        //$table->addHeaderCell($this->objLanguage->languageText('mod_practicals_comment', 'practicals', 'Comment'));
        $table->endHeaderRow();

        $objFile = $this->getObject('dbfile', 'filemanager');
        /*
         * Creating the link to view practical results
         */
        foreach ($submissions as $submission) {

            $isMarked = date('Y-m-d H:i:s') > $practical['closing_date'];

            $table->startRow();
            /*
              if (!$isMarked) {
              $table->addCell('<em>'.$this->objLanguage->languageText('mod_practicals_notmarked', 'practicals', 'Not Marked').'</em>');
              }
              else {
             */
            $link = new link($this->uri(array('action' => 'viewsubmission', 'id' => $submission['id'])));
            $link->link = $this->objLanguage->languageText('mod_practicals_viewscoremark', 'practicals');
            $table->addCell($link->show());
            /*
              }
             */

            $table->addCell($objDateTime->formatDate($submission['datesubmitted']));

            if (!$isMarked) {
                $table->addCell('<em>' . $this->objLanguage->languageText('mod_practicals_notmarked', 'practicals', 'Not Marked') . '</em>');
                //$table->addCell('<em>'.$this->objLanguage->languageText('mod_practicals_notmarked', 'practicals', 'Not Marked').'</em>');
            } else {

                if ($submission['mark'] == NULL) {
                    $table->addCell('<em>' . $this->objLanguage->languageText('mod_practicals_notmarked', 'practicals', 'Not Marked') . '</em>');
                    //$table->addCell('<em>'.$this->objLanguage->languageText('mod_practicals_notmarked', 'practicals', 'Not Marked').'</em>');
                } else {
                    $table->addCell($submission['mark']);
                    /*
                     * The commented line prevents view of comments untill the practical is opened for viewing the results
                     */
                    // $table->addCell($objTrimStr->strTrim($submission['commentinfo'], 50));
                }
            }

            $table->endRow();
        }

        echo $table->show();
    }

    if (true) {
        $hiddenInput = new hiddeninput('id', $practical['id']);

        $header = new htmlHeading();
        $header->type = 1;
        $header->str = $this->objLanguage->languageText('mod_practicals_submitpractical', 'practicals', 'Submit Practical');
        echo '<hr />' . $header->show();

        // Display by Practical Type
        if ($practical['closing_date'] < date('Y-m-d H:i')) {
            echo '<div class="noRecordsMessage">' . $this->objLanguage->languageText('mod_practicals_practicalclosed', 'practicals', 'Practical Closed') . '</div>';
        } else if ($practical['format'] == '0') { // Online Practical
            $form = new form('addpractical', $this->uri(array('action' => 'submitonlinepractical')));

            $htmlArea = $this->newObject('htmlarea', 'htmlelements');
            $htmlArea->name = 'text';
            $htmlArea->width = '100%';

            $button = new button('submitform', $this->objLanguage->languageText('mod_practicals_submitpractical', 'practicals', 'Submit Practical'));
            $button->setToSubmit();
            ;

            $form->addToForm($hiddenInput->show() . $htmlArea->show() . '<br />' . $button->show());

            echo $form->show();
        } else { // Upload Practical
            $header = new htmlHeading();
            $header->str = $this->objLanguage->languageText('mod_practicals_uploadnewfile', 'practicals'); //$this->objLanguage->languageText('mod_filemanager_uploadnewfile', 'filemanager', 'Upload new file')
            $header->type = 4;

            echo $header->show();

            $form = new form('addpracticalbyupload', $this->uri(array('action' => 'uploadpractical')));
            $form->extra = 'enctype="multipart/form-data"';

            $objUpload = $this->newObject('uploadinput', 'filemanager');
            $objUpload->targetDirMode = TARGETDIRMODE_USER;

            $filetypes = $this->objPracticalUploadablefiletypes->getFiletypes($practical['id']);
            if (empty($filetypes)) {
                $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $allowedFilesString = $this->objSysConfig->getValue('FILETYPES_ALLOWED', 'practicals');
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

            $button = new button('submitform', $this->objLanguage->languageText('mod_practicals_uploadpractical', 'practicals', 'Upload Practical'));
            $button->setToSubmit();

            $form->addToForm($hiddenInput->show() . $objUpload->show() . '<br />' . $button->show());
            echo $form->show();

            $header = new htmlHeading();
            $header->str = $this->objLanguage->languageText('mod_filemanager_chooseexisting', 'filemanager', 'Choose existing file from file manager');
            $header->type = 4;

            echo $header->show();

            $form = new form('submitpractical', $this->uri(array('action' => 'submitpractical')));
            $objSelectFile = $this->newObject('selectfile', 'filemanager');
            $objSelectFile->name = 'practicals';
            $objSelectFile->restrictFileList = $allowedFileTypes;
            $objSelectFile->setForceRestrictions(TRUE);

            $button = new button('submitform', 'Submit Practical');
            $button->setToSubmit();

            $form->addToForm($hiddenInput->show() . $objSelectFile->show() . '<br />' . $button->show());

            echo $form->show();
        }
    }
}

$links = '';

$backLink = new link($this->uri(array()));
$backLink->link = $this->objLanguage->languageText('mod_practicals_backtolist', 'practicals', 'Back to List of Practicals');
$links .= $backLink->show();

if ($this->isValid('edit')) {
    $exportLink = new link($this->uri(array("action" => "exporttospreadsheet", "practicalid" => $practical['id'])));
    $exportLink->link = $this->objLanguage->languageText('mod_practicals_exporttospreadsheet', 'practicals');
    $links .= '<br />'.$exportLink->show();
    if ($practical['format'] == '1') {
        $downloadalllink = new link($this->uri(array("action" => "downloadall", 'id' => $practical['id'])));
        $downloadalllink->link = $this->objLanguage->languageText('mod_practicals_downloadall', 'practicals');
        $links .= '<br />'.$downloadalllink->show();
    }
}

echo $links;
?>