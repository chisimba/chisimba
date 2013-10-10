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
$headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
$this->appendArrayVar('headerParams', $headerParams);

// set up html elements
$objIcon = $this->newObject('geticon', 'htmlelements');
$objHeader = $this->loadClass('htmlheading', 'htmlelements');
$objTable = $this->loadClass('htmltable', 'htmlelements');
$objInput = $this->loadClass('textinput', 'htmlelements');
$objButton = $this->loadClass('button', 'htmlelements');
$objForm = $this->loadClass('form', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objLayer = $this->loadClass('layer', 'htmlelements');
$objFieldset = $this->loadClass('fieldset', 'htmlelements');

// set up language items
$heading = $this->objLanguage->languageText('mod_internalmail_managefolders', 'internalmail');
$backLabel = $this->objLanguage->languageText('word_back');
$folderListLabel = $this->objLanguage->languageText('mod_internalmail_folderlist', 'internalmail');
$addFolderLabel = $this->objLanguage->languageText('mod_internalmail_addfolder', 'internalmail');
$editFolderLabel = $this->objLanguage->languageText('mod_internalmail_editfolder', 'internalmail');
$deleteFolderLabel = $this->objLanguage->languageText('mod_internalmail_deletefolder', 'internalmail');
$folderLabel = $this->objLanguage->languageText('word_folder');
$readLabel = $this->objLanguage->languageText('word_read');
$unreadLabel = $this->objLanguage->languageText('word_unread');
$totalLabel = $this->objLanguage->languageText('word_total');
$submitLabel = $this->objLanguage->languageText('word_submit');
$cancelLabel = $this->objLanguage->languageText('word_cancel');
$confirmLabel = $this->objLanguage->languageText('mod_internalmail_confirmfolder', 'internalmail');
$requiredLabel = $this->objLanguage->languageText('mod_internalmail_requiredfolder', 'internalmail');

// set up add icon
$objIcon->title = $addFolderLabel;
$addIcon = $objIcon->getLinkedIcon($this->uri(array(
    'action' => 'managefolders',
    'mode' => 'add',
    'currentFolderId' => $currentFolderId
)) , 'add');

// set up heading
$objHeader = new htmlHeading();
$objHeader->str = $heading.'&#160;'.$addIcon;
$objHeader->type = 1;
$pageData = $objHeader->show();

// set up htmlelements
$objInput = new textinput('folderName', '', '', '60');
$objInput->extra = 'MAXLENGTH="50"';
$folderNameInput = $objInput->show();

// set up folders table
$objTable = new htmltable();
$objTable->id = 'folderList';
$objTable->css_class = 'sorttable';
$objTable->cellpadding = '4';
$objTable->row_attributes = ' name="row_'.$objTable->id.'"';
$objTable->startRow();
$objTable->addCell('<b>'.$folderLabel.'</b>', '50%', '', '', 'heading', '');
$objTable->addCell('<b>'.$unreadLabel.'</b>', '20%', '', 'center', 'heading', '');
$objTable->addCell('<b>'.$totalLabel.'</b>', '20%', '', 'center', 'heading', '');
$objTable->addCell('', '10%', '', '', 'heading', '');
$objTable->endRow();
foreach($arrFolderList as $folder) {
    // set up edit icon
    if ($folder['user_id'] != 'system') {
        $objIcon->title = $editFolderLabel;
        $editIcon = $objIcon->getEditIcon($this->uri(array(
            'action' => 'managefolders',
            'mode' => 'edit',
            'folderId' => $folder['id'],
            'currentFolderId' => $currentFolderId
        )));
    } else {
        $editIcon = '';
    }
    // set up edit input
    if ($mode == 'edit' && $folder['id'] == $folderId) {
        $objInput = new textinput('folderId', $folderId, 'hidden', '');
        $folderName = $objInput->show();

        $objInput = new textinput('folderName', $folder['folder_name'], '', '60');
        $objInput->extra = 'MAXLENGHTH="50"';
        $folderName.= $objInput->show();
    } else {
        $folderName = $folder['folder_name'];
    }
    // set up delete icon
    if ($folder['user_id'] != 'system') {
        $deleteArray = array(
            'action' => 'managefolders',
            'mode' => 'delete',
            'folderId' => $folder['id'],
            'currentFolderId' => $currentFolderId
        );
        $deleteIcon = $objIcon->getDeleteIconWithConfirm('', $deleteArray, 'internalmail', $confirmLabel);
    } else {
        $deleteIcon = '';
    }
    if ($folder['unreadmail'] != 0) {
        $unreadMail = '<span class="unreadmail"><b>'.$folder['unreadmail'].'</b></span>';
    } else {
        $unreadMail = $folder['unreadmail'];
    }
    $objTable->startRow();
    $objTable->addCell($folderName, '', '', '', '', '');
    $objTable->addCell($unreadMail, '', '', 'center', '', '');
    $objTable->addCell($folder['allmail'], '', '', 'center', '', '');
    $objTable->addCell($editIcon.'&#160;'.$deleteIcon, '', '', 'center', '', '');
    $objTable->endRow();
}
if ($mode == 'add') {
    $objTable->startRow();
    $objTable->addCell($folderNameInput, '', '', '', 'odd', '');
    $objTable->addCell('', '', '', 'right', 'odd', '');
    $objTable->addCell('', '', '', 'right', 'odd', '');
    $objTable->addCell('', '', '', 'right', 'odd', '');
    $objTable->endRow();
}
$folderTable = $objTable->show();

$objFieldset = new fieldset();
$objFieldset->contents = $folderTable;
$folderFieldset = $objFieldset->show();

// set up buttons
if ($mode == 'add') {
    $objButton = new button('addbutton', $submitLabel);
    $objButton->setToSubmit();
    $buttons = '<br />'.$objButton->show();

    $objButton = new button('cancelbutton', $cancelLabel);
    $objButton->extra = ' onclick="javascript:$(\'input_cancelbutton\').value=\'Cancel\';
        $(\'form_hiddenform\').submit();"';
    $buttons.= '&#160;'.$objButton->show();
} elseif ($mode == 'edit') {
    $objButton = new button('editbutton', $submitLabel);
    $objButton->setToSubmit();
    $buttons = '<br />'.$objButton->show();

    $objButton = new button('cancelbutton', $cancelLabel);
    $objButton->extra = ' onclick="javascript:
        $(\'input_cancelbutton\').value=\'Cancel\';
        $(\'form_hiddenform\').submit();"';
    $buttons.= '&#160;'.$objButton->show();
} else {
    $buttons = '';
}

// set up form
$objForm = new form('folderform', $this->uri(array(
    'action' => 'managefolders',
    'currentFolderId' => $currentFolderId
)));
$objForm->addToForm($folderFieldset);
$objForm->addToForm($buttons);
if ($mode == 'add' || $mode == 'edit') {
    $objForm->addRule('folderName', $requiredLabel, 'required');
}
$folderForm = $objForm->show();

// hidden element
$objInput = new textinput('cancelbutton', '', 'hidden', '');
$hiddenInput = $objInput->show();

$objForm = new form('hiddenform', $this->uri(array(
    'action' => 'managefolders',
    'currentFolderId' => $currentFolderId
)));
$objForm->addToForm($hiddenInput);
$hiddenForm = $objForm->show();
$pageData.= $folderForm.$hiddenForm;

// set up exit link
$objLink = new link($this->uri(array(
    'action' => 'gotofolder',
    'folderId' => $currentFolderId
)) , 'internalmail');
$objLink->link = $backLabel;
$pageData.= '<br />'.$objLink->show();

$objLayer = new layer();
$objLayer->padding = '10px';
$objLayer->addToStr($pageData);
$pageLayer = $objLayer->show();
echo $pageLayer;
?>