<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_forumside_class_inc
 *
 * @author monwabisi
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_forumside extends object {

        var $contextObject;
        var $contextCode;
        var $objPost;
        var $objUser;
        var $objContextUser;
        var $objSysConfig;
        var $objForum;
        var $objTopic;

        //put your code here
        function init() {
                $this->loadClass('link', 'htmlelements');
                $this->title = "Forum Side block";
                // Get Context Code Settings
                $this->contextObject = & $this->getObject('dbcontext', 'context');
                $this->contextCode = $this->contextObject->getContextCode();
                $this->objPost = $this->getObject('dbpost', 'forum');
                $this->objUser = $this->getObject('user', 'security');
                $this->objContextUser = $this->getObject('usercontext', 'context');
                $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $this->objForum = $this->getObject('dbforum', 'forum');
                $this->objTopic = $this->getObject('dbtopic', 'forum');
        }

        function buildForm() {
                $forums = $this->objForum->showAllForums($this->contextCode);
                if (count($forums) > 0) {
                        $div = "";
                        $pointerLink = new link("#");
                        $pointerLink->link = ">&nbsp;";
                        //looping forums
                        foreach ($forums as $forum) {
                                if ($this->contextObject->isInContext() || $forum['forum_context'] == 'root') {
                                        $objLink = new link(/* $this->uri(array('module' => 'forum', 'action' => 'forum', 'id' => $forum['forum_id'])) */"#");
                                        $topics = $this->objTopic->showTopicsInForum($forum['id'], $this->objUser->userId($this->objUser->userName()), $forum['archivedate'], NULL, NULL, NULL, NULL);
                                        $objLink->link = $forum['forum_name'];
                                        $objLink->cssId = $forum['id'];
                                        $objLink->cssClass = "indicatorparent";
                                        $html = $objLink->show() . "<br/>";
                                        $html .= "<ul id='{$forum['id']}' class='indicator' >";
                                        //looping topics
                                        foreach ($topics as $topic) {
                                                $topicLink = new link($this->uri(array('module' => 'forum', 'action' => 'viewtopic', 'id' => $topic['topic_id'])));
                                                $topicLink->link = $topic['post_title'];
                                                $html .= "<br/><li id='{$topic['forum_id']}'  class='indicator'> {$topicLink->show()}<span class='indicator'> {$topic['replies']}</span></li>";
                                        }
                                        $html .= '</ul><br/>';
                                        $div .= $html;
                                }
                        }
                }
                return $div . $this->getjavascriptFile('effects.js', 'forum');
        }

        function show() {
                return $this->buildForm();
        }

}

?>
