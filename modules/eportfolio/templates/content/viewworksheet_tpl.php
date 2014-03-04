<?php
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_worksheet_worksheet', 'worksheet', 'Worksheet') . ': ' . $worksheet['name'];
echo '<br />' . $header->show();
echo $worksheet['description'];
$objDateTime = $this->getObject('dateandtime', 'utilities');
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_worksheet_closingdate', 'worksheet', 'Closing Date') . '</strong>: ' . $objDateTime->formatDate($worksheet['closing_date']) , '55%');
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_worksheet_questions', 'worksheet', 'Questions') . '</strong>: ' . count($questions) , '15%');
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_worksheet_percentage', 'worksheet', 'Percentage') . '</strong>: ' . $worksheet['percentage'] . '%', '15%');
$table->addCell('<strong>' . $this->objLanguage->languageText('mod_worksheet_totalmark', 'worksheet', 'Total Mark') . '</strong>: ' . $worksheet['total_mark'], '15%');
$table->endRow();
echo $table->show();
echo '<hr />';
$header = new htmlheading();
$header->type = 3;
$header->str = $this->objLanguage->languageText('mod_worksheet_result', 'worksheet', 'Result') . ':';
echo $header->show();
if ($worksheetResult == FALSE) {
    echo '<p>' . $this->objLanguage->languageText('mod_worksheet_result_notcompleted', 'worksheet', 'Worksheet not completed prior to expiry date') . ' - 0</p>';
} else {
    if ($worksheetResult['mark'] == '-1') {
        echo '<p class="error">' . $this->objLanguage->languageText('mod_worksheet_result_notmarked', 'worksheet', 'Worksheet submitted but not yet marked') . '.</p>';
    } else {
        $score = $this->objLanguage->code2Txt('mod_worksheet_result_marked', 'worksheet', NULL, '[-mark-] out of [-total-]');
        $score = str_replace('[-mark-]', $worksheetResult['mark'], $score);
        $score = str_replace('[-total-]', $worksheet['total_mark'], $score);
        echo '<p>' . $score . '</p>';
    }
}
echo '<hr />';
$objWashout = $this->getObject('washout', 'utilities');
$counter = 1;
foreach($questions as $question) {
    $str = '<div class="newForumContainer">';
    $str.= '<div class="newForumTopic">';
    $str.= '<strong>' . $this->objLanguage->languageText('mod_worksheet_question', 'worksheet', 'Question') . ' ' . $counter . ':</strong><br />';
    $str.= $objWashout->parseText($question['question']);
    $str.= '<strong>' . $this->objLanguage->languageText('mod_worksheet_marks', 'worksheet', 'Marks') . '</strong> (' . $question['question_worth'] . ')';
    $str.= '</div>';
    $str.= '<div class="newForumContent">';
    $studentAnswer = $this->objWorksheetAnswers->getAnswer($question['id'], $this->objUser->userId());
    if ($studentAnswer != FALSE) {
        $str.= $studentAnswer['answer'];
        $str.= '</div><div class="newForumContent">';
        if ($studentAnswer['mark'] == NULL) {
            $str.= '<p class="error">' . 'Your answer has not been marked yet.' . '</p>';
        } else {
            $table = $this->newObject('htmltable', 'htmlelements');
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_worksheet_mark', 'worksheet', 'Mark') . ':', 180);
            $table->addCell($studentAnswer['mark']);
            $table->endRow();
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_worksheet_comment', 'worksheet', 'Comment') . ':');
            $table->addCell($studentAnswer['comments']);
            $table->endRow();
            $table->startRow();
            $table->addCell(ucwords($this->objLanguage->code2Txt('mod_worksheet_lecturer', 'worksheet', NULL, '[-author-]')) . ':');
            $table->addCell($objUser->fullName($studentAnswer['lecturer_id']));
            $table->endRow();
            $str.= $table->show();
        }
    } else {
        $str.= '<div class="noRecordsMessage">' . $this->objLanguage->languageText('mod_worksheet_notanswered', 'worksheet', 'Not answered') . '</div>';
    }
    $str.= '</div>';
    $str.= '</div>';
    echo $str;
    $counter++;
}
//Get Object
$this->objIcon = &$this->newObject('geticon', 'htmlelements');
$objLayer3 = $this->newObject('layer', 'htmlelements');
$this->objIcon->setIcon('close');
$this->objIcon->extra = " onclick='javascript:window.close()'";
$objLayer3->align = 'center';
$objLayer3->str = $this->objIcon->show();
echo $objLayer3->show();
?>
