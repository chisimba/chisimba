<?php
/**
 * Template for adding / editing an answer to / in a question in a test.
 * @package mcqtests
 * @param array $data The question being answered.
 * @param array $answer The answer being edited.
 * @param string $mode Add / Edit.
 */
// set up layout template'
     $this->setLayoutTemplate('mcqtests_layout_tpl.php');

// set up html elements
     $objTable = &$this->loadClass('htmltable', 'htmlelements');
     $objForm = &$this->loadClass('form', 'htmlelements');
     $objInput = &$this->loadClass('textinput', 'htmlelements');
     $objText = &$this->loadClass('textarea', 'htmlelements');
     $objButton = &$this->loadClass('button', 'htmlelements');
     $objInput = &$this->loadClass('dropdown', 'htmlelements');
     $objInput = &$this->loadClass('checkbox', 'htmlelements');
     $objRadio = $this->loadClass('radio', 'htmlelements');

// set up language items
     $heading = $this->objLanguage->languageText('mod_mcqtests_addanswers', 'mcqtests');
     $editHeading = $this->objLanguage->languageText('mod_mcqtests_editanswer', 'mcqtests');
     $questionLabel = $this->objLanguage->languageText('mod_mcqtests_question', 'mcqtests');
     $answerLabel = $this->objLanguage->languageText('mod_mcqtests_answer', 'mcqtests');
     $commentLabel = $this->objLanguage->languageText('mod_mcqtests_comment', 'mcqtests');
     $correctLabel = $this->objLanguage->languageText('mod_mcqtests_selectcorrect', 'mcqtests');
     $saveLabel = $this->objLanguage->languageText('word_save');
    $exitLabel = $this->objLanguage->languageText('word_cancel');

if ($mode == 'edit') {
    $this->setVarByRef('heading', $editHeading);
} else {
    $this->setVarByRef('heading', $heading);
}
if ($mode == 'edit' && !empty($answer)) {
    $dAnswer = $answer['answer'];
    $num = $answer['answerorder'];
} else {
    $dAnswer = '';
    //$aOrder = $data['count']+1;
}

$objWashout = $this->getObject('washout', 'utilities');
//print_r($data['count']);

   $num = 1;
// Display test info
echo '<strong>'.$questionLabel.':</strong>&nbsp;&nbsp;'.$objWashout->parseText($data['question']);

	$objTable = new htmltable();
	$objTable->cellpadding = 5;
	$objTable->width = '99%';
	$objTable->startRow();
	$objTable->addCell('<b>'.$answerLabel.' '.$num++.':</b>', '', '', '', '', 'colspan="3"');
	$objTable->endRow();


   $j = 1;

	for($i=0; $i <= 3; $i++) {

		$j = $i+1;
      $objText = new textarea('answer'.$j,'', 2, 80);


        if (isset($answers[$i])) {
            $objText->value = $answers[$i]['answer'];
        }
		$objTable->startRow();
		$objTable->addCell($objText->show() , '', '', '', '', 'colspan="2"');
		$objTable->endRow();
		$objTable->row_attributes = 'height="15"';
		$objTable->startRow();
		$objTable->addCell('', '', '', '', '', 'colspan="3"');
		if ($i == 0){
      $objTable->startRow();
      $objTable->addCell('<b>'.$this->objLanguage->languageText('mod_mcqtests_alternativeanswers', 'mcqtests').'</b>','','','','colspan="3"');
      $objTable->endRow();
      }

	 }
//else{
	//  foreach($answers as $answer) {
	//	$objText = new textarea('answer'.$j,$answer['answer'], 2, 80);
		//$objTable->startRow();
		//$objTable->addCell($objText->show() , '', '', '', '', 'colspan="2"');
		//$objTable->endRow();

		//$objTable->row_attributes = 'height="15"';
		//$objTable->startRow();
		//$objTable->addCell('', '', '', '', '', 'colspan="3"');
	//$objTable->endRow();

	//}
//}
//}

// hidden elements
   $objInput = new textinput('testId', $data['testid']);
   $objInput->fldType = 'hidden';
   $hidden = $objInput->show();
   $objInput = new textinput('questionId', $data['id']);
   $objInput->fldType = 'hidden';
   $hidden.= $objInput->show();

      $objInput = new textinput('qtype','freeform');
   $objInput->fldType = 'hidden';
   $hidden.= $objInput->show();
   if ($mode == 'edit' && !empty($answer)) {
      $objInput = new textinput('answerId', $answer['id']);
      $objInput->fldType = 'hidden';
      $hidden.= $objInput->show();
   }
// Save and exit buttons
   $objButton = new button('save', $saveLabel);
   $objButton->setToSubmit();
   $btn = $objButton->show();
   $objButton = new button('save', $exitLabel);
   $objButton->setToSubmit();
   $btn.= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();
   $objTableButtons = new htmltable();
   $objTableButtons->startRow();
   $objTableButtons->addCell($hidden);
   $objTableButtons->addCell($btn, '', '', '', '', 'colspan="2"');
   $objTableButtons->endRow();
// Create form and add the table
   $objForm = new form('addfreeformanswer', $this->uri(array(
    'action' => 'applyfreeformanswer'
)));
   $objForm->addToForm($objTable->show());
   $objForm->addToForm($objTableButtons->show());
   $str = $objForm->show();

echo $str;
?>
