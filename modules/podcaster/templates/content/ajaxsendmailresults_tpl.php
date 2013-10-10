<?php

$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$buttonLabel = $this->objLanguage->languageText('word_next', 'system', 'System') . " " . $this->objLanguage->languageText('mod_podcaster_wordstep', 'podcaster', 'Step');
$buttonNote = $this->objLanguage->languageText('mod_podcaster_clickthreefromemail', 'podcaster', 'Click on the "Next step" button to proceed to upload podcast');
$emailSuccess = $this->objLanguage->languageText('mod_podcaster_emailsuccess', 'podcaster', 'An email was successfully sent to these addresses');
$emailLength = explode(',', $emails);
$emailLength = count($emailLength);

//Get Admin email address
$userAdmin = $this->objUser->getUserDetails('1');

if ($emailLength > 1) {
    $emailFail = $this->objLanguage->languageText('mod_podcaster_emailfail', 'podcaster', 'Unfortunately, we could not send an email to these addresses');
} else {
    $emailFail = $this->objLanguage->languageText('mod_podcaster_emailfailsingle', 'podcaster', 'Unfortunately, we could not send an email to this address');
}

$emailFailCont = $this->objLanguage->languageText('mod_podcaster_emailfailcont', 'podcaster', 'Kindly consult the site administrator through this email address')." ".$userAdmin['emailaddress'];

$button = new button('submit', $buttonLabel);

$button->cssId = 'savebutton';

$nextActionButton = $button->show();

$descProdLink = new link($this->uri(array(
                    'module' => 'podcaster',
                    'action' => 'upload',
                    'path' => $path,
                    'createcheck' => $createcheck,
                    'folderid' => $folderid
                )));
$descProdLink->link = $nextActionButton;
$linkDescribe = $descProdLink->show();

$this->setVar('pageSuppressXML', TRUE);

if ($sendstatus == 'send_success') {
    $this->appendArrayVar('bodyOnLoad', '

var par = window.parent.document;
window.history.forward(1);

par.forms[\'sendemail_' . $id . '\'].reset();
par.getElementById(\'form_sendemail_' . $id . '\').style.display=\'block\';
par.getElementById(\'sendmailresults\').style.display=\'block\';
par.getElementById(\'sendmailresults\').innerHTML = \'<span class="confirm">' . $emailSuccess . ': ' . $emails . '<br /><br />'.$buttonNote.'</span><br /><br /> ' . $linkDescribe . ' \';
par.getElementById(\'div_sendmail_' . $id . '\').style.display=\'none\';

window.location = "' . str_replace('&amp;', '&', $this->uri(array('action' => 'tempiframe', 'id' => $id))) . '";
');
} else {
    $this->appendArrayVar('bodyOnLoad', '

var par = window.parent.document;
window.history.forward(1);

par.forms[\'sendemail_' . $id . '\'].reset();
par.getElementById(\'form_sendemail_' . $id . '\').style.display=\'block\';
par.getElementById(\'sendmailresults\').style.display=\'block\';
par.getElementById(\'sendmailresults\').innerHTML = \'<span class="confirm">' . $emailFail . ': ' . $emails . '<br />' . $emailFailCont . '<br /><br />'.$buttonNote.'</span><br /><br /> ' . $linkDescribe . ' \';
par.getElementById(\'div_sendmail_' . $id . '\').style.display=\'none\';

window.location = "' . str_replace('&amp;', '&', $this->uri(array('action' => 'tempiframe', 'id' => $id))) . '";
');
}
?>