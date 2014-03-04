<?php

//Sending display to 1 column layout
//ob_start();
//
//$this->loadClass('form', 'htmlelements');
//$this->loadClass('button', 'htmlelements');
//$this->loadClass('dropdown', 'htmlelements');
//$this->loadClass('label', 'htmlelements');
//$this->loadClass('link', 'htmlelements');
//
//$objIcon = $this->getObject('geticon', 'htmlelements');
//
//$Header =& $this->getObject('htmlheading', 'htmlelements');
//$Header->type=1;
//$Header->str=$this->objLanguage->languageText('mod_discussion_discussionAdministration', 'discussion','Discussion administration').': '.$contextTitle;
//
//echo ($Header->show());
//
//// Look for any messages to display
//if ($this->getParam('message') != '') {
//
//    if ($this->getParam('message') == 'discussioncreated') {
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_newdiscussioncreated', 'discussion'));
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//    if ($this->getParam('message') == 'discussionupdated') {
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_discussionupdated', 'discussion'));
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//    if ($this->getParam('message') == 'defaultdiscussionchanged') {
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_discussion_defaultdiscussionchanged', 'discussion'));
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//    if ($this->getParam('message') == 'visibilityunchanged') {
//        $updatedDiscussion = $this->objDiscussion->getDiscussion($this->getParam('discussion'));
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage('Visibility of the '.$updatedDiscussion['discussion_name'].' Discussion has NOT changed');
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//    if ($this->getParam('message') == 'visibilityupdated') {
//        $updatedDiscussion = $this->objDiscussion->getDiscussion($this->getParam('discussion'));
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage('Visibility of the '.$updatedDiscussion['discussion_name'].' Discussion has been updated. Now set to _________________________________ ');
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//    if ($this->getParam('message') == 'discussiondeleted') {
//        $discussion = $this->getParam('discussion');
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage($discussion.' has been deleted');
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//}
//
//$table=$this->newObject('htmltable','htmlelements');
//
//$table->attributes=' align="center" border="0"';
//$table->cellspacing='1';
//$table->cellpadding='5';
//$table->border='0';
//$table->width='98%';
//
//$table->startHeaderRow();
////$table->addCell('<p style=" layout-flow : vertical-ideographic;">Default</p>');
//$table->addHeaderCell( $this->objLanguage->languageText('mod_discussion_wordName', 'discussion'));
//$table->addHeaderCell($this->objLanguage->languageText('mod_discussion_visible', 'discussion'), NULL, NULL, 'center');
//$table->addHeaderCell($this->objLanguage->languageText('mod_discussion_discussionlocked', 'discussion'), NULL, NULL, 'center');
//$table->addHeaderCell($this->objLanguage->languageText('mod_discussion_ratings', 'discussion'), NULL, NULL, 'center');
//$table->addHeaderCell(ucwords($this->objLanguage->code2Txt('mod_discussion_studentsstartTopics', 'discussion')), NULL, NULL, 'center');
//$table->addHeaderCell($this->objLanguage->languageText('mod_discussion_attachments', 'discussion'), NULL, NULL, 'center');
//$table->addHeaderCell('Email', NULL, NULL, 'center');
//$table->addHeaderCell($this->objLanguage->languageText('mod_discussion_archivedate', 'discussion'), NULL, NULL, 'center');
//$table->addHeaderCell('&nbsp;');
//
//$table->endHeaderRow();
//
//$rowcount = '';
//
//foreach ($discussionsList as $discussion)
//{
//    $oddOrEven = ($rowcount == 0) ? "odd" : "even";
//
//    $tableRow = array();
//
//    $table->startRow();
//
//
//    $discussionLink = new link($this->uri(array( 'module'=> 'discussion', 'action' => 'discussion', 'id'=>$discussion['id'])));
//    $discussionLink->link = $discussion['discussion_name'];
//    $discussionLink->title = $this->objLanguage->languageText('mod_discussion_gotodiscussion', 'discussion');
//
//    if ($discussion['defaultdiscussion'] == 'Y') {
//        $discussionLinkStr = '<strong>* </strong>';
//    } else {
//        $discussionLinkStr = ' &nbsp;&nbsp; ';
//    }
//
//    $editLink =& new link($this->uri(array( 'module'=> 'discussion', 'action' => 'editdiscussion', 'id'=>$discussion['id'])));
//    $editLink->link = $this->objLanguage->languageText('word_edit', 'system');
//    $editLink->title = $this->objLanguage->languageText('mod_discussion_editDiscussionSettings', 'discussion');
//
//    $table->addCell($discussionLinkStr.$discussionLink->show().' ('.$editLink->show().')');
//
//    $table->addCell($discussion['discussion_visible'], NULL, NULL, 'center');
//
//    $table->addCell($discussion['discussionlocked'], NULL, NULL, 'center');
//
//    $table->addCell($discussion['ratingsenabled'], NULL, NULL, 'center');
//
//    $table->addCell($discussion['studentstarttopic'], NULL, NULL, 'center');
//
//    $table->addCell($discussion['attachments'], NULL, NULL, 'center');
//
//    $table->addCell($discussion['subscriptions'], NULL, NULL, 'center');
//
//    if ($discussion['archivedate'] == '0000-00-00' || $discussion['archivedate'] == '') {
//        $table->addCell('n/a', NULL, NULL, 'center');
//    } else {
//        $table->addCell($discussion['archivedate'], NULL, NULL, 'center');
//    }
//
//
//
//    $editLink = new link($this->uri(array( 'module'=> 'discussion', 'action' => 'editdiscussion', 'id'=>$discussion['id'])));
//    $objIcon->setIcon('edit');
//    $editLink->link = $objIcon->show();
//    $editLink->alt = $this->objLanguage->languageText('word_edit', 'discussion');
//    $editLink->title = $this->objLanguage->languageText('mod_discussion_editDiscussionSettings', 'discussion');
//
//    $editDeleteLink = $editLink->show();
//
//    if ($discussion['defaultdiscussion'] != 'Y') {
//        $deleteLink = new link($this->uri(array( 'module'=> 'discussion', 'action' => 'deletediscussion', 'id'=>$discussion['id'])));
//        $objIcon->setIcon('delete');
//        $deleteLink->link = $objIcon->show();
//        $deleteLink->title = $this->objLanguage->languageText('mod_discussion_deletediscussion', 'discussion');
//        $deleteLink->alt = $this->objLanguage->languageText('word_delete', 'discussion');
//        $editDeleteLink .= ' &nbsp; '.$deleteLink->show();
//    }
//    $table->addCell($editDeleteLink, NULL, NULL, NULL, 'nowrap');
//
//
//
//
//    $table->endRow();
//
//}
//
//echo $table->show();
//
//$form = new form('defaultdiscussion', $this->uri( array('module'=>'discussion', 'action'=>'setdefaultdiscussion')));
//$form->displayType = 3;
//
//
//$form->addToForm('<fieldset>');
//
//$discussionLabel = new label('<nobr>* '.$this->objLanguage->languageText('mod_discussion_defaultDiscussion', 'discussion','Default Discussion').' &nbsp;&nbsp;/&nbsp;&nbsp; '.$this->objLanguage->languageText('mod_discussion_setDefaultDiscussion', 'discussion','Set defualt discussion').':</nobr>', 'input_discussion');
//$form->addToForm($discussionLabel->show(), 100);
//
//$discussionType = new dropdown('discussion');
//
//
//foreach ($visibleDiscussions as $discussion)
//{
//
//	$discussionType->addOption($discussion['id'], $discussion['discussion_name']);
//
//}
//$discussionType->setSelected($defaultDiscussion['id']);
//
//$form->addToForm($discussionType->show());
//
//$submitButton = new button('submitform', $this->objLanguage->languageText('word_submit', 'system','Submit'));
//$submitButton->cssClass = 'save';
//$submitButton->setToSubmit();
//
//$form->addToForm($submitButton->show());
//
//$form->addToForm('</fieldset>');
//
//echo $form->show();
//
//$editLink = new link($this->uri(array( 'module'=> 'discussion', 'action' => 'creatediscussion')));
//$editLink->link = $this->objLanguage->languageText('mod_discussion_createNewDiscussion', 'discussion','Create new discussion');
//
//$backToDiscussionListLink = new link($this->uri(NULL));
//$backToDiscussionListLink->link = $this->objLanguage->languageText('mod_discussion_backtodiscussionindex', 'discussion');
//
//echo ('<p>'.$editLink->show().' / '.$backToDiscussionListLink->show().'</p>');
//
//$display = ob_get_contents();
//ob_end_clean();
//
//$this->setVar('middleColumn', $display);
//
//
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
                "block" : "discussionadmin"
                }
        </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>