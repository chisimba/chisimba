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
// check if the site signup user string is set, if so, use it to populate the fields
if (isset($userstring)) {
    $userstring = base64_decode($userstring);
    $userstring = explode(',', $userstring);
} else {
    $userstring = NULL;
}
$this->loadClass('form', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->appendArrayVar('headerParams', '
	<script type="text/javascript">
		var uri = "' . str_replace('&amp;', '&', $this->uri(array(
    'module' => 'liftclub',
    'action' => 'jsongetcities'
))) . '"; 
 </script>');
//Ext stuff
$ext = '<link rel="stylesheet" href="' . $this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css', 'htmlelements') . '" type="text/css" />';
$ext.= $this->getJavaScriptFile('ext-3.0-rc2/adapter/ext/ext-base.js', 'htmlelements');
$ext.= $this->getJavaScriptFile('ext-3.0-rc2/ext-all.js', 'htmlelements');
$ext.= $this->getJavaScriptFile('extjsgetcity.js', 'liftclub');
$ext.= $this->getJavaScriptFile('extjsgetcityb.js', 'liftclub');
//$ext .=$this->getJavaScriptFile('forum-search.js', 'rimfhe');
$ext.= '<link rel="stylesheet" href="' . $this->getResourceUri('combos.css', 'liftclub') . '"type="text/css" />';
$ext.= $this->getJavaScriptFile('ext-3.0-rc2/examples/shared/examples.js', 'htmlelements');
$this->appendArrayVar('headerParams', $ext);
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText("phrase_registeron", 'liftclub', "Register On") . ' ' . $this->objConfig->getSitename();
echo '<div style="padding:10px;">' . $header->show();
$required = '<span class="warning"> * ' . $this->objLanguage->languageText('word_required', 'system', 'Required') . '</span>';
$str = $this->objLanguage->languageText('mod_liftclub_firstneedtoregister', 'liftclub', 'In order to be able to access [[SITENAME]], you first need to register');
$str = str_replace('[[SITENAME]]', $this->objConfig->getSitename() , $str);
echo '<p>' . $str . '<br />';
echo $this->objLanguage->languageText('mod_liftclub_pleaseenterdetails', 'liftclub', 'Please enter your details, email address and desired user name in the form below.') . '</p>';
$form = new form('register', $this->uri(array(
    'action' => 'register'
)));
$messages = array();
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$username = new textinput('register_username');
$username->extra = "maxlength=255";
$usernameLabel = new label($this->objLanguage->languageText('word_username', 'system') . '&nbsp;', 'input_register_username');
$usernameContents = new label($this->objLanguage->languageText('phrase_usernamemayconsistof', 'userdetails', 'May consist of a-z, 0-9 and underscore') , 'input_register_username');
if ($mode == 'addfixup') {
    $username->value = $this->getParam('register_username');
    if ($this->getParam('register_username') == '' || strlen($this->getParam('register_username')) > 255) {
        $messages[] = $this->objLanguage->languageText('phrase_enterusername', 'system', 'Please enter a username');
    }
}
$table->addCell($usernameLabel->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($username->show() . $required . ' - <em>' . $usernameContents->show() . '</em>');
//, NULL, NULL, NULL, NULL, 'colspan="2"');
$table->endRow();
$table->startRow();
$password = new textinput('register_password');
$password->fldType = 'password';
$password->extra = "maxlength=255";
$passwordLabel = new label($this->objLanguage->languageText('word_password', 'system') , 'input_register_password');
$confirmPassword = new textinput('register_confirmpassword');
$confirmPassword->fldType = 'password';
$confirmPassword->extra = 'maxlength=255';
$confirmPasswordLabel = new label($this->objLanguage->languageText('phrase_confirmpassword', 'liftclub', 'Confirm Password') , 'input_register_confirmpassword');
$table->addCell($passwordLabel->show() , 150, 'top', 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($password->show() . $required);
$table->endRow();
$table->startRow();
$table->addCell($confirmPasswordLabel->show() , 150, 'top', 'right');
$table->addCell('&nbsp;', 5);
$table->addCell($confirmPassword->show() . $required);
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_accountdetails', 'liftclub', 'Account Details');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
$table = $this->newObject('htmltable', 'htmlelements');
$titlesDropdown = new dropdown('register_title');
$titlesLabel = new label($this->objLanguage->languageText('word_title', 'system') . '&nbsp;', 'input_register_title');
$titles = array(
    "title_mr",
    "title_miss",
    "title_mrs",
    "title_ms",
    "title_dr",
    "title_prof",
    "title_rev",
    "title_assocprof"
);
foreach($titles as $title) {
    $_title = trim($this->objLanguage->languageText($title));
    $titlesDropdown->addOption($_title, $_title);
}
if ($mode == 'addfixup') {
    $titlesDropdown->setSelected($this->getParam('register_title'));
}
$table->startRow();
$table->addCell($titlesLabel->show() , 150, NULL, 'right');
$table->addCell($titlesDropdown->show());
$table->endRow();
$firstname = new textinput('register_firstname');
$firstnameLabel = new label($this->objLanguage->languageText('phrase_firstname', 'system') . '&nbsp;', 'input_register_firstname');
if ($mode == 'addfixup') {
    $firstname->value = $this->getParam('register_firstname');
    if ($this->getParam('register_firstname') == '') {
        $messages[] = $this->objLanguage->languageText('mod_userdetails_enterfirstname', 'userdetails');
    }
}
if (isset($userstring) && $mode == 'add') {
    $firstname->value = $userstring[0];
}
$table->startRow();
$table->addCell($firstnameLabel->show() , 150, NULL, 'right');
$table->addCell($firstname->show() . $required);
$table->endRow();
$surname = new textinput('register_surname');
$surnameLabel = new label($this->objLanguage->languageText('word_surname', 'system') . '&nbsp;', 'input_register_surname');
if ($mode == 'addfixup') {
    $surname->value = $this->getParam('register_surname');
    if ($this->getParam('register_surname') == '') {
        $messages[] = $this->objLanguage->languageText('mod_userdetails_entersurname', 'userdetails');
    }
}
if (isset($userstring[1]) && $mode == 'add') {
    $surname->value = $userstring[1];
}
$table->startRow();
$table->addCell($surnameLabel->show() , 150, NULL, 'right');
$table->addCell($surname->show() . $required);
$table->endRow();
$staffnum = new textinput('register_staffnum', '', 'hidden');
$staffnumLabel = new label($this->objLanguage->languageText('phrase_staffstudnumber', 'liftclub', 'Staff / Student number') . '&nbsp;', 'input_register_staffnum');
$staffnumguestLabel = new label($this->objLanguage->languageText('mod_liftclub_ifguestleaveblank', 'liftclub', 'If you are a guest, please leave this blank') , 'input_register_staffnum');
if ($mode == 'addfixup') {
    $staffnum->value = $this->getParam('register_staffnum');
}
/*
$table->startRow();
$table->addCell($staffnumLabel->show(), 150, NULL, 'right');
$table->addCell($staffnum->show().' <em>'.$staffnumguestLabel->show().'</em>');
$table->endRow();
*/
$cellnum = new textinput('register_cellnum');
$cellnumLabel = new label($this->objLanguage->languageText('phrase_cellnumber', 'liftclub', 'Cell Number') . '&nbsp;', 'input_register_cellnum');
if ($mode == 'addfixup') {
    $cellnum->value = $this->getParam('register_cellnum');
}
$table->startRow();
$table->addCell($cellnumLabel->show() , 150, NULL, 'right');
$table->addCell($cellnum->show() . $staffnum->show());
$table->endRow();
$sexRadio = new radio('register_sex');
$sexRadio->addOption('M', $this->objLanguage->languageText('word_male', 'system'));
$sexRadio->addOption('F', $this->objLanguage->languageText('word_female', 'system'));
$sexRadio->setBreakSpace(' &nbsp; ');
if ($mode == 'addfixup') {
    $sexRadio->setSelected($this->getParam('register_sex'));
} else {
    $sexRadio->setSelected('M');
}
$table->startRow();
$table->addCell($this->objLanguage->languageText('word_gender', 'liftclub', 'Gender') . '&nbsp;', 150, NULL, 'right');
$table->addCell($sexRadio->show());
$table->endRow();
$table->startRow();
$objCountries = &$this->getObject('languagecode', 'language');
$table->addCell($this->objLanguage->languageText('word_country', 'system') . '&nbsp;', 150, NULL, 'right');
if ($mode == 'addfixup') {
    $table->addCell($objCountries->countryAlpha($this->getParam('country')));
} else {
    $table->addCell($objCountries->countryAlpha());
}
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_userdetails', 'liftclub', 'User Details');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
// Email Address
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$email = new textinput('register_email');
$emailLabel = new label($this->objLanguage->languageText('word_email', 'system', 'Email') , 'input_register_email');
$confirmEmail = new textinput('register_confirmemail');
$confirmEmailLabel = new label($this->objLanguage->languageText('phrase_confirmemail', 'system', 'Confirm Email') , 'input_register_confirmemail');
$emailInfoLabel = new label('Please Enter a Valid Email Address', 'input_register_email');
if ($mode == 'addfixup') {
    $email->value = $this->getParam('register_email');
    $confirmEmail->value = $this->getParam('register_confirmemail');
}
if (isset($userstring[2]) && $mode == 'add') {
    $email->value = $userstring[2];
    $confirmEmail->value = $userstring[2];
}
$table->addCell($emailInfoLabel->show() , 150, NULL, 'right');
$table->addCell('&nbsp;', 10);
$table->addCell($emailLabel->show() . $required . '<br />' . $email->show() , '20%');
$table->addCell($confirmEmailLabel->show() . $required . '<br />' . $confirmEmail->show());
$table->endRow();
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = $this->objLanguage->languageText('phrase_emailaddress', 'system', 'Email Address');
$fieldset->contents = $table->show();
$form->addToForm($fieldset->show());
$form->addToForm('<br />');
//Add form captcha
$objCaptcha = $this->getObject('captcha', 'utilities');
$captcha = new textinput('request_captcha');
$captchaLabel = new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request') , 'input_request_captcha');
$fieldset = $this->newObject('fieldset', 'htmlelements');
$fieldset->legend = 'Verify Image';
$fieldset->contents = stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.')) . '<br /><div id="captchaDiv">' . $objCaptcha->show() . '</div>' . $captcha->show() . $required . '  <a href="javascript:redraw();">' . $this->objLanguage->languageText('word_redraw', 'security', 'Redraw') . '</a>';
$form->addToForm($fieldset->show());
$button = new button('submitform', 'Complete Registration');
$button->setToSubmit();
$form->addToForm('<p align="center"><br />' . $button->show() . '</p><br/ ><br/ >');
if ($mode == 'addfixup') {
    foreach($problems as $problem) {
        $messages[] = $this->explainProblemsInfo($problem);
    }
}
if ($mode == 'addfixup' && count($messages) > 0) {
    echo '<ul><li><span class="error">' . $this->objLanguage->languageText('mod_userdetails_infonotsavedduetoerrors', 'userdetails') . '</span>';
    echo '<ul>';
    foreach($messages as $message) {
        if ($message != '') {
            echo '<li class="error">' . $message . '</li>';
        }
    }
    echo '</ul></li></ul>';
}
echo $form->show();
echo '</div>';
?>
