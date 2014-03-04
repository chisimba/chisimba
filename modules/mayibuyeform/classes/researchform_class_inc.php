<?php

class researchform extends dbTable
{
   	public $objLanguage;
	var $captcha;



 public function init()

{
 $this->objLanguage = $this->getObject('language', 'language');
 	parent::init('tbl_mayibuyeform_researchform');
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
   	$objForm = new form('researchform', $this->getFormAction());
	$table = $this->newObject('htmltable', 'htmlelements');
	$form = new form ('register', $this->uri(array('action'=>'register')));
	$required = '<span class="required_field"> * '.$this->objLanguage->languageText('word_required', 'system', 'Required').'</span>';
 
 	//Create a new label for the text labels	
	$headingLabel = new label($this->objLanguage->languageText("mod_mayibuye_heading","mayibuyeform"),"heading");
	$objForm->addToForm('<p align="center">'.$headingLabel->show().$required.'</p>');  
	
	$table->startRow();
	$titleLabel = new label($this->objLanguage->languageText("mod_mayibuyeform_commenttitle","mayibuyeform"),"title");
	$table->addCell('<p>'.$titleLabel->show().'</p>'); 
	$table->endRow(); 	

	$table->startRow();
	$objdate = new textinput('date');
	$objdateLabel =  new label($this->objLanguage->languageText("mod_mayibuye_commentdate","mayibuyeform"),"date"); 
	$table->addCell($objdateLabel->show(),'', 'center', 'left', '');
	//$table->addCell($objdate->show().$required);
	$table->addCell($objdate->show(),'','center','left','');
	$objForm->addRule('date', $this->objLanguage->languageText("mod_mayibuye_date_required", "mayibuyeform"), 'required');
        $table->endRow();

        $table->startRow();
	$objnameofresearcher = new textinput('name_resign');
	$objnameofReseacherLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentnameofresearch","mayibuyeform"),"name_resign");
	$table->addCell($objnameofReseacherLabel->show(),'', 'center', 'left', '');
	$table->addCell($objnameofresearcher->show());
	$objForm->addRule('name_resign','Please enter your name','required');

        $table->endRow();

        $table->StartRow();
	$objTelno = new textinput('tellno');
	$objTelLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commenttelno","mayibuyeform"),"Telno");
	$table->addCell($objTelLabel->show(),'', 'center', 'left', '');
	$table->addCell($objTelno->show());
	$objForm->addRule('tellno', 'Telephone Must contain valid numbers', 'numeric');
	$table->EndRow();

	
	$table->startRow();	
	$objFaxno = new textinput('faxno');
	$objFaxLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentfaxno","mayibuyeform"),"faxno");
	$table->addCell($objFaxLabel->show(),'', 'center', 'left', '');
	$table->addCell($objFaxno->show());
	$table->endRow(); 
	
	$table->StartRow();
	$objEmailaddress = new textinput('emailaddress');
	$objEmailaddressLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentemailaddress","mayibuyeform"),"email");
	$table->addCell($objEmailaddressLabel->show(),'', 'center', 'left', '');
	$table->addCell($objEmailaddress->show());
 	$objForm->addRule('emailaddress', 'Not a valid Email', 'email');
	$table->endRow(); 
	

	$fieldset = $this->newObject('fieldset', 'htmlelements');
	$fieldset->legend = $this->objLanguage->languageText('phrase_accountdetails', 'userregistration', 'Researcher Details');
	$fieldset->contents = $table->show();

	$objForm->addToForm($fieldset->show());
   
	$button = new button ('submitform', 'Continue');
	$button->setToSubmit();
       	$objForm->addToForm('<p align="center"><br />'.$button->show().'</p>');
	 return $objForm->show();

}

function insertStudentRecord($date, $nameofreseacher, $tellno, $faxxno, $email)
	 {
           $id = $this->insert(array(
                'date'=>$date,
		'name'=>$nameofreseacher,
		'telno' =>$tellno,
		'faxno' =>$faxxno,
		'emailaddress' =>$email	
        ));
        return $id;
}


private function getFormAction()
{
$formAction = $this->uri(array("action" => "send_researchform"), "mayibuyeform");
        return $formAction;
}

public function Show()
{
return $this->buildForm();

}
}
?>
