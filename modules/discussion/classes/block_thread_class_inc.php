<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_class_thread_inc
 *
 * @author monwabisi
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_thread extends object {

    var $objUser;
    var $objPost;
    var $objTopic;
    var $objDiscussion;
    var $contextObject;
    var $contextCode;
    var $objLanguage;
    var $objDiscussionRatings;
    var $objPostRatings;
    var $domDoc;

    //put your code here
    public function init() {
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('dbpost', 'discussion');
        $this->domDoc = new DOMDocument('utf-8');
        $js = '
<script type="text/javascript">
    //<![CDATA[

    function SubmitForm()
    {
        document.forms["postReplyForm"].submit();
    }

    //]]>
</script>
';
        echo $js;
        $this->title = "Thread view";
        $this->objUser = $this->getObject('user', 'security');
        $this->objPost = $this->getObject('dbpost', 'discussion');
        $this->objTopic = $this->getObject('dbtopic', 'discussion');
        $this->objDiscussion = $this->getObject('dbdiscussion', 'discussion');
        // Get Context Code Settings
        $this->contextObject = & $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();
        $this->objLanguage = $this->getObject('language', 'language');
        // Discussion Ratings
        $this->objDiscussionRatings = & $this->getObject('dbdiscussion_ratings');
        $this->objPostRatings = & $this->getObject('dbpost_ratings');
    }

    function buildForm() {
        $topic_id = $this->getParam('id');
        $this->setVar('pageSuppressXML', true);
        $js = $this->getJavascriptFile('contracthead.js', 'discussion');
        $this->appendArrayVar('headerParams', $js);
        $objIcon = $this->getObject('geticon', 'htmlelements');
        $html = "";

        if ($this->getParam('message') == 'invalidattachment') {
            $this->setErrorMessage($this->objLanguage->languageText('mod_discussion_attachment_not_found', 'discussion', 'Could not find requested attachment.'));
        }
        $header = new htmlheading();
        $header->type = 1;
        $post = $this->objPost->getRootPost($topic_id);
        $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
        $link = new link($this->uri(array('action' => 'discussion', 'id' => $post['discussion_id'], 'type' => $discussion['discussion_type'])));
        $link->link = $post['discussion_name'];
        $headerString = $link->show() . ' &gt; ' . stripslashes($post['post_title']);
        $header->str = $headerString;
        // Check if discussion is locked - if true - disable / editing replies
        if ($this->objDiscussion->checkIfDiscussionLocked($post['discussion_id'])) {
            $this->objPost->repliesAllowed = FALSE;
            $this->objPost->editingPostsAllowed = FALSE;
            $this->objPost->discussionLocked = TRUE;
            $discussionlocked = TRUE;
        } else {
            $discussionlocked = FALSE;
            if ($this->objUser->isCourseAdmin($this->contextCode)) {
                $this->objPost->showModeration = TRUE;
            }
        }

        if ($this->objUser->isCourseAdmin($this->contextCode) && !$discussionlocked && $discussion['discussion_type'] != 'workgroup' && $this->objUser->isLoggedIn()) {
            $objIcon->setIcon('moderate');
            $objIcon->title = $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion');
            $objIcon->alt = $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion');

            $moderateTopicLink = new link($this->uri(array('action' => 'moderatetopic', 'id' => $post['topic_id'], 'type' => $discussion['discussion_type'])));
            $moderateTopicLink->link = $objIcon->show();
            $elements = $moderateTopicLink->show();
            $header->str .= ' ' . $moderateTopicLink->show();
        }
        $html .= $elements;
        ////Confirmation messages
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

//// Error Messages
        if ($this->getParam('message') == 'cantreplydiscussionlocked') {
            $this->setErrorMessage('This Discussion has been Locked. You cannot post a reply to this Topic'); // LTE
        }
        if ($this->getParam('message') == 'cantreplytopiclocked') {
            $this->setErrorMessage('This Topic has been Locked. You cannot post a reply to this Topic'); // LTE
        }
        $changeDisplayForm = $this->objTopic->showChangeDisplayTypeForm($topic_id, 'thread');
        $html = $changeDisplayForm;
        if ($post['status'] == 'CLOSE') {
            $html = '<div class="discussionTangentIndent">';
            $html .= '<strong>' . $this->objLanguage->languageText('mod_discussion_topiclockedby', 'discussion') . ' ' . $this->objUser->fullname($post['lockuser']) . ' on ' . $this->objDateTime->formatdate($post['lockdate']) . '</strong>';
            $html .= '<p>' . $post['lockreason'] . '</p>';
            $html .= '</div>';
        }
        $ratingsForm = new form('savepostratings', $this->uri(array('action' => 'savepostratings')));
        // Create the indented thread
        $thread = $this->objPost->displayThread($topic_id);
        $ratingsForm->addToForm($thread);
        // Check if ratings allowed in Discussion
        if ($discussion['ratingsenabled'] == 'Y') {
            $this->objPost->discussionRatingsArray = $this->objDiscussionRatings->getDiscussionRatings($post['discussion_id']);
            $this->objPost->showRatings = TRUE;
            $showRatingsForm = TRUE;
        } else {
            $showRatingsForm = FALSE;
            $this->objPost->showRatings = FALSE;
        }
//        $ratingsForm->addToForm($thread);
        //// Determine whether to show the submit form for the ratings form
//// Without this button, form is a waste, but need to make efficient
        if ($showRatingsForm) {
            $objButton = &new button('submitForm');
            $objButton->cssClass = 'save';
            $objButton->setValue($this->objLanguage->languageText('mod_discussion_sendratings', 'discussion'));
            $objButton->setToSubmit();

            if ($post['status'] != 'CLOSE' && !$discussionlocked) {
                $ratingsForm->addToForm('<p align="right">' . $objButton->show() . '</p>');
            }
            // These elements are need for the redirect
            $hiddenTopicId = new textinput('topic', $post['topic_id']);
            $hiddenTopicId->fldType = 'hidden';
            $ratingsForm->addToForm($hiddenTopicId->show());
//            $html .= $ratingsForm->show();
        }
        $replylink = new link($this->uri(array('action' => 'postreply', 'id' => $post['post_id'], 'type' => $discussion['discussion_type'])));
        $replylink->link = $this->objLanguage->languageText('mod_discussion_replytotopic', 'discussion');
        $newtopiclink = new link($this->uri(array('action' => 'newtopic', 'id' => $post['discussion_id'], 'type' => $discussion['discussion_type'])));
        $newtopiclink->link = $this->objLanguage->languageText('mod_discussion_startnewtopic', 'discussion');

        $returntodiscussion = new link($this->uri(array('action' => 'discussion', 'id' => $post['discussion_id'], 'type' => $discussion['discussion_type'])));
        $returntodiscussion->link = $this->objLanguage->languageText('mod_discussion_returntodiscussion', 'discussion');

        $moderateTopicLink = new link($this->uri(array('action' => 'moderatetopic', 'id' => $post['topic_id'], 'type' => $discussion['discussion_type'])));
        $moderateTopicLink->link = $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion');

        $html .= $ratingsForm->show();
        $tangentsTable = $this->objTopic->showTangentsTable($post['topic_id']);
        if (isset($tangentsTable)) {
            $html .= $tangentsTable;
        }
        $popUpDiv = "<div >{$this->objPost->showPostReplyForm($topic_id, TRUE)}</div>";
        return $html;
    }

    function show() {
        return $this->buildForm();
    }

}

?>
