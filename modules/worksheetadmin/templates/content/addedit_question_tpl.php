<?php
/**
* Template for adding or editing a question.
* @package worksheetadmin
*/

/**
* @param $worksheet The id of the worksheet
* @param $question The info of the question to edit (if in edit mode)
*/
$this->setLayoutTemplate('worksheetadmin_layout_tpl.php');

// HTML Element Classes to be Used
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$objIcon = $this->newObject('geticon','htmlelements');
$objRadio = $this->newObject('radio','htmlelements');
$objField = $this->newObject('fieldset', 'htmlelements');

// set up language items
$question=$objLanguage->languageText('mod_worksheetadmin_question','worksheetadmin');
$worth=$objLanguage->languageText('mod_worksheetadmin_howmuch','worksheetadmin');
$save=$objLanguage->languageText('word_save');//.' '.$question;
$exit=$objLanguage->languageText('word_cancel');
$heading=$objLanguage->languageText('mod_worksheetadmin_adda','worksheetadmin').' '.$question
.' '.$objLanguage->languageText('word_for').' '.$objLanguage->languageText('mod_worksheetadmin_worksheet','worksheetadmin')
.': '.$worksheet['name'];
$saveadd = ucwords($save.' '.$objLanguage->languageText('mod_worksheetadmin_andaddanother','worksheetadmin'));
$totalLabel=$objLanguage->languageText('mod_worksheetadmin_totalmarks','worksheetadmin');
$answer=$objLanguage->languageText('mod_worksheetadmin_modelanswer','worksheetadmin');

$imageLabel = $objLanguage->languageText('mod_worksheetadmin_image','worksheetadmin');
$addImageLabel = $objLanguage->languageText('mod_worksheetadmin_includeanimage','worksheetadmin');
$includeImageLabel = $objLanguage->languageText('mod_worksheetadmin_includeimage','worksheetadmin');
$deleteImageLabel = $objLanguage->languageText('mod_worksheetadmin_deleteimage','worksheetadmin');
$lbYes = $objLanguage->languageText('word_yes');
$lbNo = $objLanguage->languageText('word_no');

$lnPlain = $this->objLanguage->languageText('mod_testadmin_plaintexteditor');
$lnWysiwyg = $this->objLanguage->languageText('mod_testadmin_wysiwygeditor');

$errMark = $objLanguage->languageText('mod_worksheetadmin_numericmark','worksheetadmin');

// submission via the upload/remove image buttons
$javascript="<script language=\"javascript\" type=\"text/javascript\">
    function submitForm(val){
    document.worksheet.action.value=val;
    document.worksheet.submit();
    }
    </script>";
echo $javascript;

// set up heading and data
if($mode == 'edit'){
    $qNum = $questions['question_order'];
    $heading = $objLanguage->languageText('word_edit').' '.$question.' '.$qNum;
    $dataQuestion = $questions['question'];
    $dataAnswer = $questions['model_answer'];
    $dataWorth = $questions['question_worth'];
    $qNum .= ' / '.$numQuestions;
    $imageName = $questions['imagename'];
    $imageId = $questions['imageid'];
    $action = 'updatequestion';

}else{
    $qNum = $worksheet['num_questions']+1;
    $qNum2 = $worksheet['num_questions']+2;
    $dataQuestion = '';
    $dataAnswer = '';
    $dataWorth = 0;
    $imageName = '';
    $imageId = '';
    $action = 'savequestion';
}
$this->setVarByRef('heading',$heading);

// total marks for entire worksheet

echo '<b>'.$totalLabel.': '.$worksheet['total_mark'].'</b>';


$formAction=$this->uri(array('action'=>$action));
$objForm = new form('worksheet');
//Set the action for the form to the uri with paramArray
$objForm->setAction($formAction);
//Set the displayType to 3 for freeform
$objForm->displayType=3;
$objForm->extra=" ENCTYPE='multipart/form-data'";

$table = $this->newObject('htmltable', 'htmlelements');
$table->cellpadding='5';
$table->cellspacing='2';
$table->width='99%';

/* ******** Question 1 ************* */
// Question
$table->startRow();
$label = new label($question.':', 'input_question');
$table->addCell($label->show(), NULL, "top", NULL, NULL, ' colspan="2"');

$table->endRow();

$table->startRow();


