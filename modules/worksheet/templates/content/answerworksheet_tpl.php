<?php

if ($this->getParam('message') == 'worksheetsaved') {
    $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
    $timeoutMessage->message = $this->objLanguage->languageText('mod_worksheet_answerssaved', 'worksheet', 'Worksheet Answers have been saved');
    echo $timeoutMessage->show();
}

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$header = new htmlheading();
$header->type = 1;

$header->str = $this->objLanguage->languageText('mod_worksheet_worksheet', 'worksheet', 'Worksheet').': '.$worksheet['name'];



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

$form = new form ('saveanswers', $this->uri(array('action'=>'saveanswers')));

$hiddenInput = new hiddeninput('id', $id);
$form->addToForm($hiddenInput->show());

$userInput = new hiddeninput('user', $this->objUser->userId());
$form->addToForm($userInput->show());

$this->objCond = $this->newObject('contextCondition', 'contextpermissions');
$isStudent=$this->objCond->isContextMember('Students');

    $counter = 1;
    foreach ($questions as $question)
    {
        $str = '<div class="newForumContainer">';
            $str .= '<div class="newForumTopic">';
                $str .= '<strong>'.$this->objLanguage->languageText('mod_worksheet_question', 'worksheet', 'Question').' '.$counter.':</strong><br />';
                $str .= $this->objWashout->parseText($question['question']);
                $str .= '<strong>'.$this->objLanguage->languageText('mod_worksheet_marks', 'worksheet', 'Marks').'</strong> ('.$question['question_worth'].')';
            $str .= '</div>';
        if ($isStudent){
            $str .= '<div class="newForumContent">';
            
                $htmlArea = $this->newObject('htmlarea', 'htmlelements');
                $htmlArea->name = $question['id'];
                $htmlArea->setBasicToolbar();
                
                $studentAnswer = $this->objWorksheetAnswers->getAnswer($question['id'], $this->objUser->userId());
                
                if ($studentAnswer != FALSE) {
                    $htmlArea->value = $studentAnswer['answer'];
                }
                
                
                $str .= $htmlArea->show();
            $str .= '</div>';
        }
        
        $str .= '</div>';
        
        $form->addToForm($str);
        $counter++;
    }

if ($isStudent){
    $button = new button ('saveanswers', $this->objLanguage->languageText('mod_worksheet_saveanswers', 'worksheet', 'Save Answers'));
    $button->setToSubmit();

    $button2 = new button ('saveandclose', $this->objLanguage->languageText('mod_worksheet_saveanswersandsubmit', 'worksheet', 'Save Answers and Submit for Marking'));
    $button2->setToSubmit();
    $form->addToForm('<br /><p align="center">'.$button->show().' &nbsp; '.$button2->show().'</p><br />&nbsp;');
    }

echo $form->show();

?>
