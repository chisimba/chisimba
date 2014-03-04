<?php
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
// set up javascript headers
$headerParams = $this->getJavascriptFile('selectall.js', 'htmlelements');
$this->appendArrayVar('headerParams', $headerParams);
$headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
$this->appendArrayVar('headerParams', $headerParams);
//$this->objScriptaculous =& $this->getObject('scriptaculous', 'ajaxwrapper');
//$this->objScriptaculous->show();

$headerParams = $this->getJavascriptFile('entries.js', 'internalmail');
$this->appendArrayVar('headerParams', $headerParams);

// set up style for autocomplete
$style = '<style type="text/css">
    div.autocomplete {
        position:absolute;
        background-color:white;
    }    
    div.autocomplete ul {
        list-style-type:none;
        margin:0px;
        padding:0px;
    }    
    div.autocomplete ul li.selected {
        border:1px solid #888;
        background-color: #ffb;
    }
    div.autocomplete ul li {
        border:1px solid #888;
        list-style-type:none;
        display:block;
        margin:0;
        cursor:pointer;
    }
</style>';
echo $style;

// set up html elements
$objHeader = $this->loadClass('htmlheading', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objTable = $this->loadClass('htmltable', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objInput = $this->loadClass('textinput', 'htmlelements');
$objEditor = $this->loadClass('htmlarea', 'htmlelements');
$objTabbedbox = $this->loadClass('tabbedbox', 'htmlelements');
$objCheck = $this->loadClass('checkbox', 'htmlelements');
$objRadio = $this->loadClass('radio', 'htmlelements');
$objFieldset = $this->loadClass('fieldset', 'htmlelements');
$objLayer = $this->loadClass('layer', 'htmlelements');

// set up language items
$heading = $this->objLanguage->languageText('mod_internalmail_addressbookentries', 'internalmail');
$backLabel = $this->objLanguage->languageText('word_back');
$submitLabel = $this->objLanguage->languageText('word_submit');
$cancelLabel = $this->objLanguage->languageText('word_cancel');
$searchSurnameLabel = $this->objLanguage->languageText('phrase_searchbysurname');
$searchNameLabel = $this->objLanguage->languageText('phrase_searchbyfirstname');
$searchUsernameLabel = $this->objLanguage->languageText('phrase_searchbyusername');
$addEntryLabel = $this->objLanguage->languageText('mod_internalmail_addentry', 'internalmail');
$deleteEntryLabel = $this->objLanguage->languageText('mod_internalmail_deleteentry', 'internalmail');
$confirmLabel = $this->objLanguage->languageText('mod_internalmail_confirmentry', 'internalmail');
$usernameLabel = $this->objLanguage->languageText('word_username');
$fullnameLabel = $this->objLanguage->languageText('phrase_fullname');
$noEntriesLabel = $this->objLanguage->languageText('mod_internalmail_noentries', 'internalmail');
$selectallLabel = $this->objLanguage->languageText('phrase_selectall');
$deselectLabel = $this->objLanguage->languageText('phrase_deselectall');
$sendMailLabel = $this->objLanguage->languageText('phrase_sendmail');
$surnameLabel = $this->objLanguage->languageText('word_surname');
$nameLabel = $this->objLanguage->languageText('phrase_firstname');

// set up add icon
if($mode == 'show'){
    $addIcon = '';
}else{
    $objIcon->title = $addEntryLabel;
    $addIcon = $objIcon->getLinkedIcon($this->uri(array(
        'action' => 'addentry',
        'bookId' => $bookId,
        'currentFolderId' => $currentFolderId,
    )) , 'add');
}
if ($bookId != NULL) {
    $arrBookData = $this->dbBooks->getBook($bookId);
    $subHeading = $arrBookData['book_name'];
} else {
    $addIcon = '';
    $arrContextData = $this->objContext->getContextDetails($contextCode);
    $subHeading = $arrContextData['menutext'];
}
if($mode == 'show'){
    $currentFolderId = '';
}

// set up heading
$objHeader = new htmlHeading();
$objHeader->str = $heading;
$objHeader->type = 1;
$pageData = $objHeader->show();

$objHeader = new htmlHeading();
$objHeader->str = $subHeading.'&#160;'.$addIcon;
$objHeader->type = 3;
$pageData.= $objHeader->show();

// set up input table
if ($mode == 'add') {
    // set up username input
    $objInput = new textinput('username', '', '', '50');
    $objInput->extra = 'onkeyup="javascript:listusername();"';
    $usernameInput = $objInput->show();
    
    $objLayer = new layer();
    $objLayer->id = 'usernameDiv';
    $objLayer->cssClass = 'autocomplete';
    $usernameLayer = $objLayer->show();

    $objTable = new htmltable();
    $objTable->cellpadding = '4';
    $objTable->startRow();
    $objTable->addCell($usernameInput.$usernameLayer, '50%', '', '', '', '');
    $objTable->endRow();
    $usernameTable = $objTable->show();

    $objFieldset = new fieldset();
    $objFieldset->extra = ' style="border: 1px solid #808080; margin: 3px; padding: 10px;"';
    $objFieldset->legend = '<b>'.$searchUsernameLabel.'</b>';
    $objFieldset->contents = $usernameTable;
    $usernameFieldset = $objFieldset->show();

    // set up firstname input
    $objInput = new textinput('firstname', '', '', '50');
    $objInput->extra = 'onkeyup="javascript:listfirstname();"';
    $firstnameInput = $objInput->show();

    $objLayer = new layer();
    $objLayer->id = 'firstnameDiv';
    $objLayer->cssClass = 'autocomplete';
    $firstnameLayer = $objLayer->show();

    $objTable = new htmltable();
    $objTable->cellpadding = '4';
    $objTable->startRow();
    $objTable->addCell($firstnameInput.$firstnameLayer, '50%', '', '', '', '');
    $objTable->endRow();
    $firstnameTable = $objTable->show();

    $objFieldset = new fieldset();
    $objFieldset->extra = ' style="border: 1px solid #808080; margin: 3px; padding: 10px;"';
    $objFieldset->legend = '<b>'.$searchNameLabel.'</b>';
    $objFieldset->contents = $firstnameTable;
    $nameFieldset = $objFieldset->show();

    // set up surname input
    $objInput = new textinput('surname', '', '', '50');
    $objInput->extra = 'onkeyup="javascript:listsurname();"';
    $surnameInput = $objInput->show();

    $objLayer = new layer();
    $objLayer->id = 'surnameDiv';
    $objLayer->cssClass = 'autocomplete';
    $surnameLayer = $objLayer->show();

    $objTable = new htmltable();
    $objTable->cellpadding = '4';
    $objTable->startRow();
    $objTable->addCell($surnameInput.$surnameLayer, '50%', '', '', '', '');
    $objTable->endRow();
    $surnameTable = $objTable->show();

    $objFieldset = new fieldset();
    $objFieldset->extra = ' style="border: 1px solid #808080; margin: 3px; padding: 10px;"';
    $objFieldset->legend = '<b>'.$searchSurnameLabel.'</b>';
    $objFieldset->contents = $surnameTable;
    $surnameFieldset = $objFieldset->show();

    // set up table
    $objTable = new htmltable();
    $objTable->cellpadding = '4';
    $objTable->startRow();
    $objTable->addCell($usernameFieldset, '', '', '', '', '');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell($nameFieldset, '', '', '', '', '');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell($surnameFieldset, '', '', '', '', '');
    $objTable->endRow();
    $entryTable = $objTable->show();

    // set up hidden userid input
    $objInput = new textinput('userid', '', 'hidden', '');
    $useridInput = $objInput->show();

    $objButton = new button('addbutton', $submitLabel);
    $objButton->setToSubmit();
    $buttons = '<br />'.$objButton->show();

    $objButton = new button('cancelbutton', $cancelLabel);
    $objButton->extra = ' onclick="javascript:
        $(\'input_cancelbutton\').value=\'Cancel\';
        $(\'form_hiddenform\').submit();"';
    $buttons.= '&#160;'.$objButton->show();

    // set up form
    $objForm = new form('entryform', $this->uri(array(
        'action' => 'submitentry',
        'currentFolderId' => $currentFolderId,
        'bookId' => $bookId
    )));
    $objForm->addToForm($entryTable);
    $objForm->addToForm($useridInput);
    $objForm->addToForm($buttons);
    $entryForm = $objForm->show();

    // hidden element
    $objInput = new textinput('cancelbutton', '', 'hidden', '');
    $hiddenInput = $objInput->show();

    $objForm = new form('hiddenform', $this->uri(array(
        'action' => 'addressbook',
        'currentFolderId' => $currentFolderId,
        'bookId' => $bookId
    )));
    $objForm->addToForm($hiddenInput);
    $hiddenForm = $objForm->show();

    $objTabbedbox = new tabbedbox();
    $objTabbedbox->extra = 'style="padding: 10px;"';
    $objTabbedbox->addTabLabel($addEntryLabel);
    $objTabbedbox->addBoxContent($entryForm.$hiddenForm);
    $entryTab = $objTabbedbox->show();
    $pageData.= $entryTab;
}

// set up check all button
$objButton = new button('checkallbutton', $selectallLabel);
$objButton->setOnClick('javascript:
    SetAllCheckBoxes(\'sendform\',\'userId[]\',true);');
$selectAllButton = $objButton->show();

// set up uncheck all button
$objButton = new button('uncheckallbutton', $deselectLabel);
$objButton->setOnClick('javascript:
    SetAllCheckBoxes(\'sendform\',\'userId[]\',false);');
$selectNoneButton = $objButton->show();

// set up send button
$objButton = new button('sendmail', $sendMailLabel);
$objButton->setToSubmit();
$sendButton = $objButton->show();
$buttons = $selectAllButton.'&#160;'.$selectNoneButton.'&#160;'.$sendButton;

// set up user list tabel
$objTable = new htmltable();
$objTable->cellpadding = '4';
$objTable->id = 'userListTable';
$objTable->css_class = 'sorttable';
$objTable->row_attributes = ' name="row_'.$objTable->id.'"';
$objTable->startRow();
$objTable->addCell('', '5%', '', '', 'heading', '');
$objTable->addCell($usernameLabel, '30%', '', '', 'heading', '');
$objTable->addCell($nameLabel, '30%', '', '', 'heading', '');
$objTable->addCell($surnameLabel, '', '', '', 'heading', '');
if (empty($contextCode)) {
    $objTable->addCell('', '10%', '', '', 'heading', '');
}
$objTable->endRow();
if (!empty($contextCode)) {
    if (empty($arrContextUserList)) {
        $objTable->startRow();
        $objTable->addCell($noEntriesLabel, '', '', '', 'noRecordsMessage', 'colspan="5"');
        $objTable->endRow();
    } else {
        foreach($arrContextUserList as $user) {
            // set up checkbox
            $objCheck = new checkbox('userId[]');
            $objCheck->value = $user['userid'];
            $userCheck = $objCheck->show();
            $objTable->startRow();
            $objTable->addCell($userCheck, '', '', 'center', '', '');
            $objTable->addCell($user['username'], '', '', '', '', '');
            $objTable->addCell(strtoupper($user['firstname']) , '', '', '', '', '');
            $objTable->addCell(strtoupper($user['surname']) , '', '', '', '', '');
            $objTable->endRow();
        }
    }
} else {
    if (empty($arrBookEntryList)) {
        $objTable->startRow();
        $objTable->addCell($noEntriesLabel, '', '', '', 'noRecordsMessage', 'colspan="5"');
        $objTable->endRow();
    } else {
        $i = 1;
        foreach($arrBookEntryList as $entry) {
            // set up delete icon
            $deleteArray = array(
                'action' => 'deleteentry',
                'bookId' => $bookId,
                'entryId' => $entry['id'],
                'currentFolderId' => $currentFolderId,
            );
            $deleteIcon = $objIcon->getDeleteIconWithConfirm('', $deleteArray, 'internalmail', $confirmLabel);
            if($mode == 'show'){
                $deleteIcon = '';
            }

            // set up checkbox
            $objCheck = new checkbox('userId[]');
            $objCheck->value = $entry['recipient_id'];
            $userCheck = $objCheck->show();
            $objTable->startRow();
            $objTable->addCell($userCheck, '', '', 'center', '', '');
            $objTable->addCell($this->objUser->userName($entry['recipient_id']) , '', '', '', '', '');
            $objTable->addCell(strtoupper($this->objUser->getFirstname($entry['recipient_id'])) , '', '', '', '', '');
            $objTable->addCell(strtoupper($this->objUser->getSurname($entry['recipient_id'])) , '', '', '', '', '');
            $objTable->addCell($deleteIcon, '', '', 'center', '', '');
            $objTable->endRow();
        }
    }
}
$userTable = $objTable->show();

// set up form
if($mode == 'show'){
    $objForm = new form('sendform', $this->uri(array(
        'action' => 'compose',
        'subject' => $subject,
        'message' => $message,
        'recipientList' => $recipientList,
    )));
}else{
    $objForm = new form('sendform', $this->uri(array(
        'action' => 'compose',
    )));
}
if (!empty($arrContextUserList) || !empty($arrBookEntryList)) {
    $objForm->addToForm($buttons);
}
$objForm->addToForm($userTable);
if (!empty($arrContextUserList) || !empty($arrBookEntryList)) {
    $objForm->addToForm($buttons);
}
$sendForm = $objForm->show();

$objFieldset = new fieldset();
$objFieldset->contents = $sendForm;
$sendFieldset = $objFieldset->show();
$pageData.= $sendFieldset;

// set up exit link
if($mode == 'show'){
    $objLink = new link($this->uri(array(
        'action' => 'compose',
        'userId' => $recipientList,
        'subject' => $subject,
        'message' => $message,
    ) , 'internalmail'));    
}else{
    $objLink = new link($this->uri(array(
        'action' => 'manageaddressbooks',
        'currentFolderId' => $currentFolderId,
    ) , 'internalmail'));
}
$objLink->link = $backLabel;
$pageData.= '<b />'.$objLink->show();

$objLayer = new layer();
$objLayer->padding = '10px';
$objLayer->addToStr($pageData);
$pageLayer = $objLayer->show();
echo $pageLayer;
?>