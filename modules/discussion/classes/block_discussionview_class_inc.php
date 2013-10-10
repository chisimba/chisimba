<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_discussionview_class_inc
 *
 * @author monwabisi
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end security check
class block_discussionview extends object {

    var $objGroups;
    var $objTranslatedDate;
    var $objLanguage;
    var $objDiscussion;
    var $objTopic;
    var $objUser;
    var $domDoc;
    var $showFullName;
    var $discussionDetails;
    var $discussionid;
    var $contextObject;
    var $objIcon;

    //put your code here
    public function init() {
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('multitabbedbox', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('dbtopic', 'discussion');
        $this->domDoc = new DOMDocument('utf-8');
//                $this->title = "Discussion view";
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objTranslatedDate = $this->getObject('translatedatedifference', 'utilities');
//                $this->objGroups = $this->getObject('groupadmin_model', 'groupadmin');
        $this->objDiscussion = $this->getObject('dbdiscussion', 'discussion');
        $this->contextObject = & $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();
        $this->objTopic = $this->getObject('dbtopic', 'discussion');
        $this->objUser = $this->getObject('user', 'security');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->showFullName = $this->objSysConfig->getValue('SHOWFULLNAME', 'discussion');
        $this->discussionid = $this->getParam('id');
        //get the discussion ID
        $this->discussionDetails = $this->objDiscussion->getDiscussion($this->discussionid);
        $this->title = '';
    }

    /**
     * Biuld all page contents
     * 
     * @return type
     */
    public function buildDiscussionView() {
//                $this->title = $this->discussionDetails['discussion_name'];
        $newTopicIcon = $this->getObject('geticon', 'htmlelements');
        $newTopicIcon->setIcon('notes');
        $newTopicIcon->alt = $this->objLanguage->languageText('mod_discussion_startnewtopic', 'discussion');
        $newTopicIcon->title = $this->objLanguage->languageText('mod_discussion_startnewtopic', 'discussion');
//        $discussiontype = & $this->getVar('discussiontype');
        $tblTopic = $this->newObject('htmltable', 'htmlelements');
        $objTranslatedDate = $this->getObject('translatedatedifference', 'utilities');
        // Link to start new topic
        $newTopicLink = new link($this->uri(array('action' => 'newtopic', 'id' => $this->discussionid, 'type' => $this->discussionDetails['discussion_type'])));
        $newTopicLink->link = $newTopicIcon->show();

// Start of First Row

        $tblTopic->startHeaderRow();

//                $tblTopic->addHeaderCell($this->objDiscussion->discussionSortLink($this->discussionid, 'status', $this->objLanguage->languageText('word_status', 'discussion', 'Status')), '30', 'center');
//
//                // --------------
//
//                $tblTopic->addHeaderCell($this->objDiscussion->discussionSortLink($this->discussionid, 'read', $this->objLanguage->languageText('word_noun_read', 'discussion')), '30', 'center');
//
//                // --------------
//
//
//                $tblTopic->addHeaderCell($this->objDiscussion->discussionSortLink($this->discussionid, 'type', $this->objLanguage->languageText('word_type', 'discussion', 'Type')), '30', 'center');
//
//                // --------------
//
//
//                $tblTopic->addHeaderCell($this->objDiscussion->discussionSortLink($this->discussionid, 'topic', $this->objLanguage->languageText('mod_discussion_topicconversation', 'discussion')), '30%', 'center');
//
//                // --------------
//
//
//                $tblTopic->addHeaderCell($this->objDiscussion->discussionSortLink($this->discussionid, 'author', $this->objLanguage->languageText('word_author')), Null, 'center', 'center');
//
//                // --------------
//
//                $tblTopic->addHeaderCell($this->objDiscussion->discussionSortLink($this->discussionid, 'replies', $this->objLanguage->languageText('word_replies', 'system', 'Replies')), Null, 'center', 'center');
//
//                // --------------
//
//                $tblTopic->addHeaderCell($this->objDiscussion->discussionSortLink($this->discussionid, 'views', $this->objLanguage->languageText('word_views', 'system', 'Views')), Null, 'center', 'center');
//
//                // --------------
//
//                $tblTopic->addHeaderCell($this->objDiscussion->discussionSortLink($this->discussionid, 'lastpost', $this->objLanguage->languageText('mod_discussion_lastpost', 'discussion')), Null, 'center', 'center');
//                $tblTopic->endHeaderRow();

        $header = new htmlheading();
        $header->type = 1;
        $header->str = $this->discussionDetails['discussion_name'];
        //admimn functions table
        $tblAdmin = new htmlTable();
        $tblAdmin->startHeaderRow();
//                $tblTopic->startRow();
//// Start checking whether to show the link
        if ($this->discussionDetails['discussionlocked'] != 'Y' || $this->discussionDetails['studentstarttopic'] == 'Y') {
            // Check if students can start topic
//                        if () {
////                                $header->str .= ' ' . $newTopicLink->show();
//                                $tblTopic->addHeaderCell($newTopicLink->show() . "<br/><label class='menu' >{$this->objLanguage->languageText('mod_discussion_startnewtopic', 'discussion')}");
////                                $tblAdmin->addCell($newTopicLink->show() . "<br/><label class='menu' >{$this->objLanguage->languageText('mod_discussion_startnewtopic', 'discussion')}", NULL, NULL, 'center');
//                                // Else check if user is lecturer or admin
//                        }
        }
        if ($this->objUser->isCourseAdmin($this->contextCode) || $this->discussionDetails['studentstarttopic'] == 'Y') {
//                                $header->str .= ' ' . $newTopicLink->show();
//                                $tblAdmin->addCell($newTopicLink->show(),NULL,NULL,'center');
            $newTopicLink->cssClass = "sexybutton";
            $newTopicLink->link .= "<br/><label class='menu' >{$this->objLanguage->languageText('mod_discussion_startnewtopic', 'discussion')}";
            $tblTopic->addHeaderCell($newTopicLink->show(), NULL, NULL, 'center', NULL);
        }
        $tblTopic->endHeaderRow();
//                $tblAdmin->endHeaderRow();
        $tblTopic->endRow();
        /**
         * @todo Return object containing the start new topic icon/string
         */
        //discussion search object
        $objDiscussionSearch = $this->getObject('discussionsearch');
        $objDiscussionSearch->defaultDiscussion = $this->discussionid;
        /**
         * @todo Return the discussion search object
         */
        // Get Order and Sorting Values
        $order = $this->getSession('sortorder', $this->getSession('sortorder', 'date'));
        $this->objTopic = $this->getObject('dbtopic', 'discussion');
        $direction = $this->getParam('direction', $this->getSession('sortdirection', 'asc'));
        $page = $this->getParam('page', 1);
        $limitPerPage = 30;        // Prevent Users from adding alphabetical items to page
        if (!is_numeric($page)) {
            $page = 1;
        }        // Prevent URL by hacking
        // If page limit is too high, set to 1
        if ($page > $this->objTopic->getNumDiscussionPages($this->discussionid, $limitPerPage, FALSE)) {
            $page = 1;
        }
        $limit = ' LIMIT ' . ($page - 1) * $limitPerPage . ', ' . $limitPerPage;
        $paging = $this->objTopic->prepareTopicPagingLinks($this->discussionid, $page, $limitPerPage);
        $allTopics = $this->objTopic->showTopicsInDiscussion($this->discussionid, $this->objUser->userId($this->objUser->userName()), NULL, $order, $direction, NULL, NULL);
//        echo $order;
        $topicsNum = count($allTopics);
//                if ($topicsNum > 0) {
//                        /**
//                         * @todo Append the number of topics to object
//                         */
////                        echo $paging;
//                }
        $tblTopic->attributes = ' align="center" border="0"';
        $tblTopic->cellspacing = '1';
        $tblTopic->cellpadding = '4';
        $tblTopic->border = '0';
        $tblTopic->width = '99%';

        if ($topicsNum > 0) {
            $tblTopic->css_class = 'discussiontopics';
        }

        $wrapperDiv = "";
        if ($topicsNum > 0) {
            foreach ($allTopics as $topic) {
                $altRowCSS = NULL;

                //
                $divClass = "";
                $divID = "";
                $divContent = "";
                $divHeader = "";
                $divStatus = "";

                $objIcon = $this->getObject('geticon', 'htmlelements');

                if ($topic['topicstatus'] == 'OPEN') {
                    $objIcon->setIcon('unlock', NULL, 'icons/discussion/');
//                                        $objIcon->title = $this->objLanguage->languageText('mod_discussion_topicisopen', 'discussion');
                    $rowCSS = $altRowCSS;
                    //
//                                        $divContent = "locked";
                } else {
                    $objIcon->setIcon('lock', NULL, 'icons/discussion/');
//                                        $objIcon->title = $this->objLanguage->languageText('mod_discussion_topicislocked', 'discussion');
                    $rowCSS = 'closedTopic';
                    //
                    $divClass = "unlocked";
                }

                if ($topic['sticky'] == '1') {
                    $rowCSS = 'stickyTopic';
                    //
//                                        $divContent .= "<br/>Sticky";
                }

                $tblTopic->startRow($rowCSS);

                if ($this->showFullName) {
                    $tblTopic->addCell($this->objUser->getUserImage($topic['userid']) . "<br/>" . $topic['firstname'] . ' ' . $topic['surname'], 100, NULL, 'center');
                } else {
                    $tblTopic->addCell($topic['username'], 100, NULL, 'center');
                    //
//                                        $divContent .= '<br/>' . $topic['username'];
                }

                $tblTopic->addCell($objIcon->show(), 50,NULL, 'center');

//                if ($topic['readtopic'] == '') {
//                    $objIcon->setIcon('unreadletter');
//                    $objIcon->title = $this->objLanguage->languageText('mod_discussion_newunreadtopic', 'discussion');
//                    //
//                    $divContent .= "unread";
//                } else if ($topic['lastreadpost'] == $topic['last_post']) {
//                    $objIcon->setIcon('readletter');
//                    $objIcon->title = $this->objLanguage->languageText('mod_discussion_readtopic', 'discussion');
//                    //
//                    $divContent .= "<br/>read";
//                } else {
//                    $objIcon->setIcon('readnewposts');
//                    $objIcon->title = $this->objLanguage->languageText('mod_discussion_hasnewposts', 'discussion');
//                    //
//                    $divContent .= "<br/>readnewpost";
//                }
//                $tblTopic->addCell($objIcon->show(), Null, 'center');

                $objIcon->setIcon($topic['type_icon'], NULL, 'icons/discussion/');
//                                $objIcon->title = $topic['type_name'];
                //
//                $divContent .= '<br/>'.$topic['type_icon'];

                $tblTopic->addCell($objIcon->show(),50, Null, 'center');

                $link = new link($this->uri(array('action' => 'viewtopic', 'id' => $topic['topic_id'], 'type' => $this->discussionDetails['discussion_type'])));

                $link->link = "<span class='discussionname' >" . stripslashes($topic['post_title']) . "</span>";

                if ($topic['sticky'] == '1') {
                    //
                    $objIcon->setIcon('sticky_yes');
//                    $divContent .= '<br/>'.$objIcon->show();
                    $objIcon->title = $this->objLanguage->languageText('mod_discussion_stickytopic', 'discussion', 'Sticky Topic');
                    $sticky = $objIcon->show() . ' ';
                    $tblTopic->addCell($link->show(), 30, 'center', "center", NULL, 'class=sticky', NULL);
                } else {
                    $tblTopic->addCell($link->show(), 200, 'center', 'left', NULL, NULL, NULL);
                    $sticky = '';
                }


                //
//                                $divContent .= '<br/>' . $topic['replies'];\
                $rpls = "";
                if ($topic['replies'] > 1 || $topic['replies'] == 0) {
                    $rpls = $this->objLanguage->languageText('word_replies', 'system');
                } else {
                    $rpls = $this->objLanguage->languagetext('word_reply', 'system');
                }
                $tblTopic->addCell("<span class='numberindicator' >{$topic['replies']}</span><br/><label class='menu' >{$rpls}</label>", 90, 'center', 'center');

                //
//                                $divContent .= '<br/>' . $topic['views'];
                $vws = "";
                if ($topic['views'] > 1 || $topic['views'] == 0) {
                    $vws = $this->objLanguage->languageText('word_views', 'system');
                } else {
                    $vws = substr($this->objLanguage->languageText('word_views', 'system'), 0, 4);
                }
                $tblTopic->addCell("<span class='numberindicator' >{$topic['views']}</span><br/><label class='menu'>{$vws}</label>", 90, 'center', 'center');

                // if (formatDate($topic['lastdate']) == date('j F Y')) {
                // $datefield = 'Today at '.formatTime($topic['lastdate']);
                // } else {
                // $datefield = formatDate($topic['lastdate']).' - '.formatTime($topic['lastdate']);
                // }
                //
//                $divContent .= '<br/>' . $topic['lastdate'];
                $datefield = $objTranslatedDate->getDifference($topic['lastdate']);

                $objIcon->setIcon('gotopost', NULL, 'icons/discussion/');
                $objIcon->title = $this->objLanguage->languageText('mod_discussion_gotopost', 'discussion');

                $lastPostLink = new link($this->uri(array('action' => 'viewtopic', 'id' => $topic['topic_id'], 'post' => $topic['last_post'], 'type' => $this->discussionDetails['discussion_type'])));
                $lastPostLink->link = $objIcon->show();

                if ($this->showFullName) {
                    //
//                    $divContent .= '<br/>' . $datefield . '<br />' . $topic['lastfirstname'] . ' ' . $topic['lastsurname'] . $lastPostLink->show() . '<br/><br/>';
                    if (strpos($datefield, 'minutes') != FALSE) {
                        $this->objIcon->setIcon('clock');
                    }
                    $tblTopic->addCell($this->objIcon->show().' '. $datefield . '<br />' . $this->objLanguage->languageText('word_by','system').' '.$topic['lastfirstname'] . ' ' . $topic['lastsurname'] . $lastPostLink->show(), 90, 'center', 'left', 'smallText');
                } else {
                    //
                    $divContent .= '<br/>' . $datefield . '<br />' . $topic['lastfirstname'] . ' ' . $topic['lastsurname'] . $lastPostLink->show();
                    $tblTopic->addCell($datefield . '<br />' . $topic['lastusername'] . $lastPostLink->show(), Null, 'center', 'right', 'smallText');
                }

                $objIcon->align = 'absmiddle';

                $tblTopic->endRow();

                if ($topic['tangentcheck'] != '') {
                    $tangents = $this->objTopic->getTangents($topic['topic_id']);
                    foreach ($tangents as $tangent) {
                        $tblTopic->startRow();
                        $tblTopic->addCell('&nbsp;', Null, 'center');
                        $tblTopic->addCell('&nbsp;', Null, 'center');
                        $tblTopic->addCell('&nbsp;', Null, 'center');

                        $link = new link($this->uri(array('action' => 'viewtopic', 'id' => $tangent['id'], 'type' => $this->discussionDetails['discussion_type'])));
                        $link->link = $tangent['post_title'];

                        $objIcon->setIcon('tangent', NULL, 'icons/discussion/');
                        $objIcon->title = $this->objLanguage->languageText('word_tangent');

                        //
                        $divContent .= '<br/>' . $objIcon->sho() . ' ' . $link->show();
                        $tblTopic->addCell($objIcon->show() . ' ' . $link->show(), Null, 'center');

                        if ($this->showFullName) {
                            //
                            $divContent .= '<br/>' . $tangent['firstname'] . ' ' . $tangent['surname'];
                            $tblTopic->addCell($tangent['firstname'] . ' ' . $tangent['surname'], Null, 'center', 'center');
                        } else {
                            $tblTopic->addCell($tangent['username'], Null, 'center', 'center');
                            //
                            $divContent .= '<br/>' . $tangent['username'];
                        }
                        //
//                        $divContent .= '<br/>'.$tangent['replies'].'<br/>';
//                        $divContent .= '<br/>'.$tangent['views'].'<br/>';

                        $tblTopic->addCell($tangent['replies'], Null, 'center', 'center');
                        $tblTopic->addCell($tangent['views'], Null, 'center', 'center');

                        // if (formatDate($tangent['lastdate']) == date('j F Y')) {
                        // $datefield = $this->objLanguage->languageText('mod_discussion_todayat').' '.formatTime($tangent['lastdate']);
                        // } else {
                        // $datefield = formatDate($tangent['lastdate']).' - '.formatTime($tangent['lastdate']);
                        // }

                        $datefield = $objTranslatedDate->getDifference($tangent['lastdate']);

                        $objIcon->setIcon('gotopost', NULL, 'icons/discussion/');
                        $objIcon->title = $this->objLanguage->languageText('mod_discussion_gotopost');

                        $lastPostLink = new link($this->uri(array('action' => 'viewtopic', 'id' => $tangent['id'], 'post' => $tangent['last_post'], 'type' => $this->discussionDetails['discussion_type'])));
                        $lastPostLink->link = $objIcon->show();

                        $objIcon->setIcon('gotopost', NULL, 'icons/discussion/');

                        if ($this->showFullName) {
//                            $tblTopic->addCell($this->objIcon->show() . $datefield . '<br />' . $tangent['lastfirstname'] . ' ' . $tangent['lastsurname'] . $lastPostLink->show(), Null, 'center', 'right', 'smallText');
                        } else {
//                            $tblTopic->addCell($datefield . '<br />' . $tangent['lastusername'] . $lastPostLink->show(), Null, 'center', 'right', 'smallText');
                        }
                        $tblTopic->endRow();
                    }
                }
            }
        } else {

            $noposts = '<div class="noRecordsMessage">';
            $noposts .= $this->objLanguage->languageText('mod_discussion_nopostsindiscussion', 'discussion') . '.<br /><br />' . $this->objLanguage->languageText('mod_discussion_clicklinkstarttopic', 'discussion') . '.';
            $noposts .= '</div>';

            $tblTopic->startRow();

            $tblTopic->addCell($noposts, null, null, null, null, ' colspan="8"');
            $tblTopic->endRow();
        }
        return $tblAdmin->show() . $tblTopic->show();
    }

    public function show() {
        return $this->buildDiscussionView();
    }

}

?>
