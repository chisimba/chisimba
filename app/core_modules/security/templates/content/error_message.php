<?php
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('tabbedbox', 'htmlelements');


// --- Login Form --- 
$form = new form('login', $this->uri(array('action'=>'login'), 'security'));

$table =& $this->getObject('htmltable', 'htmlelements');
$table->cellpadding = 5;

$usernameLabel = new label(ucwords($this->objLanguage->languageText('word_username')), 'input_username');
$username = new textinput('username');

$table->addRow(array($usernameLabel->show()));
$table->addRow(array($username->show()));

$passwordLabel = new label(ucwords($this->objLanguage->languageText('word_password')), 'input_password');
$password = new textinput('password');
$password->fldType = 'password';

$table->addRow(array($passwordLabel->show()));
$table->addRow(array($password->show()));

if ($this->objConfig->getuseLDAP()) {
    $ldap = new checkbox('useLdap','yes');
    $ldapLabel = new label('Network Id', 'input_useLdap'); ///*********************************************************** Language Text Element
    $table->addRow(array($ldapLabel->show().' '.$ldap->show()));
}

$button = new button ('submitform');
$button->setValue(ucwords($this->objLanguage->languageText('word_login')));
$button->setToSubmit();

$table->addRow(array($button->show()));

$tabBox = new tabbedbox();
$tabBox->addTabLabel(ucwords($this->objLanguage->languageText('word_login')));
$tabBox->addBoxContent($table->show());

$form->addToForm($tabBox->show());

// --- END -> Login Form --- 

// --- Middle Conent --- 

$Header =& $this->getObject('htmlheading', 'htmlelements');
$Header->type=1;
$Header->cssClass = 'error';

if ($this->getParam('message') == 'wrongpassword') {
$Header->str=$this->objLanguage->languageText('mod_security_incorrectpassword','security');
} else {
    $Header->str=$this->objLanguage->languageText('mod_security_noaccount','security');
}

if ($this->getParam('message')=='no_ldap') {
    $Header->str=$this->objLanguage->languageText('mod_security_no_ldap');
}

$middleContent = $Header->show();

if ($this->getParam('message') == 'wrongpassword') {
    $middleContent .= '<p>'.$this->objLanguage->languageText('mod_security_incorrectpasswordmessage','security');
} else {
    $middleContent .= '<p>'.$this->objLanguage->languageText('mod_security_noaccountmessage','security');
}

if ($this->getParam('message')=='no_ldap') {
    $middleContent .= '<p>'.$this->objLanguage->languageText('mod_security_no_ldap','security');
}

$middleContent .=' '.$this->objLanguage->languageText('mod_security_emailsysadmin','security');

$sysAdminEmail = new link ('mailto:'.$objConfig->getsiteEmail());
$sysAdminEmail->link = $objConfig->getsiteEmail();

$middleContent .= ' ('.$sysAdminEmail->show().'). </p>';

$newPasswordLink = new link ($this->uri(array('action'=>'needpassword'), 'useradmin'));
$newPasswordLink->link = $this->objLanguage->languageText('mod_security_requestnewpassword','security');

$registerLink = new link ($this->uri(array('action'=>'register'), 'useradmin'));
$registerLink->link = $this->objLanguage->languageText('word_register');

$backHomeLink = new link ($this->uri(NULL, '_default'));
$backHomeLink->link = $this->objLanguage->languageText('phrasebacktohomepage','security');

if ($this->getParam('message') == 'wrongpassword') {
    $middleContent .= $newPasswordLink->show().' / ';
} else if ($this->objConfig->getallowSelfRegister()) {
    $middleContent .= $registerLink->show().' / ';
}

$middleContent .= $backHomeLink->show();

$cssLayout =& $this->getObject('csslayout', 'htmlelements');
$cssLayout->setLeftColumnContent($form->show());
$cssLayout->setMiddleColumnContent($middleContent);

echo $cssLayout->show();
?>
