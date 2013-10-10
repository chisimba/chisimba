<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end security check

$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('form','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('textarea','htmlelements');


$header = $this->getObject('htmlheading','htmlelements');
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_lrs_feedbackheader','award');

$companyInput = new textinput('buname','','text',20);
$occupationInput = new textinput('socname','','text',20);
$increaseInput = new textinput('increase','','text',5);
$wageInput = new textinput('wage','','text',8);
$oldWageInput = new textinput('oldwage','','text',8);
$hourInput = new textinput('hours','','text',2);
$nameInput = new textinput('name','','text',20);
$telephoneInput = new textinput('telephone','','text',20);
$emailInput = new textinput('email','','text',20);
$commentInput = new textarea('comment');

$pptypes = $this->objDbPayPeriodType->getAll();
$wageType = new dropdown('pptype');
$oldWageType = new dropdown('oldpptype');
$wageType->addOption(-1, $this->objLanguage->languageText('mod_lrs_default_drop', 'award'));
$wageType->addFromDB($pptypes,'name','id');
$wageType->setSelected(-1);
$oldWageType->addOption(-1, $this->objLanguage->languageText('mod_lrs_default_drop', 'award'));
$oldWageType->addFromDB($pptypes,'name','id');
$oldWageType->setSelected(-1);

$dateInput = $this->getObject('datepicker','htmlelements');
$dateInput->name = 'agreedate';

$sendButton = new button('send',$this->objLanguage->languageText('word_send'));
$sendButton->setToSubmit();
$backButton = new button('back',$this->objLanguage->languageText('word_back'),"javascript:window.location='".$this->uri(array('action'=>'selectbu','unit'=>$buid,'selected'=>'init_02'))."'");

$bu = new textinput('buid',$buid,'hidden');

$objTable = $this->newObject('htmltable','htmlelements');
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_name').": ",'20%');
$objTable->addCell($nameInput->show(),'80%');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_telephone').": ",'20%');
$objTable->addCell($telephoneInput->show(),'80%');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_email').": ",'20%');
$objTable->addCell($emailInput->show(),'80%');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_bucomp').": ",'20%');
$objTable->addCell($companyInput->show(),'80%');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_occupation').": ");
$objTable->addCell($occupationInput->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_startdate').": ");
$objTable->addCell($dateInput->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_oldwagerate').": ");
$objTable->addCell($oldWageInput->show()."".$oldWageType->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_increase')." (%): ");
$objTable->addCell($increaseInput->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_newwagerate').": ");
$objTable->addCell($wageInput->show()."".$wageType->show().$bu->show());
$objTable->endRow();
$objTable->startRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_hours').": ");
$objTable->addCell($hourInput->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_comments').": ");
$objTable->addCell($commentInput->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->addCell('&nbsp;');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($sendButton->show(),null,null,'right');
$objTable->addCell($backButton->show());
$objTable->endRow();

$objForm = new form('feedback',$this->uri(array('action'=>'submitfeedback','buid'=>$buid)));
$objForm->addToForm($objTable->show());
$objForm->addRule(array('telephone','email'), $this->objLanguage->languageText('mod_award_contactrequired', 'award'), 'either');
$objForm->addRule('buname', $this->objLanguage->languageText('mod_lrs_unit_rule', 'award'), 'required');
$objForm->addRule('socname', $this->objLanguage->languageText('mod_lrs_nameGrp_rule', 'award'), 'required');
$objForm->addRule('oldwage', $this->objLanguage->languageText('mod_lrs_wage_rule', 'award'), 'required');
$objForm->addRule('oldpptype', $this->objLanguage->languageText('mod_lrs_payperiod_rule', 'award'), 'select');
$objForm->addRule('wage', $this->objLanguage->languageText('mod_lrs_wage_rule', 'award'), 'required');
$objForm->addRule('pptype', $this->objLanguage->languageText('mod_lrs_payperiod_rule', 'award'), 'select');
$objForm->addRule('hours', $this->objLanguage->languageText('mod_award_hoursrequired', 'award'), 'required');

$message = "<p><b><i>".$this->objLanguage->languageText('mod_lrs_feedbackmessage','award')."</i></b></p>";

echo $header->show().$message.$objForm->show();

?>