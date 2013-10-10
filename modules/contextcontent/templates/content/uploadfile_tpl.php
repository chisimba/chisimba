<?php
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('fieldsetex', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objWashout = $this->getObject('washout', 'utilities');


$objIcon->setIcon('edit');
$editIcon = $objIcon->show();

$objIcon->setIcon('delete');
$deleteIcon = $objIcon->show();

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_contextcontent_createpagefromfile', 'contextcontent','Create page from file');
$middleContent="";
$middleContent.= $header->show();

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_filemanager_uploadnewfile', 'filemanager', $this->objLanguage->languageText('mod_filemanager_uploadnewfile', 'filemanager', 'Upload new file'));
$header->type = 4;



$form = new form('uploadfileform', $this->uri(array('action'=>'uploadfile','chapterid'=>$chapterid)));
$form->extra = 'enctype="multipart/form-data"';

$objUpload = $this->newObject('uploadinput', 'filemanager');
$objSelectFile->restrictFileList = array('htm','html','odt','doc','dcox','pdf','png','gif','jpg');

$button = new button('submitform', $this->objLanguage->languageText('mod_contextcontent_createpage', 'contextcontent','Create page'));
$button->setToSubmit();

$label = new label ($this->objLanguage->languageText('mod_contextcontent_pagetitle','contextcontent'), 'input_menutitle');

$menuTitle = new textinput('menutitle');
$menuTitle->size = '80%';

$fieldsetupload=$this->getObject('fieldset','htmlelements');
$fieldsetupload->setLegend($header->show());
$fieldsetupload->addContent($label->show().'<br/>'.$menuTitle->show().'<br /><br />'.$objUpload->show().'<br />'.$button->show());


$form->addToForm($fieldsetupload->show());
$form->addRule('menutitle', $this->objLanguage->languageText('mod_contextcontent_pleaseenterpagetitle','contextcontent'), 'required');

$middleContent.=$form->show();

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_filemanager_chooseexisting', 'filemanager', 'Choose existing file from file manager');
$header->type = 4;

$form2 = new form('createpagefromfileform', $this->uri(array('action'=>'createpagefromfile','chapterid'=>$chapterid)));


$objSelectFile2 = $this->newObject('selectfile', 'filemanager');
$objSelectFile2->name = 'pagefile';
$objSelectFile2->restrictFileList = array('htm','html','odt','doc','dcox','pdf','png','gif','jpg');

$button = new button('submitform', $this->objLanguage->languageText('mod_contextcontent_createpage', 'contextcontent','Create page'));
$button->setToSubmit();

$fieldsetchoosefile=new fieldset();
$fieldsetchoosefile->setLegend($header->show());
$title= '<br/><b class="selectfiletitle">'.$this->objLanguage->languageText('mod_filemanager_selectfile', 'filemanager','Select file').'</b><br/>';
$fieldsetchoosefile->addContent($label->show().'<br/>'.$menuTitle->show().$title.''.$objSelectFile2->show().'<br />'.$button->show());

$form2->addToForm($fieldsetchoosefile->show());
$middleContent.='<br /><br />'.$form2->show();

$cssLayout = $this->getObject('csslayout', 'htmlelements');
$toolbar = $this->getObject('contextsidebar', 'context');

$cssLayout->setLeftColumnContent($toolbar->show());
$cssLayout->setMiddleColumnContent($middleContent);

echo $cssLayout->show();
?>