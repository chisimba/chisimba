<?php
$scripts = $this->getJavaScriptFile('jquery-ui-personalized-1.6rc6/jquery-1.3.1.js', 'jquery');
$scripts .= $this->getJavaScriptFile('jquery-ui-personalized-1.6rc6/jquery-ui-personalized-1.6rc6.js', 'jquery');
$scripts .= '<link type="text/css" href="'.$this->getResourceUri('jquery-ui-personalized-1.6rc6/theme/ui.all.css', 'jquery').'" rel="Stylesheet" />';
$this->appendArrayVar('headerParams', $scripts);

$this->appendArrayVar('bodyOnLoad', 'loadRecipientList();');


// security check-must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * @package email
 * Default template for the new email module
 * Author Kevin Cyster
 */

// set up scriptaculous

$headerParams = $this->getJavascriptFile('compose.js', 'internalmail');
$this->appendArrayVar('headerParams', $headerParams);

// set up html elements
$objIcon = $this->newObject('geticon', 'htmlelements');
$objHeader = $this->loadClass('htmlheading', 'htmlelements');
$objTable = $this->loadClass('htmltable', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objInput = $this->loadClass('textinput', 'htmlelements');
$objText = $this->loadClass('textarea', 'htmlelements');
$objTabbedbox = $this->loadClass('tabbedbox', 'htmlelements');
$objFieldset = $this->loadClass('fieldset', 'htmlelements');
$objLayer = $this->loadClass('layer', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader', 'gif');

// set up language items
$heading = $this->objLanguage->languageText('mod_internalmail_compose', 'internalmail');
$backLabel = $this->objLanguage->languageText('word_back');
$composeLabel = $this->objLanguage->languageText('mod_internalmail_compose', 'internalmail');
$toLabel = $this->objLanguage->languageText('word_to');
$subjectLabel = $this->objLanguage->languageText('word_subject');
$messageLabel = $this->objLanguage->languageText('mod_internalmail_message', 'internalmail');
$sendLabel = $this->objLanguage->languageText('word_send');
$cancelLabel = $this->objLanguage->languageText('word_reset');
$requiredLabel = $this->objLanguage->languageText('mod_internalmail_requiredrecipient', 'internalmail');
$searchSurnameLabel = $this->objLanguage->languageText('phrase_searchbysurname');
$emailLabel = $this->objLanguage->languageText('word_email');
$textLabel = $this->objLanguage->languageText('mod_internalmail_text', 'internalmail');
$addressLabel = $this->objLanguage->languageText('mod_internalmail_addressbooks', 'internalmail');
$attachmentsLabel = $this->objLanguage->languageText('word_attachments');
$uploadLabel = $this->objLanguage->languageText('word_upload');
$errorLabel = $this->objLanguage->languageText('mod_internalmail_nofile', 'internalmail');
$filesLabel = $this->objLanguage->languageText('mod_internalmail_attachments', 'internalmail');
$deleteLabel = $this->objLanguage->languageText('phrase_deleteattachment');
$confirmLabel = $this->objLanguage->languageText('mod_internalmail_delattachment', 'internalmail');
$searchFirstnameLabel = $this->objLanguage->languageText('phrase_searchbyfirstname');
$blankErrorLabel = $this->objLanguage->code2Txt('mod_internalmail_blankfile', 'internalmail');

// set up code to text
$array = array(
    'filesize' => $this->maxSize
);
$maxUploadLabel = $this->objLanguage->code2Txt('mod_internalmail_filesize', 'internalmail', $array);
$sizeErrorLabel = $this->objLanguage->code2Txt('mod_internalmail_errorfilesize', 'internalmail', $array);

// set up data
$configs = $this->getSession('configs');
$signature = isset($configs['signature']) ? $configs['signature'] : NULL;
$text = substr($message, (-1*(strlen($signature))));
if ($text != $signature) {
    $message = $message."\n".'--'."\n".$signature;
}

// set up heading
$objHeader = new htmlHeading();
$objHeader->str = $heading;
$objHeader->type = 1;
$pageData = '<div class="internalmail_send_heading">' . $objHeader->show() . '</div>';

// set up html elements
$objInput = new textinput('firstname', '', '', '50');
//$objInput->extra = ' onfocus="javascript:this.value=\'\'" onkeyup="javascript:listfirstname();"';
$firstnameInput = $objInput->show();

$objLayer = new layer();
$objLayer->id = 'firstnameDiv';
$objLayer->value = 'firstnameDiv';
$objLayer->cssClass = 'autocomplete';
$nameLayer = $objLayer->show();

$objInput = new textinput('surname', '', '', '50');
//$objInput->extra = ' onfocus="javascript:this.value=\'\'" onkeyup="javascript:listsurname();"';
$surnameInput = $objInput->show();

$objLayer = new layer();
$objLayer->id = 'surnameDiv';
$objLayer->cssClass = 'autocomplete';
$surnameLayer = $objLayer->show();

$objLayer = new layer();
$objLayer->id = 'add_load';
$objLayer->floating = 'left';
$objLayer->visibility = 'hidden';
$objLayer->str = $objIcon->show();
$loadLayer = $objLayer->show();

// The users inside the to box.
$objLayer = new layer();
$objLayer->id = 'toList';
$objLayer->str = $toList;
$toLayer = $objLayer->show();

$objFieldset = new fieldset();
$objFieldset->extra = ' style="height: 100px; border: 1px solid #808080; margin: 3px; padding: 10px;"';
$objFieldset->contents = $loadLayer.$toLayer;
$toFieldset = $objFieldset->show();

$objInput = new textinput('recipient', $recipientList, 'hidden', '');
$recipientInput = $objInput->show();

$objInput = new textinput('subject', $subject, '', '115');
$subjectInput = $objInput->show();

$objText = new textarea('message', $message, 12, '132');
$messageText = $objText->show();

// set up address book icon
$action = $this->uri(array(
    'action' => 'showbooks'
));
$objIcon->title = $addressLabel;
$objIcon->setIcon('addressbook', 'png');
$objIcon->extra=' onclick="javascript:
    $(\'#form_composeform\').attr(\'action\',\''.$action.'\');
    $(\'#form_composeform\').submit();"';
$addressIcon='<a href="#">'.$objIcon->show().'</a>';

// set up search fieldset
$objTable = new htmltable();
$objTable->cellpadding = '4';
$objTable->startRow();
$objTable->addCell($textLabel, '', '', '', 'warning', 'colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($firstnameInput.$nameLayer, '50%', '', '', '', '');
$objTable->endRow();
$searchTable = $objTable->show();

$objFieldset = new fieldset();

$objFieldset->extra = ' style="border: 1px solid #808080; margin: 3px; padding: 10px;"';
$objFieldset->legend = '<b>'.$searchFirstnameLabel.'</b>';
$objFieldset->contents = $searchTable;
$searchFieldset = $objFieldset->show();

// set up search fieldset
$objTable = new htmltable();
$objTable->cellpadding = '4';
$objTable->startRow();
$objTable->addCell($textLabel, '', '', '', 'warning', 'colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($surnameInput.$surnameLayer, '50%', '', '', '', '');
$objTable->endRow();
$searchTable = $objTable->show();

$objFieldset = new fieldset();
$objFieldset->extra = ' style="border: 1px solid #808080; margin: 3px; padding: 10px;"';
$objFieldset->legend = '<b>'.$searchSurnameLabel.'</b>';
$objFieldset->contents = $searchTable;
$searchFieldset.= $objFieldset->show();

$f = '<form id="searchform" name="searchform" autocomplete="off">
				<p>
					<label>Search Users</label><br/>
					<input type="text" id="suggest4">
					<input type="hidden" id="hiddensuggest4" name="username">
					<input id="searchbutton" type="button" onclick="submitSearchForm(this.form)" value="Add Recipient" />
				</p>
			</form>';
$searchFieldset = $f;

// set up tables and tabbedboxes
$objTable = new htmltable();
$objTable->cellpadding = '4';
$objTable->startRow();
$objTable->addCell($searchFieldset, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>'.$toLabel.':</b><br />'.$recipientInput, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($toFieldset, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>'.$subjectLabel.':</b><br />'.$subjectInput, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>'.$messageLabel.':</b><br />'.$messageText, '', '', '', '', '');
$objTable->endRow();
$emailTable = $objTable->show();

$objTabbedbox = new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel($addressIcon);
$objTabbedbox->addBoxContent($emailTable);
$emailTab = $objTabbedbox->show();

$objTable = new htmltable();
$objTable->cellpadding = '4';
$objTable->startRow();
$objTable->addCell($emailTab, '', '', '', '', '');
$objTable->endRow();
$composeTable = $objTable->show();

// set up attachments tabbed box
$objInput = new textinput('attachment', '', 'file', '50');
$objInput->extra = ' maxlength="100"';
$attachInput = $objInput->show();
$action = $this->uri(array(
    'action' => 'upload'
));

$objButton = new button('upload', $uploadLabel);
$objButton->extra = ' onclick="javascript:
    $(\'#form_composeform\').attr(\'action\',\''.$action.'\');
    $(\'#form_composeform\').submit();"';
$uploadButton = $objButton->show();

$objTable = new htmltable();
$objTable->cellpadding = '4';
if ($error) {
    $objTable->startRow();
    $objTable->addCell('<b>'.$error.'</b>', '', '', '', 'error', 'colspan="3"');
    $objTable->endRow();
}
$objTable->startRow();
$objTable->addCell($attachInput.'&#160;&#160;'.$uploadButton, '', '', '', '', 'colspan="3"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>'.$maxUploadLabel.'</b>', '', '', '', 'warning', 'colspan="3"');
$objTable->endRow();
if ($emailId != NULL) {
    $this->emailFiles->createAttachments($emailId);
}
$attachments = $this->emailFiles->getAttachments();
if ($attachments != NULL) {
    $objTable->startRow();
    $objTable->addCell('<b>'.$filesLabel.'</b>', '', '', '', '', 'colspan="3"');
    $objTable->endRow();
    $i = 1;
    foreach($attachments as $attachment) {
        $deleteArray = $this->uri(array(
            'action' => 'deleteattachment',
            'file' => $attachment['filename'],
        ));
        $objIcon->title = $deleteLabel;
        $objIcon->setIcon('delete', 'png');
        $objIcon->extra = ' onclick="javascript:
            if(confirm(\''.$confirmLabel.'\')){
               	$(\'#form_composeform\').attr(\'action\',\''.$deleteArray.'\');
                $(\'#form_composeform\').submit();
            }"';
        $deleteIcon = '<a href="#">'.$objIcon->show() .'</a>';
        $objTable->startRow();
        $objTable->addCell($i++.'.', '3%', '', '', '', '');
        $objTable->addCell($attachment['filename'], '50%', '', '', '', '');
        $objTable->addCell($deleteIcon, '', '', 'left', '', '');
        $objTable->endRow();
    }
}
$fileTable = $objTable->show();

$objTabbedbox = new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel($attachmentsLabel);
$objTabbedbox->addBoxContent($fileTable);
$attachmentsTab = $objTabbedbox->show();

$objTable = new htmltable();

$objTable->cellpadding = '4';
$objTable->startRow();
$objTable->addCell($attachmentsTab, '', '', '', '', '');
$objTable->endRow();
$attachmentsTable = $objTable->show();

// set up buttons
$objButton = new button('submitbutton', $sendLabel);
$objButton->extra = ' onclick="SubmitForm()"';

$buttons = '<br />'.$objButton->show();

$objButton = new button('cancelbutton', $cancelLabel);
$objButton->extra = ' onclick="javascript:
	 $(\'#form_composeform\')[0].reset();"';
$buttons.= '&#160;'.$objButton->show();

// set up form
$objInput = new textinput('sendbutton', '', 'hidden', '');
$hiddenInput = $objInput->show();

$objForm = new form('composeform', $this->uri(array(
    'action' => 'sendemail'
)));
$objForm->extra = ' enctype="multipart/form-data"';
$objForm->addToForm($composeTable);
$objForm->addToForm($attachmentsTable);
$objForm->addToForm($hiddenInput);


$objForm->addToForm($buttons);
$composeForm = $objForm->show();

// set up hidden form
$objInput = new textinput('cancelbutton', '', 'hidden', '');
$hiddenInput = $objInput->show();

$objForm = new form('hiddenform', $this->uri(array(
    'action' => 'sendemail'
)));
$objForm->addToForm($hiddenInput);
$hiddenForm = $objForm->show();
$pageData.= $composeForm.$hiddenForm;

// set up exit link
$objLink = new link($this->uri(array(
    ''
) , 'internalmail'));
$objLink->link = $backLabel;
$pageData.= '<br />'.$objLink->show();

$objLayer = new layer();
$objLayer->cssClass='internalmail';
$objLayer->padding = '10px';
$objLayer->str = $pageData;
$pageLayer = $objLayer->show();
echo $pageLayer;



$script = $this->getJavaScriptFile('jquery.autocomplete.js', 'jquery');
$this->appendArrayVar('headerParams', $script);
$str = '<link rel="stylesheet" href="'.$this->getResourceUri('jquery.autocomplete.css', 'jquery').'" type="text/css" />';
$this->appendArrayVar('headerParams', $str);


	$str = '<script type="text/javascript">

        function SubmitForm()
        {
          document.composeform.submit();
       }


$().ready(function() {

	function findValueCallback(event, data, formatted) {
		$("<li>").html( !data ? "No match!" : "Selected: " + formatted).appendTo("#result");
	}

	function formatItem(row) {
		return row[0] + " (<strong>username: " + row[1] + "</strong>)";
	}
	function formatResult(row) {
		//return row[0].replace(/(<.+?>)/gi, \'\');
		return row[0];
	}

$(":text, textarea").result(findValueCallback).next().click(function() {
		$(this).prev().search();
	});

	$("#suggest4").autocomplete(\'index.php?module=internalmail&action=searchusers\').result(function (evt, data, formatted) {

					$("#hiddensuggest4").val(data[1]);
					});
/*, {
		width: 300,
		multiple: false,
		matchContains: true,
		formatItem: formatItem,
		formatResult: formatResult,

	}).result(function (evt, data, formatted) {

					$("#hiddensuggest4").val(data[1]);
					});

*/
	$("#clear").click(function() {
		$(":input").unautocomplete();
	});
});

function submitSearch(data)
{

	alert(data[0]);
}


function changeOptions(){
	var max = parseInt(window.prompt(\'Please type number of items to display:\', jQuery.Autocompleter.defaults.max));
	if (max > 0) {
		$("#suggest1").setOptions({
			max: max
		});
	}
}

function submitSearchForm(frm)
{
	username = frm.hiddensuggest4.value;
	//groupId = frm.groupid.value;
	if(username)
	{

		addRecipient(username);
	}

	frm.hiddensuggest4.value = "";
	frm.suggest4.value = "";

}
	</script>';
	$this->appendArrayVar('headerParams', $str);
?>
