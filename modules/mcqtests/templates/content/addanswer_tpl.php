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
    $dComment = $answer['commenttext'];
    $num = $answer['answerorder'];
} else {
    $dAnswer = '';
    $dComment = '';
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
	
if($truefalse == true){
	$tf = array();
	$tf[] = 'true';
	$tf[] = 'false';
}else{
	$tf = array();
	for($i=0; $i < $qNum; $i++) {
		$tf[] = '';
	}
}
$j = 1;


//if(empty($answers))
//{
	for($i=0; $i < $qNum; $i++) {
		$j = $i+1;
		$objRadio = new radio('correctans');
	    $objRadio->addOption($j, '');
        
        // Setting default answer
        if(empty($answers)) {
            // If none, set first to be default answer
            $objRadio->setSelected(1);
        } else {
            // If reducing number of options, and not default selected, set to 1
            if ($correctAnswerNum > $qNum) {
                $objRadio->setSelected(1);
            } else {
                // Else set to existing correct answer
                $objRadio->setSelected($correctAnswerNum);
            }
            
        }
        
		$objText = new textarea('answer'.$j,$tf[$i], 2, 80);
        
        if (isset($answers[$i])) {
            $objText->value = $answers[$i]['answer'];
        }
        
		$objTable->startRow();
        $objTable->addCell('<b>'.$answerLabel.' '.$num++.':</b>', '', '', '', '', 'colspan="3"');
        $objTable->endRow();
        $objTable->startRow();
		$objTable->addCell($objRadio->show());
		$objTable->addCell($objText->show() , '', '', '', '', 'colspan="2"');
		$objTable->endRow();
		$objInput = new textinput('comment'.$j, $dComment);
		$objInput->size = 70;
		$objTable->startRow();
		$objTable->addCell('<b>'.$commentLabel.':</b>', '7%', 'center', '', '');
		$objTable->addCell($objInput->show() , '', '', '', '');
		$objTable->endRow();
		$objTable->row_attributes = 'height="15"';
		$objTable->startRow();
		$objTable->addCell('', '', '', '', '', 'colspan="3"');
		$objTable->endRow();
	}

/*
}else{
	foreach($answers as $answer) {
		$objRadio = new radio('correctans');
	    $objRadio->addOption($j, '');
	    if($answer['correct'] == 1){
	    	$objRadio->setSelected($answer['answerorder']);
	    }
		$objText = new textarea('answer'.$j,$answer['answer'], 2, 80);
		$objTable->startRow();
		$objTable->addCell($objRadio->show());
		$objTable->addCell($objText->show() , '', '', '', '', 'colspan="2"');
		$objTable->endRow();
		$objInput = new textinput('comment'.$j, $answer['commenttext']);
		$objInput->size = 70;
		$objTable->startRow();
		$objTable->addCell('<b>'.$commentLabel.':</b>', '7%', 'center', '', '');
		$objTable->addCell($objInput->show() , '', '', '', '');
		$objTable->endRow();
		$objTable->row_attributes = 'height="15"';
		$objTable->startRow();
		$objTable->addCell('', '', '', '', '', 'colspan="3"');
		$objTable->endRow();
		$j++;
	}
}
*/

// hidden elements
$objInput = new textinput('testId', $data['testid']);
$objInput->fldType = 'hidden';
$hidden = $objInput->show();
$objInput = new textinput('questionId', $data['id']);
$objInput->fldType = 'hidden';
$hidden.= $objInput->show();
$objInput = new textinput('qNum', $qNum);
$objInput->fldType = 'hidden';
$hidden.= $objInput->show();

/*
if ($mode == 'edit') {
    $objInput = new textinput('answerId', $answer['id']);
    $objInput->fldType = 'hidden';
    $hidden.= $objInput->show();
}*/

// Save and exit buttons
$objButton = new button('save', $saveLabel);
$objButton->setToSubmit();
$btn = $objButton->show();
$objButton = new button('save', $exitLabel);
$objButton->setToSubmit();
$btn.= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();
$objTable->startRow();
$objTable->addCell($hidden);
$objTable->addCell($btn, '', '', '', '', 'colspan="2"');
$objTable->endRow();

// Create form and add the table
$objForm = new form('addanswer', $this->uri(array('action' => 'applyaddanswer')));
$objForm->addToForm($objTable->show());

echo $objForm->show();

?>