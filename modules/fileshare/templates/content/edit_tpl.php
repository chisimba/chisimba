<?php
	$this->loadClass('form','htmlelements');
	$this->loadclass('textinput','htmlelements');
	$this->loadclass('textarea','htmlelements');
    $pageTitle = $this->newObject('htmlheading','htmlelements');
    $pageTitle->type=1;
    $pageTitle->align='left';
    $pageTitle->str=$this->objLanguage->languageText("mod_fileshare_heading_edit",'fileshare');
	echo $pageTitle->show();
	$objForm = new form('edit',$this->uri(array('action'=>'editconfirm','id'=>$id)));
	$objForm->displayType = 4;
	$objForm->addToFormEx($objLanguage->languageText('mod_fileshare_file','fileshare'),$filename);
	$objForm->addToFormEx($objLanguage->languageText('mod_fileshare_title','fileshare'),new textinput('title',$title,null,255));
	$objForm->addToFormEx($objLanguage->languageText('mod_fileshare_description','fileshare'),new textarea('description',$description));
	$objForm->addToFormEx($objLanguage->languageText('mod_fileshare_version','fileshare'),new textinput('version',$version,null,255));
	$objForm->addToFormEx("", "<input type='submit' class='button' value='".$this->objLanguage->languageText('word_submit')."' />");
	echo $objForm->show();
?>