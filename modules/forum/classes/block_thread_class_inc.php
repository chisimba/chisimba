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
    var $objForum;
    var $contextObject;
    var $contextCode;
    var $objLanguage;
    var $objForumRatings;
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
        $this->loadClass('dbpost', 'forum');
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

    function buildForm() {
        $topic_id = $this->getParam('id');
        $this->setVar('pageSuppressXML', true);
        $js = $this->getJavascriptFile('contracthead.js', 'forum');
        $this->appendArrayVar('headerParams', $js);
        $objIcon = $this->getObject('geticon', 'htmlelements');
        $html = "";

        if ($this->getParam('message') == 'invalidattachment') {
            $this->setErrorMessage($this->objLanguage->languageText('mod_forum_attachment_not_found', 'forum', 'Could not find requested attachment.'));
        }
        $header = new htmlheading();
        $header->type = 1;
        $post = $this->objPost->getRootPost($topic_id);
        $forum = $this->objForum->getForum($post['forum_id']);
        $link = new link($this->uri(array('action' => 'forum', 'id' => $post['forum_id'], 'type' => $forum['forum_type'])));
        $link->link = $post['forum_name'];
        $headerString = $link->show() . ' &gt; ' . stripslashes($post['post_title']);
        $header->str = $headerString;
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

        if ($this->objUser->isCourseAdmin($this->contextCode) && !$forumlocked && $forum['forum_type'] != 'workgroup' && $this->objUser->isLoggedIn()) {
            $objIcon->setIcon('moderate');
            $objIcon->title = $this->objLanguage->languageText('mod_forum_moderatetopic', 'forum');
            $objIcon->alt = $this->objLanguage->languageText('mod_forum_moderatetopic', 'forum');

            $moderateTopicLink = new link($this->uri(array('action' => 'moderatetopic', 'id' => $post['topic_id'], 'type' => $forum['forum_type'])));
            $moderateTopicLink->link = $objIcon->show();
            $elements = $moderateTopicLink->show();
            $header->str .= ' ' . $moderateTopicLink->show();
        }
        $html .= $elements;
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
        $changeDisplayForm = $this->objTopic->showChangeDisplayTypeForm($topic_id, 'thread');
        $html = $changeDisplayForm;
        if ($post['status'] == 'CLOSE') {
            $html = '<div class="forumTangentIndent">';
            $html .= '<strong>' . $this->objLanguage->languageText('mod_forum_topiclockedby', 'forum') . ' ' . $this->objUser->fullname($post['lockuser']) . ' on ' . $this->objDateTime->formatdate($post['lockdate']) . '</strong>';
            $html .= '<p>' . $post['lockreason'] . '</p>';
            $html .= '</div>';
        }
        $ratingsForm = new form('savepostratings', $this->uri(array('action' => 'savepostratings')));
        // Create the indented thread
        $thread = $this->objPost->displayThread($topic_id);
        $ratingsForm->addToForm($thread);
        // Check if ratings allowed in Forum
        if ($forum['ratingsenabled'] == 'Y') {
            $this->objPost->forumRatingsArray = $this->objForumRatings->getForumRatings($post['forum_id']);
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
            $objButton->setValue($this->objLanguage->languageText('mod_forum_sendratings', 'forum'));
            $objButton->setToSubmit();

            if ($post['status'] != 'CLOSE' && !$forumlocked) {
                $ratingsForm->addToForm('<p align="right">' . $objButton->show() . '</p>');
            }
            // These elements are need for the redirect
            $hiddenTopicId = new textinput('topic', $post['topic_id']);
            $hiddenTopicId->fldType = 'hidden';
            $ratingsForm->addToForm($hiddenTopicId->show());
//            $html .= $ratingsForm->show();
        }
        $replylink = new link($this->uri(array('action' => 'postreply', 'id' => $post['post_id'], 'type' => $forum['forum_type'])));
        $replylink->link = $this->objLanguage->languageText('mod_forum_replytotopic', 'forum');
        $newtopiclink = new link($this->uri(array('action' => 'newtopic', 'id' => $post['forum_id'], 'type' => $forum['forum_type'])));
        $newtopiclink->link = $this->objLanguage->languageText('mod_forum_startnewtopic', 'forum');

        $returntoforum = new link($this->uri(array('action' => 'forum', 'id' => $post['forum_id'], 'type' => $forum['forum_type'])));
        $returntoforum->link = $this->objLanguage->languageText('mod_forum_returntoforum', 'forum');

        $moderateTopicLink = new link($this->uri(array('action' => 'moderatetopic', 'id' => $post['topic_id'], 'type' => $forum['forum_type'])));
        $moderateTopicLink->link = $this->objLanguage->languageText('mod_forum_moderatetopic', 'forum');

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
