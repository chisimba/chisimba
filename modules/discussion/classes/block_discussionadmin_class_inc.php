<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_discussionadmin_class
 *
 * @author monwabisi
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
include 'block_discussionlist_class_inc.php';

class block_discussionadmin extends object {

        var $contextObject;
        var $contextTitle;
        var $objLanguage;
        var $objDiscussion;
        var $contextDiscussions;

        //put your code here
        public function init() {
                $this->title = "Admin block";
                $this->loadClass('form', 'htmlelements');
                $this->loadClass('button', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                $this->loadClass('label', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
                // Get Context Code Settings
                $this->contextObject = & $this->getObject('dbcontext', 'context');
                // If not in context, set code to be 'root' called 'Lobby'
                $this->contextTitle = $this->contextObject->getTitle();
                $this->contextCode = $this->contextObject->getContextCode();
                $this->objLanguage = $this->getObject('language', 'language');
                // Discussion Classes
                $this->objDiscussion = & $this->getObject('dbdiscussion');
                $this->contextDiscussions = $this->objDiscussion->getAllContextDiscussions($this->contextCode);
        }

        public function biuldForm() {
                $objIcon = $this->getObject('geticon', 'htmlelements');
                $Header = & $this->getObject('htmlheading', 'htmlelements');
                $Header->type = 1;
                $Header->str = $this->objLanguage->languageText('mod_discussion_discussionAdministration', 'discussion', 'Discussion administration') . ': ' . $this->contextTitle;
                if ($this->getParam('message') == 'discussioncreated') {
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_newdiscussioncreated', 'discussion'));
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'discussionupdated') {
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_discussionupdated', 'discussion'));
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'defaultdiscussionchanged') {
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_defaultdiscussionchanged', 'discussion'));
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'visibilityunchanged') {
                        $updatedDiscussion = $this->objDiscussion->getDiscussion($this->getParam('discussion'));
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage('Visibility of the ' . $updatedDiscussion['discussion_name'] . ' Discussion has NOT changed');
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'visibilityupdated') {
                        $updatedDiscussion = $this->objDiscussion->getDiscussion($this->getParam('discussion'));
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage('Visibility of the ' . $updatedDiscussion['discussion_name'] . ' Discussion has been updated. Now set to _________________________________ ');
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'discussiondeleted') {
                        $discussion = $this->getParam('discussion');
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($discussion . ' has been deleted');
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                $table = $this->newObject('htmltable', 'htmlelements');
                $table->attributes = ' align="center" border="0"';
                $table->cellspacing = '1';
                $table->cellpadding = '5';
                $table->border = '0';
                $table->width = '98%';
                $table->startHeaderRow();
//                $table->addCell('<p style=" layout-flow : vertical-ideographic;">Default</p>');
                $table->addHeaderCell($this->objLanguage->languageText('mod_discussion_wordName', 'discussion'));
                $table->addHeaderCell($this->objLanguage->languageText('mod_discussion_visible', 'discussion'), NULL, NULL, 'center');
                $table->addHeaderCell($this->objLanguage->languageText('mod_discussion_discussionlocked', 'discussion'), NULL, NULL, 'center');
                $table->addHeaderCell($this->objLanguage->languageText('mod_discussion_ratings', 'discussion'), NULL, NULL, 'center');
                $table->addHeaderCell(ucwords($this->objLanguage->code2Txt('mod_discussion_studentsstartTopics', 'discussion')), NULL, NULL, 'center');
                $table->addHeaderCell($this->objLanguage->languageText('mod_discussion_attachments', 'discussion'), NULL, NULL, 'center');
                $table->addHeaderCell('Email', NULL, NULL, 'center');
                $table->addHeaderCell($this->objLanguage->languageText('mod_discussion_archivedate', 'discussion'), NULL, NULL, 'center');
                $table->addHeaderCell('&nbsp;');

                $oddOrEven = "";
                $rowcount = "";
                $discussionsList = &$this->getVar('discussionsList');
                foreach ($this->contextDiscussions as $discussion) {
                        $oddOrEven = ($rowcount == 0) ? "odd" : "even";

                        $tableRow = array();

                        $table->startRow();

                        $discussionLink = new link($this->uri(array('module' => 'discussion', 'action' => 'discussion', 'id' => $discussion['id'])));
                        $discussionLink->link = $discussion['discussion_name'];
                        $discussionLink->title = $this->objLanguage->languageText('mod_discussion_gotodiscussion', 'discussion');

                        if ($discussion['defaultdiscussion'] == 'Y') {
                                $discussionLinkStr = '<strong>* </strong>';
                        } else {
                                $discussionLinkStr = ' &nbsp;&nbsp; ';
                        }

                        $editLink = & new link($this->uri(array('module' => 'discussion', 'action' => 'editdiscussion', 'id' => $discussion['id'])));
                        $editLink->link = $this->objLanguage->languageText('word_edit', 'system');
                        $editLink->title = $this->objLanguage->languageText('mod_discussion_editDiscussionSettings', 'discussion');

                        $table->addCell($discussionLinkStr . $discussionLink->show() . ' (' . $editLink->show() . ')');

                        //dropdown menu for adjusting discussion visibility
                        $visibility = new dropdown('visibility');
                        $visibility->addOption('Y', 'Y');
                        $visibility->addOption('N', 'N');
                        $visibility->cssClass = $discussion['id'];
                        $visibility->setSelected($discussion['discussion_visible']);
                        $data = 'discussion_id='.$discussion['id'].'&discussion_visible=';
                        //send an ajax call to change the discussion's visibility
                        $visibility->addOnChange("
                                var visibility_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=discussion&action=updatediscussionsetting',
                                        type: 'post',
                                        data: 'discussion_id='+'{$discussion['id']}&discussion_setting=discussion_visible&discussion_status='+visibility_value+'',
                                        success: function(){
                                                jQuery().displayConfirmationMessage();
                                        }
                                });
                                ");
                        $table->addCell($visibility->show(), NULL, NULL, 'center');

                        //dropdown menu for adjusting the discussion lock status
                        $locked = new dropdown('locked');
                        $locked->addOption('Y', 'Y');
                        $locked->addOption('N', 'N');
                        $locked->cssClass = $discussion['id'];
                        $locked->setSelected($discussion['discussionlocked']);
                        $locked->addOnChange("
                                var visibility_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=discussion&action=updatediscussionsetting',
                                        type: 'post',
                                        data: 'discussion_id='+'{$discussion['id']}&discussion_setting=discussionlocked&discussion_status='+visibility_value+'',
                                        success: function(){
                                                jQuery.fn.displayConfirmationMessage();
                                        }
                                });
                                ");
                        $table->addCell($locked->show(), NULL, NULL, 'center');

                        //dropdown menu for adjusting the discussion lock status
                        $ratings = new dropdown('ratings');
                        $ratings->addOption('Y', 'Y');
                        $ratings->addOption('N', 'N');
                        $ratings->cssClass = $discussion['id'];
                        $ratings->setSelected($discussion['ratingsenabled']);
                        $ratings->addOnChange("
                                var ratings_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=discussion&action=updatediscussionsetting',
                                        type: 'post',
                                        data: 'discussion_id='+'{$discussion['id']}&discussion_setting=ratingsenabled&discussion_status='+ratings_value+'',
                                        success: function(){
                                                jQuery.displayConfirmationMessage.call();
                                        }
                                });
                                ");
                        $table->addCell($ratings->show(), NULL, NULL, 'center');

                        //dropdown menu for adjusting the discussion lock status
                        $studentStartsTopic = new dropdown('studentstarttopic');
                        $studentStartsTopic->addOption('Y', 'Y');
                        $studentStartsTopic->addOption('N', 'N');
                        $studentStartsTopic->cssClass = $discussion['id'];
                        $studentStartsTopic->setSelected($discussion['studentstarttopic']);
                        $studentStartsTopic->addOnChange("
                                var students_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=discussion&action=updatediscussionsetting',
                                        type: 'post',
                                        data: 'discussion_id='+'{$discussion['id']}&discussion_setting=studentstarttopic&discussion_status='+students_value+'',
                                        success: function(){
                                                jQuery().displayConfirmationMessage();
                                        }
                                });
                                ");
                        $table->addCell($studentStartsTopic->show(), NULL, NULL, 'center');
//                        $table->addCell($discussion['studentstarttopic'], NULL, NULL, 'center');

                        //dropdown menu for adjusting the discussion lock status
                        $attachments = new dropdown('studentstarttopic');
                        $attachments->addOption('Y', 'Y');
                        $attachments->addOption('N', 'N');
                        $attachments->cssClass = $discussion['id'];
                        $attachments->setSelected($discussion['attachments']);
                        $attachments->addOnChange("
                                var attachments_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=discussion&action=updatediscussionsetting',
                                        type: 'post',
                                        data: 'discussion_id='+'{$discussion['id']}&discussion_setting=attachments&discussion_status='+attachments_value+'',
                                        success: function(){
                                                jQuery().displayConfirmationMessage();
                                        }
                                });
                                ");
                        $table->addCell($attachments->show(), NULL, NULL, 'center');

