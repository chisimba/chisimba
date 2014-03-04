<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of block_discussionside_class_inc
 *
 * @author monwabisi
 */

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

// end security check
class block_discussionside extends object {

        var $contextObject;
        var $contextCode;
        var $objPost;
        var $objUser;
        var $objContextUser;
        var $objSysConfig;
        var $objDiscussion;
        var $objTopic;

        //put your code here
        function init() {
                $this->loadClass('link', 'htmlelements');
                $this->title = "Discussion Side block";
                // Get Context Code Settings
                $this->contextObject = & $this->getObject('dbcontext', 'context');
                $this->contextCode = $this->contextObject->getContextCode();
                $this->objPost = $this->getObject('dbpost', 'discussion');
                $this->objUser = $this->getObject('user', 'security');
                $this->objContextUser = $this->getObject('usercontext', 'context');
                $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $this->objDiscussion = $this->getObject('dbdiscussion', 'discussion');
                $this->objTopic = $this->getObject('dbtopic', 'discussion');
        }

        function buildForm() {
                $discussions = $this->objDiscussion->showAllDiscussions($this->contextCode);
                if (count($discussions) > 0) {
                        $div = "";
                        $pointerLink = new link("#");
                        $pointerLink->link = ">&nbsp;";
                        //looping discussions
                        foreach ($discussions as $discussion) {
                                if ($this->contextObject->isInContext() || $discussion['discussion_context'] == 'root') {
                                        $objLink = new link(/* $this->uri(array('module' => 'discussion', 'action' => 'discussion', 'id' => $discussion['discussion_id'])) */"#");
                                        $topics = $this->objTopic->showTopicsInDiscussion($discussion['id'], $this->objUser->userId($this->objUser->userName()), $discussion['archivedate'], NULL, NULL, NULL, NULL);
                                        $objLink->link = $discussion['discussion_name'];
                                        $objLink->cssId = $discussion['id'];
                                        $objLink->cssClass = "indicatorparent";
                                        $html = $objLink->show() . "<br/>";
                                        $html .= "<ul id='{$discussion['id']}' class='indicator' >";
                                        //looping topics
                                        foreach ($topics as $topic) {
                                                $topicLink = new link($this->uri(array('module' => 'discussion', 'action' => 'viewtopic', 'id' => $topic['topic_id'])));
                                                $topicLink->link = $topic['post_title'];
                                                $html .= "<br/><li id='{$topic['discussion_id']}'  class='indicator'> {$topicLink->show()}<span class='indicator'> {$topic['replies']}</span></li>";
                                        }
                                        $html .= '</ul><br/>';
                                        $div .= $html;
                                }
                        }
                }
                return $div . $this->getjavascriptFile('effects.js', 'discussion');
        }

        function show() {
                return $this->buildForm();
        }

}

?>
