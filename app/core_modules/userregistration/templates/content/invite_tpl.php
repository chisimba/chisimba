<?php
$this->setLayoutTemplate = NULL;
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$headerinv = new htmlheading();
$headerinv->type = 1;
$headerinv->str = $this->objLanguage->languageText('phrase_invitemate', 'userregistration').' '.$this->objConfig->getSitename();

$middleColumnContent = NULL;
$middleColumnContent .= $headerinv->show();

$cssLayout = $this->getObject('csslayout', 'htmlelements');
$cssLayout->setLeftColumnContent("DUDE!");
$cssLayout->setMiddleColumnContent($middleColumnContent);
echo $cssLayout->show();
