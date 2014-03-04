<?php
/*
* Template for uploading essays.
* @package essayadmin
*/

//echo '<pre>';
//$msg = $this->getSession('MSG','');
//echo "[".$msg."]\n";
//$this->setVarByRef('msg',$msg);
//$this->unsetSession('MSG');
//echo '</pre>';

$styleSheet = "<style type=\"text/css\">
.form_widget_amount_slider{
	border-top:1px solid #9d9c99;
	border-left:1px solid #9d9c99;
	border-bottom:1px solid #eee;
	border-right:1px solid #eee;
	background-color:#f0ede0;
	height:3px;
	position:absolute;
	bottom:0px;
}
</style>";
$this->appendArrayVar('headerParams', $styleSheet);
$javaScript = '<script language="JavaScript"  type="text/javascript" src="'.$this->getResourceUri('dhtmlslider.js').'"></script>';
$this->appendArrayVar('headerParams', $javaScript);

// set up language items
$essayhead=$this->objLanguage->languageText('mod_essayadmin_essay','essayadmin');
//$btnupload=;
//$uploadhead=$btnupload.' '.$essayhead;
//$uploadhead=;
//$head=$uploadhead;
$markshead=' '.$this->objLanguage->languageText('mod_essayadmin_marks','essayadmin');
//$btnsubmit=;
$btnexit=$this->objLanguage->languageText('word_exit');
$wordstudent=ucwords($this->objLanguage->languageText('mod_context_readonly'));
$markhead=$this->objLanguage->languageText('mod_essayadmin_mark','essayadmin').' (%)';
$commenthead=$this->objLanguage->languageText('mod_essayadmin_comment','essayadmin');
$rubrichead=$this->objLanguage->languageText('mod_essayadmin_use','essayadmin').' '.$this->objLanguage->languageText('rubric_rubric');
$errMark = $this->objLanguage->languageText('mod_essayadmin_entermark','essayadmin');

/**
* new language items added 5/apr/06
* @author: otim samuel, sotim@dicts.mak.ac.ug
*/

$downloadEssay=0;
$downloadEssay=$this->objLanguage->languageText('mod_essayadmin_downloadessay','essayadmin');
$dateSubmitted=0;
$dateSubmitted=$this->objLanguage->languageText('mod_essayadmin_submitted','essayadmin');
$dateSubmittedLate=0;
$dateSubmittedLate=$this->objLanguage->languageText('mod_essayadmin_submittedlate','essayadmin');

$this->loadClass('htmltable','htmlelements');
$this->loadclass('textinput','htmlelements');
$this->loadClass('dropdown', 'htmlelements');
//$this->loadclass('timeoutmessage','htmlelements');

/*
// javascript
$javascript = "<script language=\"javascript\" type=\"text/javascript\">
    function submitExitForm(){
        document.exit.submit();
    }

</script>";
echo $javascript;
*/

// get topic id
//$topic=$this->getParam('id');
// Get booked essay
$data=$this->dbbook->getBooking("WHERE id='$book'");
// Get topic area data
$topicdata=$this->dbtopic->getTopic($data[0]['topicid']);
// Get essay data
$essay=$this->dbessays->getEssay($data[0]['essayid'],'topic');
// Get student ID
$studentid=$data[0]['studentid'];
// Get student name
$studentname=$this->objUser->fullname($studentid);
// Get essay title
$essaytitle=$essay[0]['topic'];

// Confirmation message
if ($message != '') {
    $objConfirm = $this->getObject('timeoutmessage','htmlelements');
    $objConfirm->timeout = 0;
    $objConfirm->setMessage($message);
    echo $objConfirm->show();
}

$objTable = new htmltable();

// Student name
$objTable->startRow();
$objTable->addCell('<b>'.$wordstudent.':</b>');
$objTable->addCell($studentname);
$objTable->endRow();

// Essay title
$objTable->startRow();
$objTable->addCell('<b>'.$essayhead.':</b>');
$objTable->addCell($essaytitle);
$objTable->endRow();

// Submitted
$objTable->startRow();
// Is the submitted date later than the closing date?
$isLate = $this->objDateformat->getDateDifference($topicdata[0]['closing_date'], $data[0]['submitdate']);
$objTable->addCell(
    $isLate
        ?'<font color=\'red\'><strong>'.$dateSubmittedLate.':</strong></font>'
        :'<strong>'.$dateSubmitted.':</strong>'
);
$submitDate = $this->objDateformat->formatDate($data[0]['submitdate']);
$objTable->addCell($submitDate);
$objTable->endRow();

