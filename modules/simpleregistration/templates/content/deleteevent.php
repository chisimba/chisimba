<?php 

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->cssClass = 'warning';
$header->str = $this->objLanguage->languageText('mod_simpleregistration_confirmdeletestory', 'simpleregistration', 'Are you sure you want to delete this event?');

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');


$objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
echo $objHighlightLabels->show();

$form = new form ('deleteeventconfirm', $this->uri(array('action'=>'deleteeventconfirm')));

$radio = new radio ('confirm');
$radio->addOption('no', $this->objLanguage->languageText('mod_simpleregistration_nodeleteevent', 'simpleregistration', 'No - Do not delete this event'));
$radio->addOption('yes', $this->objLanguage->languageText('mod_simpleregistration_yesdeleteevent', 'simpleregistration', 'Yes - Delete this event'));
$radio->setBreakSpace(' &nbsp; / &nbsp; ');
$radio->setSelected('no');

$form->addToForm('<p>&nbsp;</p><p align="center">'.$radio->show().'</p>');

$button = new button ('confirmbutton', $this->objLanguage->languageText('mod_simpleregistration_confirmaction', 'simpleregistration', 'Confirm Action'));
$button->setToSubmit();

$form->addToForm('<p align="center">'.$button->show().'</p>');

$hiddenInput = new hiddeninput('id', $event['id']);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('deletevalue', $deleteValue);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('id', $event['id']);
$form->addToForm($hiddenInput->show());

echo $form->show();

?>
