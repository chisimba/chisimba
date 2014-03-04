<?php
$this->loadClass("radio","htmlelements");
$this->loadClass("fieldset","htmlelements");
$this->loadClass("label","htmlelements");
$this->loadClass("form","htmlelements");
$this->loadClass("button","htmlelements");
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$header = new htmlHeading();
$header->type="1";
$header->cssClass="pagetitle";
$header->str = $this->objLanguage->languageText('mod_contextcontent_selectdates', 'contextcontent','Select dates');

echo $header->show();
$objPopupcal = $this->newObject('datepickajax', 'popupcalendar');
$objTable=$this->getObject("htmltable","htmlelements");

$startLabel=$this->objLanguage->languageText('mod_contextcontent_startdate', 'contextcontent',"Start Date");
$closeLabel=$this->objLanguage->languageText('mod_contextcontent_closedate', 'contextcontent',"Close Date");
$userOptLabel=$this->objLanguage->languageText('mod_contextcontent_onlystudents', 'contextcontent',"Show student activity only");
/* *** start date & time *** */
// Set start date of test
$startField = $objPopupcal->show('startdate', 'yes', 'no', strftime('%Y-%m-%d %H:%M:%S', mktime()));
$objLabel = new label($startLabel.':', 'input_start');
$objTable->addRow(array(
    $objLabel->show() ,
    $startField
));
// Set closing date of test

$closeField = $objPopupcal->show('enddate', 'yes', 'no', strftime('%Y-%m-%d %H:%M:%S', mktime()));
$objLabel = new label($closeLabel.':', 'input_close');
$objTable->addRow(array(
    $objLabel->show() ,
    $closeField
));

$objElement = new checkbox('studentsonly','',true);  // this will checked
$check = $objElement->show();
$objTable->addRow(array(
    $userOptLabel,
    $check

));


$saveLabel="View";
$objButton = new button('save', $saveLabel);
$objButton->setToSubmit();
$btnSave = $objButton->show();
$objForm = new form('studentactivity', $this->uri(array(
    'action' => $action
)));


$objForm->addToForm($objTable->show());
$objForm->addToForm($btnSave);


echo $objForm->show();
?>
