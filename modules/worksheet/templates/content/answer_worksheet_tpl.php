<?php
/**
* Template for answering a worksheet.
* @package worksheet
*/

/**
* @param array $question The worksheet questions.
* @param array $worksheet The worksheet details.
* @param int $count The number of questions in the worksheet.
*/
$this->setLayoutTemplate('answerworksheet_layout_tpl.php');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');

// set up html objects
$objTable =& $this->newObject('htmltable','htmlelements');
$objHead =& $this->newObject('htmlheading','htmlelements');
$objLayer =& $this->newObject('layer','htmlelements');
$objForm =& $this->newObject('form','htmlelements');
$objButton =& $this->newObject('button','htmlelements');
//$objText =& $this->newObject('textarea','htmlelements');
//$objInput =& $this->newObject('textinput','htmlelements');
$objLink =& $this->newObject('link','htmlelements');
$objImage =& $this->newObject('image','htmlelements');
$objLayerBase =& $this->newObject('layer', 'htmlelements');
$objLayerContainer =& $this->newObject('layer', 'htmlelements');
$objLayerContent =& $this->newObject('layer', 'htmlelements');
$objLayerTopic =& $this->newObject('layer', 'htmlelements');

// set up language items
$worksheetLabel = $objLanguage->languageText('mod_worksheet_worksheet','worksheet');
$questionsLabel = $objLanguage->languageText('mod_worksheet_questions','worksheet');
$questionLabel = $objLanguage->languageText('mod_worksheet_question','worksheet');
$marksLabel = $objLanguage->languageText('mod_worksheet_marks','worksheet');
$answerLabel = $objLanguage->languageText('mod_worksheet_answer','worksheet');
$ofLabel = $objLanguage->languageText('mod_worksheet_of','worksheet');
$saveLabel = $objLanguage->languageText('mod_worksheet_saveworksheet','worksheet');
$continueLabel = ' & '.$objLanguage->languageText('mod_worksheet_continue','worksheet');
$submitLabel = $objLanguage->languageText('mod_worksheet_submitformarking','worksheet');
$exitLabel = $objLanguage->languageText('word_exit');
$gottoLabel = $objLanguage->languageText('mod_worksheet_goto','worksheet');
$descriptionLabel = $objLanguage->languageText('mod_worksheet_description','worksheet');
//$help.= '<P>'.$objLanguage->languageText('mod_worksheet_helpnavigation');
$saveDescript = $objLanguage->languageText('mod_worksheet_editagain','worksheet');
$submitDescript = $objLanguage->languageText('mod_worksheet_noeditagain','worksheet');

$lnPlain = $this->objLanguage->languageText('mod_testadmin_plaintexteditor');
$lnWysiwyg = $this->objLanguage->languageText('mod_testadmin_wysiwygeditor');

$noSubmit=TRUE;
$dif=$count-$question[0]['question_order'];
if($dif>3){
    $last = $question[0]['question_order']+3;
    $saveLabel.=$continueLabel;
}else {
    $last = $count;
    $noSubmit=FALSE;
}

$heading = $worksheetLabel.' '.$worksheet['name'].' - '.$questionsLabel.' '
.$question[0]['question_order'].' - '.$last.' '.$ofLabel.' '.$count;

$this->setVarByRef('heading',$heading);

echo '<b>'.$descriptionLabel.'</b><p>'.$this->objWashout->parseText($worksheet['description']).'</p>';

// set up answer form
$objForm = new form('answerWS',$this->uri(''));

$str=''; $i=0; $objHead->type=6;

