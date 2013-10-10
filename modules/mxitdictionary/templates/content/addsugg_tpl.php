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
	/**
	 * Model extension of controller that displays the interface for adding entries
	 * @authors: Qhamani Fenama
	 * @copyright 2007 University of the Western Cape
	 * @modified to work with the mxitdictionary module by: Qhamani Fenama.
	 */
	$objmsg = $this->getObject('timeoutmessage', 'htmlelements');
	$cssLayout = &$this->newObject('csslayout', 'htmlelements');
	// Set columns to 2
	$cssLayout->setNumColumns(2);
	// get the sidebar object
	$this->leftMenu = $this->newObject('usermenu', 'toolbar');
	// Initialize left column
	$leftSideColumn = $this->leftMenu->show();
	$rightSideColumn = NULL;
	$middleColumn = NULL;

	// Create link icon and link to view template
	$this->loadClass('link', 'htmlelements');
	$objIcon = $this->newObject('geticon', 'htmlelements');
	$link = new link($this->uri(array('action' => 'default')));
	$objIcon->setIcon('prev');
	$link->link = $objIcon->show();
	$update = $link->show();
	// Create header with add icon
	$pgTitle = &$this->getObject('htmlheading', 'htmlelements');
	$pgTitle->type = 1;
	$pgTitle->str = $objLanguage->languageText('mod_mxit_return', 'mxitdictionary'). "&nbsp;" . $update;
	$this->objUser = $this->getObject('user', 'security');
	$cform = new form('mxitdictionary', $this->uri(array('action' => 'suggest')));


	//start a fieldset
	$cfieldset = $this->getObject('fieldset', 'htmlelements');
	$ct = $this->newObject('htmltable', 'htmlelements');
	$ct->cellpadding = 5;
	//value textfield
	$ct->startRow();
	$ctvlabel = new label($this->objLanguage->languageText('mod_mxit_word', 'mxitdictionary').':','input_cvalue');
	$ctv = new textinput('word');
	$ct->addCell($ctvlabel->show());
	$ct->addCell($ctv->show());
	$ct->endRow();
	//value textfield
	$ct->startRow();
	$ctvlabel = new label($this->objLanguage->languageText('mod_mxit_definition', 'mxitdictionary').':','input_cvalue');
	$ctv = new textinput('definition');
	$ct->addCell($ctvlabel->show());
	$ct->addCell($ctv->show());
	$ct->endRow();

	//end off the form and add the button
	$this->objconvButton = new button($this->objLanguage->languageText('mod_mxit_wordsave', 'mxitdictionary'));
	$this->objconvButton->setValue($this->objLanguage->languageText('mod_mxit_wordsave', 'mxitdictionary'));
	$this->objconvButton->setToSubmit();
	$cfieldset->addContent($ct->show());
	$cform->addToForm($cfieldset->show());

	$cform->addToForm('<br />');

	$objCaptcha = $this->getObject('captcha', 'utilities');
	$captcha = new textinput('request_captcha');
	$captchaLabel = new label($this->objLanguage->languageText('phrase_verifyrequest', 'security', 'Verify Request'), 'input_request_captcha');

	$fieldset = $this->newObject('fieldset', 'htmlelements');
	$fieldset->legend = 'Verify Image';
	$fieldset->contents = stripslashes($this->objLanguage->languageText('mod_security_explaincaptcha', 'security', 'To prevent abuse, please enter the code as shown below. If you are unable to view the code, click on "Redraw" for a new one.')).'<br /><div id="captchaDiv">'.$objCaptcha->show().'</div>'.$captcha->show().$required.'  <a href="javascript:redraw();">'.$this->objLanguage->languageText('word_redraw', 'security', 'Redraw').'</a>';


	$cform->addToForm($fieldset->show());

	if ($mode == 'addfixup') {

		foreach ($problems as $problem)
		{
		    $messages[] = $this->explainProblemsInfo($problem);
		}

	}

	if ($mode == 'addfixup' && count($messages) > 0) {
		echo '<ul><li><span class="error">'.$this->objLanguage->languageText('mod_userdetails_infonotsavedduetoerrors', 'userdetails').'</span>';

		echo '<ul>';
		    foreach ($messages as $message)
		    {
		        if ($message != '') {
		            echo '<li class="error">'.$message.'</li>';
		        }
		    }

		echo '</ul></li></ul>';
	}

	$cform->addToForm($this->objconvButton->show());
	$cform = $cform->show();
	//create a featurebox and show all the input
	$objFeatureBox = $this->getObject('featurebox', 'navigation');
	$ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_mxit_addsugg", "mxitdictionary") , $cform);
	$middleColumn = $pgTitle->show().$ret;
	// Create link back to my view template
	$objBackLink = &$this->getObject('link', 'htmlelements');
	$objBackLink->link($this->uri(array('module' => 'mxit')));
	$objBackLink->link = $objLanguage->languageText('mod_mxit_return', 'mxitdictionary');
	//add left column
	$cssLayout->setLeftColumnContent($leftSideColumn);
	$cssLayout->setRightColumnContent($rightSideColumn);
	//add middle column
	$cssLayout->setMiddleColumnContent($middleColumn);
	echo $cssLayout->show();
?>
