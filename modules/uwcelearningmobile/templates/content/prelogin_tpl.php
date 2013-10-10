<?php

//Mobile Prelogin template
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
if (isset($error) && $error != 1) {
    echo '<div id="error">' . $error . '</div></br>';
}

//Load up the various HTML classes
$this->loadClass('button', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$formAction = $this->URI(array('action' => 'login'), 'uwcelearningmobile');

//Create a Form object
$objForm = new form('loginform', $formAction);
$objFields = new fieldset();
$objFields->setLegend('');

//--Create an element for the username
$objInput = new textinput('username', '', 'text', '');
$objInput->extra = 'maxlength="255"';
$objLabel = new label($this->objLanguage->languageText('word_username') . ': ', 'input_username');

//Add the username box to the form
$objFields->addContent($objLabel->show() . '<br />');
$objFields->addContent($objInput->show() . '<br />');

//--- Create an element for the password
$objInput = new textinput('password', '', 'password', '');
$objInput->extra = 'maxlength="255"';
$objLabel = new label($this->objLanguage->languageText('word_password') . ': ', 'input_password');
//Add the password box to the form
$objFields->addContent($objLabel->show() . '<br />');
$objFields->addContent($objInput->show());

//--- Create an element for the remember me checkbox
$objRElement = new checkbox("remember");
$objRElement->label = $this->objLanguage->languageText("phrase_rememberme", "security");
$rem = $objRElement->label . ' ' . $objRElement->show() . "<br />";

//--- Create a submit button
$objButton = '<input type="submit" value="' . $this->objLanguage->languageText("word_login") . '" />';

// Add the button to the form
$objFields->addContent('<br />' . $rem . $objButton . '<br/>');
$objForm->addToForm($objFields->show());

echo $objForm->show();
?>
