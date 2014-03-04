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

// set up scriptaculous
//$this->objScriptaculous =& $this->getObject('scriptaculous', 'ajaxwrapper');
//$this->objScriptaculous->show();

$headerParams = $this->getJavascriptFile('rules.js', 'internalmail');
$this->appendArrayVar('headerParams', $headerParams);

// set up html elements
$objIcon = $this->newObject('geticon', 'htmlelements');
$objHeader = $this->loadClass('htmlheading', 'htmlelements');
$objTable = $this->loadClass('htmltable', 'htmlelements');
$objInput = $this->loadClass('textinput', 'htmlelements');
$objDrop = $this->loadClass('dropdown', 'htmlelements');
$objButton = $this->loadClass('button', 'htmlelements');
$objForm = $this->loadClass('form', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objLayer = $this->loadClass('layer', 'htmlelements');
$objFieldset = $this->loadClass('fieldset', 'htmlelements');

// set up language items
$backLabel = $this->objLanguage->languageText('word_back');
$submitLabel = $this->objLanguage->languageText('word_submit');
$cancelLabel = $this->objLanguage->languageText('word_cancel');
$addRuleLabel = $this->objLanguage->languageText('mod_internalmail_addrule', 'internalmail');
$editRuleLabel = $this->objLanguage->languageText('mod_internalmail_editrule', 'internalmail');
$emailLabel = $this->objLanguage->languageText('word_email');
$incomingLabel = $this->objLanguage->languageText('phrase_email');
$outgoingLabel = $this->objLanguage->languageText('phrase_sentemail');
$norulesLabel = $this->objLanguage->languageText('mod_internalmail_norules', 'internalmail');
$fieldLabel = $this->objLanguage->languageText('word_field');
$criteriaLabel = $this->objLanguage->languageText('word_criteria');
$actionLabel = $this->objLanguage->languageText('word_action');
$destinationLabel = $this->objLanguage->languageText('word_destination');
$toLabel = $this->objLanguage->languageText('word_to');
$fromLabel = $this->objLanguage->languageText('word_from');
$subjectLabel = $this->objLanguage->languageText('word_subject');
$messageLabel = $this->objLanguage->languageText('word_message');
$selectLabel = $this->objLanguage->languageText('word_select');
$notApplicableLabel = $this->objLanguage->languageText('phrase_notapplicable');
$allMessagesLabel = $this->objLanguage->languageText('phrase_allmessages');
$filteredMessagesLabel = $this->objLanguage->languageText('phrase_filteredmessages');
$messagesLabel = $this->objLanguage->languageText('word_messages');
$moveLabel = $this->objLanguage->languageText('word_move');
$readLabel = $this->objLanguage->languageText('phrase_markasread');
$attachmentsLabel = $this->objLanguage->languageText('word_attachments');

// set up heading
$objHeader = new htmlHeading();
if ($mode == 'addrule') {
    $objHeader->str = $addRuleLabel;
} else {
    $objHeader->str = $editRuleLabel;
}
$objHeader->type = 1;
$pageData = $objHeader->show();
// get data
$rule = $this->dbRules->getRule($ruleId);
if ($mode == 'addrule') {
    $mailAction = '';
    $messageField = '';
} else {
    $mailAction = $rule['mail_action'];
    if ($rule['mail_field'] == NULL) {
        $messageField = 1;
    } else {
        $messageField = 2;
    }
}

// set up rules
$objDrop = new dropdown('mailAction');
$objDrop->addOption(NULL, '- '.$selectLabel.' -');
$objDrop->addOption(1, $incomingLabel);
$objDrop->addOption(2, $outgoingLabel);
$objDrop->setSelected($mailAction);
$objDrop->extra = 'onchange="javascript:mailaction();"';
$mailDrop = $objDrop->show();

$objDrop = new dropdown('messageField');
$objDrop->addOption(NULL, '- '.$selectLabel.' -');
$objDrop->addOption(1, $allMessagesLabel);
$objDrop->addOption(2, $filteredMessagesLabel);
$objDrop->setSelected($messageField);
$objDrop->extra = 'onchange="javascript:messagefield();"';
$messageDrop = $objDrop->show();

if ($mode == 'editrule') {
    if ($rule['mail_field'] != NULL) {
        $objDrop = new dropdown('mailField');
        $objDrop->addOption(NULL, '- '.$selectLabel.' -');
        $objDrop->addOption(1, $toLabel);
        $objDrop->addOption(2, $fromLabel);
        $objDrop->addOption(3, $subjectLabel);
        $objDrop->addOption(4, $messageLabel);
        $objDrop->addOption(5, $attachmentsLabel);
        $objDrop->setSelected($rule['mail_field']);
        $objDrop->extra = ' onchange="javascript:
            mailfield();"';
        $fieldDrop = $objDrop->show();
    } else {
        $fieldDrop = '<b>'.$notApplicableLabel.'</b>';
    }
} else {
    $fieldDrop = '';
}

$objLayer = new layer();
$objLayer->id = 'fieldLayer';
$objLayer->str = $fieldDrop;
$fieldLayer = $objLayer->show();

if ($mode == 'editrule') {
    if ($rule['mail_field'] != NULL && $rule['mail_field'] != 5) {
        $objInput = new textinput('criteria', $rule['criteria']);
        $criteriaInput = $objInput->show();
    } else {
        $criteriaInput = '<b>'.$notApplicableLabel.'</b>';
    }
} else {
    $criteriaInput = '';
}

$objLayer = new layer();
$objLayer->id = 'criteriaLayer';
$objLayer->str = $criteriaInput;
$criteriaLayer = $objLayer->show();

if ($mode == 'editrule') {
    $objDrop = new dropdown('ruleAction');
    $objDrop->addOption(NULL, '- '.$selectLabel.' -');
    $objDrop->addOption(1, $moveLabel);
    $objDrop->addOption(2, $readLabel);
    $objDrop->setSelected($rule['rule_action']);
    $objDrop->extra = ' onchange="javascript:
        ruleaction();"';
    $actionDrop = $objDrop->show();
} else {
    $actionDrop = '';
}

$objLayer = new layer();
$objLayer->id = 'actionLayer';
$objLayer->str = $actionDrop;
$actionLayer = $objLayer->show();

if ($mode == 'editrule') {
    if ($rule['rule_action'] != 2) {
        $arrFolderList = $this->dbFolders->listFolders();
        $objDrop = new dropdown('destFolderId');
        $objDrop->addOption(NULL, '- '.$selectLabel.' -');
        foreach($arrFolderList as $folder) {
            if ($folder['id'] != 'init_1' && $mailAction == '1') {
                $objDrop->addOption($folder['id'], $folder['folder_name']);
            }
            if ($folder['id'] != 'init_3' && $mailAction == '2') {
                $objDrop->addOption($folder['id'], $folder['folder_name']);
            }
        }
        $objDrop->setSelected($rule['dest_folder_id']);
        $folderDrop = $objDrop->show();
    } else {
        $folderDrop = '<b>'.$notApplicableLabel.'</b>';
    }
} else {
    $folderDrop = '';
}

$objLayer = new layer();
$objLayer->id = 'destLayer';
$objLayer->str = $folderDrop;
$destLayer = $objLayer->show();

$objTable = new htmltable();
$objTable->cellpadding = '4';
$objTable->startRow();
$objTable->addCell('<b>'.$emailLabel.'</b>', '25%', '', '', '', '');
$objTable->addCell($mailDrop, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>'.$messagesLabel.'</b>', '', '', '', '', '');
$objTable->addCell($messageDrop, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>'.$fieldLabel.'</b>', '', '', '', '', '');
$objTable->addCell($fieldLayer, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>'.$criteriaLabel.'</b>', '', '', '', '', '');
$objTable->addCell($criteriaLayer, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>'.$actionLabel.'</b>', '', '', '', '', '');
$objTable->addCell($actionLayer, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>'.$destinationLabel.'</b>', '', '', '', '', '');
$objTable->addCell($destLayer, '', '', '', '', '');
$objTable->endRow();
$rulesTable = $objTable->show();

$objFieldset = new fieldset();
$objFieldset->extra = ' style="border: 1px solid #808080; margin: 3px; padding: 10px;"';
$objFieldset->contents = $rulesTable;
$ruleFieldset = $objFieldset->show();

$objButton = new button('submitbutton', $submitLabel);
$objButton->setToSubmit();
$buttons = '<br/>'.$objButton->show();

$objButton = new button('cancelbutton', $cancelLabel);
$objButton->extra = ' onclick="javascript:
    $(\'form_hiddenform\').submit();"';
$buttons.= '&#160;'.$objButton->show();

$objInput = new textinput('ruleId', $ruleId, 'hidden', '');
$hiidenInput = $objInput->show();

$objForm = new form('rulesform', $this->uri(array(
    'action' => 'saverule',
    'mode' => $mode
)));
$objForm->addToForm($ruleFieldset);
$objForm->addToForm($buttons);
$objForm->addToForm($hiidenInput);
$forms = $objForm->show();

$objForm = new form('hiddenform', $this->uri(array(
    'action' => 'managesettings'
)));
$forms.= $objForm->show();
$pageData.= $forms;
// set up exit link

$objLink = new link($this->uri(array(
    'action' => 'managesettings'
)) , 'internalmail');
$objLink->link = $backLabel;
$pageData.= '<br />'.$objLink->show();

$objLayer = new layer();
$objLayer->padding = '10px';
$objLayer->addToStr($pageData);
$pageLayer = $objLayer->show();
echo $pageLayer;
?>