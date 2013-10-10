<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$header = new htmlheading();
$header->type = 1;

$link = new link ($this->uri(array('action'=>'worksheetinfo', 'id'=>$id)));
$link->link = $worksheet['name'];

$header->str = $this->objLanguage->languageText('mod_worksheet_worksheet', 'worksheet', 'Worksheet').': '.$link->show();



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

$header = new htmlheading();
$header->type = 3;
$header->str = $this->objLanguage->languageText('mod_worksheet_result', 'worksheet', 'Result').':';
echo $header->show();


if ($worksheetResult == FALSE) {
    echo '<p>'.$this->objLanguage->languageText('mod_worksheet_result_notcompleted', 'worksheet', 'Worksheet not completed prior to expiry date').' - 0</p>';
} else {
    if ($worksheetResult['mark'] == '-1') {
        echo '<p class="error">'.$this->objLanguage->languageText('mod_worksheet_result_notmarked', 'worksheet', 'Worksheet submitted but not yet marked').'.</p>';
    } else {
        $score = $this->objLanguage->code2Txt('mod_worksheet_result_marked', 'worksheet', NULL, '[-mark-] out of [-total-]');
        $score = str_replace('[-mark-]', $worksheetResult['mark'], $score);
        $score = str_replace('[-total-]', $worksheet['total_mark'], $score);
        echo '<p>'.$score.'</p>';
    }
}

echo '<hr />';

$form = new form ('savestudentmark', $this->uri(array('action'=>'savestudentmark')));

$hiddenInput = new hiddeninput('worksheet', $id);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('student', $worksheetResult['userid']);
$form->addToForm($hiddenInput->show());

$counter = 1;
foreach ($questions as $question)
{
    $str = '<div class="newForumContainer">';
        $str .= '<div class="newForumTopic">';
            $str .= '<strong>'.$this->objLanguage->languageText('mod_worksheet_question', 'worksheet', 'Question').' '.$counter.':</strong><br />';
            $str .= $this->objWashout->parseText($question['question']);
            $str .= '<strong>'.$this->objLanguage->languageText('mod_worksheet_marks', 'worksheet', 'Marks').'</strong> ('.$question['question_worth'].')';
        $str .= '</div>';
        $str .= '<div class="newForumContent">';
        
        $studentAnswer = $this->objWorksheetAnswers->getAnswer($question['id'], $worksheetResult['userid']);
        
        if ($studentAnswer != FALSE) {
            $str .= $studentAnswer['answer'];
            
            $str .= '</div><div class="newForumContent">';
            
            $str .= '<p><strong>'.$this->objLanguage->languageText('mod_worksheet_markanswer', 'worksheet', 'Mark Answer').':</strong></p>';
            
            $table = $this->newObject('htmltable', 'htmlelements');
            
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_worksheet_modelanswer', 'worksheet', 'Model Answer').':', 180);
            $table->addCell(nl2br(htmlentities($question['model_answer'])));
            $table->endRow();
            
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_worksheet_mark', 'worksheet', 'Mark').':');
            //var_dump($studentAnswer);
            //var_dump($question);
            
            $slider = $this->newObject('slider', 'htmlelements');
            $slider->name = $studentAnswer['id'];
            $slider->maxValue = $question['question_worth'];
            
            if ($studentAnswer['mark'] != '') {
                $slider->value = $studentAnswer['mark'];
            }
            
            $table->addCell($slider->show());
            $table->endRow();
            $table->startRow();
            $table->addCell($this->objLanguage->languageText('mod_worksheet_comment', 'worksheet', 'Comment').':');
            
            $textArea = new textarea('comment_'.$studentAnswer['id']);
            $textArea->value = $studentAnswer['comments'];
            //$htmlArea->height = '200px';
            
            $table->addCell($textArea->show());
            $table->endRow();
            
            $str .= $table->show();
            
        } else {
            $str .= '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_worksheet_notanswered', 'worksheet', 'Not answered').'</div>';
        }
            
        $str .= '</div>';
    
    $str .= '</div>';
    
    $form->addToForm($str);
    $counter++;
}


$button = new button ('save', $this->objLanguage->languageText('mod_worksheet_savemarks', 'worksheet', 'Save Marks'));
$button->setToSubmit();

$form->addToForm('<p align="center">'.$button->show().'</p>');


echo $form->show();

$link = new link ($this->uri(NULL));
$link->link = $this->objLanguage->languageText('mod_worksheet_backtoworksheets', 'worksheet', 'Back to Worksheets');

$link2 = new link ($this->uri(array('action'=>'worksheetinfo', 'id'=>$id)));
$link2->link =  $this->objLanguage->languageText('mod_worksheet_backtoworksheet', 'worksheet', 'Back to Worksheet').' - '.$worksheet['name'];

echo '<p>'.$link->show().' / '.$link2->show().'</p>';


?>