<?php
//Get the CSS layout to make two column layout
$cssLayout = $this->newObject('csslayout', 'htmlelements');
//Add some text to the left column
$cssLayout->setLeftColumnContent("Please fill in all fields:");
//get the editform object and instantiate it
$objEditForm = $this->getObject('editmessage', 'hosportal');
//Add the form to the middle (right in two column layout) area
$cssLayout->setMiddleColumnContent($objEditForm->show());
//$fileUploader = $this->getObject('fileuploader', 'files');
//
//          //$fileUploader->allowedCategories = array('documents', 'images');
//          // OR
//          $fileUploader->allowedExtensions = array('pdf', 'gif', 'png');
//
//          $fileUploader->savePath = '/uploader/'; // This will then be saved in usrfiles/uploader
//          $fileUploader->overwriteExistingFile = TRUE;
//
//          $results = $fileUploader->uploadFile('fileupload1');
        // $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
  //echo $objHighlightLabels->show();
echo $cssLayout->show();






//$this->setLayoutTemplate('assignment_layout_tpl.php');
//
//// set up html elements
//$this->loadClass('language', 'language');
//$this->objLanguage = $this->getObject('language', 'language');
//$this->loadClass('htmlheading', 'htmlelements');
//$this->loadclass('htmltable','htmlelements');
//$this->loadclass('textinput','htmlelements');
//$objConfirm = $this->newObject('timeoutmessage','htmlelements');
//$objSelectFile = $this->newObject('selectfile','filemanager');
//// set up language items
//
//$essayhead=$this->objLanguage->languageText('mod_essay_essay', 'assignment');
//$btnupload=$this->objLanguage->languageText('mod_assignment_upload' ,'assignment');
//$uploadhead=$btnupload.' '.$essayhead;
//$head=$uploadhead;
//$btnexit=$this->objLanguage->languageText('word_exit');
//$wordstudent=ucwords($this->objLanguage->languageText('mod_context_readonly'));
//
///************************* set up table ******************************/
//
//
//$header = new htmlHeading();
//$header->str = $this->objLanguage->languageText('mod_filemanager_uploadnewfile', 'filemanager', 'Upload new file');
//$header->type = 4;
//
//echo $header->show();
//
//$form = new form('addpodcastbyupload', $this->uri(array('action'=>'directuploadsubmit','id'=>$id)));
//$form->extra = 'enctype="multipart/form-data"';
//
//$objUpload = $this->newObject('uploadinput', 'filemanager');
//
//$button = new button('submitform', $btnupload);
//$button->setToSubmit();
//
//$form->addToForm($objUpload->show().'<br />'.$button->show());
//
//
//echo $form->show();
//
//// header
////$this->setVarByRef('heading',$head);
//
//// get booked essays in topic
////$data=$this->dbbook->getBooking("where id='$book'");
//
//// get essay title
////$essay=$this->dbessays->getEssay($data[0]['essayid'],'topic');
////$essaytitle=$essay[0]['topic'];
//
//// display essay title
//$objTable = new htmltable();
///*$objTable->startRow();
//$objTable->addCell('','','','','even');
//$objTable->addCell('<b>'.$essaytitle.'</b>','','','center','even',' colspan="2"');
//$objTable->addCell('','','','','even');
//$objTable->endRow();
//*/
//$objTable->row_attributes=' height="2"';
//$objTable->startRow();
//$objTable->addCell('','','','','',' colspan="4"');
//$objTable->endRow();
//
//$this->objButton = new button('submit', $btnexit);
//$this->objButton->setToSubmit();
//$btn4=$this->objButton->show();
//
//// display confirmation message
///*if(!empty($msg)){
//    $objConfirm->setMessage($msg);
//    $confirmMsg = $objConfirm->show();
//}else{
//    $confirmMsg = '';
//}*/
//$objTable->row_attributes=' height="40"';
//$objTable->startRow();
//$objTable->addCell('','20%');
////$objTable->addCell($confirmMsg,'60%','','center','',' colspan="2"');
//$objTable->addCell('','20%');
//$objTable->endRow();
//
//$objTable->row_attributes=' height="10"';
//$objTable->startRow();
//$objTable->addCell('');
//$objTable->endRow();
//
//
//
//$objSelectFile->name ='file';
//
//
//
//// submit and exit buttons
//$this->objButton = new button('submit',$btnupload);
//$this->objButton->setToSubmit();
//$btn1=$this->objButton->show();
//
//$objTable->row_attributes=' height="10"';
//$objTable->startRow();
//$objTable->addCell('');
//$objTable->endRow();
//
//$objTable->startRow();
//$objTable->addCell('');
//$objTable->addCell($btn1,'','','right');
//$objTable->addCell('&nbsp;&nbsp;&nbsp;&nbsp;'.$btn4,'','','left');
//$objTable->endRow();
//
//$objTable->row_attributes=' height="10"';
//$objTable->startRow();
//$objTable->addCell('');
//$objTable->endRow();
//
//
//
///************************* set up form ******************************/
//
//$this->objForm = new form('upload',$this->uri(array('action'=>'uploadsubmit','id'=>$id)));
//$this->objForm->extra=" enctype='multipart/form-data'";
//$this->objForm->addToForm($objSelectFile->show());
//$this->objForm->addToForm($objTable->show());
//
///************************* display page ******************************/
//
//$header = new htmlHeading();
//$header->str = $this->objLanguage->languageText('mod_filemanager_chooseexisting', 'filemanager', 'Choose existing file from file manager');
//$header->type = 4;
//
//echo $header->show();
//
//echo $this->objForm->show();

?>