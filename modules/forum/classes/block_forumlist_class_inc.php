<?php

/**
 * @package forum
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check

class block_forumlist extends object {

        var $objCkEdtitorTwo;
        var $objLanguage;
        var $objPost;
        var $objUser;
        var $forumContext;
        var $trimStrObj;
        var $objSysConfig;
        var $objDateTime;
        var $objForum;
        var $objUserContext;
        var $contextObject;
        var $objIcon;
        var $objTimeOutMessage;

        public function init() {
                $this->loadClass('link', 'htmlelements');
                $this->loadClass('form', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                $this->loadClass('textinput', 'htmlelements');
//                $this->loadClass('button', 'htmlelements');
                $this->loadClass('label', 'htmlelements');
                $this->loadClass('htmlheading', 'htmlelements');
                $this->loadClass('hiddeninput', 'htmlelements');
                $this->loadClass('user', 'security');
                $this->objUserContext = $this->getObject('usercontext', 'context');
                $this->objLanguage = $this->getObject('language', 'language');
                $this->title = '';
                $this->objPost = $this->getObject('dbpost', 'forum');
                $this->objUser = $this->getObject('user', 'security');
                $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $this->objForum = $this->getObject('dbforum', 'forum');
                $this->objIcon = $this->getObject('geticon', 'htmlelements');
                // Get Context Code Settings
                $this->contextObject = & $this->getObject('dbcontext', 'context');
                $this->contextCode = $this->contextObject->getContextCode();
                // Trim String Functions
                $this->trimstrObj = & $this->getObject('trimstr', 'strings');
                $this->objDateTime = & $this->getObject('dateandtime', 'utilities');
                $this->objTimeOutMessage = $this->getObject('timeoutmessage', 'htmlelements');
                $this->objCkEdtitorTwo = $this->getObject('ckeditor2', 'ckeditor2');
        }

        public function buildHome() {
                $tblclass = $this->newObject('htmltable', 'htmlelements');
                $tblclass->width = '';
                $tblclass->attributes = " align='center'";
                $tblclass->cellspacing = '0';
                $tblclass->cellpadding = '5';
                $tblclass->border = '0';
                $tblclass->width = '100%';
//                $tblclass->startHeaderRow();
//                $tblclass->addHeaderCell('&nbsp;', 10, 'center');
//                $tblclass->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_forum', 'forum') . '</strong>', '40%');
//                $tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('word_topics') . '</nobr></strong>', 100, NULL, 'center');
//                $tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('word_posts') . '</nobr></strong>', 100, NULL, 'center');
//                $tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('mod_forum_lastpost', 'forum') . '</nobr></strong>', 100);
//                $tblclass->endHeaderRow();
                $dropdown = new dropdown('forum');
                $dropdown->addOption('all', 'All Forums');
                $homeForm = $this->getObject('form', 'htmlelements');
                $rowcount = 0;
//user object to be used in determining if user is admin
//                $objUser = $this->getObject('user', 'security');
//                $objDB = &$this->getObject('dbforum', 'forum');
//        $forums = $objDB->getAll();
                $forums = $this->objForum->showAllForums($this->contextCode);

                //admin table
                $admintable = new htmlTable();
                $admintable->startHeaderRow();
                if ($this->objUser->isCourseAdmin($this->contextCode)) {
                        $administrationLink = new link($this->uri(array('module' => 'forum', 'action' => 'administration')));
//                        $administrationLink->link = $this->objLanguage->languageText('mod_forum_forumadministration', 'forum');
                        $administrationLink->cssClass = "sexybutton";
                        $this->objIcon->setIcon('settings');
                        $administrationLink->link = $this->objIcon->show() . "<br/><label class='menu' >{$this->objLanguage->languageText('mod_forum_forumadministration', 'forum')} </label>";
                        $admintable->addHeaderCell($administrationLink->show(), NULL, NULL, 'center', NULL);
//                        $homeForm->addToForm('<br/>' . $administrationLink->show());
                }
                $admintable->endHeaderRow();
                $homeForm->addToForm($admintable->show());
                foreach ($forums as $forum) {
                        $forumDetails = $this->contextObject->getContextDetails($forum['forum_context']);
                        if ($this->objUserContext->isContextMember($this->objUser->userId(), $forum['forum_context']) || $forum['forum_context'] == 'root' || strtolower($forumDetails['access']) == 'public' || strtolower($forumDetails['access']) == 'open') {
//                                
//                        }
//                                if ($this->contextObject->isInContext() || $forum['forum_context'] == 'root') {
//                                echo $forum['forum_context'].'<br/>';
//                                echo "<br/>In context mode {$this->contextObject->getContextCode()}<br/>";
//                                if ($this->objUserContext->isContextMember($this->objUser->userId($this->objUser->email()), $this->contextObject->getContextCode()) || $forum['forum_context'] == 'root') {
//                                        echo "<br/>member of context<br/>";
//                                } else {
//                                        continue;
//                                }
//                        }
                                $oddOrEven = ($rowcount == 0) ? "odd" : "odd";
                                $dropdown->addOption($forum['id'], $forum['forum_name']);
                                $forumLink = new link($this->uri(array('module' => 'forum', 'action' => 'forum', 'id' => $forum['forum_id'])));
                                $forumLink->link = $forum['forum_name'];
                                $forumName = $forumLink->show();
                                $this->contextCode = $forum['forum_context'];
                                if ($forum['defaultforum'] == 'Y') {
                                        $forumName .= '<em> - ' . $this->objLanguage->languageText('mod_forum_defaultForum', 'forum', 'Default Forum') . '</em>';
                                }
                                if ($forum['forumlocked'] == 'Y') {
                                        $this->objIcon->setIcon('lock', NULL, 'icons/forum/');
                                        $this->objIcon->title = $this->objLanguage->languageText('mod_forum_forumislocked', 'forum');
                                } else {
                                        $this->objIcon->setIcon('unlock', NULL, 'icons/forum/');
                                        $this->objIcon->title = $this->objLanguage->languageText('mod_forum_forumisopen', 'forum');
                                }
                                $tblclass->startRow($oddOrEven);
                                $tblclass->addCell($this->objIcon->show(), '50', NULL, 'center');
                                $tblclass->addCell($forumName . '<br />' . $this->objLanguage->abstractText($forum['forum_description']), '200', NULL, 'left');
                                //Check the number in order to display the correct value
                                $tpcs = "";
                                if ($forum['topics'] > 1 || $forum['topics'] == 0) {
                                        $tpcs = $this->objLanguage->languageText('word_topics', 'system');
                                } else {
//                                $tpcs = strlen($this->objLanguage->languageText('word_topics','system'));
                                        $tpcs = substr($this->objLanguage->languageText('word_topics', 'system'), 0, 5);
//                                $tpcs = "Topic";
                                }
                                $psts = "";
                                if ($forum['post'] > 1 || $forum['post'] == 0) {
                                        $psts = $this->objLanguage->languageText('word_posts', 'system');
                                } else {
                                        $psts = substr($this->objLanguage->languageText('word_posts', 'system'), 0, 4);
//                                $psts = "Post";
                                }
                                $tblclass->addCell('<span class="numberindicator">' . $forum['topics'] . '</span>' . '<br/>' . $tpcs, 100, NULL, 'center');
                                $tblclass->addCell('<span class="numberindicator" >' . $forum['post'] . '</span>' . '<br/>' . $psts, 100, NULL, 'center');
                                $post = $this->objPost->getLastPost($forum['id']);
                                if ($post == FALSE) {
                                        $postDetails = '<em>' . $this->objLanguage->languageText('mod_forum_nopostsyet', 'forum') . '</em>';
                                        $cssClass = NULL;
                                } else {
                                        $cssClass = 'smallText';
                                        $postLink = new link($this->uri(array('module' => 'forum', 'action' => 'viewtopic', 'id' => $post['topic_id'], 'post' => $post['post_id'])));
                                        $postLink->link = stripslashes($post['post_title']);
                                        $postDetails = '<strong>' . $postLink->show() . '</strong>';
                                        $postDetails .= '<br />' . $this->trimstrObj->strTrim(stripslashes(str_replace("\r\n", ' ', strip_tags($post['post_text']))), 80);

                                        $this->showFullName = $this->objSysConfig->getValue('SHOWFULLNAME', 'forum');
                                        if ($post['firstname'] != '') {
                                                if ($this->showFullName) {
                                                        $user = 'By: ' . $post['firstname'] . ' ' . $post['surname'] . ' - ';
                                                } else {
                                                        $user = 'By: ' . $post['username'] . ' - ';
                                                }
                                        } else {
                                                $user = '';
                                        }
                                        if ($this->objDateTime->formatDateOnly($post['datelastupdated']) == date('j F Y')) {
                                                $datefield = $this->objLanguage->languageText('mod_forum_todayat', 'forum') . ' ' . $this->objDateTime->formatTime($post['datelastupdated']);
                                        } else {
                                                $datefield = $this->objDateTime->formatDateOnly($post['datelastupdated']) . ' - ' . $this->objDateTime->formatTime($post['datelastupdated']);
                                        }

                                        $postDetails .= '<br /><strong>' . $user . $datefield . '</strong>';
                                }
                                $tblclass->addCell($postDetails, 300, NULL, NULL, 'center', $cssClass);
                                $tblclass->endRow();
                                // Set rowcount for bitwise determination of odd or even
                                $rowcount = ($rowcount == 0) ? 1 : 0;
//                                }
                        }
                }
                $homeForm->addToForm($tblclass->show());
                $objSearch = $this->getObject('forumsearch');
//        $homeForm->addToForm($editLink->show());
                $homeForm->addToForm($objSearch->show());
                return '<div class="forum_main" >' . $homeForm->show() . '</div>';
        }

        public function show() {
                return $this->buildHome();
        }

}

?>
