<script type="text/javascript">
//<![CDATA[
function init () {
	$('input_redraw').onclick = function () {
		redraw();
	}
}
function redraw () {
	var url = 'index.php';
	var pars = 'module=security&action=generatenewcaptcha';
	var myAjax = new Ajax.Request( url, {method: 'get', parameters: pars, onComplete: showResponse} );
}
function showLoad () {
	$('load').style.display = 'block';
}
function showResponse (originalRequest) {
	var newData = originalRequest.responseText;
	$('captchaDiv').innerHTML = newData;
}
//]]>
</script>

<?php

switch ($this->getParam('error'))
{
    case 'captcha' : 
        $error = $this->objLanguage->languageText('mod_security_error_imagecode', 'security', 'The image code you entered did not match the image. Please try again'); 
        break;
        
    case 'details' : 
        $error = $this->objLanguage->languageText('mod_security_error_usernamedoesntmatchemail', 'security', 'The username and email address combination you entered did not match. Please try again'); 
        break;
        
    case 'ldap'    : 
        $error = $this->objLanguage->languageText('mod_security_error_networkidretrievepassword', 'security', 'The username and email address you entered uses a Network Identification. This feature does not work for accounts that use Network Identification. Please contact your System Administrator for assistance'); 
        break;
        
    default : $error = ''; break;
}

if ($error != '') {

    echo '<h1 class="error">'.ucfirst($this->objLanguage->languageText('word_error')).':</h1><ul><li class="error"> '.$error.'</li></ul>';
    
    echo '<h3>Forgot your Password</h3>';

} else {
    echo '<h1>Forgot your Password</h1>';
}


echo '<p>'.$this->objLanguage->languageText('mod_security_forgotpasswordprocess', 'security', 'If you have forgotten your password, please complete the form below. We will generate a NEW password for you to use, and send it to your email address.').'</p>';

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$form = new form ('requestpassword', $this->uri(array('action'=>'needpasswordconfirm')));

$username = new textinput('request_username');
$usernameLabel = new label($this->objLanguage->languageText('word_username'), 'input_request_username');

$email = new textinput('request_email');
$emailLabel = new label($this->objLanguage->languageText('phrase_emailaddress'), 'input_request_email');

$objCaptcha = $this->getObject('captcha', 'utilities');
$captcha = new textinput('request_captcha');
$captchaLabel = new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request'), 'input_request_captcha');


$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
    $table->addCell($usernameLabel->show(), 100);
    $table->addCell($username->show());
$table->endRow();

$table->startRow();
    $table->addCell($emailLabel->show());
    $table->addCell($email->show());
$table->endRow();

$table->startRow();
    $table->addCell('&nbsp;');
    $table->addCell('&nbsp;');
$table->endRow();

$table->startRow();
    $redrawButton = new button ('redraw', 'Redraw', 'redraw();');
    $table->addCell($captchaLabel->show());
    $table->addCell(stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.')).'<br /><div id="captchaDiv">'.$objCaptcha->show().'</div>'.$captcha->show().' <a href="javascript:redraw();">'.$this->objLanguage->languageText('word_redraw', 'security', 'Redraw').'</a>');
$table->endRow();

$form->addToForm($table->show());


$button = new button ('submitform', $this->objLanguage->languageText('mod_security_sendmenewpassword', 'security', 'Send me a new password'));
$button->setToSubmit();

$form->addToForm('<p><br />'.$button->show().'</p>');

$form->addRule('request_username','Please enter your username','required');
//$form->addRule('request_email', 'Not a valid Email', 'email');
$form->addRule('request_email','Please enter your emailaddress','required');
$form->addRule('request_captcha','Please enter the code in the image','required');

echo $form->show();
//$this->setLayoutTemplate(NULL);
//$this->setVar('pageSuppressXML', TRUE);
//echo '<div id="captchaDiv">'.$objCaptcha->show().'</div>'.$redrawButton->show();

echo '<p>'.$this->objLanguage->languageText('word_note', 'security', 'Note').': ';

echo $this->objLanguage->languageText('mod_security_passworddoesntworkldap', 'security', 'This does not work for accounts that use Network Identification. For assistance in this regard, please contact your System Administrator');

echo ' ('.$this->objConfig->getsiteEmail().')</p>'; 

?>