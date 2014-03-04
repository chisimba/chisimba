<?php
//Sending display to 1 column layout
//ob_start();
//$js='
//<script type="text/javascript">
//    //<![CDATA[
//
//    function SubmitForm()
//    {
//        document.forms["postReplyForm"].submit();
//    }
//
//    //]]>
//</script>
//';
//echo $js;
///**
//* This template displays a topic in a threaded view format
//*/
//
//$this->setVar('pageSuppressXML',true);
//
//$js = $this->getJavascriptFile('contracthead.js', 'discussion');
//$this->appendArrayVar('headerParams', $js);
//
//$this->loadClass('htmlheading', 'htmlelements');
//$this->loadClass('link', 'htmlelements');
//$this->loadClass('form', 'htmlelements');
//$this->loadClass('dropdown', 'htmlelements');
//$this->loadClass('textinput', 'htmlelements');
//$this->loadClass('button', 'htmlelements');
//
//$objIcon = $this->getObject('geticon', 'htmlelements');
//
//if ($this->getParam('message') == 'invalidattachment') {
//    $this->setErrorMessage($this->objLanguage->languageText('mod_discussion_attachment_not_found', 'discussion', 'Could not find requested attachment.'));
//}
//$header = new htmlheading();
//$header->type=1;
//
//$link = new link($this->uri(array('action'=>'discussion', 'id'=>$post['discussion_id'], 'type'=>$discussiontype)));
//$link->link = $post['discussion_name'];
//$headerString = $link->show().' &gt; '.stripslashes($post['post_title']);
//
//$header->str=$headerString;
//
//if ($this->objUser->isCourseAdmin($this->contextCode) && !$discussionlocked && $discussiontype != 'workgroup' && $this->isLoggedIn) {
//    $objIcon->setIcon('moderate');
//    $objIcon->title = $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion');
//    $objIcon->alt = $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion');
//
//    $moderateTopicLink = new link($this->uri(array('action'=>'moderatetopic', 'id'=>$post['topic_id'], 'type'=>$discussiontype)));
//    $moderateTopicLink->link = $objIcon->show();
//
//    $header->str .= ' '.$moderateTopicLink->show();
//}
//
//echo $header->show();
//
////Confirmation messages
//if ($this->getParam('message') == 'save') {
//    $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
//    $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_postsaved', 'discussion'));
//    $timeoutMessage->setTimeout(20000);
//    echo ('<p>'.$timeoutMessage->show().'</p>');
//}
//if ($this->getParam('message') == 'postupdated') {
//    $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
//    $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_postupdated', 'discussion'));
//    $timeoutMessage->setTimeout(10000);
//    echo ('<p>'.$timeoutMessage->show().'</p>');
//}
//if ($this->getParam('message') == 'replysaved') {
//    $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
//    $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_replysaved', 'discussion'));
//    $timeoutMessage->setTimeout(10000);
//    echo ('<p>'.$timeoutMessage->show().'</p>');
//}
//
//// Error Messages
//if ($this->getParam('message') == 'cantreplydiscussionlocked') {
//    $this->setErrorMessage('This Discussion has been Locked. You cannot post a reply to this Topic'); // LTE
//}
//if ($this->getParam('message') == 'cantreplytopiclocked') {
//    $this->setErrorMessage('This Topic has been Locked. You cannot post a reply to this Topic'); // LTE
//}
//
//echo $changeDisplayForm;
//
//if ($post['status'] =='CLOSE') {
//    echo '<div class="discussionTangentIndent">';
//    echo '<strong>'.$this->objLanguage->languageText('mod_discussion_topiclockedby', 'discussion').' '.$this->objUser->fullname($post['lockuser']).' on '.$this->objDateTime->formatdate($post['lockdate']).'</strong>';
//    echo '<p>'.$post['lockreason'].'</p>';
//    echo '</div>';
//}
//
//$ratingsForm = new form('savepostratings', $this->uri(array('action'=>'savepostratings')));
//
//$ratingsForm->addToForm($thread);
//
//// Determine whether to show the submit form for the ratings form
//// Without this button, form is a waste, but need to make efficient
//if ($showRatingsForm) {
//    $objButton=&new button('submitForm');
//    $objButton->cssClass = 'save';
//    $objButton->setValue($this->objLanguage->languageText('mod_discussion_sendratings', 'discussion'));
//    $objButton->setToSubmit();
//
//    if ($post['status'] != 'CLOSE' && !$discussionlocked) {
//        $ratingsForm->addToForm('<p align="right">'.$objButton->show().'</p>');
//    }
//
//    // These elements are need for the redirect
//    $hiddenTopicId = new textinput('topic', $post['topic_id']);
//    $hiddenTopicId->fldType = 'hidden';
//    $ratingsForm->addToForm($hiddenTopicId->show());
//}
//
//echo $ratingsForm->show();
//
//if (isset($tangentsTable)) {
//    echo $tangentsTable;
//}
//
//
//$replylink = new link($this->uri(array('action'=>'postreply', 'id'=>$post['post_id'], 'type'=>$discussiontype)));
//$replylink->link = $this->objLanguage->languageText('mod_discussion_replytotopic', 'discussion');
//
//$newtopiclink = new link($this->uri(array('action'=>'newtopic', 'id'=>$post['discussion_id'], 'type'=>$discussiontype)));
//$newtopiclink->link = $this->objLanguage->languageText('mod_discussion_startnewtopic', 'discussion');
//
//$returntodiscussion = new link($this->uri(array('action'=>'discussion', 'id'=>$post['discussion_id'], 'type'=>$discussiontype)));
//$returntodiscussion->link = $this->objLanguage->languageText('mod_discussion_returntodiscussion', 'discussion');
//
//$moderateTopicLink = new link($this->uri(array('action'=>'moderatetopic', 'id'=>$post['topic_id'], 'type'=>$discussiontype)));
//$moderateTopicLink->link = $this->objLanguage->languageText('mod_discussion_moderatetopic', 'discussion');
//
//if ($post['status'] != 'CLOSE' && !$discussionlocked && $this->isLoggedIn) {
//    $header = new htmlheading();
//    $header->type=3;
//    $header->str = $this->objLanguage->languageText('mod_discussion_replytotopic', 'discussion');
//
//    echo $header->show();
//
//    echo $this->objPost->showPostReplyForm($post['post_id'], FALSE);
//}
//
//echo '<p align="center">';
//
//// if ($post['status'] != 'CLOSE' && !$discussionlocked) {
//    // echo $replylink->show().' / ';
//// }
//
//if ((!$discussionlocked && $this->objUser->isCourseAdmin($this->contextCode)) || $discussiontype == 'workgroup') {
//    echo $newtopiclink->show().' / ';
//}
//
//if ($this->objUser->isCourseAdmin($this->contextCode) && !$discussionlocked && $discussiontype != 'workgroup' && $this->isLoggedIn) {
//    echo $moderateTopicLink->show().' / ';
//}
//
//
//echo $returntodiscussion->show().'</p>';
//
//echo $this->showDiscussionFooter($post['discussion_id'], FALSE);
//
//$display = ob_get_contents();
//ob_end_clean();
//
//$this->setVar('middleColumn', $display);
?>
<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixTwo();
?>

<div id="twocolumn">
        <div id="Canvas_Content_Body_Region2">
                {
                "display" : "block",
                "module" : "discussion",
                "block" : "thread"
                }
        </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>