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
    var $objForum;
    var $objContextObject;
    var $contextCode;
    var $objLanguage;
    var $objForumRatings;
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
        $this->objPost = $this->getObject('dbpost', 'forum');
        $this->objTopic = $this->getObject('dbtopic', 'forum');
        $this->objForum = $this->getObject('dbforum', 'forum');
        // Get Context Code Settings
        $this->contextObject = & $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();
        $this->objLanguage = $this->getObject('language', 'language');
        // Forum Ratings
        $this->objForumRatings = & $this->getObject('dbforum_ratings');
        $this->objPostRatings = & $this->getObject('dbpost_ratings');
    }

    function buildform() {
        $topic_id = $this->getParam('id');
        $objIcon = $this->getObject('geticon', 'htmlelements');
        $header = $this->getObject('htmlheading', 'htmlelements');
        $post = $this->objPost->getRootPost($topic_id);
        $forum = $this->objForum->getForum($post['forum_id']);
        $link = new link($this->uri(array('action' => 'forum', 'id' => $post['forum_id'], 'type' => $forum['forum_type'])));
        $link->link = $post['forum_name'];
        // Check if forum is locked - if true - disable / editing replies
        if ($this->objForum->checkIfForumLocked($post['forum_id'])) {
            $this->objPost->repliesAllowed = FALSE;
            $this->objPost->editingPostsAllowed = FALSE;
            $this->objPost->forumLocked = TRUE;
            $forumlocked = TRUE;
        } else {
            $forumlocked = FALSE;
            if ($this->objUser->isCourseAdmin($this->contextCode)) {
                $this->objPost->showModeration = TRUE;
            }
        }
        if ($this->objUser->isCourseAdmin() && !$forumlocked && $forum['forum_type'] != 'workgroup' && $this->objUser->isLoggedIn()) {
            $objIcon->setIcon('moderate');
            $objIcon->title = $this->objLanguage->languageText('mod_forum_moderatetopic', 'forum');
            $objIcon->alt = $this->objLanguage->languageText('mod_forum_moderatetopic', 'forum');

            $moderateTopicLink = new link($this->uri(array('action' => 'moderatetopic', 'id' => $post['topic_id'], 'type' => $forum['forum_type'])));
            $moderateTopicLink->link = $objIcon->show();

            $header->str .= ' ' . $moderateTopicLink->show() . "Moderate Topic";
        }

        $this->objTopic->updateTopicViews($topic_id);
        ////Confirmation messages
        if ($this->getParam('message') == 'save') {
            $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
            $timeoutMessage->setMessage($this->objLanguage->languageText('mod_forum_postsaved', 'forum'));
            $timeoutMessage->setTimeout(20000);
            echo ('<p>' . $timeoutMessage->show() . '</p>');
        }
        if ($this->getParam('message') == 'postupdated') {
            $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
            $timeoutMessage->setMessage($this->objLanguage->languageText('mod_forum_postupdated', 'forum'));
            $timeoutMessage->setTimeout(10000);
            echo ('<p>' . $timeoutMessage->show() . '</p>');
        }
        if ($this->getParam('message') == 'replysaved') {
            $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
            $timeoutMessage->setMessage($this->objLanguage->languageText('mod_forum_replysaved', 'forum'));
            $timeoutMessage->setTimeout(10000);
            echo ('<p>' . $timeoutMessage->show() . '</p>');
        }
        //// Error Messages
        if ($this->getParam('message') == 'cantreplyforumlocked') {
            $this->setErrorMessage('This Forum has been Locked. You cannot post a reply to this Topic'); // LTE
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
        if ($this->objUser->isCourseAdmin() && !$forumlocked && $forum['forum_type'] != 'workgroup' && $this->objUser->isLoggedIn()) {
            $js .= $moderateTopicLink->show() . ' / ';
        }
        $js .= $changeDisplayForm;
        $htmlElements = $js;

        if ($post['status'] == 'CLOSE') {
            echo '<div class="forumTangentIndent">';
            echo '<strong>' . $this->objLanguage->languageText('mod_forum_topiclockedby', 'forum') . ' ' . $this->objUser->fullname($post['lockuser']) . ' on ' . $this->objDateTime->formatdate($post['lockdate']) . '</strong>';
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

        // Check if ratings allowed in Forum
        if ($forum['ratingsenabled'] == 'Y') {
            $this->objPost->forumRatingsArray = $this->objForumRatings->getForumRatings($post['forum_id']);
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
            $objButton->setValue($this->objLanguage->languageText('mod_forum_sendratings', 'forum'));
            $objButton->setToSubmit();

            if ($post['status'] != 'CLOSE' && !$forumlocked) {
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