foreach($question as $line){
    $str = '<b >'.$questionLabel.' '.$line['question_order'].':</b> '.$this->objWashout->parseText($line['question']);

    // Display image if set
    if(!empty($line['imageName'])){
        $objImage = new image();
        $objImage->src = $this->uri(array('action'=>'viewimage', 'fileid'=>$line['imageId']));

        $str .= '<p>'.$objImage->show().'</p>';
    }

    $str .= '<br /><b>'.$marksLabel.':</b> '.$line['question_worth'];

    $objLayerTopic->str = $str;
    $objLayerTopic->cssClass = 'forumTopic';
    $LayerQuestion = $objLayerTopic->show();

    $str1 = '<b>'.$answerLabel.': </b><br />';
//     $objText = new textarea('answer'.$i,$line['answer'],8,90);
//     $str1 .= $objText->show();

    $type = $this->getParam('editor', 'ww');
    if($type == 'plaintext'){
        // Hidden element for the editor type
        $objInput = new textinput('neweditor', 'ww', 'hidden');
        $extra = $objInput->show();
        $objInput = new textinput('editor', 'plaintext', 'hidden');
        $extra .= $objInput->show();

        $objText = new textarea('answer'.$i, $line['answer'], 8, 90);
        $str1 .= $objText->show();

        $objLink = new link("javascript:submitForm2('changeeditor')");
        $objLink->link = $lnWysiwyg;
        $str1 .= '<br />'.$objLink->show().$extra.'<br /><br />';
    }else{
        // Hidden element for the editor type
        $objInput = new textinput('neweditor', 'plaintext', 'hidden');
        $extra = $objInput->show();
        $objInput = new textinput('editor', 'ww', 'hidden');
        $extra .= $objInput->show();

        $objEditor = $this->newObject('htmlarea', 'htmlelements');
        $objEditor->init('answer'.$i, $line['answer'], '300px', '500px');
        $objEditor->setDefaultToolBarSetWithoutSave();

        $str1 .= $objEditor->show();

        $objLink = new link("javascript:submitForm2('changeeditor')");
        $objLink->link = $lnPlain;
        $str1 .= '<br />'.$objLink->show().$extra.'<br /><br />';
    }

    $objLayerContent->str = $str1;
    $objLayerContent->cssClass = 'forumContent';
    $layerAnswer = $objLayerContent->show();

    $objInput = new textinput('question'.$i,$line['id']);
    $objInput->fldType = 'hidden';

    $objLayerContainer->str = $LayerQuestion.$layerAnswer.$objInput->show();
    $objLayerContainer->cssClass = 'topicContainer';

    $objLayerBase->str = $objLayerContainer->show().'<br />';
    $objLayerBase->cssClass = 'forumBase';

    $objForm->addToForm($objLayerBase->show());
    $i++;
}

$objInput = new textinput('worksheet_id', $worksheet['id'], 'hidden');
$hidden = $objInput->show();

$objInput = new textinput('qNum', $worksheet['qNum']+4, 'hidden');
$hidden .= $objInput->show();

$objInput = new textinput('num', $i, 'hidden');
$hidden .= $objInput->show();

$objInput = new textinput('action', 'saveanswer', 'hidden');
$hidden .= $objInput->show();

$objForm->addToForm($hidden);

// submit buttons
$objButton = new button('save',$saveLabel);
$objButton->setToSubmit();
$buttons = $objButton->show();
$btnDescript = '( '.$saveLabel.'&nbsp;=&nbsp;'.$saveDescript.' )';

if(!$noSubmit){
    $objButton = new button('submitAns', $submitLabel);
    $objButton->setToSubmit();
    $buttons.= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();
    $btnDescript .= '<br />( '.$submitLabel.'&nbsp;=&nbsp;'.$submitDescript.' )';
}

$objButton = new button('save', $exitLabel);
$objButton->setToSubmit();
$buttons.='&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();

// navigation & submission
$javascript="<script language=\"javascript\" type=\"text/javascript\">
    function submitform(val){
        document.answerWS.qNum.value=val;
        document.answerWS.submit();
    }

    function submitForm2(val){
        document.answerWS.action.value=val;
        document.answerWS.submit();
    }
    </script>";
echo $javascript;

$nav = '<b>'.$gottoLabel.' '.$questionsLabel.':</b><p>';
$nav .= $this->generateLinks($question[0]['question_order'],$count).'</p>';

$objLayer->align='center';
$objLayer->str=$buttons.'<p>'.$btnDescript.'</p>'.$nav;

$objForm->addToForm($objLayer->show()); 
echo $objForm->show();
?>