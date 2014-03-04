<?php
/**
* Template to display a list of student submissions for a worksheet.
* @package worksheetadmin
*/

/**
* @param array $data The students answers to the worksheet questions
* @param array $worksheet The name of the worksheet being marked
*/
$this->setLayoutTemplate('worksheetadmin_layout_tpl.php');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');

// set up html elements
$objTable = $this->newObject('htmltable','htmlelements');
//$objText = $this->newObject('textarea','htmlelements');
//$objInput = $this->newObject('textinput','htmlelements');
$objLink = $this->newObject('link','htmlelements');
$objForm = $this->newObject('form','htmlelements');
$objButton = $this->newObject('button','htmlelements');
$objImage = $this->newObject('image','htmlelements');
$objDrop = $this->newObject('dropdown','htmlelements');

// set up language items
$markLabel = $objLanguage->languageText('mod_worksheetadmin_mark','worksheetadmin');
$worksheetLabel = $objLanguage->languageText('mod_worksheetadmin_worksheet','worksheetadmin');
$studentLabel = ucwords($objLanguage->languageText('mod_context_readonly','worksheetadmin'));
$nameLabel = $objLanguage->languageText('mod_worksheetadmin_wordname','worksheetadmin');
$numberLabel = $objLanguage->languageText('mod_worksheetadmin_number','worksheetadmin');
$questionLabel = $objLanguage->languageText('mod_worksheetadmin_question','worksheetadmin');
$modelansLabel = $objLanguage->languageText('mod_worksheetadmin_modelanswer','worksheetadmin');
$answerLabel = $objLanguage->languageText('mod_worksheetadmin_answer','worksheetadmin');
$commentLabel = $objLanguage->languageText('mod_worksheetadmin_comment','worksheetadmin');
$outofLabel = $objLanguage->languageText('mod_worksheetadmin_outof','worksheetadmin');

$firstLabel = $objLanguage->languageText('mod_worksheetadmin_first','worksheetadmin');
$prevLabel = $objLanguage->languageText('mod_worksheetadmin_prev','worksheetadmin');
$nextLabel = $objLanguage->languageText('mod_worksheetadmin_next','worksheetadmin');
$lastmarkedLabel = $objLanguage->languageText('mod_worksheetadmin_last','worksheetadmin').' '.$questionLabel
.' '.$objLanguage->languageText('mod_worksheetadmin_marked','worksheetadmin');
$saveLabel = $objLanguage->languageText('word_save');
$submitLabel = $objLanguage->languageText('word_submit');
$exitLabel = $objLanguage->languageText('word_exit');

$errMark = $objLanguage->languageText('mod_worksheetadmin_numericmark','worksheetadmin');
$errMarkReq = $objLanguage->languageText('mod_worksheetadmin_markrequired','worksheetadmin');

$heading = $markLabel .' '.$worksheetLabel.' '.$worksheet[0]['name']
.'&nbsp;&nbsp;&nbsp;'.$questionLabel.' '.$data['question_order'].' / '.$data['count'];
$this->setVarByRef('heading',$heading);

$studentNum=$data['student_id'];
$studentName=$this->objUser->fullname($studentNum);

$objTable->width='99%';
$objTable->cellpadding='2';
$objTable->row_attributes=' height=25';
$objTable->startRow();
$objTable->addCell('<b>'.$studentLabel.' '.$numberLabel.': </b>'.$studentNum,'49%','','left','odd');
$objTable->addCell('','1%','','','odd');
$objTable->addCell('<b>'.$nameLabel.': </b>'.$studentName,'50%','','left','odd');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>'.$questionLabel.' '.$data['question_order'].': </b>'.$data['question'],'','','','even','colspan=3');
$objTable->endRow();