if(!empty($rubric)){
    $objTable->startRow();
    //$objTable->addCell('');
    $objTable->addCell('<b>'.$rubrichead.'<b>','','','center','','colspan="2"');
    $objTable->endRow();
    // iframe containing rubric
    $this->objIframe->iframe();
    $this->objIframe->name='rubric';
    $this->objIframe->id='rubric';
    $this->objIframe->width='500';
    $this->objIframe->height='230';
    $this->objIframe->frameborder=0;
    $this->objIframe->scrolling=1;
    $this->objIframe->src=$this->uri(array('action'=>'usetable','tableId'=>$rubric,'NoBanner'=>'yes','studentNo'=>$studentid,'student'=>$studentname),'rubric');
    $objTable->startRow();
    //$objTable->addCell('');
    $objTable->addCell($this->objIframe->show(),'','','center','','colspan="2"');
    $objTable->endRow();
    /*
    $objTable->row_attributes=' height="15"';
    $objTable->startRow();
    $objTable->addCell('');
    $objTable->endRow();
    */
}

echo $objTable->show();

// Download link for the student's submitted essay
$objLink=new link($this->uri(array('action'=>'download','fileid'=>$data[0]['studentfileid'])));
$objLink->link=$downloadEssay;
$objLink->title = $downloadEssay;
echo $objLink->show();

//echo $objTable->show();

$content = '';

// Mark
/*
$objSubTable->startRow();
$objSubTable->addCell(,'','','','','colspan="2"');
$objSubTable->endRow();
*/
$content .= '<b>'.$markhead.'<b>';
//$objSubTable->startRow();
$objMarkTable = new htmltable();
$objMarkTable->width = NULL;
$objMarkTable->startRow();
$objMarkTable->addCell("<span id='slider_target'></span>");
$objTextinput = new textinput('mark',$mark);
$objTextinput->size='5';
$objTextinput->extra='maxlength="3"';
$objMarkTable->addCell($objTextinput->show());
$objMarkTable->endRow();
$content .= '<br />'.$objMarkTable->show();
//$objSubTable->addCell(,'','','','','colspan="2"'); //60% //right //id=""
//$objSubTable->addCell(.'&nbsp;%','','',''); //40% //center
//$objSubTable->endRow();

// Comment
/*
$objSubTable->startRow();
$objSubTable->addCell(,'','','','','colspan="2"');
$objSubTable->endRow();
*/
$content .= '<b>'.$commenthead.'<b>';
//$objSubTable->startRow();
$objTextArea = new textarea('comment',$comment,5,60); //$data[0]['comment']
$objTextArea->extra='wrap=soft';
$content .= '<br />'.$objTextArea->show();
//$objSubTable->addCell(,'','','','','colspan="2"');
//$objSubTable->endRow();

// Upload
/*
$objSubTable->startRow();
$objSubTable->addCell(,'','','','','colspan="2"');
$objSubTable->endRow();
*/
$content .= '<b>'.$this->objLanguage->languageText('mod_essayadmin_uploadmarkedessay','essayadmin').'<b>';
//$objSubTable->startRow();
$objInput = new textinput('file');
$objInput->fldType='file';
$objInput->size='';
$content .= '<br />'.$objInput->show();
//$objSubTable->addCell(,'','','','','colspan="2"');
//$objSubTable->endRow();

//$objSubTable = new htmltable();
//$objSubTable->width="60%";

// Save button
$objButton = new button('save', $this->objLanguage->languageText('word_save'));
$objButton->setToSubmit();
$buttonSave = $objButton->show();

// Cancel button
$objButton = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action'=>'marktopic', 'id'=>$topic));
// submitExitForm()
$objButton->setOnClick("javascript: window.location='{$uri}';");
$buttonCancel = $objButton->show();

/*
// Upload button
$objButton = new button('save', $this->objLanguage->languageText('mod_essayadmin_upload','essayadmin'));
$objButton->setToSubmit();
$buttonUpload = $this->objButton->show();
*/

$hidden = '';

// Topic ID
$objInput = new textinput('id', $topic);
$objInput->fldType='hidden';
$hidden .= $objInput->show();

// Book ID
$objInput = new textinput('book', $book);
$objInput->fldType='hidden';
$hidden .= $objInput->show();

// Form
$this->objForm = new form('upload',$this->uri(array('action'=>'uploadsubmit')));
$this->objForm->extra="enctype='multipart/form-data'";
$this->objForm->addToForm($hidden);
$this->objForm->addToForm($content);
$this->objForm->addToForm('<br /><br />'.$buttonSave.'&nbsp;'.$buttonCancel);
$this->objForm->addRule('mark', $errMark, 'required');
echo $this->objForm->show();

$slider="<script type=\"text/javascript\">
form_widget_amount_slider('slider_target',document.upload.mark,200,0,100,\"\",'".$this->getResourceUri('slider_handle.gif')."');
</script>";
echo $slider;

/*
// exit form
$this->objForm = new form('exit', $this->uri(array('action'=>'uploadsubmit','book'=>$book)));
//hidden input: topic id
$this->objInput = new textinput('id',$topic);
$this->objInput->fldType='hidden';
$this->objForm->addToForm($this->objInput->show());
$this->objInput = new textinput('save',$btnexit);
$this->objInput->fldType='hidden';
$this->objForm->addToForm($this->objInput->show());
echo $this->objForm->show();
*/
?>