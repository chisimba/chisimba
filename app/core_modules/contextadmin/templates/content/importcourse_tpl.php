<?php

$objH = & $this->newObject('htmlheading','htmlelements');
$objForm = & $this->newObject('form','htmlelements');
$label = $this->newObject('label','htmlelements');
$inpButton =  $this->newObject('button','htmlelements');

$this->loadClass('dropdown','htmlelements');
$objElement = new dropdown('blah');

//Dropdown 
foreach($dbData as $dataOld)
{
$objElement->addOption($dataOld['contextcode']);
}

//Label
$label->setLabel("Select a Course");

//Button
$inpButton->cssClass = 'f-submit';
$inpButton->setValue('Import');
$inpButton->setToSubmit();

//setup the form
$objForm->name = 'impfrm';
$objForm->action = $this->uri(array('action' => 'submitcourse'));

$objForm->addToForm($label->show());
$objForm->addToForm($objElement->show());
$objForm->addToForm($inpButton->show());

print $objForm->show().'<br/>';

?>