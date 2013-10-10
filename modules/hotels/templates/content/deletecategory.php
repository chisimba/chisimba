<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('radio', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->cssClass = 'warning';
$header->str = $this->objLanguage->languageText('phrase_deletecategory', 'phrase', 'Delete Category').': '.$category['categoryname'];

echo $header->show();


$objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
echo $objHighlightLabels->show();

$form = new form ('deletecategoryconfirm', $this->uri(array('action'=>'deletecategoryconfirm')));

$radio = new radio ('confirm');
$radio->addOption('no', $this->objLanguage->languageText('mod_hotels_nodeletecategory', 'hotels', 'No - Do not delete this category'));
$radio->addOption('yes', $this->objLanguage->languageText('mod_hotels_yesdeletecategory', 'hotels', 'Yes - Delete this category'));
$radio->setBreakSpace(' &nbsp; / &nbsp; ');
$radio->setSelected('no');

$form->addToForm('<p>'.$this->objLanguage->languageText('mod_hotels_requestconfirmdeletecategory', 'hotels', 'Are you sure you want to delete this category?').'</p><p>'.$radio->show().'</p>');

$button = new button ('confirmbutton', $this->objLanguage->languageText('mod_hotels_confirmaction', 'hotels', 'Confirm Action'));
$button->setToSubmit();

$form->addToForm('<p>'.$button->show().'</p>');

$hiddenInput = new hiddeninput('id', $item['id']);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('category', $category['id']);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('deletevalue', $deleteValue);
$form->addToForm($hiddenInput->show());

echo $form->show();


?>