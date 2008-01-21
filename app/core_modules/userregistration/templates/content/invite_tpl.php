<?php
$this->setLayoutTemplate = NULL;
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('htmlarea', 'htmlelements');

$required = '<span class="warning"> * '.$this->objLanguage->languageText('word_required', 'system', 'Required').'</span>';

$headerinv = new htmlheading();
$headerinv->type = 1;
$headerinv->str = $this->objLanguage->languageText('phrase_invitemate', 'userregistration').' '.$this->objConfig->getSitename();

$middleColumnContent = NULL;
$middleColumnContent .= $headerinv->show();

// start the form
$form = new form ('invite', $this->uri(array('action'=>'sendinvite')));

// form elements
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$friendname = new textinput('friend_firstname');
$friendnameLabel = new label($this->objLanguage->languageText('friendname', 'userregistration').'&nbsp;', 'input_friendname');
$table->addCell($friendnameLabel->show(), 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($friendname->show().$required);
$table->endRow();

// surname
$table->startRow();
$friendsurname = new textinput('friend_surname');
$friendsurnameLabel = new label($this->objLanguage->languageText('friendsurname', 'userregistration').'&nbsp;', 'input_friendsurname');
$table->addCell($friendsurnameLabel->show(), 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($friendsurname->show().$required);
$table->endRow();

// message to include to mate
$table->startRow();
$friendmsg = $this->newObject('htmlarea', 'htmlelements');
$friendmsg->name = 'friend_msg';
$friendmsgLabel = new label($this->objLanguage->languageText('friendmessage', 'userregistration').'&nbsp;', 'input_friendmsg');
$table->addCell($friendmsgLabel->show(), 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$friendmsg->toolbarSet = 'simple';
$table->addCell($friendmsg->show());
$table->endRow();

$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = ''; // $this->objLanguage->languageText('phrase_invitefriend', 'userregistration');
$fieldset->contents = $table->show();
// add the form to the fieldset
$form->addToForm($fieldset->show());

$button = new button ('submitform', 'Complete Invitation');
$button->setToSubmit();
$form->addToForm('<p align="center"><br />'.$button->show().'</p>');

$middleColumnContent .= $form->show();
$cssLayout = $this->getObject('csslayout', 'htmlelements');
$cssLayout->setLeftColumnContent("DUDE!");
$cssLayout->setMiddleColumnContent($middleColumnContent);
echo $cssLayout->show();
