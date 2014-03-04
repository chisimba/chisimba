<?php

class researchstud extends dbTable
{
   	public $objLanguage;
	var $captcha;



 public function init()

{
 $this->objLanguage = $this->getObject('language', 'language');
 	parent::init('tbl_mayibuye_researchformstud');
}

private function loadElements()

{
// load form class
$this->loadClass('form','htmlelements');

// load textbox or input class
$this->loadClass('textinput','htmlelements');

// load label text class
$this->loadClass('label', 'htmlelements');

// load button class
$this->loadClass('button', 'htmlelements');

}
private function buildForm()
	{

   	$this->loadElements();
   	$objForm = new form('researchft', $this->getFormAction());
	$table = $this->newObject('htmltable', 'htmlelements');
	$required = '<span class="required_field"> * '.$this->objLanguage->languageText('word_required', 'system', 'Required').'</span>';
 
	$headingLabel = new label($this->objLanguage->languageText("mod_mayibuye_heading","mayibuyeform"),"heading");
	$objForm->addToForm('<p align="center">'.$headingLabel->show().$required.'</p>');  
	
	$table->startRow();
       	$objvatLabel = new label($this->objLanguage->LanguageText("mod_mayibuye_commentsubheading","mayibuyeform"),"heading");
	$table->addCell($objvatLabel->show(),'', 'center', 'left', '');
	$table->endRow(); 

	$table->startRow();
	$objname = new textinput('name');
	$objnameLabel = new label($this->objLanguage->LanguageText("mod_mayibuye_commentname2","mayibuyeform"),"name");
	$table->addCell($objnameLabel->show(),'', 'center', 'left', '');
	$table->addCell($objname->show());
	$objForm->addRule('name','Please enter your name','required');
	$table->endRow();
	
	$table->startRow();
	$objjob = new textinput('jobtitle');
	$objjobLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentjobtitle","mayibuyeform"),"jobtitle");
	$table->addCell($objjobLabel->show(),'', 'center', 'left', '');         
	$table->addCell($objjob->show());
	$objForm->addRule('jobtitle','Please enter your Job title','required');
	$table->endRow();

	$table->startRow();	
	$objorg = new textinput('orgranization2');
	$objorgLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentorganizationname","mayibuyeform"),"organazation");
	$table->addCell($objorgLabel->show(),'', 'center', 'left', '');         
	$table->addCell($objorg->show());
	$objForm->addRule('orgranization2','Please enter your Organization','required');
	$table->endRow();
	
	$table->startRow();
       	$objpostaladdress = new textinput('postaladdress');
	$objpostaladLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentpostaladrres","mayibuyeform"),"postaladdress");
	$table->addCell($objpostaladLabel->show(),'', 'center', 'left', '');         
	$table->addCell($objpostaladdress->show(),'', 'center', 'left', '');
	$objForm->addRule('postaladdress','Please enter your Postal Address','required');
	$table->endRow();

	$table->startRow();
	$objTelno3 = new textinput('tellno_3');
	$objTel3Label = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commenttelno","mayibuyeform"),"telno_3");
	$table->addCell($objTel3Label->show(),'', 'center', 'left', '');
	$table->addCell($objTelno3->show());
	$objForm->addRule('tellno_3', 'Telephone Must contain valid numbers', 'numeric');
	$table->startRow();
	
	$table->startRow();
	$objFaxno3 = new textinput('faxno_3');
	$objFax3Label = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentfaxno","mayibuyeform"),"faxno_3");
	$table->addCell($objFax3Label->show(),'', 'center', 'left', '');
	$table->addCell($objFaxno3->show(),'', 'center', 'left', '');
	$table->startRow();
	
	
	$fieldset = $this->newObject('fieldset', 'htmlelements');
	$fieldset->legend = $this->objLanguage->languageText('phrase_accountdetails', 'userregistration', 'Next of kin Details');
	$fieldset->contents = $table->show();

	$objForm->addToForm($fieldset->show());

	$button = new button ('submitform', 'Continue');
	$button->setToSubmit();
        //$objForm->addToForm($table->show()); 	
	$objForm->addToForm('<p align="center"><br />'.$button->show().'</p>');

	 return $objForm->show();
}

function insertResearchStudRecord($nameofresi,$jotitle,$organizationname,$postadd,$tel,$faxx)
	 {
           $id = $this->insert(array(
                'nameofresgn'=>$nameofresi,
		'jobtitle2'=>$jotitle,
		'organizationname'=>$organizationname,
                'postalddress2'=>$postadd,
		'tell'=>$tel,
		'fax'=>$faxx
		
        ));
        return $id;
}


private function getFormAction()
{
$formAction = $this->uri(array("action" => "send_researchstud"), "mayibuyeform");
        return $formAction;
}

public function Show()
{
return $this->buildForm();

}
}
?>
