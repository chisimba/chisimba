<?php

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
            
                $htmlArea = $this->newObject('htmlarea', 'htmlelements');
                $htmlArea->name = $question['id'];
                $htmlArea->setBasicToolbar();
                
                
                
                $str .= $htmlArea->show();
            $str .= '</div>';
        
        $str .= '</div>';
        
        $form->addToForm($str);
        $counter++;
    }

$button = new button ('back', $this->objLanguage->languageText('mod_worksheet_back', 'worksheet', 'Back'),'history.go(-1);return true;');

$form->addToForm('<br /><p align="center">'.$button->show() .'</p><br />&nbsp;');

echo $form->show();

?>