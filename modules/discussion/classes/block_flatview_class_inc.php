<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bllock_flatview_class_inc
 *
 * @author monwabisi
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_flatview extends object {

        var $objUser;
        var $objLanguage;
        var $objPost;
        var $objDiscussion;
        var $js;
        var $Icon;
        var $contextObject;
        var $contextCode;
        var $objTopic;
        var $objPostRatings;
        var $objDiscussionRatings;
        var $dbDiscussionSubscriptions;
        var $dbDiscussionPost;

        //put your code here
        function init() {
                $this->loadClass('htmlheading', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
                $this->loadClass('form', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                $this->loadClass('textinput', 'htmlelements');
                $this->loadClass('button', 'htmlelements');
                $this->loadClass('checkbox', 'htmlelements');
                $this->loadClass('radio', 'htmlelements');
                // Get Context Code Settings
                $this->contextObject = & $this->getObject('dbcontext', 'context');
                $this->contextCode = $this->contextObject->getContextCode();
                $this->objLanguage = $this->getObject('language', 'language');
                $this->objUser = $this->getObject('user', 'security');
                $this->objPost = $this->getObject('dbpost', 'discussion');
                $this->objDiscussion = $this->getObject('dbdiscussion', 'discussion');
                $this->dbDiscussionPost = $this->getObject('dbtopicsubscriptions', 'discussion');
                // Discussion Ratings
                $this->objDiscussionRatings = & $this->getObject('dbdiscussion_ratings');
                //discussion subscriptions
                $this->dbDiscussionSubscriptions = $this->getObject('dbdiscussionsubscriptions', 'discussion');
                $this->objPostRatings = & $this->getObject('dbpost_ratings');
                $this->objIcon = $this->newObject('geticon', 'htmlelements');
                $this->objTopic = $this->getObject('dbtopic', 'discussion');
                $this->objDateTime = & $this->getObject('dateandtime', 'utilities');
                $this->title=NULL;
                $this->js = '
<script type="text/javascript">
    //<![CDATA[

    function SubmitForm()
    {
        document.forms["postReplyForm"].submit();
    }

    //]]>
</script>
';
        }

        function buildform() {
                // Get details on the topic
                $topic_id = $this->getParam('id');
                $post = $this->objPost->getRootPost($topic_id);
                $discussionlocked = FALSE;
                $discussiontype = $this->getParam('type');
                // Get details of the Discussion
                $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
                $header = new htmlheading();
                $header->type = 1;
                $link = new link($this->uri(array('action' => 'discussion', 'id' => $post['discussion_id'], 'type' => $discussiontype)));
                $link->link = $post['discussion_name'];
                //the heading
                $headerString = $link->show() . ' &gt; ' . stripslashes($post['post_title']);
                $header->str = $headerString;
                $hardHTML = '';
                //table
                $htmlTable = $this->getObject('htmltable', 'htmlelements');
                $htmlTable->cssId = "flatview";
                $topicDetails = $this->objTopic->getTopicDetails($topic_id);
                $this->title = $this->objLanguage->languageText('mod_discussion_replytotopic', 'discussion').$post['post_title'];
                // Check if discussion is locked - if true - disable / editing replies
                if ($this->objDiscussion->checkIfDiscussionLocked($post['discussion_id'])) {
                        $this->objPost->repliesAllowed = FALSE;
                        $this->objPost->editingPostsAllowed = FALSE;
                        $this->objPost->discussionLocked = TRUE;
                        $discussionlocked = TRUE;
                } else {
                        if ($this->objUser->isCourseAdmin($this->contextCode)) {
                                $this->objPost->showModeration = TRUE;
                        }
                }
                if ($this->objUser->isCourseAdmin($this->contextCode) && !$discussionlocked && $discussiontype != 'workgroup' && $this->objUser->isLoggedIn()) {
                        $this->objIcon->setIcon('notes');
                        $newtopiclink = new link($this->uri(array('action' => 'newtopic', 'id' => $post['discussion_id'], 'type' => $discussiontype)));
                        $newtopiclink->link = $this->objIcon->show()."<br/>".$this->objLanguage->languageText('mod_discussion_startnewtopic', 'discussion');
                        $newtopiclink->cssClass .= 'sexybutton';
                        $newtopiclink->title = $this->objLanguage->languageText('phrase_starttopic','system');
                        $this->objIcon->setIcon('moderate');
                        $this->objIcon->title = $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion');
//                        $this->objIcon->alt = $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion');

                        $moderateTopicLink = new link($this->uri(array('action' => 'moderatetopic', 'id' => $post['topic_id'], 'type' => $discussiontype)));
                        $moderateTopicLink->cssClass .= 'sexybutton';
                        $moderateTopicLink->link = $this->objIcon->show()."<br/>{$this->objLanguage->languageText('mod_discussion_moderatetopic','discussion')}";
                        $moderateTopicLink->cssId = "moderatetopic";
                        //moderation options
//                        $this->loadClass('checkbox', 'htmlelements');
//                        $checkBoxOne = new checkbox('sticky', '&nbsp; <b></b>');
//                        $checkBoxOne->setValue('true');
//                        $checkBoxOne->extra = "display:none;";
                        $moderationDiv = "<div class='' >";
//                        $moderationDiv .= $checkBoxOne->show();
                        //
                        $checkBoxTwo = new checkbox('lock', '&nbsp; <b>Sticky topic</b>');
                        $checkBoxTwo->setValue('true');
//                        $moderationDiv .= '<br/>'.$checkBoxTwo->show();
                        /**
                         * @TOPIC_OBJECT
                         */
//                        if ($topicDetails['status'] == "CLOSE") {
//                                $checkBoxOne->setChecked(TRUE);
//                        }
//                        $frmModerate->addToForm($checkBoxOne->show() . '<br/>');
//                        $frmModerate->addToForm($checkBoxTwo->show() . '<br/>');
//                        $frmModerate->addToForm($cancelButton->show() . '&nbsp;&nbsp;&nbsp;');
//                        $moderationDiv .= $frmModerate->show();
                        $moderationDiv .= "</div >";
//                        $moderateTopicLink->link .= $moderationDiv;
//                                $moderateTopicLink->link .= $optionLink->show();
                }
                /**
                 * @SAVE_BUTTON
                 */
                $saveButton = new button();
                $saveButton->cssId = "moderationSave";
                $saveButton->value = $this->objLanguage->languageText('phrase_save','system');
//                        $frmModerate->addToForm($saveButton->show() . '&nbsp;&nbsp;');
                /**
                 * @CANCEL_BUTTON
                 */
                $cancelButton = new button();
                $cancelButton->cssId = "moderationCancel";
                $cancelButton->value = $this->objLanguage->languageText('word_cancel','system');
                ////Confirmation messages
                if ($this->getParam('message') == 'deletesuccess') {
                        $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_postremoved', 'discussion'));
                        $timeoutMessage->setTimeout(20000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'save') {
                        $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_postsaved', 'discussion'));
                        $timeoutMessage->setTimeout(20000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'postupdated') {
                        $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_postupdated', 'discussion'));
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }
                if ($this->getParam('message') == 'replysaved') {
                        $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
                        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_replysaved', 'discussion'));
                        $timeoutMessage->setTimeout(10000);
                        echo ('<p>' . $timeoutMessage->show() . '</p>');
                }

// Error Messages
                if ($this->getParam('message') == 'cantreplydiscussionlocked') {
                        $this->setErrorMessage('This Discussion has been Locked. You cannot post a reply to this Topic'); // LTE
                }
                if ($this->getParam('message') == 'cantreplytopiclocked') {
                        $this->setErrorMessage('This Topic has been Locked. You cannot post a reply to this Topic'); // LTE
                }

                if ($post['status'] == 'CLOSE') {
                        $hardHTML = '<div class="discussionTangentIndent">';
                        $hardHTML.= '<strong>' . $this->objLanguage->languageText('mod_discussion_topiclockedby', 'discussion') . ' ' . $this->objUser->fullname($post['lockuser']) .$this->objLanguage->languageText('word_on','system') . $this->objDateTime->formatdate($post['lockdate']) . '</strong>';
                        $hardHTML .= '<p>' . $post['lockreason'] . '</p>';
                        $hardHTML .= '</div>';
                }
                //ratings form
                $ratingsForm = new form('savepostratings', $this->uri(array('action' => 'savepostratings')));
                // Create the indented thread
                $thread = $this->objPost->displayFlatThread($topic_id);

                $ratingsForm->addToForm($thread);
                //hidden input
                $hiddenTypeInput = new textinput('discussionType');
                $hiddenTypeInput->fldType = 'hidden';
                $hiddenTypeInput->value = $post['type_id'];
//                $ratingsForm->addToForm($hiddenTypeInput->show());


                $hiddenTangentInput = new textinput('parent');
                $hiddenTangentInput->fldType = 'hidden';
                $hiddenTangentInput->value = $post['post_id'];
//                $ratingsForm->addToForm($hiddenTangentInput->show());

                $topicHiddenInput = new textinput('topic');
                $topicHiddenInput->fldType = 'hidden';
                $topicHiddenInput->value = $post['topic_id'];
//                $ratingsForm->addToForm($topicHiddenInput->show());
                $hiddenDiscussionInput = new textinput('discussion');
                $hiddenDiscussionInput->fldType = 'hidden';
                if (isset($discussion)) {
                        $hiddenDiscussionInput->value = $discussion['id'];
                }
//                $ratingsForm->addToForm($hiddenDiscussionInput->show());
                //show ratings variable, set to false by default
                $showRatingsForm = FALSE;
                // Check if ratings allowed in Discussion
                if ($discussion['ratingsenabled'] == 'Y') {
                        $this->objPost->discussionRatingsArray = $this->objDiscussionRatings->getDiscussionRatings($post['discussion_id']);
                        $this->objPost->showRatings = TRUE;
                        $showRatingsForm = TRUE;
                } else {
                        $this->objPost->showRatings = FALSE;
                }
                // Determine whether to show the submit form for the ratings form
// Without this button, form is a waste, but need to make efficient
                if ($showRatingsForm) {
                        $objButton = new button('submitForm');
                        $objButton->cssClass = 'save';
                        $objButton->setValue($this->objLanguage->languageText('mod_discussion_sendratings', 'discussion'));
                        $objButton->setToSubmit();

                        if ($post['status'] != 'CLOSE' && !$discussionlocked) {
//                                $ratingsForm->addToForm('<p align="right">' . $objButton->show() . '</p>');
                        }
                }

                $details = $this->getSession($this->getParam('tempid'));
                $temporaryId = $details['temporaryId'];
                $hiddenTemporaryId = new textinput('temporaryId');
                $hiddenTemporaryId->fldType = 'hidden';
                if (!isset($temporaryId)) {
                        $temporaryId = "";
                }

                if ($post['topic_tangent_parent'] == '0') {
                        $tangentsTable = $this->objTopic->showTangentsTable($post['topic_id']);
                }

                if (isset($tangentsTable)) {
                        echo $tangentsTable;
                }
                $htmlTable->startHeaderRow();
                if ($this->objUser->isCourseAdmin($this->contextCode) && !$discussionlocked && $discussiontype != 'workgroup' && $this->objUser->isLoggedIn()) {
                        $htmlTable->addHeaderCell($moderateTopicLink->show() . $moderationDiv, NULL, NULL, "center");
                        $htmlTable->addHeaderCell($newtopiclink->show(), NULL, NULL, "center");
                }
                $noAlerts = new radio('subscription');
                $noAlerts->addOption('nosubscription', 'Do not notify me');
//                $noAlerts->setvalue('nosubscription');
                /**
                 * @test
                 */
                /**
                 * @testEnd
                 */
                $notifyThread = new radio('subscription');
                $notifyThread->addOption('subscribetopic', '&nbsp; Notify me of this topic');
//                $notifyThead->setValue("subscribetopic");
                $notifyAll = new radio('subscription');
                $notifyAll->addOption('subscribetoall', '&nbsp; Notify me of all topics and replies in this discussion');
                $this->objIcon->title = $this->objLanguage->languageText('phrase_nitification','system');
                /**
                 * if user is subscribed to discussion, indicate
                 */
                if ($this->dbDiscussionSubscriptions->isSubscribedToDiscussion($discussion['id'], $this->objUser->userId($this->objUser->email()))) {
                        $notifyAll->selected = TRUE;
                        $this->objIcon->setIcon('alerts-on');
                }
                /**
                 * if user is subscribed to topic, indicate by selecting the topic radio by default
                 */
                if ($this->dbDiscussionPost->isSubscribedToTopic($topic_id, $this->objUser->userId($this->objUser->email()))) {
                        $notifyThread->selected = TRUE;
                        $this->objIcon->setIcon('alerts-on');
                }
                if (!$this->dbDiscussionPost->isSubscribedToTopic($topic_id, $this->objUser->userId($this->objUser->email()))) {
                        if (!$this->dbDiscussionPost->isSubscribedToTopic($topic_id, $this->objUser->userId($this->objUser->email()))) {
                                $noAlerts->selected = TRUE;
                                $this->objIcon->setIcon('alerts');
                        }
                }
                //subscribsion
                $subscribeLink = new link("#");
//                $this->objIcon->setIcon('alerts');
                $subscribeLink->cssClass = "moderatetopic sexybutton";
                $subscribeLink->link = $this->objIcon->show()."<br/>{$this->objLanguage->languageText('phrase_nitification','system')}";
                //floating div
                $subscribeDiv = "<div class='hiddenOptions' >";
//                $notifyAll->setvalue("subscribetoall");
                //hidden form object to carry the topic ID and the discussion ID
                $discussionHiddenInput = new hiddeninput('discussion_id', $topicDetails['discussion_id']);
                $topicHiddenInput = new hiddeninput('topic_id', $topic_id);
                //form
                if ($discussion['subscriptions'] == 'Y') {
                        $frmModerate = new form('topicModeration');
                        //add objects to the form
                        $frmModerate->addToForm($discussionHiddenInput->show());
                        $frmModerate->addToForm($topicHiddenInput->show());
                        $frmModerate->addToForm($noAlerts->show() . '<br/>');
                        $frmModerate->addToForm($notifyThread->show() . '<br/>');
                        $frmModerate->addToForm($notifyAll->show() . '<br/>');
                        $frmModerate->addToForm($saveButton->show());
                        $frmModerate->addToForm($cancelButton->show());
                        $subscribeDiv .= $frmModerate->show();
                        $htmlTable->addHeaderCell($subscribeLink->show() . $subscribeDiv, NULL, NULL, "center");
                }
//                $subscribeDiv .= $noAlerts->show().'<br/>'.$notifyThead->show().'<br>'.$notifyAll->show().'</div>';
                $htmlTable->endHeaderRow();

//        $elements .= $this->objTopic->showChangeDisplayTypeForm($topic_id, 'flatview');
                $elements = $htmlTable->show().'<br/><br/>' . $ratingsForm->show();
                return $elements;
        }

        function show() {
                return $this->buildform();
        }

}

?>
