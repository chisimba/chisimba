<?php

//Sending display to 1 column layout
//ob_start();
//
//$this->loadClass('link', 'htmlelements');
//$this->loadClass('form', 'htmlelements');
//$this->loadClass('dropdown', 'htmlelements');
//$this->loadClass('textinput', 'htmlelements');
//$this->loadClass('button', 'htmlelements');
//$this->loadClass('label', 'htmlelements');
//$this->loadClass('htmlheading', 'htmlelements');
//$this->loadClass('hiddeninput', 'htmlelements');
//$this->loadClass('user', 'security');
//
//
//// Error Messages
//switch (strtolower($this->getParam('error'))) {
//        default: break;
//        case 'topicdoesntexist':
//                $this->setErrorMessage('The topic you tried to view doesn\'t exist or has been deleted.');
//                break;
//        case 'moderatetopicdoesnotexist':
//                $this->setErrorMessage('The topic you tried to moderate doesn\'t exist or has been deleted.');
//                break;
//}
//
//
//
//$header = new htmlheading();
//$header->type = 1;
//
//$string = str_replace('{Context}', $contextTitle, $this->objLanguage->languageText('mod_discussion_discussionsincontext', 'discussion'));
//
//$header->str = $string;
//
//echo $header->show();
//
//
//$tblclass = $this->newObject('htmltable', 'htmlelements');
//
//$tblclass->width = '';
//$tblclass->attributes = " align='center'";
//$tblclass->cellspacing = '0';
//$tblclass->cellpadding = '5';
//$tblclass->border = '0';
//$tblclass->width = '100%';
//
//$tblclass->startHeaderRow();
//$tblclass->addHeaderCell('&nbsp;', 10, 'center');
//$tblclass->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_discussion', 'discussion') . '</strong>', '40%');
//$tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('word_topics') . '</nobr></strong>', 100, NULL, 'center');
//$tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('word_posts') . '</nobr></strong>', 100, NULL, 'center');
//$tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('mod_discussion_lastpost', 'discussion') . '</nobr></strong>', 100);
//$tblclass->endHeaderRow();
//
//$dropdown = new dropdown('discussion');
//$dropdown->addOption('all', 'All Discussions');
//$rowcount = 0;
//$div = "<div>";
////user object to be used in determining if user is admin
//$objUser = $this->getObject('user', 'security');
//foreach ($discussions as $discussion) {
//        $oddOrEven = ($rowcount == 0) ? "odd" : "even";
//        $dropdown->addOption($discussion['discussion_id'], $discussion['discussion_name']);
//        $discussionLink = new link($this->uri(array('module' => 'discussion', 'action' => 'discussion', 'id' => $discussion['discussion_id'])));
//        $discussionLink->link = $discussion['discussion_name'];
//        $discussionName = $discussionLink->show();
//        if ($discussion['defaultdiscussion'] == 'Y') {
//                $discussionName .= '<em> - ' . $this->objLanguage->languageText('mod_discussion_defaultDiscussion', 'discussion', 'Default Discussion') . '</em>';
//        }
//        $objIcon = $this->getObject('geticon', 'htmlelements');
//        if ($discussion['discussionlocked'] == 'Y') {
//                $objIcon->setIcon('lock', NULL, 'icons/discussion/');
//                $objIcon->title = $this->objLanguage->languageText('mod_discussion_discussionislocked', 'discussion');
//        } else {
//                $objIcon->setIcon('unlock', NULL, 'icons/discussion/');
//                $objIcon->title = $this->objLanguage->languageText('mod_discussion_discussionisopen', 'discussion');
//        }
//        $tblclass->startRow($oddOrEven);
//        $tblclass->addCell($objIcon->show(), 10, NULL, 'center');
//        $tblclass->addCell($discussionName . '<br />' . $this->objLanguage->abstractText($discussion['discussion_description']), '40%', 'center');
//        $tblclass->addCell($discussion['topics'], NULL, NULL, 'center');
//        $tblclass->addCell($discussion['posts'], 100, NULL, 'center');
//        $post = $this->objPost->getLastPost($discussion['discussion_id']);
//        if ($post == FALSE) {
//                $postDetails = '<em>' . $this->objLanguage->languageText('mod_discussion_nopostsyet', 'discussion') . '</em>';
//                $cssClass = NULL;
//        } else {
//                $cssClass = 'smallText';
//                $postLink = new link($this->uri(array('module' => 'discussion', 'action' => 'viewtopic', 'id' => $post['topic_id'], 'post' => $post['post_id'])));
//                $postLink->link = stripslashes($post['post_title']);
//                $postDetails = '<strong>' . $postLink->show() . '</strong>';
//                $postDetails .= '<br />' . $this->trimstrObj->strTrim(stripslashes(str_replace("\r\n", ' ', strip_tags($post['post_text']))), 80);
//
//                if ($post['firstname'] != '') {
//                        if ($this->showFullName) {
//                                $user = 'By: ' . $post['firstname'] . ' ' . $post['surname'] . ' - ';
//                        } else {
//                                $user = 'By: ' . $post['username'] . ' - ';
//                        }
//                } else {
//                        $user = '';
//                }
//                if ($this->objDateTime->formatDateOnly($post['datelastupdated']) == date('j F Y')) {
//                        $datefield = $this->objLanguage->languageText('mod_discussion_todayat', 'discussion') . ' ' . $this->objDateTime->formatTime($post['datelastupdated']);
//                } else {
//                        $datefield = $this->objDateTime->formatDateOnly($post['datelastupdated']) . ' - ' . $this->objDateTime->formatTime($post['datelastupdated']);
//                }
//
//                $postDetails .= '<br /><strong>' . $user . $datefield . '</strong>';
//        }
//        $tblclass->addCell($postDetails, '40%', 'center', NULL, $cssClass);
//        $tblclass->endRow();
//        //Edit Icon
//        $editLink = new link($this->uri(array('module' => 'discussion', 'action' => 'editdiscussion', 'id' => $discussion['id'])));
//        $editLink->link = $this->objLanguage->languageText('word_edit', 'system');
//        $editLink->title = $this->objLanguage->languageText('mod_discussion_editDiscussionSettings', 'discussion');
//        $paragraph = "<p>";
//        if ($objUser->isAdmin()) {
//                $paragraph .= $editLink->show();
//        }
//        // Set rowcount for bitwise determination of odd or even
//        $rowcount = ($rowcount == 0) ? 1 : 0;
//        $div .= "<div class='discussions-list' >
//        {$paragraph}
//                <h3>{$discussionLink->show()}</h3>
//                <ul class='discussions-list'>
//                <li><b>Topics: </b><span class='indicator' >4</span></li>
//                <li><b>Posts: </b><span class='indicator' >4</span></li>
//                <li><b>Last post: </b>{$postDetails}</li>
//                <li class='discussion-desc'><b>Description: </b>" . substr($discussion['discussion_description'], 0, 50) . "</li>
//                </ul>
//                <br/><br/>
//            </div>";
//}
//$div .= "</div>";
//
//echo "<div class='alldiscussions'>" . $div . "</div>";
//
//$objSearch = $this->getObject('discussionsearch');
//echo $objSearch->show();
//
//if ($this->objUser->isCourseAdmin($this->contextCode) && $this->isLoggedIn) {
//        $administrationLink = new link($this->uri(array('module' => 'discussion', 'action' => 'administration')));
//        $administrationLink->link = $this->objLanguage->languageText('mod_discussion_discussionadministration', 'discussion');
//        echo "<div class='discussion_administration'>" . $administrationLink->show() . '</div>';
//}
//$display = ob_get_contents();
//ob_end_clean();
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
                "block" : "discussionlist"
                }
        </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>