                         //dropdown menu for adjusting the discussion lock status
                        $subscriptions = new dropdown('studentstarttopic');
                        $subscriptions->addOption('Y', 'Y');
                        $subscriptions->addOption('N', 'N');
                        $subscriptions->cssClass = $discussion['id'];
                        $subscriptions->setSelected($discussion['subscriptions']);
                        $subscriptions->addOnChange("
                                var subscription_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=discussion&action=updatediscussionsetting',
                                        type: 'post',
                                        data: 'discussion_id='+'{$discussion['id']}&discussion_setting=subscriptions&discussion_status='+subscription_value+'',
                                        success: function(){
                                                jQuery().displayConfirmationMessage();
                                        }
                                });
                                ");
                        $table->addCell($subscriptions->show(), NULL, NULL, 'center');
//                        $table->addCell($discussion['subscriptions'], NULL, NULL, 'center');

                        if ($discussion['archivedate'] == '0000-00-00' || $discussion['archivedate'] == '') {
                                $table->addCell('n/a', NULL, NULL, 'center');
                        } else {
                                $table->addCell($discussion['archivedate'], NULL, NULL, 'center');
                        }



                        $editLink = new link($this->uri(array('module' => 'discussion', 'action' => 'editdiscussion', 'id' => $discussion['id'])));
                        $objIcon->setIcon('edit');
                        $editLink->link = $objIcon->show();
                        $editLink->alt = $this->objLanguage->languageText('word_edit', 'discussion');
                        $editLink->title = $this->objLanguage->languageText('mod_discussion_editDiscussionSettings', 'discussion');

                        $editDeleteLink = $editLink->show();

                        if ($discussion['defaultdiscussion'] != 'Y') {
                                $deleteLink = new link($this->uri(array('module' => 'discussion', 'action' => 'deletediscussion', 'id' => $discussion['id'])));
                                $objIcon->setIcon('delete');
                                $deleteLink->link = $objIcon->show();
                                $deleteLink->title = $this->objLanguage->languageText('mod_discussion_deletediscussion', 'discussion');
                                $deleteLink->alt = $this->objLanguage->languageText('word_delete', 'discussion');
                                $editDeleteLink .= ' &nbsp; ' . $deleteLink->show();
                        }
                        $table->addCell($editDeleteLink, NULL, NULL, NULL, 'nowrap');
                        $table->endRow();
                }

