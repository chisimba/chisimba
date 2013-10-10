<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_forumadmin_class
 *
 * @author monwabisi
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
include 'block_forumlist_class_inc.php';

class block_forumadmin extends object {

        var $contextObject;
        var $contextTitle;
        var $objLanguage;
        var $objForum;
        var $contextForums;
        var $objUser;
        var $contextCode;
        var $userContext;

        //put your code here
        public function init() {
                $this->title = '';
                $this->objUser = $this->getObject('user', 'security');
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
                $this->userContext = $this->getObject('usercontext', 'context');
                // Forum Classes
                $this->objForum = & $this->getObject('dbforum');
                $this->contextForums = $this->objForum->getAllContextForums($this->contextCode);
        }

        public function biuldForm() {
                $objIcon = $this->getObject('geticon', 'htmlelements');
                $Header = & $this->getObject('htmlheading', 'htmlelements');
                $Header->type = 1;
                $Header->str = $this->objLanguage->languageText('mod_forum_forumAdministration', 'forum', 'Forum administration') . ': ' . $this->contextTitle;
                if ($this->getParam('message') == 'forumcreated') {
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_forum_newforumcreated', 'forum'));
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'forumupdated') {
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_forum_forumupdated', 'forum'));
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'defaultforumchanged') {
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_forum_defaultforumchanged', 'forum'));
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'visibilityunchanged') {
                        $updatedForum = $this->objForum->getForum($this->getParam('forum'));
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage('Visibility of the ' . $updatedForum['forum_name'] . ' Forum has NOT changed');
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'visibilityupdated') {
                        $updatedForum = $this->objForum->getForum($this->getParam('forum'));
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage('Visibility of the ' . $updatedForum['forum_name'] . ' Forum has been updated. Now set to _________________________________ ');
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'forumdeleted') {
                        $forum = $this->getParam('forum');
                        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($forum . ' has been deleted');
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                $table = $this->newObject('htmltable', 'htmlelements');
                $table->attributes = ' align="center" border="0"';
                $table->cellspacing = '1';
                $table->cellpadding = '5';
                $table->border = '0';
                $table->width = '98%';
//                $table->startHeaderRow();
//                $table->addCell('<p style=" layout-flow : vertical-ideographic;">Default</p>');
//                $table->addHeaderCell($this->objLanguage->languageText('mod_forum_wordName', 'forum'));
//                $table->addHeaderCell($this->objLanguage->languageText('mod_forum_visible', 'forum'), NULL, NULL, 'center');
//                $table->addHeaderCell($this->objLanguage->languageText('mod_forum_forumlocked', 'forum'), NULL, NULL, 'center');
//                $table->addHeaderCell($this->objLanguage->languageText('mod_forum_ratings', 'forum'), NULL, NULL, 'center');
//                $table->addHeaderCell(ucwords($this->objLanguage->code2Txt('mod_forum_studentsstartTopics', 'forum')), NULL, NULL, 'center');
//                $table->addHeaderCell($this->objLanguage->languageText('mod_forum_attachments', 'forum'), NULL, NULL, 'center');
//                $table->addHeaderCell('Email', NULL, NULL, 'center');
//                $table->addHeaderCell($this->objLanguage->languageText('mod_forum_archivedate', 'forum'), NULL, NULL, 'center');
//                $table->addHeaderCell('&nbsp;');

                $oddOrEven = "";
                $rowcount = "";
                $forumsList = &$this->getVar('forumsList');
                foreach ($this->contextForums as $forum) {
                        if ($this->userContext->isContextMember($this->objUser->userId(), $this->contextCode) || $forum['forum_context'] == 'root') {
                                $oddOrEven = ($rowcount == 0) ? "odd" : "even";

                                $tableRow = array();

                                $table->startRow();

                                $forumLink = new link($this->uri(array('module' => 'forum', 'action' => 'forum', 'id' => $forum['id'])));
                                $forumLink->link = $forum['forum_name'];
                                $forumLink->title = $this->objLanguage->languageText('mod_forum_gotoforum', 'forum');

                                if ($forum['defaultforum'] == 'Y') {
                                        $forumLinkStr = '<strong>* </strong>';
                                } else {
                                        $forumLinkStr = ' &nbsp;&nbsp; ';
                                }

                                $editLink = & new link($this->uri(array('module' => 'forum', 'action' => 'editforum', 'id' => $forum['id'])));
                                $editLink->link = $this->objLanguage->languageText('word_edit', 'system');
                                $editLink->title = $this->objLanguage->languageText('mod_forum_editForumSettings', 'forum');

                                $table->addCell($forumLinkStr . $forumLink->show() . ' (' . $editLink->show() . ')');

                                //dropdown menu for adjusting forum visibility
                                $visibility = new dropdown('visibility');
                                $visibility->addOption('Y', 'Y');
                                $visibility->addOption('N', 'N');
                                $visibility->cssClass = $forum['id'];
                                $visibility->setSelected($forum['forum_visible']);
                                $data = 'forum_id=' . $forum['id'] . '&forum_visible=';
                                //send an ajax call to change the forum's visibility
                                $visibility->addOnChange("
                                var visibility_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=forum&action=updateforumsetting',
                                        type: 'post',
                                        data: 'forum_id='+'{$forum['id']}&forum_setting=forum_visible&forum_status='+visibility_value+'',
                                        success: function(){
                                                jQuery().displayConfirmationMessage();
                                        }
                                });
                                ");
                                $table->addCell(''.$this->objLanguage->languageText('mod_forum_visible', 'forum').''.'<br/>'.$visibility->show(), NULL, NULL, 'center');

                                //dropdown menu for adjusting the forum lock status
                                $locked = new dropdown('locked');
                                $locked->addOption('Y', 'Y');
                                $locked->addOption('N', 'N');
                                $locked->cssClass = $forum['id'];
                                $locked->setSelected($forum['forumlocked']);
                                $locked->addOnChange("
                                var visibility_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=forum&action=updateforumsetting',
                                        type: 'post',
                                        data: 'forum_id='+'{$forum['id']}&forum_setting=forumlocked&forum_status='+visibility_value+'',
                                        success: function(){
                                                jQuery.fn.displayConfirmationMessage();
                                        }
                                });
                                ");
                                $table->addCell(''.$this->objLanguage->languageText('mod_forum_forumlocked', 'forum').'<br/>'.$locked->show(), NULL, NULL, 'center');

                                //dropdown menu for adjusting the forum lock status
                                $ratings = new dropdown('ratings');
                                $ratings->addOption('Y', 'Y');
                                $ratings->addOption('N', 'N');
                                $ratings->cssClass = $forum['id'];
                                $ratings->setSelected($forum['ratingsenabled']);
                                $ratings->addOnChange("
                                var ratings_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=forum&action=updateforumsetting',
                                        type: 'post',
                                        data: 'forum_id='+'{$forum['id']}&forum_setting=ratingsenabled&forum_status='+ratings_value+'',
                                        success: function(){
                                                jQuery.displayConfirmationMessage.call();
                                        }
                                });
                                ");
                                $table->addCell(''.$this->objLanguage->languageText('mod_forum_ratings', 'forum').'<br/>'.$ratings->show(), NULL, NULL, 'center');

                                //dropdown menu for adjusting the forum lock status
                                $studentStartsTopic = new dropdown('studentstarttopic');
                                $studentStartsTopic->addOption('Y', 'Y');
                                $studentStartsTopic->addOption('N', 'N');
                                $studentStartsTopic->cssClass = $forum['id'];
                                $studentStartsTopic->setSelected($forum['studentstarttopic']);
                                $studentStartsTopic->addOnChange("
                                var students_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=forum&action=updateforumsetting',
                                        type: 'post',
                                        data: 'forum_id='+'{$forum['id']}&forum_setting=studentstarttopic&forum_status='+students_value+'',
                                        success: function(){
                                                jQuery().displayConfirmationMessage();
                                        }
                                });
                                ");
                                $table->addCell(''.ucfirst($this->objLanguage->code2txt('mod_forum_studentsstartTopics', 'forum')).'<br/>'.$studentStartsTopic->show(), NULL, NULL, 'center');
//                        $table->addCell($forum['studentstarttopic'], NULL, NULL, 'center');
                                //dropdown menu for adjusting the forum lock status
                                $attachments = new dropdown('studentstarttopic');
                                $attachments->addOption('Y', 'Y');
                                $attachments->addOption('N', 'N');
                                $attachments->cssClass = $forum['id'];
                                $attachments->setSelected($forum['attachments']);
                                $attachments->addOnChange("
                                var attachments_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=forum&action=updateforumsetting',
                                        type: 'post',
                                        data: 'forum_id='+'{$forum['id']}&forum_setting=attachments&forum_status='+attachments_value+'',
                                        success: function(){
                                                jQuery().displayConfirmationMessage();
                                        }
                                });
                                ");
                                $table->addCell(''.$this->objLanguage->languageText('mod_forum_attachments', 'forum').'<br/>'.$attachments->show(), NULL, NULL, 'center');

                                //dropdown menu for adjusting the forum lock status
                                $subscriptions = new dropdown('studentstarttopic');
                                $subscriptions->addOption('Y', 'Y');
                                $subscriptions->addOption('N', 'N');
                                $subscriptions->cssClass = $forum['id'];
                                $subscriptions->setSelected($forum['subscriptions']);
                                $subscriptions->addOnChange("
                                var subscription_value = jQuery(this).val();
                                jQuery.ajax({
                                        url: 'index.php?module=forum&action=updateforumsetting',
                                        type: 'post',
                                        data: 'forum_id='+'{$forum['id']}&forum_setting=subscriptions&forum_status='+subscription_value+'',
                                        success: function(){
                                                jQuery().displayConfirmationMessage();
                                        }
                                });
                                ");
                                $table->addCell(''.ucfirst($this->objLanguage->languageText('word_email', 'system')).'<br/>'.$subscriptions->show(), NULL, NULL, 'center');
//                        $table->addCell($forum['subscriptions'], NULL, NULL, 'center');

                                if ($forum['archivedate'] == '0000-00-00' || $forum['archivedate'] == '') {
                                        $table->addCell(''.$this->objLanguage->languageText('mod_forum_archivedate', 'forum').'<br/>'.'n/a', NULL, NULL, 'center');
                                } else {
                                        $table->addCell($forum['archivedate'], NULL, NULL, 'center');
                                }



                                $editLink = new link($this->uri(array('module' => 'forum', 'action' => 'editforum', 'id' => $forum['id'])));
                                $objIcon->setIcon('edit');
                                $editLink->link = $objIcon->show();
                                $editLink->alt = $this->objLanguage->languageText('word_edit', 'forum');
                                $editLink->title = $this->objLanguage->languageText('mod_forum_editForumSettings', 'forum');

                                $editDeleteLink = $editLink->show();

                                if ($forum['defaultforum'] != 'Y') {
                                        $deleteLink = new link($this->uri(array('module' => 'forum', 'action' => 'deleteforum', 'id' => $forum['id'])));
                                        $objIcon->setIcon('delete');
                                        $deleteLink->link = $objIcon->show();
                                        $deleteLink->title = $this->objLanguage->languageText('mod_forum_deleteforum', 'forum');
                                        $deleteLink->alt = $this->objLanguage->languageText('word_delete', 'forum');
                                        $editDeleteLink .= ' &nbsp; ' . $deleteLink->show();
                                }
                                $table->addCell($editDeleteLink, NULL, NULL, NULL, 'nowrap');
                                $table->endRow();
                        }
                }

