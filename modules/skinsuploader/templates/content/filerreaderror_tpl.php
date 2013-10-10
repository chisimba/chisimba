<?php

$objForm = & $this->newObject('form','htmlelements');
$inpButton = & $this->newObject('button','htmlelements');
$infoLabel = & $this->newObject('label','htmlelements');

//setup the form
$objForm->name = 'impfrm';
$objForm->action = $this->uri(array('action' => 'default'));

//Button
$inpButton->cssClass = 'f-submit';
$inpButton->setValue('Back');
$inpButton->setToSubmit();

//Label
$infoLabel->setLabel('Error: Not a zip-file');

$objForm->addToForm($infoLabel->show().'<br/>');
$objForm->addToForm($inpButton->show().'<br/>');

print $objForm->show().'<br/>';

?>