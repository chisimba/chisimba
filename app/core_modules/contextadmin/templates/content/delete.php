<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = ucwords($this->objLanguage->code2Txt('mod_contextadmin_deletecontext', 'contextadmin', NULL, 'Delete [-context-]')).':'.$context['title'];

echo $header->show();


$objDisplayContext = $this->getObject('displaycontext', 'context');
echo $objDisplayContext->formatContextDisplayBlock($context);

$form = new form ('deletecontext', $this->uri(array('action'=>'deleteconfirm')));

$radio = new radio ('deleteconfirm');
$radio->addOption('no', ' '.$this->objLanguage->languageText('word_no', 'system', 'No').' - '.$this->objLanguage->code2Txt('mod_contextadmin_donotdeletecontext', 'contextadmin', NULL, 'Do not delete the [-context-]'));
$radio->addOption('yes', ' '.$this->objLanguage->languageText('word_yes', 'system', 'Yes').' - '.$this->objLanguage->code2Txt('mod_contextadmin_dodeletecontext', 'contextadmin', NULL, 'Delete the [-context-]'));
$radio->setSelected('no');
$radio->setBreakSpace(' &nbsp; &nbsp; ');

$form->addToForm('<p>'.$this->objLanguage->languageText('mod_contextadmin_confirmdeletecontext', 'contextadmin', 'Are you sure you want to delete this context?').'</p>');
$form->addToForm('<p>'.$radio->show().'</p>');

$button = new button ('confirm', $this->objLanguage->languageText('word_confirm', 'system', 'Confirm'));
$button->setToSubmit();

$cancelButton = new button ('cancel', $this->objLanguage->languageText('word_cancel', 'system', 'Cancel'));
$cancelButton->setOnClick("javascript: history.go(-1);");

$form->addToForm('<p>'.$button->show().' '.$cancelButton->show().'</p>');

$hiddenInput = new hiddeninput('contextcode', $context['contextcode']);
$form->addToForm($hiddenInput->show());

echo $form->show();

?>