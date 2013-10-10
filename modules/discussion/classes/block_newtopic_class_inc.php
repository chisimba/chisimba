<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author monwabisi
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_newtopic extends object {

    var $objDiscussion;
    var $objLanguage;
    var $objUser;
    var $contextObject;
    var $contextCode;
    var $discussionTypes;
    var $objDiscussionSubscriptions;
    var $objTopicSubscriptions;

    //put your code here
    public function init() {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('iframe', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->objDiscussion = $this->getObject('dbdiscussion', 'discussion');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        // Get Context Code Settings
        $this->contextObject = & $this->getObject('dbcontext', 'context');
        $this->objDiscussionType = & $this->getObject('dbdiscussiontypes');
        $this->contextCode = $this->contextObject->getContextCode();
        $this->objTopicSubscriptions = & $this->getObject('dbtopicsubscriptions');
        // Load Discussion Subscription classes
        $this->objDiscussionSubscriptions = & $this->getObject('dbdiscussionsubscriptions');
        $this->title = "<h1>{$this->objLanguage->languageText('mod_discussion_startnewtopic','discussion')}</h1>";
    }

    public function biuldEntryForm() {
        $discussionId =  $this->getParam('id');
        $discussion = $this->objDiscussion->getDiscussion($discussionId);
        $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
        $js = '<script type="text/javascript">
      function SubmitForm()
    {
    if (document.getElementById("title").value == ""){
    alert("Provide title");
    }else
    {
        document.forms["newTopicForm"].submit();
    }
    }


</script>';
        $mode = '';
        $temporaryId = '';
        $details ='';
                // Check if form is a result of server-side validation or not'
        if ($this->getParam('message') == 'missing') {
            $details = $this->getSession($this->getParam('tempid'));
//            $this->setVarByRef('details', $details);
            $temporaryId = $details['temporaryId'];
            $this->setVar('mode', 'fix');
            $mode = 'fix';
        } else {
            $temporaryId = $this->objUser->userId() . '_' . mktime();
            $this->setVar('mode', 'new');
            $mode = "new";
        }
        //topic form
        $discussiontype = 'root';
        $newTopicForm = new form('newTopicForm', $this->uri(array('module' => 'discussion', 'action' => 'savenewtopic', 'type' => $discussiontype)));
        $newTopicForm->displayType = 3;
        $newTopicForm->addRule('title', $this->objLanguage->languageText('mod_discussion_addtitle', 'discussion'), 'required');
        //topic table
        $discussionLink = new link($this->uri(array('action' => 'discussion', 'id' => $discussionId)));
        $discussionLink->link = $discussion['discussion_name'];
        $discussionLink->title = $this->objLanguage->languageText('mod_discussion_returntodiscussion', 'discussion');
        //heading
        $header = $this->getObject('htmlheading', 'htmlelements');
        $header->type = 1;
        $header->str = $discussionLink->show() . ' - ' . $this->objLanguage->languageText('mod_discussion_postnewmessage', 'discussion');
        $mode = $this->getVar('mode');
        if ($mode == 'fix') {
            echo '<span class="noRecordsMessage error"><strong>' . $this->objLanguage->languageText('mod_discussion_messageisblank', 'discussion') . '</strong><br />&nbsp;</span>';
        }
        //table
        $addTable = $this->getObject('htmltable', 'htmlelements');
        $addTable->width = '99%';
        $addTable->cellpadding = 10;
        //title
        $titleInput = new textinput('title');
        $titleInput->size = 50;
        $titleInput->setId('title');
        // Title
        $addTable->startRow();
        $subjectLabel = new label($this->objLanguage->languageText('word_subject', 'system') . ':', 'input_title');
        $addTable->addCell($subjectLabel->show(), 120);
        if ($mode == 'fix') {
            $titleInput->value = $details['title'];
        }

        $addTable->addCell($titleInput->show());
        $addTable->endRow();

// Type of Topic

        $addTable->startRow();

        $discussionTypeLabel = new label('<nobr>' . $this->objLanguage->languageText('mod_discussion_typeoftopic', 'discussion') . ':</nobr>', 'input_discussionType');
        $addTable->addCell($discussionTypeLabel->show(), 120);
        $discussionTypes = $this->objDiscussionType->getDiscussionTypes();
        $discussionType = new dropdown('discussionType');
        foreach ($discussionTypes as $element) {
            $discussionType->addOption($element['id'], $element['type_name']);
        }
        $counter = 0;
        $objIcon = & $this->getObject('geticon', 'htmlelements');

        $objRadioButton = new radio('discussionType');
        $objRadioButton->setTableColumns(3);
        $objRadioButton->setBreakSpace('table');
        foreach ($discussionTypes as $element) {
            $objIcon->setIcon($element['type_icon'], NULL, 'icons/discussion/');

            $objRadioButton->addOption($element['id'], $objIcon->show() . ' ' . htmlentities($element['type_name']));

            //$objRadioButton->extra = 'onclick="changeLabel();"';
        }

// TODO: Set to NULL and add client side validation
        if ($mode == 'fix') {
            $objRadioButton->setSelected($details['type']);
        } else {
            $objRadioButton->setSelected($discussionTypes[0]['id']);
        }
        // TODO: Set to NULL and add client side validation
        if ($mode == 'fix') {
            $objRadioButton->setSelected($details['type']);
        } else {
            $objRadioButton->setSelected($discussionTypes[0]['id']);
        }

        $addTable->addCell($objRadioButton->show());
        $addTable->endRow();
        // Show Sticky Topic
        if ($this->objUser->isCourseAdmin($this->contextCode)) {
            $addTable->startRow();
            $addTable->addCell($this->objLanguage->languageText('mod_discussion_stickytopic', 'discussion', 'Sticky Topic') . ':');

            $sticky = new radio('stickytopic');

            $objIcon->setIcon('sticky_yes');
            $sticky->addOption('1', $objIcon->show() . $this->objLanguage->languageText('word_yes'));
            $objIcon->setIcon('sticky_no');
            $sticky->addOption('0', $objIcon->show() . $this->objLanguage->languageText('word_no'));
            $sticky->setSelected('0');
            $sticky->setBreakSpace(' &nbsp; ');
            $addTable->addCell($sticky->show());
            $addTable->endRow();
        } else {
            $sticky = new hiddeninput('stickytopic', 'no');
            $newTopicForm->addToForm($sticky->show());
        }
// Language

        $addTable->startRow();

        $languageLabel = new label($this->objLanguage->languageText('word_language', 'system') . ':', 'input_language');
        $addTable->addCell($languageLabel->show(), 120);
        $languageDropdown = new dropdown('language');
        $languageCodes = & $this->newObject('languagecode', 'language');
// Sort Associative Array by Language, not ISO Code
        $languageList = $languageCodes->iso_639_2_tags->codes;
        asort($languageList);

        foreach ($languageList as $key => $value) {
            $languageDropdown->addOption($key, $value);
        }
        if ($mode == 'fix') {
            $languageDropdown->setSelected($details['language']);
        } else {
            $languageDropdown->setSelected($languageCodes->getISO($this->objLanguage->currentLanguage()));
        }
        $addTable->addCell($languageDropdown->show());
        $addTable->endRow();

        $addTable->startRow();
        $htmlareaLabel = new label($this->objLanguage->languageText('word_message') . ':', 'message');

        if ($mode == 'fix') {
            $messageCSS = 'error';
        } else {
            $messageCSS = NULL;
        }
        $addTable->addCell($htmlareaLabel->show(), 120, 'top', NULL, $messageCSS);

        $editor = &$this->newObject('htmlarea', 'htmlelements');
        $editor->toolbarSet = 'simple';
        $editor->setName('message');

        $objContextCondition = &$this->getObject('contextcondition', 'contextpermissions');
        $this->isContextLecturer = $objContextCondition->isContextMember('Lecturers');
        $addTable->addCell($editor->show());

        $addTable->endRow();
        if ($discussion['attachments'] == 'Y') {
            $addTable->startRow();


            $attachmentsLabel = new label($this->objLanguage->languageText('mod_discussion_attachments', 'discussion') . ':', 'attachments');
            $addTable->addCell($attachmentsLabel->show(), 120);

            $form = new form('saveattachment', $this->uri(array('action' => 'saveattachment')));

            $objSelectFile = $this->newObject('selectfile', 'filemanager');
            $objSelectFile->name = 'attachment';
            $form->addToForm($objSelectFile->show());
            // Fix undefined variable error for $discussionId
            if (!isset($discussionId)) {
                $discussionId = "";
            }
            $hiddeninput = new hiddeninput('id', $discussionId);
            $form->addToForm($hiddeninput->show());

            $button = new button('save_attachment_button', 'Attach File');
            $button->cssClass = 'save';
            $button->extra = 'onclick="saveAttachment(this.parentNode)"';
            if (isset($files)) {
                if (count($files) > 0) {

                    foreach ($files AS $file) {
                        $icon = $objIcon->getDeleteIconWithConfirm($file['id'], array('action' => 'deleteattachment', 'id' => $file['id'], 'attachmentwindow' => $discussionId), 'discussion', 'Are you sure wou want to remove this attachment');
                        $link = '<li>' . $file['filename'] . ' ' . $icon . '</li>';
                        $form->addToForm($link);
                    }
                }
            }
            $hiddenDiscussionInput = new hiddeninput('discussion', $discussionId);
            $form->addToForm($hiddenDiscussionInput->show());

            $details = $this->getVar('details');
            $temporaryId = $details['temporaryId'];
            $hiddenTemporaryId = new hiddeninput('temporaryId', $temporaryId);
            $form->addToForm($hiddenTemporaryId->show());
            $addTable->addCell($form->show());
            $addTable->endRow();
        }

        if ($discussion['subscriptions'] == 'Y') {
            $addTable->startRow();
            $addTable->addCell($this->objLanguage->languageText('mod_discussion_emailnotification', 'discussion', 'Email Notification') . ':');
            $subscriptionsRadio = new radio('subscriptions');
            $subscriptionsRadio->addOption('nosubscriptions', $this->objLanguage->languageText('mod_discussion_donotsubscribetothread', 'discussion', 'Do not subscribe to this thread'));
            $subscriptionsRadio->addOption('topicsubscribe', $this->objLanguage->languageText('mod_discussion_notifytopic', 'discussion', 'Notify me via email when someone replies to this thread'));
            $subscriptionsRadio->addOption('discussionsubscribe', $this->objLanguage->languageText('mod_discussion_notifydiscussion', 'discussion', 'Notify me of ALL new topics and replies in this discussion.'));
            $subscriptionsRadio->setBreakSpace('<br />');

            $numTopicSubscriptions = $this->objTopicSubscriptions->getNumTopicsSubscribed($discussionId, $this->objUser->userId());
            $discussionSubscription = $this->objDiscussionSubscriptions->isSubscribedToDiscussion($discussionId, $this->objUser->userId());
            if ($discussionSubscription) {
                $subscriptionsRadio->setSelected('discussionsubscribe');
                $subscribeMessage = $this->objLanguage->languageText('mod_discussion_youaresubscribedtodiscussion', 'discussion', 'You are currently subscribed to the discussion, receiving notification of all new posts and replies.');
            } else {
                $subscriptionsRadio->setSelected('nosubscriptions');
                $subscribeMessage = $this->objLanguage->languageText('mod_discussion_youaresubscribedtonumbertopic', 'discussion', 'You are currently subscribed to [NUM] topics.');
                $subscribeMessage = str_replace('[NUM]', $numTopicSubscriptions, $subscribeMessage);
            }

            $div = '
    <div class="discussionTangentIndent">' . $subscribeMessage . '</div>';

            $addTable->addCell($subscriptionsRadio->show() . $div);
            $addTable->endRow();
        }

        $addTable->startRow();

        $addTable->addCell(' ');

        $submitButton = new button('submitform', $this->objLanguage->languageText('word_submit'));
        $submitButton->value = $this->objLanguage->languageText('phrase_save','system');
        $submitButton->cssClass = 'save';
//$submitButton->setToSubmit();
        $submitButton->extra = ' onclick="SubmitForm()"';

        $cancelButton = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $cancelButton->cssClass = 'cancel';
        $returnUrl = $this->uri(array('action' => 'discussion', 'id' => $discussionId, 'type' => $discussiontype));
        $cancelButton->setOnClick("window.location='$returnUrl'");

        $addTable->addCell($submitButton->show() . ' ' . $cancelButton->show());

        $addTable->endRow();

        $newTopicForm->addToForm($js.$addTable->show());

//                $newTopicForm->addToForm($addTable->show());
        return $newTopicForm->show();
    }

    public function show() {
        return $this->biuldEntryForm();
    }

}

?>
