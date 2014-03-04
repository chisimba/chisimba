<?php
$this->loadClass('htmlheading','htmlelements');

$objHead = new htmlheading();
$objHead->str=$this->objLanguage->languageText('mod_practicals_submittedpracticals', 'practicals', 'Submitted Practicals');
$objHead->type=1;

echo $objHead->show();

$assgnFunctions = $this->objPracticalFunctions->displayPracticals();
$this->objLink->link($this->uri(array('action'=>'home')));
$this->objLink->link=$this->objLanguage->languageText('mod_practicals_backtolist', 'practicals', 'Back to List of Practicals');

echo $assgnFunctions.$this->objLink->show();

?>
