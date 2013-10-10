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
 *
 */
// set up javascript headers
$headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
$this->appendArrayVar('headerParams', $headerParams);
$headerParams = $this->getJavascriptFile('settings.js', 'internalmail');
$this->appendArrayVar('headerParams', $headerParams);
//$this->objScriptaculous =& $this->getObject('scriptaculous', 'ajaxwrapper');
//$this->objScriptaculous->show();
//$this->setVar('pageSuppressXML', TRUE);

// set up html elements
$objIcon = $this->newObject('geticon', 'htmlelements');
$objHeader = $this->loadClass('htmlheading', 'htmlelements');
$objTable = $this->loadClass('htmltable', 'htmlelements');
$objInput = $this->loadClass('textinput', 'htmlelements');
$objText = $this->loadClass('textarea', 'htmlelements');
$objCheck = $this->loadClass('checkbox', 'htmlelements');
$objDrop = $this->loadClass('dropdown', 'htmlelements');
$objButton = $this->loadClass('button', 'htmlelements');
$objForm = $this->loadClass('form', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objLayer = $this->loadClass('layer', 'htmlelements');
$objTabbedbox = $this->loadClass('tabbedbox', 'htmlelements');
$objTimeOut = $this->newObject('timeoutMessage','htmlelements');

// set up language items
$heading = $this->objLanguage->languageText('mod_internalmail_managesettings', 'internalmail');
$backLabel = $this->objLanguage->languageText('word_back');
$submitLabel = $this->objLanguage->languageText('word_submit');
$cancelLabel = $this->objLanguage->languageText('word_cancel');
$autoDeleteLabel = $this->objLanguage->languageText('mod_internalmail_autodelete', 'internalmail');
$signatureTextLabel = $this->objLanguage->languageText('mod_internalmail_signature', 'internalmail');
$signatureLabel = $this->objLanguage->languageText('word_signature');
$autoTextLabel = $this->objLanguage->languageText('mod_internalmail_autodelete', 'internalmail');
$autoLabel = $this->objLanguage->languageText('phrase_autodelete');
$emailRulesLabel = $this->objLanguage->languageText('phrase_emailrules');
$yesLabel = $this->objLanguage->languageText('word_yes');
$noLabel = $this->objLanguage->languageText('word_no');
$updateLabel = $this->objLanguage->languageText('word_update');
$surnameLabel = $this->objLanguage->languageText('mod_internalmail_surname', 'internalmail');
$usernameLabel = $this->objLanguage->languageText('mod_internalmail_username', 'internalmail');
$userDisplayLabel = $this->objLanguage->languageText('phrase_userdisplay');
$userLabel = $this->objLanguage->languageText('mod_internalmail_display', 'internalmail');
$rulesTextLabel = $this->objLanguage->languageText('mod_internalmail_rules', 'internalmail');
$addRuleLabel = $this->objLanguage->languageText('mod_internalmail_addrule', 'internalmail');
$editRuleLabel = $this->objLanguage->languageText('mod_internalmail_editrule', 'internalmail');
$deleteRuleLabel = $this->objLanguage->languageText('mod_internalmail_deleterule', 'internalmail');
$folderTextLabel = $this->objLanguage->languageText('mod_internalmail_defaultfolder', 'internalmail');
$defaultFolderLabel = $this->objLanguage->languageText('phrase_defaultfolder');
$emailLabel = $this->objLanguage->languageText('word_email');
$incomingLabel = $this->objLanguage->languageText('phrase_email');
$outgoingLabel = $this->objLanguage->languageText('phrase_sentemail');
$norulesLabel = $this->objLanguage->languageText('mod_internalmail_norules', 'internalmail');
$fieldLabel = $this->objLanguage->languageText('word_field');
$criteriaLabel = $this->objLanguage->languageText('word_criteria');
$actionLabel = $this->objLanguage->languageText('word_action');
$deleteLabel = $this->objLanguage->languageText('word_delete');
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
$confirmLabel = $this->objLanguage->languageText('mod_internalmail_confirmrule', 'internalmail');
$attachmentsLabel = $this->objLanguage->languageText('word_attachments');

// set up data
$configs = $this->getSession('configs');
$surnameFirst = $configs['surname_first'];
$hideUsername = $configs['hide_username'];
$defaultFolderId = isset($configs['default_folder_id']) ? $configs['default_folder_id'] : 'init_1';
$autoDelete = isset($configs['auto_delete']) ? $configs['auto_delete'] : 0;
$signature = isset($configs['signature']) ? $configs['signature'] : '';
$updated = isset($configs['updated']) ? $configs['updated'] : date('Y-m-d H:i:s');
$name = $this->dbRouting->getName($this->userId);
// set up code to text
$date = $this->objDate->formatDate($updated);
$array = array(
    'date' => $date
);
    
$confimUpdateLabel = $this->objLanguage->code2Txt('mod_internalmail_update', 'internalmail', $array);

$objTimeOut->init();
$objTimeOut->setMessage($confimUpdateLabel);
$objTimeOut->setTimeout(3000);
$msg = '<b>'.$objTimeOut->show().'</b>';

// set up heading
$objHeader = new htmlHeading();
$objHeader->str = $heading;
$objHeader->type = 1;
$pageData = $objHeader->show();

// set up user display
$objDrop = new dropdown('name');
$objDrop->addOption(0, $noLabel);
$objDrop->addOption(1, $yesLabel.'&#160;');
$objDrop->setSelected($surnameFirst);
$objDrop->extra = 'onchange="javascript:nameOrder();"';
$surnameDrop = $objDrop->show();

$objDrop = new dropdown('username');
$objDrop->addOption(1, $noLabel);
$objDrop->addOption(0, $yesLabel.'&#160;');
$objDrop->setSelected($hideUsername);
$objDrop->extra = 'onchange="javascript:displayUsername();"';
$usernameDrop = $objDrop->show();

$objLayer = new layer();
$objLayer->id = 'userdisplay';
$objLayer->str = $name;
$userLayer = $objLayer->show();

$objLayer = new layer();
$objLayer->id = 'user_button';
$buttonLayer = $objLayer->show();

$objTable = new htmltable();
//    $objTable->cellspacing='2';
$objTable->cellpadding = '4';
if ($section == 'user') {
    $objTable->startRow();
    $objTable->addCell($msg, '', '', '', 'confirm', 'colspan="2"');
    $objTable->endRow();
}
$objTable->startRow();
$objTable->addCell($userLabel, '', '', '', 'warning', 'colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('<b>'.$userLayer.'</b>', '', '', '', 'error', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($surnameLabel, '30%', '', '', '', '');
$objTable->addCell($surnameDrop, '', '', '', '', 'colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($usernameLabel, '', '', '', '', '');
$objTable->addCell($usernameDrop, '', '', '', '', 'colspan="2"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($buttonLayer, '', '', '', '', '');
$objTable->endRow();
$userTable = $objTable->show();

$objTabbedbox = new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel('<b>'.$userDisplayLabel.'</b>');
$objTabbedbox->addBoxContent($userTable);
$userTabbedbox = $objTabbedbox->show();

// set up default folder
$objText = new textarea('signature', $signature, '5', '40');
$objText->extra = 'onkeyup="javascript:signatureButton();"';
$signatureText = $objText->show();

$objLayer = new layer();
$objLayer->id = 'signature_button';
$buttonLayer = $objLayer->show();

$objTable = new htmltable();
$objTable->cellpadding = '4';
if ($section == 'signature') {
    $objTable->startRow();
    $objTable->addCell($msg, '', '', '', 'confirm', 'colspan="2"');
    $objTable->endRow();
}
$objTable->startRow();
$objTable->addCell($signatureTextLabel, '', '', '', 'warning', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($signatureText, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($buttonLayer, '', '', '', '', '');
$objTable->endRow();
$signatureTable = $objTable->show();

$objTabbedbox = new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel('<b>'.$signatureLabel.'</b>');
$objTabbedbox->addBoxContent($signatureTable);
$signatureTabbedbox = $objTabbedbox->show();

// set up signature
$objDrop = new dropdown('defaultfolder');
foreach($arrFolderList as $folder) {
    $objDrop->addOption($folder['id'], $folder['folder_name']);
}
$objDrop->setSelected($defaultFolderId);
$objDrop->extra = 'onchange="javascript:folderButton();"';
$folderDrop = $objDrop->show();

$objLayer = new layer();
$objLayer->id = 'folder_button';
$buttonLayer = $objLayer->show();

$objTable = new htmltable();
//    $objTable->cellspacing='2';
$objTable->cellpadding = '4';
if ($section == 'folder') {
    $objTable->startRow();
    $objTable->addCell($msg, '', '', '', 'confirm', 'colspan="2"');
    $objTable->endRow();
}
$objTable->startRow();
$objTable->addCell($folderTextLabel, '', '', '', 'warning', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($folderDrop, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($buttonLayer, '', '', '', '', '');
$objTable->endRow();
$folderTable = $objTable->show();

$objTabbedbox = new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel('<b>'.$defaultFolderLabel.'</b>');
$objTabbedbox->addBoxContent($folderTable);
$folderTabbedbox = $objTabbedbox->show();

// set up auto delete
$objDrop = new dropdown('autodelete');
$objDrop->addOption(0, $noLabel);
$objDrop->addOption(1, $yesLabel.'&nbsp;');
$objDrop->setSelected($autoDelete);
$objDrop->extra = 'onchange="javascript:deleteButton();"';
$autoDrop = $objDrop->show();

$objLayer = new layer();
$objLayer->id = 'delete_button';
$buttonLayer = $objLayer->show();

$objTable = new htmltable();
$objTable->cellpadding = '4';
if ($section == 'delete') {
    $objTable->startRow();
    $objTable->addCell($msg, '', '', '', 'confirm', 'colspan="2"');
    $objTable->endRow();
}
$objTable->startRow();
$objTable->addCell($autoTextLabel, '', '', '', 'warning', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($autoDrop, '', '', '', '', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($buttonLayer, '', '', '', '', '');
$objTable->endRow();
$autoTable = $objTable->show();

$objTabbedbox = new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel('<b>'.$autoLabel.'</b>');
$objTabbedbox->addBoxContent($autoTable);
$autoTabbedbox = $objTabbedbox->show();

// set up config form
$objForm = new form('config', $this->uri(array(
    'action' => 'updateconfig'
)));
$objForm->addToForm($userTabbedbox.'<br />');
$objForm->addToForm($folderTabbedbox.'<br />');
$objForm->addToForm($autoTabbedbox.'<br />');
$objForm->addToForm($signatureTabbedbox.'<br />');
$configForm = $objForm->show();

// set up rules
$objIcon->title = $addRuleLabel;
$addIcon = $objIcon->getLinkedIcon($this->uri(array(
    'action' => 'managerules',
    'mode' => 'addrule'
)) , 'add');
$objHeader->str = $addRuleLabel.$addIcon;
$objHeader->type = 3;
$addRuleHeading = $objHeader->show();

$objTable = new htmltable();
//    $objTable->cellspacing='2';
$objTable->cellpadding = '4';
$objTable->startRow();
$objTable->addCell($rulesTextLabel, '', '', '', 'warning', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($addRuleHeading, '', '', '', '', '');
$objTable->endRow();
$ruleTextTable = $objTable->show();

$objTable = new htmltable();
$objTable->id = 'rulesTable';
$objTable->css_class = 'sorttable';
//    $objTable->cellspacing='2';
$objTable->cellpadding = '4';
$objTable->row_attributes = 'name="row_'.$objTable->id.'"';
$objTable->startRow();
$objTable->addCell($emailLabel, '', '', '', 'heading', '');
$objTable->addCell($messagesLabel, '', '', '', 'heading', '');
$objTable->addCell($fieldLabel, '', '', '', 'heading', '');
$objTable->addCell($criteriaLabel, '', '', '', 'heading', '');
$objTable->addCell($actionLabel, '', '', '', 'heading', '');
$objTable->addCell($destinationLabel, '', '', '', 'heading', '');
$objTable->addCell('', '', '', '', 'heading', '');
$objTable->endRow();
if (empty($arrRulesList)) {
    $objTable->startRow();
    $objTable->addCell($norulesLabel, '', '', '', 'noRecordsMessage', 'colspan="8"');
    $objTable->endRow();
} else {
    foreach($arrRulesList as $rule) {
        if ($rule['mail_action'] == '1') {
            $mailAction = $incomingLabel;
        } elseif ($rule['mail_action'] == '2') {
            $mailAction = $outgoingLabel;
        }
        $criteriaField = $rule['criteria'];
        if ($rule['mail_field'] == '1') {
            $mailField = $toLabel;
        } elseif ($rule['mail_field'] == '2') {
            $mailField = $fromLabel;
        } elseif ($rule['mail_field'] == '3') {
            $mailField = $subjectLabel;
        } elseif ($rule['mail_field'] == '4') {
            $mailField = $messageLabel;
        } elseif ($rule['mail_field'] == '5') {
            $mailField = $attachmentsLabel;
            $criteriaField = '<b>'.$notApplicableLabel.'</b>';
        } else {
            $mailField = '<b>'.$notApplicableLabel.'</b>';
            $criteriaField = '<b>'.$notApplicableLabel.'</b>';
        }
        if ($rule['mail_field'] < '1') {
            $messageField = $allMessagesLabel;
        } else {
            $messageField = $filteredMessagesLabel;
        }
        if ($rule['rule_action'] == '1') {
            $ruleField = $moveLabel;
        } elseif ($rule['rule_action'] == '2') {
            $ruleField = $readLabel;
        }
        if ($rule['rule_action'] == '2') {
            $folderName = '<b>'.$notApplicableLabel.'</b>';
        } else {
            $folder = $this->dbFolders->getFolder($rule['dest_folder_id']);
            $folderName = $folder['folder_name'];
        }
        // set up edit icon
        $objIcon->title = $editRuleLabel;
        $editIcon = $objIcon->getEditIcon($this->uri(array(
            'action' => 'managerules',
            'mode' => 'editrule',
            'ruleId' => $rule['id']
        )));
        // set up delete icon
        $deleteArray = array(
            'action' => 'deleterule',
            'ruleId' => $rule['id']
        );
        $deleteIcon = $objIcon->getDeleteIconWithConfirm('', $deleteArray, 'internalmail', $confirmLabel);
        $objTable->startRow();
        $objTable->addCell($mailAction, '', '', '', '', '');
        $objTable->addCell($messageField, '', '', '', '', '');
        $objTable->addCell($mailField, '', '', '', '', '');
        $objTable->addCell($criteriaField, '', '', '', '', '');
        $objTable->addCell($ruleField, '', '', '', '', '');
        $objTable->addCell($folderName, '', '', '', '', '');
        $objTable->addCell($editIcon.'&#160;'.$deleteIcon, '', '', 'center', '', '');
        $objTable->endRow();
    }
}
$rulesTable = $objTable->show();

$objTabbedbox = new tabbedbox();
$objTabbedbox->extra = 'style="padding: 10px;"';
$objTabbedbox->addTabLabel('<b>'.$emailRulesLabel.'</b>');
$objTabbedbox->addBoxContent($ruleTextTable.$rulesTable);
$ruleTabbedbox = $objTabbedbox->show();
$pageData.= $configForm.$ruleTabbedbox;

// set up exit link
$objLink = new link($this->uri('') , 'internalmail');
$objLink->link = $backLabel;
$pageData.= '<br />'.$objLink->show();

$objLayer = new layer();
$objLayer->padding = '10px';
$objLayer->addToStr($pageData);
$pageLayer = $objLayer->show();
echo $pageLayer;
?>