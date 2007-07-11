<?php
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('tabbedbox', 'htmlelements');


$Header =& $this->getObject('htmlheading', 'htmlelements');
$Header->type=1;
$Header->cssClass = 'error';

// Determine the error mesasge to display
switch ($this->getParam('message')){
    case 'loginrequired':
    case 'needlogin':
        $Header->str=$this->objLanguage->languageText('mod_security_needlogin', 'security');
        $smallText = $this->objLanguage->languageText('mod_security_needloginmessage','security');
        break;
    case 'wrongpassword':
        $Header->str=$this->objLanguage->languageText('mod_security_incorrectpassword', 'security');
        $smallText = $this->objLanguage->languageText('mod_security_incorrectpasswordmessage','security');
        break;
    case 'noaccount':
        $Header->str=$this->objLanguage->languageText('mod_security_noaccount', 'security');
        $smallText = $this->objLanguage->languageText('mod_security_noaccountmessage','security');
        break;
    case 'inactive':
        $Header->str=$this->objLanguage->languageText('mod_security_inactive', 'security');
        $smallText = $this->objLanguage->languageText('mod_security_inactivemessage','security');
        break;
    case 'no_ldap':
        $Header->str=$this->objLanguage->languageText('mod_security_no_ldap', 'security');
        $smallText = $this->objLanguage->languageText('mod_security_no_ldap','security');
        break;
    case 'alreadyloggedin':
        $Header->str=$this->objLanguage->languageText('mod_security_alreadyloggedin', 'security');
        $smallText = $this->objLanguage->languageText('mod_security_onlyonelogin','security');
        $loginURL=$this->uri(array('action'=>'login','username'=>$this->getSession('username',''),'password'=>$this->getSession('password',''),'loginstatuscheck'=>'override'));
        $loginString=$this->objLanguage->languageText('mod_security_removelogin','security');
        $smallText.="<br /><br />\n<a href='$loginURL' class='pseudobutton'>$loginString</a>\n<br />\n";
        break;
    default:
        $Header->str=$this->objLanguage->languageText('mod_security_unknownerror','security');
        $smallText = $this->objLanguage->languageText('mod_security_errormessage','security');
}

//Load up the text output with the error messages
$middleContent = stripslashes($Header->show())."\n<p>".$smallText."<br />\n";


// Email link
$middleContent .=' '.$this->objLanguage->languageText('mod_security_emailsysadmin', 'security');

$sysAdminEmail = new link ('mailto:'.$this->objConfig->getsiteEmail());
$sysAdminEmail->link = $this->objConfig->getsiteEmail();

$middleContent .= ' ('.$sysAdminEmail->show().'). </p>';

// Other links
$newPasswordLink = new link ($this->uri(array('action'=>'needpassword')));
$newPasswordLink->link = $this->objLanguage->languageText('mod_security_requestnewpassword', 'security');

$registerModule=$this->objConfig->getValue('SELFREGISTER_MODULE') or $registerModule='userregistration';
$registerLink = new link ($this->uri(array('action'=>'register'), $registerModule));
$registerLink->link = $this->objLanguage->languageText('word_register');

$backHomeLink = new link ($this->uri(NULL, $this->objConfig->getValue('KEWL_PRELOGIN_MODULE')));
$backHomeLink->link = $this->objLanguage->languageText('phrasebacktohomepage', 'security');

if ($this->getParam('message') == 'wrongpassword') {
    $middleContent .= $newPasswordLink->show().' / ';
} else if ($this->objConfig->getallowSelfRegister()) {
    $middleContent .= $registerLink->show().' / ';
}

$middleContent .= $backHomeLink->show();

echo $middleContent;
?>