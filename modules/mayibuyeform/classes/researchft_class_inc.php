<?php

class researchft extends dbTable
{
   	public $objLanguage;
	var $captcha;



 public function init()

{
 $this->objLanguage = $this->getObject('language', 'language');
 	parent::init('tbl_mayibuyeform_researchft');
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

	//Create a new label for the text labels	
	$headingLabel = new label($this->objLanguage->languageText("mod_mayibuye_heading","mayibuyeform"),"heading");
	$objForm->addToForm('<p align="center">'.$headingLabel->show().$required.'</p>');  
	
 
	$table->startRow();
	$objsubheadingLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentlabel","mayibuyeform"),"heading");
	$table->addCell('<p>'.$objsubheadingLabel->show().'</p>');
        $table->endRow();
	
	$table->startRow();
	$objNameofResignator = new textinput('resignatorname');
	$objResignatorLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentnameofsignotor","mayibuyeform"),"name of resignator");
	$table->addCell($objResignatorLabel->show(),'', 'center', 'left', '');
        $table->addCell($objNameofResignator->show());
	$objForm->addRule('resignatorname','Resignatory Required','required');

	$table->endRow();
	
	$table->startRow();
	$objjobtitle = new textinput('job_title');
	$objjobtitleLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentjobtitle","mayibuyeform"),"job_title");
	$table->addCell($objjobtitleLabel->show(),'', 'center', 'left', '');         
	$table->addCell($objjobtitle->show());
	$objForm->addRule('job_title','Title Required','required');
	$table->endRow();

	$table->startRow();
	$objorganisation = new textinput('organization');
	$objorganizationLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentorganizationname","mayibuyeform"),"organazation");
	$table->addCell($objorganizationLabel->show(),'', 'center', 'left', '');         
	$table->addCell($objorganisation->show());
	$objForm->addRule('organization','Organization Required','required');
	$table->endRow();

	$table->startRow();
       	$objpostal = new textinput('postal_address');
	$objpostalLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentpostaladrres","mayibuyeform"),"postaladdress");
	$table->addcell($objpostalLabel->show(),'', 'center', 'left', '');         
	$table->addcell($objpostal->show(),'', 'center', 'left', '');
	$objForm->addRule('postal_address','Address Required','required');
	$table->endRow();   

	$table->startRow();
       	$objphysical = new textinput('physical_address');
	$objphysicalLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentphysicaladdress","mayibuyeform"),"physicaladdress");
	$table->addcell($objphysicalLabel->show(),'', 'center', 'left', '');         
	$table->addcell($objphysical->show(),'', 'center', 'left', '');
	$objForm->addRule('physical_address','Physical Address','required');

	$table->endRow(); 
         
	$table->startRow();
	$objvat = new textinput('vat_no');
	$objvatLabel = new label($this->objLanguage->LanguageText("mod_mayibuye_commentvatno","mayibuyeform"),"vat_no");
	$table->addCell($objvatLabel->show(),'', 'center', 'left', '');         
	$table->addCell($objvat->show(),'', 'center', 'left', '');
	$table->endRow();
	
	$table->startRow();
	$objjobno = new textinput('job_no');
	$objjobnoLabel = new label($this->objLanguage->LanguageText("mod_mayibuye_commentjobno","mayibuyeform"),"job no");
	$table->addCell($objjobnoLabel->show());         
	$table->addCell($objjobno->show()."<br />");
	$table->endRow();

      	$table->startRow();
	$objTelno2 = new textinput('tell_no');
	$objTel2Label = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commenttelno","mayibuyeform"),"tel_no");
	$table->addCell($objTel2Label->show(),'', 'center', 'left', '');
	$table->addCell($objTelno2->show(),'', 'center', 'left', '');
	$objForm->addRule('tell_no', 'Telephone Must contain valid numbers', 'numeric');
	$table->endRow();

	$table->startRow();
	$objFaxno2 = new textinput('faxno_2');
	$objFax2Label = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentfaxno","mayibuyeform"),"faxno_2");
	$table->addCell($objFax2Label->show(),'', 'center', 'left', '');
	$table->addCell($objFaxno2->show(),'', 'center', 'left', '');
	$table->endRow();

    	$table->startRow();
	$objEmail = new textinput('emails');
	$objEmailLabel = new label($this->objLanguage->LanguageText("mod_mayibuyeform_commentemailaddress","mayibuyeform"),"email");
	$table->addCell($objEmailLabel->show(),'', 'center', 'left', '');
	$objForm->addRule('emails', 'Not a valid Email', 'email');
	$table->addCell($objEmail->show());
	
	$fieldset = $this->newObject('fieldset', 'htmlelements');
	$fieldset->legend = $this->objLanguage->languageText('phrase_accountdetails', 'userregistration', 'Signatory Details');
	$fieldset->contents = $table->show();
	$objForm->addToForm($fieldset->show());


	$button = new button('submitform', 'Continue');
	$button->setToSubmit();
       	$objForm->addToForm('<p align="center"><br />'.$button->show().'</p>');
	return $objForm->show();

}

function insertResearchRecord($nameofsign, $jobtitles, $organization,$postaladd,$physicaladd,$vatno,$jobnno,$telephone,$faxnumber2,$email2)
	{
           $id = $this->insert(array(
                'nameofsignotory' =>$nameofsign,
		'jobtitle' => $jobtitles,
		'nameoforganization' => $organization,
		'postaladdress'=> $postaladd,
		'physicaladdress'=> $physicaladd,
		'vatnum'=> $vatno,
		'jobno'=> $jobnno,
		'telephone'=> $telephone,
		'faxnumber'=> $faxnumber2,
		'email'=> $email2 
	));
        return $id;
}


private function getFormAction()
{
$formAction = $this->uri(array("action" => "send_researchft"), "mayibuyeform");
        return $formAction;
}

public function Show()
{
return $this->buildForm();

}
}
?>
