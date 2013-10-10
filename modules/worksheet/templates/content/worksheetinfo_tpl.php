<?php



$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('link', 'htmlelements');

/*
$js = $this->getJavascriptFile('jquery/jquery.form.js', 'htmlelements');
$this->appendArrayVar('headerParams', $js);
$this->appendArrayVar('headerParams', $this->getJavaScriptFile('worksheet.js'));

$script = "jQuery('#form_addquestion').ajaxForm(options);";
$this->appendArrayVar('bodyOnLoad', $script);
*/

$objIcon = $this->newObject('geticon', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $worksheet['name'].' : '.ucfirst($this->objLanguage->languageText('mod_worksheet_information','worksheet'));

$objStepMenu = $this->newObject('stepmenu', 'navigation');
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_worksheetinfo', 'worksheet', 'Worksheet Information'), $this->objLanguage->languageText('mod_worksheet_worksheetinfo_desc', 'worksheet', 'Add Information about the Worksheet'));
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_addquestions', 'worksheet', 'Add Questions'), $this->objLanguage->languageText('mod_worksheet_addquestions_desc', 'worksheet', 'Add Questions and Mark Allocation to the worksheet'), $this->uri(array('action'=>'managequestions', 'id'=>$id)));
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_activateworksheet', 'worksheet', 'Activate Worksheet'), $this->objLanguage->code2Txt('mod_worksheet_activateworksheet_desc', 'worksheet', NULL, 'Allow [-readonlys-] to start answering worksheet'), $this->uri(array('action'=>'activate', 'id'=>$id)));

$objStepMenu->setCurrent(1);

echo $objStepMenu->show();

echo '<br />'.$header->show();

echo $this->objWashout->parseText($worksheet['description']);

$objDateTime = $this->getObject('dateandtime', 'utilities');

$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_closingdate', 'worksheet', 'Closing Date').'</strong>: '.$objDateTime->formatDate($worksheet['closing_date']), '55%');
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_questions', 'worksheet', 'Questions').'</strong>: '.count($questions), '15%');
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_percentage', 'worksheet', 'Percentage').'</strong>: '.$worksheet['percentage'].'%', '15%');
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_totalmark', 'worksheet', 'Total Mark').'</strong>: '.$worksheet['total_mark'], '15%');
$table->endRow();

echo $table->show();

echo '<hr />';

/*
$editLink = new link ($this->uri(array('action'=>'editworksheet', 'id'=>$id)));
$editLink->link = $this->objLanguage->languageText('mod_worksheet_editworksheet', 'worksheet', 'Edit Worksheet');

$deleteLink = new link ($this->uri(array('action'=>'deleteworksheet', 'id'=>$id)));
$deleteLink->link = $this->objLanguage->languageText('mod_worksheet_deleteworksheet', 'worksheet', 'Delete Worksheet');
*/

$questionLink = new link ($this->uri(array('action'=>'managequestions', 'id'=>$id)));
$questionLink->link = $this->objLanguage->languageText('mod_worksheet_addremovequestions', 'worksheet', 'Add/ Edit / Remove Questions');

$activateLink = new link ($this->uri(array('action'=>'activate', 'id'=>$id)));
$activateLink->link = $this->objLanguage->languageText('mod_worksheet_activatedeactivateworksheet', 'worksheet', 'Activate / Deactivate Worksheet');

echo '<p>Worksheet Information | '/*.$editLink->show().' | '.$deleteLink->show().' | '*/.$questionLink->show().' | '.$activateLink->show().'</p>';

echo '<hr />';

$header = new htmlheading();
$header->type = 3;

$header->str = 'Student Submissions:';

echo $header->show();

if (count($worksheetResults) == 0 || $worksheetResults == FALSE) {
    echo '<div class="noRecordsMessage">'.$this->objLanguage->code2Txt('mod_worksheet_notstudentsattempt', 'worksheet', NULL, 'No [-readonlys-] have attempted the worksheet yet').'.</div>';
} else {
    $table = $this->newObject('htmltable', 'htmlelements');

    $table->startHeaderRow();
        $table->addHeaderCell($this->objLanguage->code2Txt('mod_worksheet_studnumber', 'worksheet', NULL, '[-readonly-] Number'), 200);
        $table->addHeaderCell($this->objLanguage->code2Txt('mod_worksheet_student', 'worksheet', NULL, '[-readonly-]'));
        $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_finalmark', 'worksheet', 'Final Mark'), 100);
        $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_datecompleted', 'worksheet', 'Date Completed'), 200);
        $table->addHeaderCell($this->objLanguage->languageText('word_view', 'system', 'View'), 100);
    $table->endHeaderRow();

    foreach ($worksheetResults as $result)
    {
        $table->startRow();
            $table->addCell($this->objUser->getStaffNumber($result['userid']));
            $table->addCell($this->objUser->fullName($result['userid']));

            if ($result['mark'] == '-1') {
                $mark = '<span class="error">'.$this->objLanguage->languageText('mod_worksheet_notmarked', 'worksheet', 'Not Marked').'</span>';
            } else {
                $mark = $result['mark'];
            }

            $table->addCell($mark);
            $table->addCell($objDateTime->formatDate($result['last_modified']));

            $link = new link ($this->uri(array('action'=>'viewstudentworksheet', 'id'=>$result['id'])));
            $link->link = $this->objLanguage->languageText('word_view', 'system', 'View');

            $table->addCell($link->show());
        $table->endRow();
    }

    echo $table->show();
}

$link = new link ($this->uri(NULL));
$link->link = $this->objLanguage->languageText('mod_worksheet_backtoworksheets', 'worksheet', 'Back to Worksheets');

echo '<p>'.$link->show().'</p>';


?>