<?php
//Sending display to 1 column layout
//ob_start();
//
//
//$js='<script type="text/javascript">
//      function SubmitForm()
//    {
//    if (document.getElementById("title").value == ""){
//    alert("Provide title");
//    }else
//    {
//        document.forms["newTopicForm"].submit();
//    }
//    }
//
//
//</script>';
//echo $js;
//
//
//
//
//$objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
//echo $objHighlightLabels->show();
//
//$this->loadClass('form', 'htmlelements');
//$this->loadClass('textinput', 'htmlelements');
//$this->loadClass('textarea', 'htmlelements');
//$this->loadClass('button', 'htmlelements');
//$this->loadClass('dropdown', 'htmlelements');
//$this->loadClass('label', 'htmlelements');
//$this->loadClass('iframe', 'htmlelements');
//$this->loadClass('htmlheading', 'htmlelements');
//$this->loadClass('link', 'htmlelements');
//$this->loadClass('radio', 'htmlelements');
//$this->loadClass('hiddeninput', 'htmlelements');
//
//
//// encodeURI( document.getElementById("id").value )
//$header = new htmlheading();
//$header->type=1;
//
//$forumLink = new link($this->uri(array('action'=>'forum', 'id'=>$forumid)));
//$forumLink->link = $forum['forum_name'];
//$forumLink->title = $this->objLanguage->languageText('mod_forum_returntoforum', 'forum');
//
//$header->str=$forumLink->show().' - '.$this->objLanguage->languageText('mod_forum_postnewmessage', 'forum');
//echo $header->show();
//
//if ($mode == 'fix') {
//    echo '<span class="noRecordsMessage error"><strong>'.$this->objLanguage->languageText('mod_forum_messageisblank', 'forum').'</strong><br />&nbsp;</span>';
//}
//
//
//$newTopicForm = new form('newTopicForm', $this->uri( array('module'=>'forum', 'action'=>'savenewtopic', 'type'=>$forumtype)));
//$newTopicForm->displayType = 3;
//$newTopicForm->addRule('title', $this->objLanguage->languageText('mod_forum_addtitle', 'forum'), 'required');
//
//
//$addTable = $this->getObject('htmltable', 'htmlelements');
//$addTable->width='99%';
//$addTable->cellpadding = 10;
//
//// Title
//$addTable->startRow();
//$subjectLabel = new label($this->objLanguage->languageText('word_subject', 'system').':', 'input_title');
//$addTable->addCell($subjectLabel->show(), 120);
//
//$titleInput = new textinput('title');
//$titleInput->size = 50;
//$titleInput->setId('title');
//
//if ($mode == 'fix') {
//    $titleInput->value = $details['title'];
//}
//
//$addTable->addCell($titleInput->show());
//
//$addTable->endRow();
//
//// Type of Topic
//
//$addTable->startRow();
//
//$discussionTypeLabel = new label('<nobr>'.$this->objLanguage->languageText('mod_forum_typeoftopic', 'forum').':</nobr>', 'input_discussionType');
//$addTable->addCell($discussionTypeLabel->show(), 120);
//
//$discussionType = new dropdown('discussionType');
//
//
//foreach ($discussionTypes as $element)
//{
//    $discussionType->addOption($element['id'], $element['type_name']);
//}
//
//
//$counter = 0;
//$objIcon =& $this->getObject('geticon', 'htmlelements');
//
//$objRadioButton = new radio('discussionType');
//$objRadioButton->setTableColumns(3);
//$objRadioButton->setBreakSpace('table');
//
//
//foreach ($discussionTypes as $element)
//{
//    $objIcon->setIcon($element['type_icon'], NULL, 'icons/forum/');
//
//    $objRadioButton->addOption($element['id'], $objIcon->show().' '.htmlentities($element['type_name']));
//
//    //$objRadioButton->extra = 'onclick="changeLabel();"';
//}
//
//// TODO: Set to NULL and add client side validation
//if ($mode == 'fix') {
//    $objRadioButton->setSelected($details['type']);
//} else {
//    $objRadioButton->setSelected($discussionTypes[0]['id']);
//}
//
//$addTable->addCell($objRadioButton->show());
//
//
//
//
//$addTable->endRow();
//
//
//// Show Sticky Topic
//if ($this->objUser->isCourseAdmin($this->contextCode)) {
//    $addTable->startRow();
//    $addTable->addCell($this->objLanguage->languageText('mod_forum_stickytopic', 'forum', 'Sticky Topic').':');
//
//    $sticky = new radio ('stickytopic');
//
//    $objIcon->setIcon('sticky_yes');
//    $sticky->addOption('1', $objIcon->show().$this->objLanguage->languageText('word_yes'));
//    $objIcon->setIcon('sticky_no');
//    $sticky->addOption('0', $objIcon->show().$this->objLanguage->languageText('word_no'));
//    $sticky->setSelected('0');
//    $sticky->setBreakSpace(' &nbsp; ');
//    $addTable->addCell($sticky->show());
//    $addTable->endRow();
//} else {
//    $sticky = new hiddeninput ('stickytopic', 'no');
//    $newTopicForm->addToForm($sticky->show());
//}
//
//// Language
//
//$addTable->startRow();
//
//$languageLabel = new label($this->objLanguage->languageText('word_language', 'system').':', 'input_language');
//$addTable->addCell($languageLabel->show(), 120);
//
////$language =& $this->newObject('language','language');
//$languageDropdown = new dropdown('language');
//
//
//$languageCodes = & $this->newObject('languagecode','language');
//
//// Sort Associative Array by Language, not ISO Code
//$languageList = $languageCodes->iso_639_2_tags->codes;
//
//asort($languageList);
//
//foreach ($languageList as $key => $value) {
//    $languageDropdown->addOption($key, $value);
//}
//
//if ($mode == 'fix') {
//    $languageDropdown->setSelected($details['language']);
//} else {
//    $languageDropdown->setSelected($languageCodes->getISO($this->objLanguage->currentLanguage()));
//}
//
//$addTable->addCell($languageDropdown->show());
//
//$addTable->endRow();
//
//
//$addTable->startRow();
//
//$htmlareaLabel = new label($this->objLanguage->languageText('word_message').':', 'message');
//
//if ($mode == 'fix') {
//    $messageCSS = 'error';
//} else {
//    $messageCSS = NULL;
//}
//$addTable->addCell($htmlareaLabel->show(), 120, 'top', NULL, $messageCSS);
//
//$editor=&$this->newObject('htmlarea','htmlelements');
//$editor->setName('message');
//
//$objContextCondition = &$this->getObject('contextcondition','contextpermissions');
//$this->isContextLecturer = $objContextCondition->isContextMember('Lecturers');
//
//
//$addTable->addCell($editor->show());
//
//$addTable->endRow();
//// ------------------------------
//
//// Only show if forum attachments are allowed
//
//if ($forum['attachments'] == 'Y') {
//    $addTable->startRow();
//
//
//    $attachmentsLabel = new label($this->objLanguage->languageText('mod_forum_attachments', 'forum').':', 'attachments');
//    $addTable->addCell($attachmentsLabel->show(), 120);
//
//    $form = new form('saveattachment', $this->uri(array('action'=>'saveattachment')));
//
//    $objSelectFile = $this->newObject('selectfile', 'filemanager');
//    $objSelectFile->name = 'attachment';
//    $form->addToForm($objSelectFile->show());
//    // Fix undefined variable error for $id
//    if (!isset($id)) {
//        $id="";
//    }
//    $hiddeninput = new hiddeninput('id', $id);
//    $form->addToForm($hiddeninput->show());
//
//    $button = new button('save_attachment_button', 'Attach File');
//    $button->cssClass = 'save';
//    $button->extra='onclick="saveAttachment(this.parentNode)"';
//    if (isset($files)) {
//        if (count($files) > 0) {
//
//            foreach ($files AS $file)
//            {
//                $icon = $objIcon->getDeleteIconWithConfirm($file['id'], array('action'=>'deleteattachment', 'id'=>$file['id'], 'attachmentwindow'=>$id), 'forum', 'Are you sure wou want to remove this attachment');
//                $link ='<li>'.$file['filename'].' '.$icon.'</li>';
//                $form->addToForm($link);
//            }
//
//        }
//    }
//    $hiddenForumInput = new hiddeninput('forum', $forumid);
//    $form->addToForm($hiddenForumInput->show());
//
//    $hiddenTemporaryId = new hiddeninput('temporaryId', $temporaryId);
//    $form->addToForm($hiddenTemporaryId->show());
//    $addTable->addCell($form->show());
//    $addTable->endRow();
//}
//// ------------------------------
//
//
//// Show Forum Subscriptions if enabled
//
//if ($forum['subscriptions'] == 'Y') {
//    $addTable->startRow();
//    $addTable->addCell($this->objLanguage->languageText('mod_forum_emailnotification', 'forum', 'Email Notification').':');
//    $subscriptionsRadio = new radio ('subscriptions');
//    $subscriptionsRadio->addOption('nosubscriptions', $this->objLanguage->languageText('mod_forum_donotsubscribetothread', 'forum', 'Do not subscribe to this thread'));
//    $subscriptionsRadio->addOption('topicsubscribe', $this->objLanguage->languageText('mod_forum_notifytopic', 'forum', 'Notify me via email when someone replies to this thread'));
//    $subscriptionsRadio->addOption('forumsubscribe', $this->objLanguage->languageText('mod_forum_notifyforum', 'forum', 'Notify me of ALL new topics and replies in this forum.'));
//    $subscriptionsRadio->setBreakSpace('<br />');
//
//    if ($forumSubscription) {
//        $subscriptionsRadio->setSelected('forumsubscribe');
//        $subscribeMessage = $this->objLanguage->languageText('mod_forum_youaresubscribedtoforum', 'forum', 'You are currently subscribed to the forum, receiving notification of all new posts and replies.');
//    } else {
//        $subscriptionsRadio->setSelected('nosubscriptions');
//        $subscribeMessage = $this->objLanguage->languageText('mod_forum_youaresubscribedtonumbertopic', 'forum', 'You are currently subscribed to [NUM] topics.');
//        $subscribeMessage = str_replace('[NUM]', $numTopicSubscriptions, $subscribeMessage);
//    }
//
//    $div = '
//    <div class="forumTangentIndent">'.$subscribeMessage.'</div>';
//
//    $addTable->addCell($subscriptionsRadio->show().$div);
//    $addTable->endRow();
//}
//
//$addTable->startRow();
//
//$addTable->addCell(' ');
//
//$submitButton = new button('submitform', $this->objLanguage->languageText('word_submit'));
//$submitButton->cssClass = 'save';
////$submitButton->setToSubmit();
//$submitButton->extra = ' onclick="SubmitForm()"';
//
//$cancelButton = new button('cancel', $this->objLanguage->languageText('word_cancel'));
//$cancelButton->cssClass = 'cancel';
//$returnUrl = $this->uri(array('action'=>'forum', 'id'=>$forumid, 'type'=>$forumtype));
//$cancelButton->setOnClick("window.location='$returnUrl'");
//
//$addTable->addCell($submitButton->show().' / '.$cancelButton->show());
//
//$addTable->endRow();
//
//$newTopicForm->addToForm($addTable->show());
//
//
//
//
//
//echo $newTopicForm->show();
//
//
//$display = ob_get_contents();
//ob_end_clean();
//
//$this->setVar('middleColumn', $display);
?>
<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixTwo();
?>

<div id="threecolumn">
        <div id="Canvas_Content_Body_Region3">
        </div>
        <div id="Canvas_Content_Body_Region2">
                {
                "display" : "block",
                "module" : "forum",
                "block" : "newtopic"
                }
                <div id="middledynamic_area" class="middledynamic_area_layer">&nbsp;</div>
                <div id="middlefeedback_area" class="middlefeedback_area_layer">&nbsp;</div>
        </div>
</div>

<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>