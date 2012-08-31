<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');


$header = new htmlHeading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_filemanager_editquota', 'filemanager', 'Edit Quota').': ';
if (substr($quota['path'], 0, 7) == 'context') {
    $header->str .= ucfirst($this->objLanguage->code2Txt('mod_filemanager_contextfilesof', 'filemanager', NULL, '[-context-] Files of')).' '. $this->objContext->getTitle(substr($quota['path'], 8));
    $defaultQuota = $this->objQuotas->getDefaultContextQuota();
} else {
    $header->str .= $this->objLanguage->languageText('mod_filemanager_userfilesof', 'filemanager', 'User files of').' '.$this->objUser->fullName(substr($quota['path'], 6));
    $defaultQuota = $this->objQuotas->getDefaultUserQuota();
}

echo $header->show();

$form = new form ('updatequota', $this->uri(array('action'=>'updatequota')));

$hiddeninput = new hiddeninput('id', $quota['id']);
$form->addToForm($hiddeninput->show());

$radio = new radio('quotatype');
$radio->addOption('Y', $this->objLanguage->languageText('mod_filemanager_usedefaultquotaof', 'filemanager', 'Use Default Quota of').' '.$defaultQuota.' MB');
$radio->addOption('N', $this->objLanguage->languageText('mod_filemanager_usecustomquota', 'filemanager', 'Use Custom Quota'));
$radio->setBreakSpace('<br />');
$radio->setSelected($quota['usedefault']);

$form->addToForm($radio->show());

$customQuota = new textinput('customquota');
$customQuota->size = 5;

if ($quota['usedefault'] == 'Y') {
    $customQuota->value = $defaultQuota;
} else {
    $customQuota->value = $quota['quota'];
}

$form->addToForm(': '.$customQuota->show().' MB');

$button = new button ('confirm', $this->objLanguage->languageText('mod_filemanager_updatequota', 'filemanager', 'Update Quota'));
$button->setToSubmit();

$form->addToForm('<br /><br />'.$button->show());

$form->addRule('customquota', $this->objLanguage->languageText('mod_filemanager_validatenumber', 'filemanager', 'Please enter a number for the custom quota'), 'numeric');

echo $form->show();

?>