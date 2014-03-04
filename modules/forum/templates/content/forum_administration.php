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
//$Header->str=$this->objLanguage->languageText('mod_forum_forumAdministration', 'forum','Forum administration').': '.$contextTitle;
//
//echo ($Header->show());
//
//// Look for any messages to display
//if ($this->getParam('message') != '') {
//
//    if ($this->getParam('message') == 'forumcreated') {
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_forum_newforumcreated', 'forum'));
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//    if ($this->getParam('message') == 'forumupdated') {
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_forum_forumupdated', 'forum'));
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//    if ($this->getParam('message') == 'defaultforumchanged') {
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage($this->objLanguage->languageText('mod_forum_defaultforumchanged', 'forum'));
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//    if ($this->getParam('message') == 'visibilityunchanged') {
//        $updatedForum = $this->objForum->getForum($this->getParam('forum'));
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage('Visibility of the '.$updatedForum['forum_name'].' Forum has NOT changed');
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//    if ($this->getParam('message') == 'visibilityupdated') {
//        $updatedForum = $this->objForum->getForum($this->getParam('forum'));
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage('Visibility of the '.$updatedForum['forum_name'].' Forum has been updated. Now set to _________________________________ ');
//        $timeoutMessage->setTimeout(10000);
//        echo ('<p>'.$timeoutMessage->show().'</p>');
//    }
//
//    if ($this->getParam('message') == 'forumdeleted') {
//        $forum = $this->getParam('forum');
//        $timeoutMessage = $this->newObject('timeoutmessage', 'htmlelements');
//        $timeoutMessage->setMessage($forum.' has been deleted');
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
//$table->addHeaderCell( $this->objLanguage->languageText('mod_forum_wordName', 'forum'));
//$table->addHeaderCell($this->objLanguage->languageText('mod_forum_visible', 'forum'), NULL, NULL, 'center');
//$table->addHeaderCell($this->objLanguage->languageText('mod_forum_forumlocked', 'forum'), NULL, NULL, 'center');
//$table->addHeaderCell($this->objLanguage->languageText('mod_forum_ratings', 'forum'), NULL, NULL, 'center');
//$table->addHeaderCell(ucwords($this->objLanguage->code2Txt('mod_forum_studentsstartTopics', 'forum')), NULL, NULL, 'center');
//$table->addHeaderCell($this->objLanguage->languageText('mod_forum_attachments', 'forum'), NULL, NULL, 'center');
//$table->addHeaderCell('Email', NULL, NULL, 'center');
//$table->addHeaderCell($this->objLanguage->languageText('mod_forum_archivedate', 'forum'), NULL, NULL, 'center');
//$table->addHeaderCell('&nbsp;');
//
//$table->endHeaderRow();
//
//$rowcount = '';
//
//foreach ($forumsList as $forum)
//{
//    $oddOrEven = ($rowcount == 0) ? "odd" : "even";
//
//    $tableRow = array();
//
//    $table->startRow();
//
//
//    $forumLink = new link($this->uri(array( 'module'=> 'forum', 'action' => 'forum', 'id'=>$forum['id'])));
//    $forumLink->link = $forum['forum_name'];
//    $forumLink->title = $this->objLanguage->languageText('mod_forum_gotoforum', 'forum');
//
//    if ($forum['defaultforum'] == 'Y') {
//        $forumLinkStr = '<strong>* </strong>';
//    } else {
//        $forumLinkStr = ' &nbsp;&nbsp; ';
//    }
//
//    $editLink =& new link($this->uri(array( 'module'=> 'forum', 'action' => 'editforum', 'id'=>$forum['id'])));
//    $editLink->link = $this->objLanguage->languageText('word_edit', 'system');
//    $editLink->title = $this->objLanguage->languageText('mod_forum_editForumSettings', 'forum');
//
//    $table->addCell($forumLinkStr.$forumLink->show().' ('.$editLink->show().')');
//
//    $table->addCell($forum['forum_visible'], NULL, NULL, 'center');
//
//    $table->addCell($forum['forumlocked'], NULL, NULL, 'center');
//
//    $table->addCell($forum['ratingsenabled'], NULL, NULL, 'center');
//
//    $table->addCell($forum['studentstarttopic'], NULL, NULL, 'center');
//
//    $table->addCell($forum['attachments'], NULL, NULL, 'center');
//
//    $table->addCell($forum['subscriptions'], NULL, NULL, 'center');
//
//    if ($forum['archivedate'] == '0000-00-00' || $forum['archivedate'] == '') {
//        $table->addCell('n/a', NULL, NULL, 'center');
//    } else {
//        $table->addCell($forum['archivedate'], NULL, NULL, 'center');
//    }
//
//
//
//    $editLink = new link($this->uri(array( 'module'=> 'forum', 'action' => 'editforum', 'id'=>$forum['id'])));
//    $objIcon->setIcon('edit');
//    $editLink->link = $objIcon->show();
//    $editLink->alt = $this->objLanguage->languageText('word_edit', 'forum');
//    $editLink->title = $this->objLanguage->languageText('mod_forum_editForumSettings', 'forum');
//
//    $editDeleteLink = $editLink->show();
//
//    if ($forum['defaultforum'] != 'Y') {
//        $deleteLink = new link($this->uri(array( 'module'=> 'forum', 'action' => 'deleteforum', 'id'=>$forum['id'])));
//        $objIcon->setIcon('delete');
//        $deleteLink->link = $objIcon->show();
//        $deleteLink->title = $this->objLanguage->languageText('mod_forum_deleteforum', 'forum');
//        $deleteLink->alt = $this->objLanguage->languageText('word_delete', 'forum');
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
//$form = new form('defaultforum', $this->uri( array('module'=>'forum', 'action'=>'setdefaultforum')));
//$form->displayType = 3;
//
//
//$form->addToForm('<fieldset>');
//
//$forumLabel = new label('<nobr>* '.$this->objLanguage->languageText('mod_forum_defaultForum', 'forum','Default Forum').' &nbsp;&nbsp;/&nbsp;&nbsp; '.$this->objLanguage->languageText('mod_forum_setDefaultForum', 'forum','Set defualt forum').':</nobr>', 'input_forum');
//$form->addToForm($forumLabel->show(), 100);
//
//$discussionType = new dropdown('forum');
//
//
//foreach ($visibleForums as $forum)
//{
//
//	$discussionType->addOption($forum['id'], $forum['forum_name']);
//
//}
//$discussionType->setSelected($defaultForum['id']);
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
//$editLink = new link($this->uri(array( 'module'=> 'forum', 'action' => 'createforum')));
//$editLink->link = $this->objLanguage->languageText('mod_forum_createNewForum', 'forum','Create new forum');
//
//$backToForumListLink = new link($this->uri(NULL));
//$backToForumListLink->link = $this->objLanguage->languageText('mod_forum_backtoforumindex', 'forum');
//
//echo ('<p>'.$editLink->show().' / '.$backToForumListLink->show().'</p>');
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
                "module" : "forum",
                "block" : "forumadmin"
                }
        </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>