<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_postreply_class_inc
 *
 * @author monwabisi
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_postreply extends object {

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
        $this->title = "Post Reply";

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
        //get the recordid
//        $post_id = $this->getParam('id');
//        $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
//        $postReplyForm = new form('postReplyForm', $this->uri(array('action' => 'savepostreply', 'type' => $discussiontype)));
//        $postReplyForm->displayType = 3;
//        $postReplyForm->addRule('title', $this->objLanguage->languageText('mod_discussion_addtitle', 'discussion'), 'required');
//
//        $addTable = $this->getObject('htmltable', 'htmlelements');
//        $addTable->width = '99%';
//        $addTable->align = 'center';
//        $addTable->cellpadding = 10;
//        $addTable->startRow();
//        $subjectLabel = new label($this->objLanguage->languageText('word_subject', 'system') . ':', 'input_title');
//        $addTable->addCell($subjectLabel->show(), 100);
//        
//        echo $objHighlightLabels->show();
//        // Get the Post
//        $post = $this->objPost->getPostWithText($post_id);
//        // Get details of the Discussion
//        $discussion = $this->objDiscussion->getDiscussion($post['discussion_id']);
//        // Check if Title has Re: attached to it   
//        if (substr($post['post_title'], 0, 3) == 'Re:') {
//            // If it does, simply strip slashes
//            $defaultTitle = stripslashes($post['post_title']);
//            $originalTitle = stripslashes($post['post_title']);
//        } else {
//            // Else strip slashes AND append Re: to the title
//            $defaultTitle = 'Re: ' . stripslashes($post['post_title']);
//            $originalTitle = 'Re: ' . stripslashes($post['post_title']);
//        }
//        // If result of server-side validation, change default title to posted one
//        if ($mode == 'fix') {
//            // Select Posted Title
//            $defaultTitle = $details['title'];
//        }
//
//        $details = "";
//        $mode = "";
//        // Check if form is a result of server-side validation or not'
//        if ($this->getParam('message') == 'missing') {
//            $details = $this->getSession($this->getParam('tempid'));
//            $this->setVarByRef('details', $details);
//            $temporaryId = $details['temporaryId'];
//            $mode = 'fix';
//        } else {
//            $temporaryId = $this->objUser->userId() . '_' . mktime();
//            $mode = 'new';
//        }
        $js='
<script type="text/javascript">
    //<![CDATA[

    function SubmitForm()
    {
        document.forms["postReplyForm"].submit();
    }

    //]]>
</script>
';
        $postID = $this->getParam('id');
        return $this->objPost->showPostReplyForm($postID).$js;
    }

    function show() {
        return $this->buildForm();
    }

}

?>
