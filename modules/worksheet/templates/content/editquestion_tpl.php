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
$header->str = $worksheet['name'].' : '.ucfirst($this->objLanguage->languageText('mod_worksheet_questions', 'worksheet'));

$objStepMenu = $this->newObject('stepmenu', 'navigation');
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_worksheetinfo', 'worksheet', 'Worksheet Information'), $this->objLanguage->languageText('mod_worksheet_worksheetinfo_desc', 'worksheet', 'Add Information about the Worksheet'), $this->uri(array('action'=>'worksheetinfo', 'id'=>$id)));
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_addquestions', 'worksheet', 'Add Questions'), $this->objLanguage->languageText('mod_worksheet_addquestions_desc', 'worksheet', 'Add Questions and Mark Allocation to the worksheet'));
$objStepMenu->addStep($this->objLanguage->languageText('mod_worksheet_activateworksheet', 'worksheet', 'Activate Worksheet'), $this->objLanguage->code2Txt('mod_worksheet_activateworksheet_desc', 'worksheet', NULL, 'Allow [-readonlys-] to start answering worksheet'), $this->uri(array('action'=>'activate', 'id'=>$id)));
$objStepMenu->setCurrent(2);

echo $objStepMenu->show();

echo '<br />'.$header->show();

echo $worksheet['description'];

$objDateTime = $this->getObject('dateandtime', 'utilities');

$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_closingdate', 'worksheet', 'Closing Date').'</strong>: '.$objDateTime->formatDate($worksheet['closing_date']), '55%');
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_questions', 'worksheet', 'Questions').'</strong>: '.$numQuestions, '15%');
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_percentage', 'worksheet', 'Percentage').'</strong>: '.$worksheet['percentage'].'%', '15%');
$table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_totalmark', 'worksheet', 'Total Mark').'</strong>: '.$worksheet['total_mark'], '15%');
$table->endRow();

echo $table->show();

echo '<hr />';





    $heading = new htmlHeading();
    $heading->type = 3;
    $heading->str = $this->objLanguage->languageText('mod_worksheet_editquestion', 'worksheet', 'Edit Question');

    echo $heading->show();

    //var_dump($questions);

    $form = new form ('addquestion', $this->uri(array('action'=>'updatequestion')));

    $table = $this->newObject('htmltable', 'htmlelements');

    // Question
    $table->startRow();
    $table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_question', 'worksheet', 'Question').'</strong>:', 200);

    $htmlArea = $this->newObject('htmlarea', 'htmlelements');
    $htmlArea->name = 'question';
    $htmlArea->value = $question['question'];

    $table->addCell($htmlArea->show());
    $table->endRow();

    // Model Answer
    $table->startRow();
    $table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_modelanswer', 'worksheet', 'Model Answer').'</strong>:');

    $textarea = new textarea('modelanswer');
    $textarea->extra = ' style="width: 100%"';
    $textarea->rows = 10;
    $textarea->value = $question['model_answer'];

    $table->addCell($textarea->show());
    $table->endRow();

    // Question Mark
    $table->startRow();
    $table->addCell('<strong>'.$this->objLanguage->languageText('mod_worksheet_questionworth', 'worksheet', 'Question Worth').'</strong>:');

    $textinput = new textinput('mark');
    $textinput->value = $question['question_worth'];

    $table->addCell($textinput->show());
    $table->endRow();

    // Spacer
    $table->startRow();
    $table->addCell('&nbsp;');
    $table->addCell('&nbsp;');
    $table->endRow();


    // Button
    $table->startRow();
    $table->addCell('&nbsp;');

    $submitButton = new button ('savequestion', $this->objLanguage->languageText('mod_worksheet_save', 'worksheet'));
    $submitButton->setToSubmit();

    $cancelButton = new button('cancel', $this->objLanguage->languageText('mod_worksheet_cancel', 'worksheet'));
    $returnUrl = $this->uri(array('action' => 'managequestions', 'id'=>$id));
    $cancelButton->setOnClick("javascript: window.location='{$returnUrl}';");

    $table->addCell($submitButton->show().'&nbsp;'.$cancelButton->show());
    $table->endRow();

    $form->addToForm($table->show());

    $hiddenInput = new hiddeninput('id', $question['id']);
    $form->addToForm($hiddenInput->show());

    $form->addRule('mark', $this->objLanguage->languageText('mod_worksheet_validation_mark', 'worksheet', 'Mark should be a number'), 'numeric');
    $form->addRule('mark', $this->objLanguage->languageText('mod_worksheet_validation_mark_req', 'worksheet', 'Please enter a mark'), 'required');

    echo $form->show();



echo '<hr />';

/*
$editLink = new link ($this->uri(array('action'=>'editworksheet', 'id'=>$id)));
$editLink->link = $this->objLanguage->languageText('mod_worksheet_editworksheet', 'worksheet', 'Edit Worksheet');

$deleteLink = new link ($this->uri(array('action'=>'deleteworksheet', 'id'=>$id)));
$deleteLink->link = $this->objLanguage->languageText('mod_worksheet_deleteworksheet', 'worksheet', 'Delete Worksheet');
*/

$infoLink = new link ($this->uri(array('action'=>'worksheetinfo', 'id'=>$id)));
$infoLink->link = $this->objLanguage->languageText('mod_worksheet_worksheetinfo', 'worksheet', 'Worksheet Information');

$addRemove = $this->objLanguage->languageText('mod_worksheet_addremovequestions', 'worksheet', 'Add / Remove Questions');

$activateLink = new link ($this->uri(array('action'=>'activate', 'id'=>$id)));
$activateLink->link = $this->objLanguage->languageText('mod_worksheet_activatedeactivateworksheet', 'worksheet', 'Activate / Deactivate Worksheet');

echo '<p>'.$infoLink->show().' | './*$editLink->show().' | '.$deleteLink->show().' | '.*/$addRemove.' | '.$activateLink->show().'</p>';

$link = new link ($this->uri(NULL));
$link->link = $this->objLanguage->languageText('mod_worksheet_backtoworksheets', 'worksheet', 'Back to Worksheets');

echo '<p>'.$link->show().'</p>';

?>