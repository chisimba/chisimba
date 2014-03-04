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
//$string = str_replace('{Context}', $contextTitle, $this->objLanguage->languageText('mod_forum_forumsincontext', 'forum'));
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
//$tblclass->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_forum', 'forum') . '</strong>', '40%');
//$tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('word_topics') . '</nobr></strong>', 100, NULL, 'center');
//$tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('word_posts') . '</nobr></strong>', 100, NULL, 'center');
//$tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('mod_forum_lastpost', 'forum') . '</nobr></strong>', 100);
//$tblclass->endHeaderRow();
//
//$dropdown = new dropdown('forum');
//$dropdown->addOption('all', 'All Forums');
//$rowcount = 0;
//$div = "<div>";
////user object to be used in determining if user is admin
//$objUser = $this->getObject('user', 'security');
//foreach ($forums as $forum) {
//        $oddOrEven = ($rowcount == 0) ? "odd" : "even";
//        $dropdown->addOption($forum['forum_id'], $forum['forum_name']);
//        $forumLink = new link($this->uri(array('module' => 'forum', 'action' => 'forum', 'id' => $forum['forum_id'])));
//        $forumLink->link = $forum['forum_name'];
//        $forumName = $forumLink->show();
//        if ($forum['defaultforum'] == 'Y') {
//                $forumName .= '<em> - ' . $this->objLanguage->languageText('mod_forum_defaultForum', 'forum', 'Default Forum') . '</em>';
//        }
//        $objIcon = $this->getObject('geticon', 'htmlelements');
//        if ($forum['forumlocked'] == 'Y') {
//                $objIcon->setIcon('lock', NULL, 'icons/forum/');
//                $objIcon->title = $this->objLanguage->languageText('mod_forum_forumislocked', 'forum');
//        } else {
//                $objIcon->setIcon('unlock', NULL, 'icons/forum/');
//                $objIcon->title = $this->objLanguage->languageText('mod_forum_forumisopen', 'forum');
//        }
//        $tblclass->startRow($oddOrEven);
//        $tblclass->addCell($objIcon->show(), 10, NULL, 'center');
//        $tblclass->addCell($forumName . '<br />' . $this->objLanguage->abstractText($forum['forum_description']), '40%', 'center');
//        $tblclass->addCell($forum['topics'], NULL, NULL, 'center');
//        $tblclass->addCell($forum['posts'], 100, NULL, 'center');
//        $post = $this->objPost->getLastPost($forum['forum_id']);
//        if ($post == FALSE) {
//                $postDetails = '<em>' . $this->objLanguage->languageText('mod_forum_nopostsyet', 'forum') . '</em>';
//                $cssClass = NULL;
//        } else {
//                $cssClass = 'smallText';
//                $postLink = new link($this->uri(array('module' => 'forum', 'action' => 'viewtopic', 'id' => $post['topic_id'], 'post' => $post['post_id'])));
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
//                        $datefield = $this->objLanguage->languageText('mod_forum_todayat', 'forum') . ' ' . $this->objDateTime->formatTime($post['datelastupdated']);
//                } else {
//                        $datefield = $this->objDateTime->formatDateOnly($post['datelastupdated']) . ' - ' . $this->objDateTime->formatTime($post['datelastupdated']);
//                }
//
//                $postDetails .= '<br /><strong>' . $user . $datefield . '</strong>';
//        }
//        $tblclass->addCell($postDetails, '40%', 'center', NULL, $cssClass);
//        $tblclass->endRow();
//        //Edit Icon
//        $editLink = new link($this->uri(array('module' => 'forum', 'action' => 'editforum', 'id' => $forum['id'])));
//        $editLink->link = $this->objLanguage->languageText('word_edit', 'system');
//        $editLink->title = $this->objLanguage->languageText('mod_forum_editForumSettings', 'forum');
//        $paragraph = "<p>";
//        if ($objUser->isAdmin()) {
//                $paragraph .= $editLink->show();
//        }
//        // Set rowcount for bitwise determination of odd or even
//        $rowcount = ($rowcount == 0) ? 1 : 0;
//        $div .= "<div class='forums-list' >
//        {$paragraph}
//                <h3>{$forumLink->show()}</h3>
//                <ul class='forums-list'>
//                <li><b>Topics: </b><span class='indicator' >4</span></li>
//                <li><b>Posts: </b><span class='indicator' >4</span></li>
//                <li><b>Last post: </b>{$postDetails}</li>
//                <li class='forum-desc'><b>Description: </b>" . substr($forum['forum_description'], 0, 50) . "</li>
//                </ul>
//                <br/><br/>
//            </div>";
//}
//$div .= "</div>";
//
//echo "<div class='allforums'>" . $div . "</div>";
//
//$objSearch = $this->getObject('forumsearch');
//echo $objSearch->show();
//
//if ($this->objUser->isCourseAdmin($this->contextCode) && $this->isLoggedIn) {
//        $administrationLink = new link($this->uri(array('module' => 'forum', 'action' => 'administration')));
//        $administrationLink->link = $this->objLanguage->languageText('mod_forum_forumadministration', 'forum');
//        echo "<div class='forum_administration'>" . $administrationLink->show() . '</div>';
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
                "module" : "forum",
                "block" : "forumlist"
                }
        </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>