//                echo $table->show();

                $form = new form('defaultforum', $this->uri(array('module' => 'forum', 'action' => 'setdefaultforum')));
                $form->displayType = 3;

                $createLink = new link($this->uri(array('module' => 'forum', 'action' => 'createforum')));
                $objIcon->setIcon('notes');
                $createLink->cssClass = "sexybutton";
                $createLink->link = $objIcon->show() . '<br/><label class="menu" >' . $this->objLanguage->languageText('mod_forum_createNewForum', 'forum', 'Create new forum') . '</label>';

//                $backToForumListLink = new link($this->uri(NULL));
//                $backToForumListLink->link = $this->objLanguage->languageText('mod_forum_backtoforumindex', 'forum');
                $adminTable = new htmlTable();
                $adminTable->startHeaderRow();
                $adminTable->addHeaderCell($createLink->show(), NULL, NULL, 'center');
                $adminTable->endHeaderRow();
                $form->addToForm($adminTable->show());

                $form->addToForm('<fieldset>');

                $forumLabel = new label('<nobr>* ' . $this->objLanguage->languageText('mod_forum_defaultForum', 'forum', 'Default Forum') . ' &nbsp;&nbsp;/&nbsp;&nbsp; ' . $this->objLanguage->languageText('mod_forum_setDefaultForum', 'forum', 'Set defualt forum') . ':</nobr>', 'input_forum');
                $form->addToForm($forumLabel->show(), 100);

                $discussionType = new dropdown('forum');

                $visibleForums = $this->objForum->getContextForums($this->contextCode);
                foreach ($visibleForums as $forum) {

                        $discussionType->addOption($forum['id'], $forum['forum_name']);
                }
                $defaultForum = $this->objForum->getDefaultForum($this->contextCode);
                $discussionType->setSelected($defaultForum['id']);

                $form->addToForm($discussionType->show());

                $submitButton = new button('submitform', $this->objLanguage->languageText('word_submit', 'system', 'Submit'));
                $submitButton->cssClass = 'save';
                $submitButton->setToSubmit();

                $form->addToForm($submitButton->show());

                $form->addToForm('</fieldset>');

//                echo $form->show();



                $form->addToForm($table->show());
//                echo ('<p>' . $editLink->show() . ' / ' . $backToForumListLink->show() . '</p>');
                $table->endHeaderRow($table);
                return $form->show();
        }

        public function show() {
                return $this->biuldForm();
        }

}

?>