//                echo $table->show();

                $form = new form('defaultdiscussion', $this->uri(array('module' => 'discussion', 'action' => 'setdefaultdiscussion')));
                $form->displayType = 3;
                
                $createLink = new link($this->uri(array('module' => 'discussion', 'action' => 'creatediscussion')));
                $objIcon->setIcon('notes');
                $createLink->cssClass = "sexybutton";
                $createLink->link = $objIcon->show().'<br/><label class="menu" >'.$this->objLanguage->languageText('mod_discussion_createNewDiscussion', 'discussion', 'Create new discussion').'</label>';
                
//                $backToDiscussionListLink = new link($this->uri(NULL));
//                $backToDiscussionListLink->link = $this->objLanguage->languageText('mod_discussion_backtodiscussionindex', 'discussion');
                $adminTable = new htmlTable();
                $adminTable->startHeaderRow();
                $adminTable->addHeaderCell($createLink->show(),NULL,NULL,'center');
                $adminTable->endHeaderRow();
                $form->addToForm($adminTable->show());

                $form->addToForm('<fieldset>');

                $discussionLabel = new label('<nobr>* ' . $this->objLanguage->languageText('mod_discussion_defaultDiscussion', 'discussion', 'Default Discussion') . ' &nbsp;&nbsp;/&nbsp;&nbsp; ' . $this->objLanguage->languageText('mod_discussion_setDefaultDiscussion', 'discussion', 'Set defualt discussion') . ':</nobr>', 'input_discussion');
                $form->addToForm($discussionLabel->show(), 100);

                $discussionType = new dropdown('discussion');

                $visibleDiscussions = $this->objDiscussion->getContextDiscussions($this->contextCode);
                foreach ($visibleDiscussions as $discussion) {

                        $discussionType->addOption($discussion['id'], $discussion['discussion_name']);
                }
                $defaultDiscussion = $this->objDiscussion->getDefaultDiscussion($this->contextCode);
                $discussionType->setSelected($defaultDiscussion['id']);

                $form->addToForm($discussionType->show());

                $submitButton = new button('submitform', $this->objLanguage->languageText('word_submit', 'system', 'Submit'));
                $submitButton->cssClass = 'save';
                $submitButton->setToSubmit();

                $form->addToForm($submitButton->show());

                $form->addToForm('</fieldset>');

//                echo $form->show();



                $form->addToForm($table->show());
//                echo ('<p>' . $editLink->show() . ' / ' . $backToDiscussionListLink->show() . '</p>');
                $table->endHeaderRow($table);
                return $form->show();
        }

        public function show() {
                return $this->biuldForm();
        }

}

?>
