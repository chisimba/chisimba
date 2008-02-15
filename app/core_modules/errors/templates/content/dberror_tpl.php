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
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$userMenu  = &$this->newObject('usermenu','toolbar');
$objTextArea = $this->loadclass('textarea', 'htmlelements');
$objHiddenInput = $this->loadclass('hiddeninput', 'htmlelements');
$objUser  = $this->getObject('user','security');
$link = $this->loadClass('href', 'htmlelements');

$objForm = new form('errormail',$this->uri(array('action'=>'errormail')));
$objTextArea = new textarea('comments','');

// Create an instance of the css layout class
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
// Set columns to 2
$cssLayout->setNumColumns(2);

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_errors_heading', 'errors');


// Add Post login menu to left column
$leftSideColumn ='';
if($objUser->isLoggedIn())
{
	$leftSideColumn = $userMenu->show();
}
else {
	$linkhome = new href($this->objConfig->getSiteRoot(), $this->objLanguage->languageText("word_home", "system"));
	$leftSideColumn = $linkhome->show();
}

$midcol = $header->show();

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

$this->href = $this->getObject('href', 'htmlelements');

$devmsg = htmlentities(stripslashes(urldecode($devmsg)));
$objHiddenInput = new hiddeninput('error', htmlentities($devmsg));
$objHiddenInput2 = new hiddeninput('server', $_SERVER['HTTP_HOST']);
$usrmsg = urldecode($usrmsg);
$devmsg = nl2br($devmsg);
$usrmsg = nl2br($usrmsg);

$blurb = $this->objLanguage->languagetext("mod_errors_blurb", "errors");
//$midcol .= $blurb;
$midcol .= $objFeatureBox->show($this->objLanguage->languagetext("mod_errors_usrtitle", "errors"), $usrmsg);//'<div class="featurebox">' . nl2br($usrmsg) . '</div>';
$midcol .= $objFeatureBox->show($this->objLanguage->languagetext("mod_errors_devtitle", "errors"), $devmsg);//'<div class="featurebox">' . nl2br($devmsg) . '</div>';

//$logfile = htmlentities(file_get_contents('error_log/system_errors.log'));
//$midcol .= $objFeatureBox->show($this->objLanguage->languagetext("mod_errors_logfiletitle", "errors"), $logfile);

// CAPTCHA
$objCaptcha = $this->getObject('captcha', 'utilities');
$captcha = new textinput('request_captcha');
$captchaLabel = NULL; // new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request'), 'input_request_captcha');
$cap = stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.')).'<br /><div id="captchaDiv">'.$objCaptcha->show().'</div>'.$captcha->show().$required.'  <a href="javascript:redraw();">'.$this->objLanguage->languageText('word_redraw', 'security', 'Redraw').'</a>';

//create the form
$objForm->displayType = 4;
$objForm->addToFormEx($objLanguage->languageText('mod_errors_submiterrs', 'errors'));
$objForm->addToFormEx($objTextArea->show());
$objForm->addToFormEx($captchaLabel, $cap);
$objForm->addToFormEx($objHiddenInput->show());
$objForm->addToFormEx($objHiddenInput2->show());
$objForm->addRule('request_captcha', $this->objLanguage->languageText("mod_blogcomments_captchaval",'blogcomments'), 'required');

$this->objButton=&new button($objLanguage->languageText('word_submit', 'system'));
$this->objButton->setValue($objLanguage->languageText('word_submit', 'system'));
$this->objButton->setToSubmit();
$objForm->addToFormEx($this->objButton->show());


$midcol .= $objFeatureBox->show($this->objLanguage->languagetext("mod_errors_mailadmins", "errors"),$objForm->show());
$cssLayout->setMiddleColumnContent($midcol);

echo $cssLayout->show();
?>