$type = $this->getParam('editor', 'ww');
if($type == 'plaintext'){
    // Hidden element for the editor type
    $objInput = new textinput('editor', 'ww', 'hidden');

    $objElement = new textarea ('question',$dataQuestion, 4, 80);

    $objLink = new link("javascript:submitForm('changeeditor')");
    $objLink->link = $lnWysiwyg;

    $table->addCell($objElement->show().'<br>'.$objLink->show().$objInput->show(), NULL, "top", NULL, NULL, ' colspan="2"');

}else{
    // Hidden element for the editor type
    $objInput = new textinput('editor', 'plaintext', 'hidden');

    $objEditor = $this->newObject('htmlarea', 'htmlelements');
    $objEditor->init('question', $dataQuestion, '300px', '500px', TRUE);
    $objEditor->setDefaultToolBarSetWithoutSave();

    $objLink = new link("javascript:submitForm('changeeditor')");
    $objLink->link = $lnPlain;

    $table->addCell($objEditor->show().''.$objLink->show().$objInput->show(), NULL, "top", NULL, NULL, ' colspan="2"');
}

$fixscript = "
               <script type=\"text/javascript\">function wakeUpFireFoxFckeditor()
               {
               var oEditor = FCKeditorAPI.GetInstance('question') ;
               try
               {
               oEditor.MakeEditable();
               }
               catch (e) {}
               oEditor.Focus();
               }
               </script>";
       $this->appendArrayVar('headerParams', $fixscript);
       $this->appendArrayVar('bodyOnLoad', 'wakeUpFireFoxFckeditor();');
 

$table->endRow();

$label = new label($answer.':', 'input_answer');

$table->startRow();
$table->addCell($label->show(), NULL, "top", NULL, NULL, ' colspan="2"');
$table->endRow();

$table->startRow();
// model answer to question
$objElement = new textarea ('answer',$dataAnswer, 4, 80);
$table->addCell($objElement->show(), NULL, "top", NULL, NULL, ' colspan="2"');
$table->endRow();

// Allocated marks
$table->startRow();
$label = new label($worth.':', 'input_worth');
$table->addCell($label->show(),'24%');

$objElement = new textinput ('worth',$dataWorth);
$objElement->size = 3;
$objForm->addRule('worth', $errMark, 'numeric');
$table->addCell($objElement->show());
$table->endRow();

// Images
/****************  Images ***************
$table->startRow();
$label = new label($addImageLabel.':', 'input_imagefile');
$table->addCell($label->show(), NULL, "top", NULL, NULL, ' colspan="2"');
$table->endRow();

$objRadio = new radio('imageconfirm');
$objRadio->setBreakSpace('&nbsp;&nbsp;/&nbsp;&nbsp;');
$objRadio->addOption('yes', $lbYes);
$objRadio->addOption('no', $lbNo);
$objRadio->setSelected('no');

if($mode == 'edit'){
    if(!empty($imageName)){
        $objRadio->setSelected('yes');
    }
}
$table->startRow();
$table->addCell($objRadio->show(), NULL, "top", NULL, NULL, ' colspan="2"');
$table->endRow();

$objInput = new textinput('imagefile');
$objInput->fldType = 'file';
$objInput->size = 70;

$topStr = $objInput->show();

if($mode == 'edit'){
    if(!empty($imageName)){
        $objButton = new button('removeimage', $deleteImageLabel);
        $objButton->setOnClick("javascript:submitForm('removeimage');");
        $imageBtn = $objButton->show();
    }else{
        $objButton = new button('save', $includeImageLabel);
        $objButton->setOnClick("javascript:submitForm('addimage');");
        $imageBtn = $objButton->show();
    }

    $topStr .= '<p>'.$imageBtn.'</p>';
}

if(!empty($imageName)){
    $topStr .= '<p><b>'.$imageLabel.':</b> '.$imageName.'</p>';

    $objImage = new image();
    $objImage->src = $this->uri(array('action'=>'viewimage', 'fileid'=>$imageId), 'worksheet');

    $topStr .= $objImage->show();

    $objInput = new textinput('fileId', $imageId);
    $objInput->fldType = 'hidden';
    $objInput->size = 5;
    $topStr .= $objInput->show();
}

$table->startRow();
$table->addCell($topStr, NULL, "top", NULL, NULL, ' colspan="5"');
$table->endRow();
********************************************************************/
$objField->contents = $table->show();
$objField->legend = $question.' '.$qNum;
$fields = '<p>'.$objField->show().'</p>';


