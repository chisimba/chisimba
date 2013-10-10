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

// set up html elements
$objIcon = $this->newObject('geticon', 'htmlelements');
$objHeader = $this->loadClass('htmlheading', 'htmlelements');
$objTable = $this->loadClass('htmltable', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objFieldset = $this->loadClass('fieldset', 'htmlelements');
$objLayer = $this->loadClass('layer', 'htmlelements');
$objInput = $this->loadClass('textinput', 'htmlelements');
$objCheck = $this->loadClass('checkbox', 'htmlelements');

// set up language items
$heading = $this->objLanguage->languageText('mod_internalmail_name', 'internalmail');
$exitLabel = $this->objLanguage->languageText('mod_internalmail_exitsystem', 'internalmail');
$folderListLabel = $this->objLanguage->languageText('mod_internalmail_folderlist', 'internalmail');
$messageListLabel = $this->objLanguage->languageText('mod_internalmail_messagelist', 'internalmail');
$selectedMessageLabel = $this->objLanguage->languageText('mod_internalmail_selectedmessage', 'internalmail');
$manageFolderLabel = $this->objLanguage->languageText('mod_internalmail_managefolders', 'internalmail');
$manageBooksLabel = $this->objLanguage->languageText('mod_internalmail_manageaddressbooks', 'internalmail');
$folderLabel = $this->objLanguage->languageText('word_folder');
$unreadLabel = $this->objLanguage->languageText('word_unread');
$totalLabel = $this->objLanguage->languageText('word_total');
$inboxLabel = $this->objLanguage->languageText('word_inbox');
$draftsLabel = $this->objLanguage->languageText('word_drafts');
$sentItemsLabel = $this->objLanguage->languageText('phrase_sentitems');
$trashLabel = $this->objLanguage->languageText('word_trash');
$noMessagesLabel = $this->objLanguage->languageText('mod_internalmail_nomessages', 'internalmail');
$noSelectedMessageLabel = $this->objLanguage->languageText('mod_internalmail_nomessageselected', 'internalmail');
$composeLabel = $this->objLanguage->languageText('mod_internalmail_compose', 'internalmail');
$toLabel = $this->objLanguage->languageText('word_to');
$fromLabel = $this->objLanguage->languageText('word_from');
$subjectLabel = $this->objLanguage->languageText('word_subject');
$dateLabel = $this->objLanguage->languageText('phrase_datesent');
$readLabel = $this->objLanguage->languageText('phrase_reademail');
$unreadEmailLabel = $this->objLanguage->languageText('phrase_unreademail');
$unreadLabel = $this->objLanguage->languageText('word_unread');
$sentLabel = $this->objLanguage->languageText('phrase_sentemail');
$messageLabel = $this->objLanguage->languageText('mod_internalmail_message', 'internalmail');
$confirmLabel = $this->objLanguage->languageText('mod_internalmail_confirmemail', 'internalmail');
$permanentLabel = $this->objLanguage->languageText('mod_internalmail_confirmpermanent', 'internalmail');
$restoreLabel = $this->objLanguage->languageText('mod_internalmail_restore', 'internalmail');
$resendLabel = $this->objLanguage->languageText('mod_internalmail_resend', 'internalmail');
$forwardLabel = $this->objLanguage->languageText('mod_internalmail_forward', 'internalmail');
$nextLabel = $this->objLanguage->languageText('mod_internalmail_next', 'internalmail');
$previousLabel = $this->objLanguage->languageText('mod_internalmail_previous', 'internalmail');
$replyLabel = $this->objLanguage->languageText('mod_internalmail_reply', 'internalmail');
$replyallLabel = $this->objLanguage->languageText('mod_internalmail_replyall', 'internalmail');
$fwdMessageLabel = $this->objLanguage->languageText('mod_internalmail_forwardmessage', 'internalmail');
$manageLabel = $this->objLanguage->languageText('mod_internalmail_manage', 'internalmail');
$attachmentLabel = $this->objLanguage->languageText('word_attachments');
$selectallLabel = $this->objLanguage->languageText('mod_internalmail_selectall', 'internalmail');
$deselectLabel = $this->objLanguage->languageText('mod_internalmail_deselectall', 'internalmail');
$moveLabel = $this->objLanguage->languageText('word_move');
$selectLabel = $this->objLanguage->languageText('word_select');
$deleteLabel = $this->objLanguage->languageText('word_delete');
$selectMoveLabel = $this->objLanguage->languageText('mod_internalmail_selectmove', 'internalmail');
$selectDeleteLabel = $this->objLanguage->languageText('mod_internalmail_selectdelete', 'internalmail');
$noFolderLabel = $this->objLanguage->languageText('mod_internalmail_nofolder', 'internalmail');
$manageSettingsLabel = $this->objLanguage->languageText('mod_internalmail_managesettings', 'internalmail');
$readLabel = $this->objLanguage->languageText('word_read');
$allLabel = $this->objLanguage->languageText('phrase_allmessages');
$markReadLabel = $this->objLanguage->languageText('phrase_markasread');
$selectMarkLabel = $this->objLanguage->languageText('mod_internalmail_selectmark', 'internalmail');

// get config data
$configs = $this->getSession('configs');
$folderId = !empty($folderId) ? $folderId : $configs['default_folder_id'];

// set up heading
$objHeader = new htmlHeading();
$objHeader->str = $heading;
$objHeader->type = 1;
$pageData = $objHeader->show() .'<hr />';

// set up folders table
$objTable = new htmltable();
$objTable->id = 'folderList';
$objTable->css_class = 'sorttable';
$objTable->cellpadding = '4';
$objTable->row_attributes = 'name="row_'.$objTable->id.'"';
$objTable->startRow();
$objTable->addCell('<b>'.$folderLabel.'</b>', '50%', '', '', 'wrapperLightBkg', '');
$objTable->addCell('<b>'.$unreadLabel.'</b>', '25%', '', 'center', 'wrapperLightBkg', '');
$objTable->addCell('<b>'.$totalLabel.'</b>', '25%', '', 'center', 'wrapperLightBkg', '');
$objTable->endRow();
$i = 0;
foreach($arrFolderList as $folder) {
    //set up folder link
    $objLink = new link($this->uri(array(
        'action' => 'gotofolder',
        'folderId' => $folder['id']
    )));
    $objLink->link = $folder['folder_name'];
    $nameLink = $objLink->show();
    // set up unread colour
    if ($folder['unreadmail'] != 0) {
        $unreadMail = '<span class="unreadmail"><b>'.$folder['unreadmail'].'</b></span>';
    } else {
        $unreadMail = $folder['unreadmail'];
    }
    if ($folderId == $folder['id']) {
        $class = 'confirm';
    } else {
        $class = '';
    }
    $objTable->startRow();
    $objTable->addCell($nameLink, '', '', '', $class, '');
    $objTable->addCell($unreadMail, '', '', 'center', $class, '');
    $objTable->addCell($folder['allmail'], '', '', 'center', $class, '');
    $objTable->endRow();
}
$folderTable = $objTable->show();

// set up data
$arrFolderData = $this->dbFolders->getFolder($folderId);
$folderName = $arrFolderData['folder_name'];
$arrEmailListData = $this->dbRouting->getAllMail($folderId, $sortOrder, $filter);

// set up message list table
$objTable = new htmltable();
$objTable->cellpadding = '4';
if (!empty($arrEmailListData)) {
    // move message
    $objInput = new textinput('folderId', $folderId, 'hidden', '');
    $folderIdInput = $objInput->show();

    $objInput = new textinput('movemessage', '', 'hidden', '');
    $moveInput = $objInput->show();

    $objInput = new textinput('deletemessage', '', 'hidden', '');
    $deleteInput = $objInput->show();

    $objInput = new textinput('markmessage', '', 'hidden', '');
    $markInput = $objInput->show();

    $objCheck = new checkbox('selectmsg');
    $objCheck->extra = ' onmouseover="javascript:
        if(this.checked){
            this.title=\''.$deselectLabel.'\';
        }else{
            this.title=\''.$selectallLabel.'\';
        }"
    onclick="javascript:
        if(this.checked){
            SetAllCheckBoxes(\'msgform\',\'msgId[]\',true);
        }else{
            SetAllCheckBoxes(\'msgform\',\'msgId[]\',false)
        }"';
    $selectCheck = $objCheck->show();

    $objButton = new button('submitbutton_1', $moveLabel);
    $objButton->extra = ' onclick="javascript:
        var elDrp=$(\'input_newFolderId\');
        var elChk=document.getElementsByName(\'msgId[]\');
        if(elDrp.value==\'\'){
            alert(\''.$noFolderLabel.'\');return false
        }
        var elChkValue=false;
        for(var i=0;i &lt; elChk.length;i++){
            if(elChk[i].checked==true){
                elChkValue=true;
            }
        }
        if(elChkValue){
            $(\'input_movemessage\').value=\'Move\';
            $(\'form_msgform\').submit();
        }else{
            alert(\''.$selectMoveLabel.'\')
        }"';
    $moveButton = $objButton->show();

    $objButton = new button('submitbutton_2', $deleteLabel);
    $objButton->extra = ' onclick="javascript:
        var elChk=document.getElementsByName(\'msgId[]\');
        var elChkValue=false;
        for(var i=0;i &lt; elChk.length;i++){
            if(elChk[i].checked==true){
                elChkValue=true;
            }
        }
        if(elChkValue){
            if(document.getElementById(\'input_folderId\').value==\'init_4\'){
                if(confirm(\''.$permanentLabel.'\')){
                    $(\'input_deletemessage\').value=\'Delete\';
                    $(\'form_msgform\').submit();
                }
            }else{
                if(confirm(\''.$confirmLabel.'\')){
                    $(\'input_deletemessage\').value=\'Delete\';
                    $(\'form_msgform\').submit();
                }
            }
        }else{
            alert(\''.$selectDeleteLabel.'\')
        }"';
    $deleteButton = $objButton->show();

    $objButton = new button('submitbutton_3', $markReadLabel);
    $objButton->extra = ' onclick="javascript:
        var elChk=document.getElementsByName(\'msgId[]\');
        var elChkValue=false;
        for(var i=0;i &lt; elChk.length;i++){
            if(elChk[i].checked==true){
                elChkValue=true;
            }
        };
        if(elChkValue){
            $(\'input_markmessage\').value=\'Mark\';
            $(\'form_msgform\').submit();
        }else{
            alert(\''.$selectMarkLabel.'\')
        }"';
    $markButton = $objButton->show();

    $objDrop = new dropdown('newFolderId');
    $objDrop->addOption(NULL, '- '.$selectLabel.' -');
    foreach($arrFolderList as $folder) {
        if ($folderId != $folder['id']) {
            $objDrop->addOption($folder['id'], $folder['folder_name']);
        }
    }
    $folderDrop = $objDrop->show();

    $objDrop = new dropdown('filter');
    $objDrop->addOption(NULL, $allLabel);
    $objDrop->addOption(1, $readLabel);
    $objDrop->addOption(2, $unreadLabel);
    $objDrop->addOption(3, $attachmentLabel);
    $objDrop->setSelected($filter);
    $objDrop->extra = ' onchange="javascript:
        $(\'form_msgform\').submit();"';
    $filterDrop = $objDrop->show();
    $objTable->startHeaderRow();
    $objTable->addHeaderCell($moveInput.$deleteInput.$markInput.$folderIdInput.$selectCheck.'&nbsp;'.$folderDrop.'&#160;'.$moveButton.'&#160;'.$deleteButton.'&#160;'.$markButton, '', '', 'left', 'wrapperLightBkg', 'colspan="3"');
    $objTable->addHeaderCell($filterDrop, '', '', 'right', 'wrapperLightBkg', 'colspan="2"');
    $objTable->endHeaderRow();
} else {
    if ($arrFolderData['allmail'] >= 1) {
        $objDrop = new dropdown('filter');
        $objDrop->addOption(NULL, $allLabel);
        $objDrop->addOption(1, $readLabel);
        $objDrop->addOption(2, $unreadLabel);
        $objDrop->addOption(3, $attachmentLabel);
        $objDrop->setSelected($filter);
        $objDrop->extra = ' onchange="javascript:
            $(\'form_msgform\').submit();"';
        $filterDrop = $objDrop->show();
        $objTable->startHeaderRow();
        $objTable->addHeaderCell('', '', '', 'left', 'wrapperLightBkg', 'colspan="3"');
        $objTable->addHeaderCell($filterDrop, '', '', 'right', 'wrapperLightBkg', '');
        $objTable->endHeaderRow();
    }
}
$headingTable = $objTable->show();

$objTable = new htmltable();
$objTable->id = 'messageListTable';
$objTable->css_class = 'sorttable';
$objTable->cellpadding = '4';
$objTable->row_attributes = ' name="row_'.$objTable->id.'"';
$objTable->startRow();
$objTable->addCell('', '15%', '', '', 'wrapperLightBkg', '');
$objTable->addCell('<b>'.$fromLabel.'</b>', '25%', '', '', 'wrapperLightBkg', '');
$objTable->addCell('<b>'.$subjectLabel.'</b>', '45%', '', '', 'wrapperLightBkg', '');
$objTable->addCell('<b>'.$dateLabel.'</b>', '15%', '', '', 'wrapperLightBkg', '');
$objTable->endRow();
if (empty($arrEmailListData)) {
    $objTable->startRow();
    $objTable->addCell($noMessagesLabel, '', '', 'center', 'noRecordsMessage', 'colspan="5"');
    $objTable->endRow();
} else {
    $i = 0;
    foreach($arrEmailListData as $email) {
        $objCheck = new checkbox('msgId[]');
        $objCheck->value = $email['routing_id'];
        $objCheck->extra = ' onclick="javascript:
            var elChk=document.getElementsByName(\'msgId[]\');
            var elChkAllTrue=true;
            var elChkAllFalse=true;
            for(var i=0;i &lt; elChk.length;i++){
                if(elChk[i].checked==true){
                    elChkAllFalse=false;
                }else{
                    elChkAllTrue=false;
                }
            };
            var elSelectAll=$(\'input_selectmsg\');
            if(elChkAllTrue==true || elChkAllFalse==true){
                if(elChkAllTrue){
                    if(elSelectAll.checked==false){
                        elSelectAll.checked=true;
                    }
                }
                if(elChkAllFalse){
                    if(elSelectAll.checked==true){
                        elSelectAll.checked=false;
                    }
                }
            }
            if(this.checked == false){
                if(elSelectAll.checked==true){
                    elSelectAll.checked=false;
                }
            }"';
        $msgCheck = $objCheck->show();

        // set message icon
        $action = $this->uri(array(
            'action' => 'gotomessage',
            'folderId' => $folderId,
            'routingId' => $email['routing_id'],
            'filter' => $filter
        ));
        if ($email['read_email'] != 1) {
            if ($email['sent_email'] != 1) {
                $objIcon->title = $unreadEmailLabel;
                $objIcon->setIcon('unreadletter', 'png');
                $objIcon->extra = ' onclick="javascript:
                    $(\'form_hiddenform\').action=\''.$action.'\';
                    $(\'form_hiddenform\').submit();"';
                $readIcon = '<a href="#">'.$objIcon->show().'</a>';
                $class = 'warning';
            } else {
                $objIcon->title = $sentLabel;
                $objIcon->setIcon('sent', 'png');
                $objIcon->extra = ' onclick="javascript:
                    $(\'form_hiddenform\').action=\''.$action.'\';
                    $(\'form_hiddenform\').submit();"';
                $readIcon = '<a href="#">'.$objIcon->show().'</a>';
                $class = 'warning';
            }
        } else {
            $objIcon->title = $readLabel;
            if ($email['sent_email'] != 1) {
                $objIcon->setIcon('readletter', 'png');
            } else {
                $objIcon->setIcon('readsent', 'png');
            }
            $objIcon->extra = ' onclick="javascript:
                $(\'form_hiddenform\').action=\''.$action.'\';
                $(\'form_hiddenform\').submit();"';
            $readIcon = '<a href="#">'.$objIcon->show().'</a>';
            $class = '';
        }

        // get message data
        $arrMessageData = $this->dbEmail->getMail($email['email_id']);
        $from = $this->dbRouting->getName($email['sender_id']);
        // set up attachment icon
        if ($email['attachments'] >= 1) {
            $objIcon->title = $attachmentLabel;
            $objIcon->setIcon('attachments', 'png');
            $attachIcon = $objIcon->show();
        } else {
            $attachIcon = '';
        }

        // set up subject link
        $objLink = new link('#');
        $objLink->link = $arrMessageData['subject'];
        $objLink->extra = ' onclick="javascript:
            $(\'form_hiddenform\').action=\''.$action.'\';
            $(\'form_hiddenform\').submit();"';
        $subjectLink = $objLink->show();
        if ($email['routing_id'] == $routingId) {
            $class = 'confirm';
        } else {
            $class = '';
        }
        $objTable->startRow();
        $objTable->addCell($msgCheck.$readIcon.$attachIcon, '', '', '', $class, '');
        $objTable->addCell('<nobr>'.$from.'</nobr>', '', '', '', $class, '');
        $objTable->addCell($subjectLink, '', '', '', $class, '');
        $objTable->addCell('<nobr>'.$arrMessageData['date_sent'].'</nobr>', '', '', '', $class, '');
        $objTable->endRow();
    }
}
$listTable = $objTable->show();

$objForm = new form('msgform', $this->uri(array(
    'action' => 'messages',
    'folderId' => $folderId
)));
$objForm->addToForm($headingTable.$listTable);
$listForm = $objForm->show();

// set up data
$messageData = $this->dbRouting->getMail($routingId);

// set up message table
$objInput = new textinput('mode', '', 'hidden', '');
$modeInput = $objInput->show();

$objInput = new textinput('messageListTable', implode('|', $sortOrder) , 'hidden', '');
$hiddenInput = $objInput->show();

// set up hidden form
$objForm = new form('hiddenform', $this->uri(array(
    'action' => 'gotomessage',
    'folderId' => $folderId,
    'routingId' => $routingId,
    'filter' => $filter
)));
$objForm->addToForm($hiddenInput.$modeInput);
$hiddenForm = $objForm->show();

$objTable = new htmltable();
$objTable->cellpadding = '4';
if (empty($messageData)) {
    $objTable->startRow();
    $objTable->addCell($noSelectedMessageLabel, '', '', 'center', 'noRecordsMessage', 'colspan="2"');
    $objTable->endRow();
    $prevIcon = '';
    $nextIcon = '';
    $icons = '';
} else {
    $emailData = $this->dbEmail->getMail($messageData['email_id']);
    $from = $this->dbRouting->getName($messageData['sender_id']);
    $recipientList = $emailData['recipient_list'];
    $arrRecipients = explode('|', $recipientList);
    $to = '';
    foreach($arrRecipients as $key => $recipient) {
        if($recipient != ""){
            $to .= $this->dbRouting->getName($recipient);
        }
        if ($key != count($arrRecipients) -1) {
            $to.= '; ';
        }
        if ($key == 9) {
            $to.= '........';
            break;
        }
    }
    // set up next and previous icons
    $objIcon->title = $previousLabel;
    $objIcon->setIcon('prev_new', 'png');
    $objIcon->extra = ' onclick="javascript:
        $(\'input_mode\').value=\'prev\';
        $(\'form_hiddenform\').submit();"';
    $prevIcon = '<a href="#">'.$objIcon->show().'</a>';

    // set up next icon
    $objIcon->title = $nextLabel;
    $objIcon->setIcon('next_new', 'png');
    $objIcon->extra = ' onclick="javascript:
        $(\'input_mode\').value=\'next\';
        $(\'form_hiddenform\').submit();"';
    $nextIcon = '<a href="#">'.$objIcon->show().'</a>';

    // set up reply icon
    $array = array(
        'date' => $this->objDate->formatDate($emailData['date_sent']) ,
        'writer' => $from
    );
    $replyMessageLabel = $this->objLanguage->code2Txt('mod_internalmail_replymessage', 'internalmail', $array);
    $replyMessage = $replyMessageLabel."\n";
    $replyMessage.= $emailData['message']."\n";
    $replyMessage.= '----------------------------------------'."\n";
    $objIcon->title = $replyLabel;
    $replyIcon = $objIcon->getLinkedIcon($this->uri(array(
        'action' => 'compose',
        'userId' => $emailData['sender_id'],
        'subject' => 'RE: '.$emailData['subject'],
        'message' => $replyMessage,
        'emailId' => $emailData['id']
    )) , 'reply', 'png');
    $icons = $replyIcon;

    // set up reply all icon
    $arrUserId = explode('|', $emailData['recipient_list']);
    if (!in_array($emailData['sender_id'], $arrUserId)) {
        $arrUserId[] = $emailData['sender_id'];
    }
    $strUserId = implode('|', $arrUserId);
    $objIcon->title = $replyallLabel;
    $replyallIcon = $objIcon->getLinkedIcon($this->uri(array(
        'action' => 'compose',
        'userId' => $strUserId,
        'subject' => 'RE: '.$emailData['subject'],
        'message' => $replyMessage,
        'emailId' => $emailData['id']
    )) , 'replyall', 'png');
    $icons.= $replyallIcon;

    // set up forward icon
    $fwdMessage = '-----  '.$fwdMessageLabel.'  -----'."\n";
    $fwdMessage.= $fromLabel.': '.$from."\n";
    $fwdMessage.= $toLabel.': '.$to."\n";
    $fwdMessage.= $dateLabel.': '.$this->objDate->formatDate($emailData['date_sent']) ."\n";
    $fwdMessage.= $subjectLabel.': '.$emailData['subject']."\n";
    $fwdMessage.= $messageLabel.': '."\n";
    $fwdMessage.= $emailData['message']."\n";
    $fwdMessage.= '----------------------------------------'."\n";
    $fwdMessage.= "\n";
    $objIcon->title = $forwardLabel;
    $forwardIcon = $objIcon->getLinkedIcon($this->uri(array(
        'action' => 'compose',
        'subject' => 'FWD: '.$emailData['subject'],
        'message' => $fwdMessage,
        'emailId' => $emailData['id']
    )) , 'forward', 'png');
    $icons.= $forwardIcon;

    // set up resend icon
    if ($messageData['sender_id'] == $this->userId && $folderId == 'init_3') {
        $objIcon->title = $resendLabel;
        $resendIcon = $objIcon->getLinkedIcon($this->uri(array(
            'action' => 'gotofolder',
            'folderId' => 'init_3',
            'mode' => 'resend',
            'emailId' => $emailData['id'],
            'filter' => $filter
        )) , 'resend', 'png');
        $icons.= $resendIcon;
    } else {
        $icons.= '';
    }

    // set up undelete icon
    if ($folderId == 'init_4') {
        $objIcon->title = $restoreLabel;
        $restoreIcon = $objIcon->getLinkedIcon($this->uri(array(
            'action' => 'gotofolder',
            'folderId' => $folderId,
            'mode' => 'restore',
            'routingId' => $messageData['id'],
            'filter' => $filter
        )) , 'restore');
        $icons.= $restoreIcon;
    } else {
        $icons.= '';
    }
    $washer = $this->getObject('washout', 'utilities');
    $objTable->startRow();
    $objTable->addCell('<b>'.$fromLabel.':</b>', '20%', '', '', '', '');
    $objTable->addCell($from, '', '', '', '', '');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$toLabel.':</b>', '', '', '', '', '');
    $objTable->addCell($to, '', '', '', '', '');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$dateLabel.':</b>', '', '', '', '', '');
    $objTable->addCell($this->objDate->formatDate($emailData['date_sent']) , '', '', '', '', '');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$subjectLabel.':</b>', '', '', '', '', '');
    $objTable->addCell($emailData['subject'], '', '', '', '', '');
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell('<b>'.$messageLabel.':</b><br /><br />'.nl2br($washer->parseText($emailData['message'])) , '', '', '', '', 'colspan="2"');
    $objTable->endRow();
}
$messageTable = $objTable->show();

$objFieldset = new fieldset();
$objFieldset->contents = $messageTable;
$messageFieldset = $objFieldset->show();
// set up attachment table
if (!empty($messageData)) {
    if ($emailData['attachments'] >= 1) {
        $arrAttachments = $this->dbAttachments->getAttachments($emailData['id']);
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $i = 1;
        foreach($arrAttachments as $attachment) {
            $mbSize = round($attachment['file_size']/1048576, 2);
            if ($mbSize < 0.1) {
                $size = round(($attachment['file_size']/1024) , 2) .'KB';
            } else {
                $size = $mbSize.'MB';
            }

            // set up attachment download link
            $objLink = new link($this->uri(array(
                'action' => 'downloadfile',
                'attachId' => $attachment['id']
            )));
            $objLink->link = $attachment['file_name'];
            $attachmentLink = $objLink->show();
            $objTable->startRow();
            $objTable->addCell($i++.'.', '5%', '', '', '', '');
            $objTable->addCell('<nobr>'.$attachmentLink.'</nobr>', '50%', '', '', '', '');
            $objTable->addCell($size, '', '', '', '', '');
            $objTable->endRow();
        }
        $attachmentTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>'.$attachmentLabel.'</b>';
        $objFieldset->extra = ' style="border: 1px solid #808080; margin: 3px; padding: 10px;"';
        $objFieldset->contents = $attachmentTable;
        $attachFieldset = $objFieldset->show();
    } else {
        $attachFieldset = '';
    }
} else {
    $attachFieldset = '';
}

// set up compose icon
$objIcon->title = $composeLabel;
$composeIcon = $objIcon->getLinkedIcon($this->uri(array(
    'action' => 'compose'
)) , 'notes', 'png');

// set up manage folder icon
$objIcon->title = $manageFolderLabel;
$objIcon->extra = '';
$manageIcon = $objIcon->getLinkedIcon($this->uri(array(
    'action' => 'managefolders',
    'currentFolderId' => $folderId
)) , 'managefolders', 'png');

// set up manage address books icon
$objIcon->title = $manageBooksLabel;
$objIcon->extra = '';
$booksIcon = $objIcon->getLinkedIcon($this->uri(array(
    'action' => 'manageaddressbooks',
    'currentFolderId' => $folderId
)) , 'addressbook', 'png');

// set up manage configs icon
$objIcon->title = $manageSettingsLabel;
$objIcon->extra = '';
$configIcon = $objIcon->getLinkedIcon($this->uri(array(
    'action' => 'managesettings',
    'currentFolderId' => $folderId
)) , 'mailconfig', 'png');

// set up main table
$objTable = new htmltable();
//    $objTable->cellspacing='2';
$objTable->cellpadding = '4';
$objTable->startRow();
$objTable->addCell($composeIcon.'&nbsp;'.$manageIcon.'&nbsp;'.$booksIcon.'&nbsp;'.$configIcon, '25%', '', '', 'heading', '');
$objTable->addCell($folderName, '75%', '', '', 'heading', 'colspan="4"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($folderTable, '', 'top', '', '', 'rowspan="5"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($listForm, '', 'top', '', '', 'colspan="4"');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($prevIcon, '3%', '', '', 'heading', '');
$objTable->addCell($selectedMessageLabel.$hiddenForm, '20%', '', '', 'heading', '');
$objTable->addCell($icons, '', '', 'center', 'heading', '');
$objTable->addCell($nextIcon, '3%', '', '', 'heading', '');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($messageFieldset, '', 'top', '', '', 'colspan="4"');
$objTable->endRow();
if ($attachFieldset != '') {
    $objTable->startRow();
    $objTable->addCell($attachFieldset, '', 'top', '', '', 'colspan="4"');
    $objTable->endRow();
}
$mainTable = $objTable->show();
$pageData.= $mainTable;

// set up exit link
$objLink = new link($this->uri(array(
    ''
) , '_default'));
$objLink->link = $exitLabel;
$pageData.= '<br />'.$objLink->show();

$objLayer = new layer();
$objLayer->cssClass='internalmail';
$objLayer->padding = '10px';
$objLayer->addToStr($pageData);
$pageLayer = $objLayer->show();
echo $pageLayer;
?>
