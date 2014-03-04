<?php
/*
* Template for uploading essays.
* @package essay
*/

// set up html elements
//$this->loadclass('htmltable','htmlelements');
//$objLayer=$this->objLayer;

// set up language items
//$essayhead=$this->objLanguage->languageText('mod_essay_essay', 'essay');
//$btnupload=$this->objLanguage->languageText('mod_essay_upload' ,'essay');
//$uploadhead=$btnupload.' '.$essayhead;
//$head=$uploadhead;
//$btnexit=;
//$wordstudent=ucwords($this->objLanguage->languageText('mod_context_readonly'));

// Get booked essays in topic area
$data = $this->dbbook->getBooking("WHERE id='{$bookId}'");
// Get essay data
$essay = $this->dbessays->getEssay($data[0]['essayid'], 'topic');
// Get essay title
$essayTitle = $essay[0]['topic'];

$this->setVar('heading', $this->objLanguage->languageText('mod_essay_uploadessay','essay'));

$str = '';

$str .= '<b>'.$this->objLanguage->languageText('mod_essay_essay','essay').':</b> '.$essayTitle.'<br />';

/*
// display confirmation message
if (empty($message)) {
    $confirmMessage = '';
} else {
    $objMessage = $this->newObject('timeoutmessage','htmlelements');
    $objMessage->setMessage($message);
    $confirmMessage = $objMessage->show();
}
$str .= $confirmMessage;
*/

//new file upload functionality
//$this->loadclass('selectfile','filemanager');
//$objSelectFile = $this->newObject('selectfile', 'filemanager');
//$objSelectFile->name = 'uploadessay';
//$objSelectFile->context = false;
//$objSelectFile->workgroup = false;

// File input field for file manager
// Setup selectfile object
$objSelectFile = $this->newObject('selectfile','filemanager');
$objSelectFile->name = 'file';
$str .= $objSelectFile->show().'<br />';

$objUploadButton = new button('submit', $this->objLanguage->languageText('word_save')); //$this->objLanguage->languageText('mod_essay_upload' ,'essay')
$objUploadButton->setToSubmit();
$buttonUpload = $objUploadButton->show();

/*
$objSubmitButton = new button('submit', $this->objLanguage->languageText('word_exit'));
$objSubmitButton->setToSubmit();
$buttonSubmit = $objSubmitButton->show();
*/

$objCancelButton = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$returnUrl = $this->uri(array('action' => 'viewallessays'));
$objCancelButton->setOnClick("javascript: window.location='{$returnUrl}';");
$buttonCancel = $objCancelButton->show();

$str .= '<br />'.$buttonUpload.'&nbsp;'.$buttonCancel.'<br />'; //$buttonSubmit

$objForm = new form('upload', $this->uri(array('action'=>'uploadsubmit','bookid'=>$bookId)));
$objForm->extra = " enctype='multipart/form-data'";
$objForm->addToForm($str);
echo $objForm->show();
?>