/* **************** Question 2 -> mode != edit ************** *
if($mode != 'edit'){
    $table->init();
    $table->cellpadding='5';
    $table->cellspacing='2';
    $table->width='99%';

    // Question
    $table->startRow();
    $label = new label($question.':', 'input_question2');
    $table->addCell($label->show(), NULL, "top", NULL, NULL, ' colspan="2"');
    $table->addCell('', '10%');

    $label = new label($answer.':', 'input_answer2');
    $table->addCell($label->show(), NULL, "top", NULL, NULL, ' colspan="2"');

    $table->endRow();

    $table->startRow();
    $objElement = new textarea ('question2', '', 4, 40);
    $table->addCell($objElement->show(), NULL, "top", NULL, NULL, ' colspan="2"');
    $table->addCell('', '10%');

    // model answer to question
    $objElement = new textarea ('answer2', '', 4, 40);
    $table->addCell($objElement->show(), NULL, "top", NULL, NULL, ' colspan="2"');
    $table->endRow();

    // Allocated marks
    $table->startRow();
    $label = new label($worth.':', 'input_worth2');
    $objForm->addRule('worth2', $errMark, 'numeric');
    $table->addCell($label->show(),'24%');

    $objElement = new textinput('worth2', 0);
    $objElement->size = 3;
    $objForm->addRule('worth', $errMark, 'numeric');
    $table->addCell($objElement->show());
    $table->endRow();

    // Images
    $table->startRow();
    $label = new label($addImageLabel.':', 'input_imagefile2');
    $table->addCell($label->show(), NULL, "top", NULL, NULL, ' colspan="2"');
    $table->endRow();

    $objRadio = new radio('imageconfirm2');
    $objRadio->setBreakSpace('&nbsp;&nbsp;/&nbsp;&nbsp;');
    $objRadio->addOption('yes', $lbYes);
    $objRadio->addOption('no', $lbNo);
    $objRadio->setSelected('no');

    $table->startRow();
    $table->addCell($objRadio->show(), NULL, "top", NULL, NULL, ' colspan="2"');
    $table->endRow();

    $objInput = new textinput('imagefile2');
    $objInput->fldType = 'file';
    $objInput->size = 70;

    $topStr = $objInput->show();

    $table->startRow();
    $table->addCell($topStr, NULL, "top", NULL, NULL, ' colspan="5"');
    $table->endRow();

    $objField->contents = $table->show();
    $objField->legend = $question.' '.$qNum2;
    $fields .= '<p>'.$objField->show().'</p>';
}
*/
/* ******** submit buttons ************ */
$submitButton = new button('save', $save);
$submitButton->setToSubmit();
$submitButton->setIconClass("save");
$btns = $submitButton->show();

if($mode != 'edit'){
    $submitButton = new button('saveadd', $saveadd);
    $submitButton->setToSubmit();
    $submitButton->setIconClass("add");
    $btns .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$submitButton->show();
}

$submitButton = new button('cancel', $exit);
$submitButton->setToSubmit();
$submitButton->setIconClass("cancel");
$btns .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$submitButton->show();

$table->init();
$table->cellpadding='5';
$table->cellspacing='2';
$table->width='99%';

$table->startRow();
$table->addCell('','24%');
$table->addCell($btns);
$table->endRow();

$fields .= '<p>'.$table->show().'</p>';

/* *********** hidden elements ************** */
if($mode=='edit'){
    $objElement = new textinput ('id');
    $objElement->value = $questions['id'];
    $objElement->fldType = 'hidden';
    $objForm->addToForm($objElement->show());
    $objElement = new textinput ('old_worth');
    $objElement->value = $dataWorth;
    $objElement->fldType = 'hidden';
    $objForm->addToForm($objElement->show());
}
$objElement = new textinput ('worksheet_id');
$objElement->value = $worksheet['id'];
$objElement->fldType = 'hidden';
$hidden = $objElement->show();

$objElement = new textinput ('action', $action);
$objElement->fldType = 'hidden';
$hidden .= $objElement->show();

$objForm->addToForm($hidden);
$objForm->addToForm($fields);

echo $objForm->show();

?>