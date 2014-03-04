<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bock_singleview_class_inc
 *
 * @author monwabisi
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_singleview extends object {

    var $objUser;
    var $objPost;
    var $objTopic;
    var $objDiscussion;
    var $objContextObject;
    var $contextCode;
    var $objLanguage;
    var $objDiscussionRatings;
    var $objPostRatings;

    //put your code here

    function init() {
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->title = "Post single view";
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

    function buildform() {
        $topic_id = $this->getParam('id');
        $objIcon = $this->getObject('geticon', 'htmlelements');
        $header = $this->getObject('htmlheading', 'htmlelements');
        $post = $this->objPost->getRootPost($topic_id);
        $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
        $link = new link($this->uri(array('action' => 'discussion', 'id' => $post['discussion_id'], 'type' => $discussion['discussion_type'])));
        $link->link = $post['discussion_name'];
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
        if ($this->objUser->isCourseAdmin() && !$discussionlocked && $discussion['discussion_type'] != 'workgroup' && $this->objUser->isLoggedIn()) {
            $objIcon->setIcon('moderate');
            $objIcon->title = $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion');
            $objIcon->alt = $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion');

            $moderateTopicLink = new link($this->uri(array('action' => 'moderatetopic', 'id' => $post['topic_id'], 'type' => $discussion['discussion_type'])));
            $moderateTopicLink->link = $objIcon->show();

            $header->str .= ' ' . $moderateTopicLink->show() . "Moderate Topic";
        }

        $this->objTopic->updateTopicViews($topic_id);
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
        $changeDisplayForm = $this->objTopic->showChangeDisplayTypeForm($topic_id, 'singlethreadview');
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
        if ($this->objUser->isCourseAdmin() && !$discussionlocked && $discussion['discussion_type'] != 'workgroup' && $this->objUser->isLoggedIn()) {
            $js .= $moderateTopicLink->show() . ' / ';
        }
        $js .= $changeDisplayForm;
        $htmlElements = $js;

        if ($post['status'] == 'CLOSE') {
            echo '<div class="discussionTangentIndent">';
            echo '<strong>' . $this->objLanguage->languageText('mod_discussion_topiclockedby', 'discussion') . ' ' . $this->objUser->fullname($post['lockuser']) . ' on ' . $this->objDateTime->formatdate($post['lockdate']) . '</strong>';
            echo '<p>' . $post['lockreason'] . '</p>';
            echo '</div>';
        }
        $ratingsForm = new form('savepostratings', $this->uri(array('action' => 'savepostratings')));

        if ($this->getParam('post') == '') {
            $postDisplay = $this->objPost->displayPost($post);
            $highlightPost = $post['post_id'];
        } else {
            $requestedPost = $this->objPost->getPostWithText($this->getParam('post'));
            $postDisplay = $this->objPost->displayPost($requestedPost);
            $highlightPost = $this->getParam('post');
        }
        $ratingsForm->addToForm($postDisplay);

        // Check if ratings allowed in Discussion
        if ($discussion['ratingsenabled'] == 'Y') {
            $this->objPost->discussionRatingsArray = $this->objDiscussionRatings->getDiscussionRatings($post['discussion_id']);
            $this->objPost->showRatings = TRUE;
            $showRatingsForm = TRUE;
        } else {
            $showRatingsForm = FALSE;
            $this->objPost->showRatings = FALSE;
        }

// Determine whether to show the submit form for the ratings form
// Without this button, form is a waste, but need to make efficient
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

            $hiddenPostId = new textinput('currentPost', $highlightPost);
            $hiddenPostId->fldType = 'hidden';
            $ratingsForm->addToForm($hiddenPostId->show());
        }

        return $js . $ratingsForm->show().$this->objPost->showPostReplyForm($highlightPost, FALSE);
    }

    function show() {
        return $this->buildform();
    }

}

?>
