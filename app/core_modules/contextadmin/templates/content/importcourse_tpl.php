<?php

$objH = & $this->newObject('htmlheading','htmlelements');
$objForm = & $this->newObject('form','htmlelements');
$label = $this->newObject('label','htmlelements');
$dropAccess = $this->newObject('dropdown','htmlelements');
$inpButton =  $this->newObject('button','htmlelements');

$dropAccess->name = 'Course';
$objForm->method = 'Get';
//Dropdown 
foreach($dbData as $dataOld)
{
$dropAccess->addOption($dataOld['contextcode']);
}
//Label
$label->setLabel("Select a Course");

//Button
$asdf="asdf";
$inpButton->cssClass = 'f-submit';
$inpButton->setValue('Import');
$inpButton->setOnClick("window.location='".$this->uri(array('action'=>'submitcourse','cc'=>$asdf))."';");

//setup the form
$objForm->name = 'impfrm';
$objForm->action = $this->uri(array('action' => 'passcourse'));

$objForm->addToForm($label->show());
$objForm->addToForm($dropAccess->show());
$objForm->addToForm($inpButton->show());

print $objForm->show().'<br/>';

?>