// Add image if set
if(!empty($data['imageName'])){
    $objImage = new image();
    $objImage->src = $this->uri(array('action'=>'viewimage', 'fileid'=>$data['imageId']), 'worksheet');

    $objTable->startRow();
    $objTable->addCell($objImage->show(),'','','','even','colspan=3');
    $objTable->endRow();
}

$objTable->startRow();
$objTable->addCell('<b>'.$modelansLabel.': </b>'.$data['model_answer'],'','','','odd','colspan=3');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>'.$studentLabel.' '.$answerLabel.': </b>'.$data['answer'],'','','','even','colspan=3');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('','','','center','','colspan=3');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('<b>'.$commentLabel.': </b>','','','center','','colspan=3');
$objTable->endRow();

// Text area for lecturers comments
$objText = new textarea('comment',$data['comments']);

$objTable->startRow();
$objTable->addCell($objText->show(),'','','center','','colspan=3');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('','','','center','','colspan=3');
$objTable->endRow();

// Text input for mark
$objDrop = new dropdown('mark');
for($i=0; $i<=$data['question_worth']; $i++){
    $objDrop->addOption($i, $i);
}
$objDrop->setSelected($data['mark']);

$objForm->addRule('mark', $errMark, 'numeric');
$objForm->addRule('mark', $errMarkReq, 'required');

$objTable->startRow();
$objTable->addCell('<b>'.$markLabel.': '.$objDrop->show().' '.$outofLabel.' </b>'
.$data['question_worth'],'','','center','odd','colspan=3');
$objTable->endRow();

// Hidden fields for answer id and the action to be performed
$objInput = new textinput('answer_id',$data['answer_id']);
$objInput->fldType='hidden';
$hidden = $objInput->show();

$objInput->textinput('nextaction','null');
$objInput->fldType='hidden';
$hidden .= $objInput->show();

$objTable->startRow();
$objTable->addCell($hidden);
$objTable->endRow();

// Navigation & submission using javascript
$javascript="<script language=\"javascript\" type=\"text/javascript\">
    function submitform(val){
        document.markWS.nextaction.value=val;
        document.markWS.submit();
    }
</script>";

echo $javascript;

if($data['question_order']<=1){
    $links=$firstLabel.' '.$questionLabel.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$prevLabel;
}else{
    $objLink->link("javascript:submitform('first');");
    $objLink->link=$firstLabel.' '.$questionLabel;
    $links = $objLink->show();

    $objLink->link("javascript:submitform('prev');");
    $objLink->link=$prevLabel;
    $links .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$objLink->show();
}

if($data['question_order']>=$data['count']){
    $links2=$nextLabel.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$lastmarkedLabel;
    $objButton->button('submitmark',$submitLabel);
    $objButton->setToSubmit();
    $submitBtn = '&nbsp;&nbsp;&nbsp;'.$objButton->show();
}else{
    $objLink->link("javascript:submitform('next');");
    $objLink->link=$nextLabel;
    $links2 = $objLink->show();

    $objLink->link("javascript:submitform('last');");
    $objLink->link=$lastmarkedLabel;
    $links2 .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$objLink->show();

    $submitBtn = '';
}

$objTable->startRow();
$objTable->addCell($links,'','','right');
$objTable->addCell('|','','','center');
$objTable->addCell($links2,'','','left');
$objTable->endRow();

// save, submit & exit buttons
$objButton->button('save',$saveLabel);
$objButton->setToSubmit();
$btns = $objButton->show().$submitBtn;
$objButton->button('exit',$exitLabel);
$objButton->setToSubmit();
$btns .= '&nbsp;&nbsp;&nbsp;'.$objButton->show();

$objTable->startRow();
$objTable->addCell($btns,'','','center','','colspan=3');
$objTable->endRow();

$objForm->form('markWS',$this->uri(array('action'=>'savemark','worksheet'=>$worksheet[0]['id'],
'student'=>$studentNum,'order'=>$data['question_order'])));
$objForm->addToForm($objTable->show());

echo $objForm->show();
?>