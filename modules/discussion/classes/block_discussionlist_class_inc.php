<?php

/**
 * @package discussion
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check

class block_discussionlist extends object {

        var $objLanguage;
        var $objPost;
        var $objUser;
        var $discussionContext;
        var $trimStrObj;
        var $objSysConfig;
        var $objDateTime;
        var $objDiscussion;
        var $objUserContext;
        var $contextObject;
        var $objIcon;
        var $objTimeOutMessage;

        public function init() {
                $this->loadClass('link', 'htmlelements');
                $this->loadClass('form', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                $this->loadClass('textinput', 'htmlelements');
                $this->loadClass('button', 'htmlelements');
                $this->loadClass('label', 'htmlelements');
                $this->loadClass('htmlheading', 'htmlelements');
                $this->loadClass('hiddeninput', 'htmlelements');
                $this->loadClass('user', 'security');
                $this->objUserContext = $this->getObject('usercontext', 'context');
                $this->objLanguage = $this->getObject('language', 'language');
                $this->title = '';
                $this->objPost = $this->getObject('dbpost', 'discussion');
                $this->objUser = $this->getObject('user', 'security');
                $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $this->objDiscussion = $this->getObject('dbdiscussion', 'discussion');
                $this->objIcon = $this->getObject('geticon', 'htmlelements');
                // Get Context Code Settings
                $this->contextObject = & $this->getObject('dbcontext', 'context');
                $this->contextCode = $this->contextObject->getContextCode();
                // Trim String Functions
                $this->trimstrObj = & $this->getObject('trimstr', 'strings');
                $this->objDateTime = & $this->getObject('dateandtime', 'utilities');
                $this->objTimeOutMessage = $this->getObject('timeoutmessage', 'htmlelements');
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
//                $tblclass->addHeaderCell('<strong>' . $this->objLanguage->languageText('mod_discussion', 'discussion') . '</strong>', '40%');
//                $tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('word_topics') . '</nobr></strong>', 100, NULL, 'center');
//                $tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('word_posts') . '</nobr></strong>', 100, NULL, 'center');
//                $tblclass->addHeaderCell('<strong><nobr>' . $this->objLanguage->languageText('mod_discussion_lastpost', 'discussion') . '</nobr></strong>', 100);
//                $tblclass->endHeaderRow();
                $dropdown = new dropdown('discussion');
                $dropdown->addOption('all', 'All Discussions');
                $homeForm = $this->getObject('form', 'htmlelements');
                $rowcount = 0;
//user object to be used in determining if user is admin
//                $objUser = $this->getObject('user', 'security');
//                $objDB = &$this->getObject('dbdiscussion', 'discussion');
//        $discussions = $objDB->getAll();
                $discussions = $this->objDiscussion->showAllDiscussions($this->contextCode);

                //admin table
                $admintable = new htmlTable();
                $admintable->startHeaderRow();
                if ($this->objUser->isCourseAdmin($this->contextCode)) {
                        $administrationLink = new link($this->uri(array('module' => 'discussion', 'action' => 'administration')));
//                        $administrationLink->link = $this->objLanguage->languageText('mod_discussion_discussionadministration', 'discussion');
                        $administrationLink->cssClass = "sexybutton";
                        $this->objIcon->setIcon('settings');
                        $administrationLink->link = $this->objIcon->show() . "<br/><label class='menu' >{$this->objLanguage->languageText('mod_discussion_discussionadministration', 'discussion')} </label>";
                        $admintable->addHeaderCell($administrationLink->show(), NULL, NULL, 'center', NULL);
//                        $homeForm->addToForm('<br/>' . $administrationLink->show());
                }
                $admintable->endHeaderRow();
                $homeForm->addToForm($admintable->show());
                foreach ($discussions as $discussion) {
                        if ($this->objUserContext->isContextMember($this->objUser->userId(), $discussion['discussion_context']) || $discussion['discussion_context'] == 'root') {
//                                
//                        }
                                if ($this->contextObject->isInContext() || $discussion['discussion_context'] == 'root') {
//                                echo $discussion['discussion_context'].'<br/>';
//                                echo "<br/>In context mode {$this->contextObject->getContextCode()}<br/>";
//                                if ($this->objUserContext->isContextMember($this->objUser->userId($this->objUser->email()), $this->contextObject->getContextCode()) || $discussion['discussion_context'] == 'root') {
//                                        echo "<br/>member of context<br/>";
//                                } else {
//                                        continue;
//                                }
//                        }
                                        $oddOrEven = ($rowcount == 0) ? "odd" : "even";
                                        $dropdown->addOption($discussion['id'], $discussion['discussion_name']);
                                        $discussionLink = new link($this->uri(array('module' => 'discussion', 'action' => 'discussion', 'id' => $discussion['discussion_id'])));
                                        $discussionLink->link = $discussion['discussion_name'];
                                        $discussionName = $discussionLink->show();
                                        $this->contextCode = $discussion['discussion_context'];
                                        if ($discussion['defaultdiscussion'] == 'Y') {
                                                $discussionName .= '<em> - ' . $this->objLanguage->languageText('mod_discussion_defaultDiscussion', 'discussion', 'Default Discussion') . '</em>';
                                        }
                                        if ($discussion['discussionlocked'] == 'Y') {
                                                $this->objIcon->setIcon('lock', NULL, 'icons/discussion/');
                                                $this->objIcon->title = $this->objLanguage->languageText('mod_discussion_discussionislocked', 'discussion');
                                        } else {
                                                $this->objIcon->setIcon('unlock', NULL, 'icons/discussion/');
                                                $this->objIcon->title = $this->objLanguage->languageText('mod_discussion_discussionisopen', 'discussion');
                                        }
                                        $tblclass->startRow($oddOrEven);
                                        $tblclass->addCell($this->objIcon->show(), '50', NULL, 'center');
                                        $tblclass->addCell($discussionName . '<br />' . $this->objLanguage->abstractText($discussion['discussion_description']), '200', NULL, 'left');
                                        //Check the number in order to display the correct value
                                        $tpcs = "";
                                        if ($discussion['topics'] > 1 || $discussion['topics'] == 0) {
                                                $tpcs = $this->objLanguage->languageText('word_topics', 'system');
                                        } else {
//                                $tpcs = strlen($this->objLanguage->languageText('word_topics','system'));
                                                $tpcs = substr($this->objLanguage->languageText('word_topics', 'system'), 0, 5);
//                                $tpcs = "Topic";
                                        }
                                        $psts = "";
                                        if ($discussion['post'] > 1 || $discussion['post'] == 0) {
                                                $psts = $this->objLanguage->languageText('word_posts', 'system');
                                        } else {
                                                $psts = substr($this->objLanguage->languageText('word_posts', 'system'), 0, 4);
//                                $psts = "Post";
                                        }
                                        $tblclass->addCell('<span class="numberindicator">' . $discussion['topics'] . '</span>' . '<br/>' . $tpcs, 100, NULL, 'center');
                                        $tblclass->addCell('<span class="numberindicator" >' . $discussion['post'] . '</span>' . '<br/>' . $psts, 100, NULL, 'center');
                                        $post = $this->objPost->getLastPost($discussion['id']);
                                        if ($post == FALSE) {
                                                $postDetails = '<em>' . $this->objLanguage->languageText('mod_discussion_nopostsyet', 'discussion') . '</em>';
                                                $cssClass = NULL;
                                        } else {
                                                $cssClass = 'smallText';
                                                $postLink = new link($this->uri(array('module' => 'discussion', 'action' => 'viewtopic', 'id' => $post['topic_id'], 'post' => $post['post_id'])));
                                                $postLink->link = stripslashes($post['post_title']);
                                                $postDetails = '<strong>' . $postLink->show() . '</strong>';
                                                $postDetails .= '<br />' . $this->trimstrObj->strTrim(stripslashes(str_replace("\r\n", ' ', strip_tags($post['post_text']))), 80);

                                                $this->showFullName = $this->objSysConfig->getValue('SHOWFULLNAME', 'discussion');
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
                                                        $datefield = $this->objLanguage->languageText('mod_discussion_todayat', 'discussion') . ' ' . $this->objDateTime->formatTime($post['datelastupdated']);
                                                } else {
                                                        $datefield = $this->objDateTime->formatDateOnly($post['datelastupdated']) . ' - ' . $this->objDateTime->formatTime($post['datelastupdated']);
                                                }

                                                $postDetails .= '<br /><strong>' . $user . $datefield . '</strong>';
                                        }
                                        $tblclass->addCell($postDetails, 300, NULL, NULL, 'center', $cssClass);
                                        $tblclass->endRow();
                                        // Set rowcount for bitwise determination of odd or even
                                        $rowcount = ($rowcount == 0) ? 1 : 0;
                                }
                        }
                }
                $homeForm->addToForm($tblclass->show());
                $objSearch = $this->getObject('discussionsearch');
//        $homeForm->addToForm($editLink->show());
                $homeForm->addToForm($objSearch->show());
                return '<div class="discussion_main" >' . $homeForm->show() . '</div>';
        }

        public function show() {
                return $this->buildHome();
        }

}

?>
