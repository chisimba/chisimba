<?php
	$this->loadClass('form','htmlelements');
	$this->loadclass('textinput','htmlelements');	
	$this->loadclass('textarea','htmlelements');	
    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    $pageTitle->str=$this->objLanguage->languageText("mod_fileshare_heading_upload",'fileshare');
    echo $pageTitle->show();	
	$objForm = new form('fileupload',$this->uri(array('action'=>'uploadconfirm')));
	$objForm->extra = " enctype='multipart/form-data'";
	$objForm->displayType = 4;
	$objForm->addToFormEx($objLanguage->languageText('mod_fileshare_file','fileshare'),new textinput('upload', '', 'file', 100));
	$objForm->addToFormEx($objLanguage->languageText('mod_fileshare_title','fileshare'),new textinput('title','', null, 100));
	$objForm->addToFormEx($objLanguage->languageText('mod_fileshare_description','fileshare'),new textarea('description',''));
	$objForm->addToFormEx($objLanguage->languageText('mod_fileshare_version','fileshare'),new textinput('version','', null, 100));
	$objForm->addToFormEx("", "<input type='submit' class='button' value='".$this->objLanguage->languageText('word_submit')."' />");
	echo $objForm->show();
?>