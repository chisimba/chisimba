<?php
// Sending display to 1 column layout
//ob_start();
//
//$this->loadClass('link', 'htmlelements');
//$this->loadClass('multitabbedbox', 'htmlelements');
//$this->loadClass('form', 'htmlelements');
//$this->loadClass('label', 'htmlelements');
//$this->loadClass('dropdown', 'htmlelements');
//$this->loadClass('button', 'htmlelements');
//$this->loadClass('textinput', 'htmlelements');
//$this->loadClass('htmlheading', 'htmlelements');
//
//$objTranslatedDate = $this->getObject('translatedatedifference', 'utilities');
////$objGroups = $this->getObject('groupadmin_model', 'groupadmin');
//
//$newTopicIcon = $this->getObject('geticon', 'htmlelements');
//$newTopicIcon->setIcon('notes');
//$newTopicIcon->alt = $this->objLanguage->languageText('mod_forum_startnewtopic', 'forum');
//$newTopicIcon->title = $this->objLanguage->languageText('mod_forum_startnewtopic', 'forum');
//
//
//$styles = '
//<style type="text/css" media="screen, tv, projection">
//tr.closedTopic {
//    background-color: #FFF2F2;
//}
//tr.stickyTopic {
//    background-color: #FFEFAE;
//}
//table.forumtopics tr:hover {
//    background-color: #66CC00;
//}
//
//table.forumtopics tr.closedTopic:hover {
//    background-color: #FF0000;
//}
//table.forumtopics tr.stickyTopic:hover {
//    background-color: #FF9900;
//}
//</style>
//';
//
//$this->appendArrayVar('headerParams', $styles);
//
//// Link to start new topic
//$newTopicLink = new link($this->uri(array('action' => 'newtopic', 'id' => $forumid, 'type' => $forumtype)));
//$newTopicLink->link = $newTopicIcon->show();
//
//$header = new htmlheading();
//$header->type = 1;
//$header->str = $forum['forum_name'];
//
//// Start checking whether to show the link
//// Check if the forum is locked
//if ($forum['forumlocked'] != 'Y') {
//        // Check if students can start topic
//        if ($forum['studentstarttopic'] == 'Y') {
//                $header->str .= ' ' . $newTopicLink->show();
//
//                // Else check if user is lecturer or admin
//        } else if ($this->objUser->isCourseAdmin($this->contextCode)) {
//                $header->str .= ' ' . $newTopicLink->show();
//        }
//}
//
//echo $header->show();
//
//$objForumSearch = $this->getObject('forumsearch');
//$objForumSearch->defaultForum = $forumid;
//echo $objForumSearch->show(FALSE);
//
//if ($topicsNum > 0) {
//        echo $paging;
//}
//
//$tblTopic = $this->newObject('htmltable', 'htmlelements');
//
//$tblTopic->attributes = ' align="center" border="0"';
//$tblTopic->cellspacing = '1';
//$tblTopic->cellpadding = '4';
//$tblTopic->border = '0';
//$tblTopic->width = '99%';
//
//if ($topicsNum > 0) {
//        $tblTopic->css_class = 'forumtopics';
//}
//
//// Start of First Row
//
//$tblTopic->startHeaderRow();
//
//
//$tblTopic->addHeaderCell($this->objForum->forumSortLink($forumid, 'status', $this->objLanguage->languageText('word_status', 'forum', 'Status')), '30', 'center');
//
//// --------------
//
//
//$tblTopic->addHeaderCell($this->objForum->forumSortLink($forumid, 'read', $this->objLanguage->languageText('word_noun_read', 'forum')), '30', 'center');
//
//// --------------
//
//
//$tblTopic->addHeaderCell($this->objForum->forumSortLink($forumid, 'type', $this->objLanguage->languageText('word_type', 'forum', 'Type')), '30', 'center');
//
//// --------------
//
//
//$tblTopic->addHeaderCell($this->objForum->forumSortLink($forumid, 'topic', $this->objLanguage->languageText('mod_forum_topicconversation', 'forum')), '30%', 'center');
//
//// --------------
//
//
//$tblTopic->addHeaderCell($this->objForum->forumSortLink($forumid, 'author', $this->objLanguage->languageText('word_author')), Null, 'center', 'center');
//
//// --------------
//
//$tblTopic->addHeaderCell($this->objForum->forumSortLink($forumid, 'replies', $this->objLanguage->languageText('word_replies', 'system', 'Replies')), Null, 'center', 'center');
//
//// --------------
//
//$tblTopic->addHeaderCell($this->objForum->forumSortLink($forumid, 'views', $this->objLanguage->languageText('word_views', 'system', 'Views')), Null, 'center', 'center');
//
//// --------------
//
//$tblTopic->addHeaderCell($this->objForum->forumSortLink($forumid, 'lastpost', $this->objLanguage->languageText('mod_forum_lastpost', 'forum')), Null, 'center', 'center');
//
//$tblTopic->endHeaderRow();
//
//// End of First Row
//
//if ($topicsNum > 0) {
//        // Still to be implemented. alternate changing colours
//        // $altRowCSS = 'odd';
//
//        foreach ($topics as $topic) {
//                $altRowCSS = NULL;
//
//                $objIcon = $this->getObject('geticon', 'htmlelements');
//
//                if ($topic['topicstatus'] == 'OPEN') {
//                        $objIcon->setIcon('unlock', NULL, 'icons/forum/');
//                        $objIcon->title = $this->objLanguage->languageText('mod_forum_topicisopen', 'forum');
//                        $rowCSS = $altRowCSS;
//                } else {
//                        $objIcon->setIcon('lock', NULL, 'icons/forum/');
//                        $objIcon->title = $this->objLanguage->languageText('mod_forum_topicislocked', 'forum');
//                        $rowCSS = 'closedTopic';
//                }
//
//                if ($topic['sticky'] == '1') {
//                        $rowCSS = 'stickyTopic';
//                }
//
//                $tblTopic->startRow($rowCSS);
//
//                $tblTopic->addCell($objIcon->show(), Null, 'center');
//
//                if ($topic['readtopic'] == '') {
//                        $objIcon->setIcon('unreadletter');
//                        $objIcon->title = $this->objLanguage->languageText('mod_forum_newunreadtopic', 'forum');
//                } else if ($topic['lastreadpost'] == $topic['last_post']) {
//                        $objIcon->setIcon('readletter');
//                        $objIcon->title = $this->objLanguage->languageText('mod_forum_readtopic', 'forum');
//                } else {
//                        $objIcon->setIcon('readnewposts');
//                        $objIcon->title = $this->objLanguage->languageText('mod_forum_hasnewposts', 'forum');
//                }
//
//                $tblTopic->addCell($objIcon->show(), Null, 'center');
//
//                $objIcon->setIcon($topic['type_icon'], NULL, 'icons/forum/');
//                $objIcon->title = $topic['type_name'];
//
//                $tblTopic->addCell($objIcon->show(), Null, 'center');
//
//                $link = new link($this->uri(array('action' => 'viewtopic', 'id' => $topic['topic_id'], 'type' => $forumtype)));
//
//                $link->link = stripslashes($topic['post_title']);
//
//                if ($topic['sticky'] == '1') {
//                        $objIcon->setIcon('sticky_yes');
//                        $objIcon->title = $this->objLanguage->languageText('mod_forum_stickytopic', 'forum', 'Sticky Topic');
//                        $sticky = $objIcon->show() . ' ';
//                } else {
//                        $sticky = '';
//                }
//
//                $tblTopic->addCell($sticky . $link->show(), '30%', 'center');
//
//                if ($this->showFullName) {
//                        $tblTopic->addCell($topic['firstname'] . ' ' . $topic['surname'], Null, 'center', 'center');
//                } else {
//                        $tblTopic->addCell($topic['username'], Null, 'center', 'center');
//                }
//
//                $tblTopic->addCell($topic['replies'], Null, 'center', 'center');
//                $tblTopic->addCell($topic['views'], Null, 'center', 'center');
//
//                // if (formatDate($topic['lastdate']) == date('j F Y')) {
//                // $datefield = 'Today at '.formatTime($topic['lastdate']);
//                // } else {
//                // $datefield = formatDate($topic['lastdate']).' - '.formatTime($topic['lastdate']);
//                // }
//
//                $datefield = $objTranslatedDate->getDifference($topic['lastdate']);
//
//                $objIcon->setIcon('gotopost', NULL, 'icons/forum/');
//                $objIcon->title = $this->objLanguage->languageText('mod_forum_gotopost', 'forum');
//
//                $lastPostLink = new link($this->uri(array('action' => 'viewtopic', 'id' => $topic['topic_id'], 'post' => $topic['last_post'], 'type' => $forumtype)));
//                $lastPostLink->link = $objIcon->show();
//
//                if ($this->showFullName) {
//                        $tblTopic->addCell($datefield . '<br />' . $topic['lastfirstname'] . ' ' . $topic['lastsurname'] . $lastPostLink->show(), Null, 'center', 'right', 'smallText');
//                } else {
//                        $tblTopic->addCell($datefield . '<br />' . $topic['lastusername'] . $lastPostLink->show(), Null, 'center', 'right', 'smallText');
//                }
//
//                $objIcon->align = 'absmiddle';
//
//                $tblTopic->endRow();
//
//                if ($topic['tangentcheck'] != '') {
//                        $tangents = $this->objTopic->getTangents($topic['topic_id']);
//                        foreach ($tangents as $tangent) {
//                                $tblTopic->startRow();
//                                $tblTopic->addCell('&nbsp;', Null, 'center');
//                                $tblTopic->addCell('&nbsp;', Null, 'center');
//                                $tblTopic->addCell('&nbsp;', Null, 'center');
//
//                                $link = new link($this->uri(array('action' => 'viewtopic', 'id' => $tangent['id'], 'type' => $forumtype)));
//                                $link->link = $tangent['post_title'];
//
//                                $objIcon->setIcon('tangent', NULL, 'icons/forum/');
//                                $objIcon->title = $this->objLanguage->languageText('word_tangent');
//
//                                $tblTopic->addCell($objIcon->show() . ' ' . $link->show(), Null, 'center');
//
//                                if ($this->showFullName) {
//                                        $tblTopic->addCell($tangent['firstname'] . ' ' . $tangent['surname'], Null, 'center', 'center');
//                                } else {
//                                        $tblTopic->addCell($tangent['username'], Null, 'center', 'center');
//                                }
//                                $tblTopic->addCell($tangent['replies'], Null, 'center', 'center');
//                                $tblTopic->addCell($tangent['views'], Null, 'center', 'center');
//
//                                // if (formatDate($tangent['lastdate']) == date('j F Y')) {
//                                // $datefield = $this->objLanguage->languageText('mod_forum_todayat').' '.formatTime($tangent['lastdate']);
//                                // } else {
//                                // $datefield = formatDate($tangent['lastdate']).' - '.formatTime($tangent['lastdate']);
//                                // }
//
//                                $datefield = $objTranslatedDate->getDifference($tangent['lastdate']);
//
//                                $objIcon->setIcon('gotopost', NULL, 'icons/forum/');
//                                $objIcon->title = $this->objLanguage->languageText('mod_forum_gotopost');
//
//                                //$tblTopic->addCell('<strong>'.$tangent['lastFirstName'].' '.$tangent['lastSurname'].'</strong> <br />'.$objIcon->show().$datefield, Null, 'center', 'center', 'smallText');
//
//                                $lastPostLink = new link($this->uri(array('action' => 'viewtopic', 'id' => $tangent['id'], 'post' => $tangent['last_post'], 'type' => $forumtype)));
//                                $lastPostLink->link = $objIcon->show();
//
//                                $objIcon->setIcon('gotopost', NULL, 'icons/forum/');
//
//                                if ($this->showFullName) {
//                                        $tblTopic->addCell($datefield . '<br />' . $tangent['lastfirstname'] . ' ' . $tangent['lastsurname'] . $lastPostLink->show(), Null, 'center', 'right', 'smallText');
//                                } else {
//                                        $tblTopic->addCell($datefield . '<br />' . $tangent['lastusername'] . $lastPostLink->show(), Null, 'center', 'right', 'smallText');
//                                }
//
//                                $tblTopic->endRow();
//                        }
//                }
//        }
//} else {
//
//        $noposts = '<div class="noRecordsMessage">';
//        $noposts .= $this->objLanguage->languageText('mod_forum_nopostsinforum', 'forum') . '.<br /><br />' . $this->objLanguage->languageText('mod_forum_clicklinkstarttopic', 'forum') . '.';
//        $noposts .= '</div>';
//
//        $tblTopic->startRow();
//
//        $tblTopic->addCell($noposts, null, null, null, null, ' colspan="8"');
//        $tblTopic->endRow();
//}
//
//echo $tblTopic->show();
//
//if ($topicsNum > 0) {
//        echo $paging;
//}
//
//// Link to start new topic
//$link = new link($this->uri(array('action' => 'newtopic', 'id' => $forumid, 'type' => $forumtype)));
//$link->link = $this->objLanguage->languageText('mod_forum_startnewtopic', 'forum');
//
//// Start checking whether to show the link
//// Check if the forum is locked
//if ($forum['forumlocked'] != 'Y') {
//        // Check if students can start topic
//        if ($forum['studentstarttopic'] == 'Y') {
//                echo '<p>' . $link->show() . '</p>';
//
//                // Else check if user is lecturer or admin
//        } else if ($this->objUser->isCourseAdmin($this->contextCode)) {
//                echo '<p>' . $link->show() . '</p>';
//        }
//}
//
//echo $this->showForumFooter($forumid);
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
                "module" : "forum",
                "block" : "forumview"
                }
        </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>