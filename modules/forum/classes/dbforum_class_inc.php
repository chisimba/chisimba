<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

/**
 * Forum Table
 * This class controls all functionality relating to the tbl_forum table
 * @author Tohir Solomons
 * @copyright (c) 2004 University of the Western Cape
 * @package forum
 * @version 1
 */
class dbForum extends dbTable {

        /**
         *  $var  Context Code for the current Context
         */
        var $contextCode;

        /**
         *  $var  Context Title for the current Context
         */
        var $contextTitle;

        /**
         * Constructor method to define the table(default)
         */
        function init() {
                parent::init('tbl_forum');

                // Context Code
                $this->contextObject = & $this->getObject('dbcontext', 'context');
                $this->contextCode = $this->contextObject->getContextCode();

                $this->contextTitle = $this->contextObject->getTitle();
                if ($this->contextCode == '') {
                        $this->contextCode = 'root';
                        $this->contextTitle = 'Lobby';
                }

                $this->objUser = & $this->getObject('user', 'security');
                $this->objLanguage = & $this->getObject('language', 'language');
        }

        /**
         * Method to get number of forums in a context
         *
         * @param string $context: Context Code
         * @return integer number of forums
         */
        function getNumForums($context = null) {
                $sql = '';
                if (isset($context)) {
                        $sql = 'WHERE forum_context = \'' . $context . '\' AND forum_type= \'context\' AND forum_visible=\'Y\'';
                }

                return $this->getRecordCount($sql);
        }

        /**
         * Method to get list of forums in a context
         *
         * @param string $context: Context Code
         * @return array list of forums
         */
        function getContextForums($context = null) {
                $sql = '';
                if (isset($context)) {
                        $sql = 'WHERE forum_context = \'' . $context . '\' AND forum_type= \'context\' AND forum_visible=\'Y\'';
                }

                return $this->getAll($sql);
        }

        /**
         * Method to get list of all forums in a context whether they are visible or not
         *
         * @param string $context: Context Code
         * @return array list of forums
         */
        function getAllContextForums($context = null) {
                $sql = '';
                if (isset($context)) {
                        $sql = 'WHERE forum_context = \'' . $context . '\' AND forum_type= \'context\'';
                }

                return $this->getAll($sql);
        }

        /**
         * Method to get automatically create a forum in a context
         *
         * @param string $context: Context Code
         * @param string $title: Title to create the forum
         */
        function autoCreateForum($context, $title) {

                // confirm there is no forums in the context
                if ($this->getNumForums($context) == 0) {
                        $forum_context = $context;
                        $forum_workgroup = '';
                        $forum_name = $title . ' Forum';
                        $forum_description = $this->objLanguage->code2Txt('mod_forum_defaultdescription', 'forum');
                        $enableDefaultForum = 'Y'; // Set to YES

                        $newForumId = $this->insertSingle($forum_context, $forum_workgroup, $forum_name, $forum_description, $enableDefaultForum);
                } else {
                        $newForumId = NULL;
                }
                return $newForumId;
        }

        /**
         * This function provides a forum by providing a context
         *  in the module, it will only be called if a forum exists, so that isn't built in
         *
         * @param string $context: Context Code
         * @return array details of the forum
         */
        function onlyForum($context) {
                return $this->getRow('forum_context', $context);
        }

        /**
         * This method gets the list of forums in a context, and the amount of topics in the forums
         *
         * @param string $context: Context Code
         * @return array list of forums
         */
        function showAllForums($context = null) {
                $sql = 'SELECT tbl_forum.id AS forum_id, tbl_forum. *,
        count( DISTINCT topicCountLink.id ) AS topics, count( DISTINCT postCountLink.id ) AS posts
        FROM tbl_forum LEFT JOIN tbl_forum_topic AS topicCountLink ON ( topicCountLink.forum_id = tbl_forum.id )
        LEFT JOIN tbl_forum_post AS postCountLink ON ( postCountLink.topic_id = topicCountLink.id )  ';

                $sql .= ' WHERE forum_visible=\'Y\' ';

                if (isset($context)) {
                        $sql .= '  AND tbl_forum.forum_context = \'' . $context . '\' AND forum_type= \'context\'';
                }

                $sql .= ' GROUP BY tbl_forum.id';
                $sql .= ' ORDER BY defaultforum, topicCountLink.id DESC  ';

                return $this->getArray($sql);
        }

        /**
         * This method gets a list of forums in a context, other than the forum specified.
         * This is used to jump between forums in a context
         *
         * @param string $forum_id: Record Id of the forum
         * @param string $context: Context Code
         * @return array list of forums
         */
        function otherForums($forum_id, $context) {
                $sql = 'SELECT tbl_forum.id AS forum_id, tbl_forum.*, count(tbl_forum_topic.id) AS topics from tbl_forum_topic RIGHT JOIN tbl_forum ON (tbl_forum_topic.forum_id = tbl_forum.id) WHERE forum_visible=\'Y\' AND forum_type= \'context\' AND tbl_forum.forum_context = \'' . $context . '\' AND tbl_forum.id != \'' . $forum_id . '\'';

                $sql .= ' GROUP BY tbl_forum.id';
                $sql .= ' ORDER BY defaultforum, topics DESC  ';

                return $this->getArray($sql);
        }

        /**
         * This method gets the details of a single forum by providing the forum_id
         *
         * @param string $forum_id: Record Id of the forum
         * @return array list of forums
         */
        function getForum($forum_id) {
                return $this->getRow('id', $forum_id);
        }

        /**
         * This method gets forum_id of the default forum with in a context
         * This method also checks that a forum exists, and creates one if none is present
         *
         * @return string Record Id of the default forum
         */
        function getContextForum() {
                $forumNum = $this->getNumForums($this->contextCode);

                if ($forumNum == 0) {
                        $forum = $this->autoCreateForum($this->contextCode, $this->contextTitle);
                }
                $forum = $this->getDefaultForum($this->contextCode);

                return $forum['id'];
        }

        /**
         * Insert a forum into into the database
         *
         * @param string $forum_context:      Context for which this forum applies
         * @param string $forum_workgroup:   Workgroup the forum belongs to
         * @param string $forum_name:          Name of the Forum
         * @param string $forum_description: Description of the forum
         * @param string $forum_visible:       Is the forum visible-
         * @param string $ratingsenabled:      Can users rate posts in the forum
         * @param string $studentstarttopic: Can students start topics in the forum
         * @param string $attachments:         Can users upload attachments in the forum
         * @param string $subscriptions:       Can users subscribe to topics in the forum
         * @param string $moderation:          Can users moderate posts in the forum // Under construction
         * @param string $defaultForum:       Is this the default forum
         */
        function insertSingle($forum_context, $forum_workgroup, $forum_name, $forum_description, $defaultForum = 'N', $forum_visible = 'Y', $enablePosts = 'Y', $ratingsenabled = 'N', $studentstarttopic = 'Y', $attachments = 'Y', $subscriptions = 'N', $moderation = 'Y', $forumlocked = 'N') {
                $this->insert(array(
                    'forum_context' => $forum_context,
                    'forum_workgroup' => $forum_workgroup,
                    'forum_name' => $forum_name,
                    'forum_description' => $forum_description,
                    'forum_visible' => $forum_visible,
                    'defaultforum' => $defaultForum,
                    'ratingsenabled' => $ratingsenabled,
                    'studentstarttopic' => $studentstarttopic,
                    'attachments' => $attachments,
                    'subscriptions' => $subscriptions,
                    'moderation' => $moderation,
                    'forumlocked' => $forumlocked
                ));


                $newForumId = $this->getLastInsertId();

                // Done to Avoid Null Values
                if ($forum_workgroup == '') {
                        $this->update('id', $newForumId, array('forum_workgroup' => ''));
                }

                $userId = $this->objUser->userId();
                $objForumDefaultRatings = & $this->getObject('dbforum_default_ratings', 'forum');
                $objForumRatings = & $this->getObject('dbforum_ratings', 'forum');

                $defaultRatings = $objForumDefaultRatings->getDefaultList();

                foreach ($defaultRatings as $rating) {
                        $objForumRatings->insertSingle(
                                $newForumId, $rating['rating_description'], $rating['rating_point'], $userId, mktime()
                        );
                }

                // Add Dynamic Block for latest post in forum
                if ($newForumId != FALSE) {
                        $this->createDynamicBlocksPost($newForumId, $forum_context, $forum_name);
                }

                // Add Dynamic Block for the forum view
                if ($newForumId != FALSE) {
                        $this->createDynamicBlocksView($newForumId, $forum_context, $forum_name);
                }

                // Add to Search
                $objIndexData = $this->getObject('indexdata', 'search');

                // Prep Data
                $docId = 'forum_entry_' . $newForumId;
                $docDate = strftime('%Y-%m-%d %H:%M:%S', mktime());
                $url = $this->uri(array('action' => 'forum', 'id' => $newForumId), 'forum');
                $title = $forum_name;
                $contents = $forum_name . ': ' . $forum_description;
                $teaser = $forum_description;
                $module = 'forum';
                $userId = $userId;
                $context = $forum_context;

                // Add to Index
                $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, $context);

                return $newForumId;
        }

        /**
         * This method gets forum_id of the default forum by providing a context
         *
         * @param string $context context code of the current context
         */
        function getDefaultForum($context) {
                $sql = 'SELECT id FROM tbl_forum WHERE forum_context= \'' . $context . '\'  AND forum_type= \'context\' AND defaultforum=\'Y\'';

                $list = $this->getArray($sql);

                if (count($list) > 0) {
                        return $list[0];
                } else {
                        return;
                }
        }

        /**
         * This method sets a forum as the default forum for a context
         *
         * @param string $forum record Id of the forum
         * @param string $context Context Code
         */
        function setDefaultForum($forum, $context) {
                $this->update('forum_context', $context, array(
                    'defaultforum' => 'N'
                ));

                $this->update('id', $forum, array(
                    'defaultforum' => 'Y',
                    'forum_visible' => 'Y'
                ));

                return;
        }

        /**
         * Update settings of a forum
         *
         * @param string $forum_id:      Context for which this forum applies
         * @param string $forum_name:          Name of the Forum
         * @param string $forum_description: Description of the forum
         * @param string $forum_visible:       Is the forum visible-
         * @param string $ratingsenabled:      Can users rate posts in the forum
         * @param string $studentstarttopic: Can students start topics in the forum
         * @param string $attachments:         Can users upload attachments in the forum
         * @param string $subscriptions:       Can users subscribe to topics in the forum
         * @param string $moderation:          Can users moderate posts in the forum // Under construction
         * @param string $archiveDate:        Date to start Archiving from // NULL if archiving is disabled
         */
        function updateSingle($forum_id, $forum_name, $forum_description, $forum_visible, $forum_locked, $ratingsenabled, $studentstarttopic, $attachments, $subscriptions, $moderation, $archiveDate) {

                $this->update('id', $forum_id, array(
                    'forum_name' => $forum_name,
                    'forum_description' => $forum_description,
                    'forum_visible' => $forum_visible,
                    'forumlocked' => $forum_locked,
                    'ratingsenabled' => $ratingsenabled,
                    'studentstarttopic' => $studentstarttopic,
                    'attachments' => $attachments,
                    'subscriptions' => $subscriptions,
                    'moderation' => $moderation,
                    'archivedate' => $archiveDate
                ));

                // Update dynamic block to display the latest post
                $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
                //if ($forum['forum_context'] == 'root') {
                $objDynamicBlocks->updateTitle('forum', 'dynamicblocks_latestpost', 'renderLatestPost', $forum_id, 'site', 'Latest Post for Forum: ' . $forum_name);
                //} else {
                //    $objDynamicBlocks->updateTitle('forum', 'dynamicblocks_latestpost', 'renderLatestPost', $forum_id, 'context', 'Latest Post for Forum: '.$forum_name);
                //}
                // Update dynamic block to display topic list
                //if ($forum['forum_context'] == 'root') {
                $objDynamicBlocks->updateTitle('forum', 'dynamicblocks_forumview', 'renderForum', $forum_id, 'site', 'Forum: ' . $forum_name);
                //} else {
                //    $objDynamicBlocks->updateTitle('forum', 'dynamicblocks_forumview', 'renderForum', $forum_id, 'context', 'Forum: '.$forum_name);
                //}
                // Add to Search
                $objIndexData = $this->getObject('indexdata', 'search');

                // Prep Data
                $docId = 'forum_entry_' . $forum_id;
                $docDate = strftime('%Y-%m-%d %H:%M:%S', mktime());
                $url = $this->uri(array('action' => 'forum', 'id' => $forum_id), 'forum');
                $title = $forum_name;
                $contents = $forum_name . ': ' . $forum_description;
                $teaser = $forum_description;
                $module = 'forum';
                $userId = $this->objUser->userId();
                @$context = $forum_context;

                // Add to Index
                $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, $context);
        }

        /**
         * Method to update the visibility of a forum
         *
         * @param string $forum_id Record Id of the Forum
         * @param string $forum_visbile Forum Visibility either Y or N
         *
         * @return Result of SQL Update
         */
        function updateForumVisibility($forum_id, $forum_visible) {
                $forum_visible = strtoupper($forum_visible);

                if ($forum_visible == 'Y' || $forum_visible == 'N') {
                        return $this->update('id', $forum_id, array('forum_visible' => $forum_visible));
                } else {
                        return FALSE;
                }
        }

        /**
         * Method to check if a forum is locked
         * @param string $forumId Record Id of the forum
         * @return boolean True if the forum is unlocked or false
         */
        function checkIfForumLocked($forumId) {
                $forum = $this->getForum($forumId);

                if (count($forum) > 0 && $forum['forumlocked'] == 'Y') {
                        return TRUE;
                } else {
                        return FALSE;
                }
        }

        /**
         * Method to update the last posted topic in a forum
         * @param string $forum_id Record Id of the forum
         * @param string $topic_id Record Id of the topic
         */
        function updateLastTopic($forum_id, $topic_id) {
                $forum = $this->getRow('id', $forum_id);

                $topicsNum = $forum['topics'] + 1;

                return $this->update('id', $forum_id, array('lasttopic' => $topic_id, 'topics' => $topicsNum));
        }

        /**
         * Method to updated the last posted post/reply in a forum
         * @param string $forum_id Record Id of the forum
         * @param string $post-id Record Id of the post
         */
        function updateLastPost($forum_id, $post_id) {
                $forum = $this->getRow('id', $forum_id);

                $postNum = $forum['post'] + 1;

                return $this->update('id', $forum_id, array('lastpost' => $post_id, 'post' => $postNum));
        }

        /**
         *
         *
         */
        function getWorkgroupForum($context, $workgroup) {
                $sql = 'SELECT id FROM tbl_forum WHERE forum_context= \'' . $context . '\'  AND forum_workgroup= \'' . $workgroup . '\'';

                $list = $this->getArray($sql);

                if (count($list) > 0) {
                        return $list[0]['id'];
                } else {
                        return NULL;
                }
        }

        /**
         * Method to get automatically create a forum in a Workgroup
         *
         * @param string $context: Context Code
         * @param string $title: Title to create the forum
         */
        function autoCreateWorkgroupForum($context, $workgroup, $title) {

                // confirm there is no forums in the workgroup
                if ($this->getWorkgroupForum($context, $workgroup) == NULL) {
                        $forum_context = $context;
                        $forum_workgroup = $workgroup;
                        $forum_name = $title . ' Forum';
                        $forum_description = $this->objLanguage->code2Txt('mod_forum_defaultdescription', 'forum');
                        $defaultForum = 'Y'; // Set to YES

                        $forum_visible = 'Y';
                        $enablePosts = 'Y';
                        $ratingsenabled = 'N';
                        $studentstarttopic = 'Y';
                        $attachments = 'Y';

                        $newForumId = $this->insertSingle($forum_context, $forum_workgroup, $forum_name, $forum_description, $defaultForum, $forum_visible, $enablePosts, $ratingsenabled, $studentstarttopic, $attachments);
                } else {
                        $newForumId = NULL;
                }
                return $newForumId;
        }

        /**
         * Method to delete a forum
         * @param string $id Record Id of the Forum
         * @return boolean Result of Delete
         */
        function deleteForum($id) {
                $forum = $this->getRow('id', $id);

                $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

                // Delete dynamic block for latest post
                if ($forum['forum_context'] == 'root') {
                        $objDynamicBlocks->removeBlock('forum', 'dynamicblocks_latestpost', 'renderLatestPost', $id, 'site');
                } else {
                        $objDynamicBlocks->removeBlock('forum', 'dynamicblocks_latestpost', 'renderLatestPost', $id, 'context');
                }

                // Delete dynamic block for topic list
                if ($forum['forum_context'] == 'root') {
                        $objDynamicBlocks->removeBlock('forum', 'dynamicblocks_forumview', 'renderForum', $id, 'site');
                } else {
                        $objDynamicBlocks->removeBlock('forum', 'dynamicblocks_forumview', 'renderForum', $id, 'context');
                }

                // Delete lucene search entry
                $objIndexData = $this->getObject('indexdata', 'search');
                $objIndexData->removeIndex('forum_entry_' . $id);

                return $this->delete('id', $id);
        }

        /**
         * Method to determine whether the current sort is in ascending or descending
         * @param string $orderParam Parameter to check for
         * @return string Direction for Parameter
         */
        function orderDirection($orderParam) {
                if ($this->order == $orderParam) {
                        if ($this->direction == 'desc') {
                                $direction = 'asc';
                        } else {
                                $direction = 'desc';
                        }
                        return $direction;
                }
        }

        /**
         * Method to create a URL to sort by column
         *
         * @param string $forum_id Record Id of the Forum
         * @sort string Column to sort by
         *
         * @return string Completed URI
         */
        function forumSortLink($forum_id, $sort, $textLink) {
                $this->loadClass('link', 'htmlelements');

                $icon = $this->getObject('geticon', 'htmlelements');

                $direction = $this->orderDirection($sort);
                $link = new link($this->uri(array('action' => 'forum', 'id' => $forum_id, 'order' => $sort, 'direction' => $direction)));

                $link->link = $textLink;
                $link->title = $this->objLanguage->languageText('sort_by', 'forum', 'Sort by') . ' ' . $textLink;

                if ($this->order == $sort) {
                        if ($direction == 'asc') {
                                $image = 'mvup';
                                $alt = $this->objLanguage->languageText('current_sort_descending', 'forum', 'Current Sort - Descending');
                        } else {
                                $image = 'mvdown';
                                $alt = $this->objLanguage->languageText('current_sort_ascending', 'forum', 'Current Sort - Ascending');
                        }

                        $icon->setIcon($image);
                        $icon->title = $alt;
                        $icon->alt = $alt;

                        return $link->show() . ' ' . $icon->show();
                } else {
                        return $link->show();
                }
        }

        /**
         * Method to update the last post and number of topics/posts in a forum after deleting a topic/post
         * @param string $forum_id Record Id of the Forum
         * @return boolean Result of Update
         */
        function updateForumAfterDelete($forum_id) {

                $lastPost = $this->getLastForumPost($forum_id);
                $numPosts = $this->getNumPostsInForum($forum_id);

                $lastPost = ($lastPost == FALSE) ? NULL : $lastPost;

                $lastTopic = $this->getLastForumTopic($forum_id);
                $numTopic = $this->getNumTopicsInForum($forum_id);

                $lastTopic = ($lastTopic == FALSE) ? NULL : $lastTopic;

                $this->update('id', $forum_id, array('lastpost' => $lastPost, 'post' => $numPosts, 'lasttopic' => $lastTopic, 'topics' => $numTopic));
        }

        /**
         * Method to get the number of posts in a forum
         * @param string $forumId Record Id of the Forum
         * @return int Number of Posts
         */
        function getNumPostsInForum($forumId) {
                $sql = 'SELECT tbl_forum_post.id FROM tbl_forum_post
        INNER JOIN tbl_forum_topic ON ( tbl_forum_post.topic_id = tbl_forum_topic.id)
        WHERE tbl_forum_topic.forum_id = "' . $forumId . '" ';

                $results = $this->getArray($sql);

                return count($results);
        }

        /**
         * Method to get the last post in a forum
         * @param string $forumId Record Id of the Forum
         * @return string Record Id of Last Post
         */
        function getLastForumPost($forumId) {
                $sql = 'SELECT tbl_forum_post.id FROM tbl_forum_post
        INNER JOIN tbl_forum_topic ON ( tbl_forum_post.topic_id = tbl_forum_topic.id)
        WHERE tbl_forum_topic.forum_id = "' . $forumId . '" ORDER BY tbl_forum_post.dateLastUpdated DESC LIMIT 1';

                $results = $this->getArray($sql);

                if (count($results) == 0) {
                        return FALSE;
                } else {
                        return $results[0]['id'];
                }
        }

        /**
         * Method to get the number of topics in a forum
         * @param string $forumId Record Id of the Forum
         * @return int Number of Topics
         */
        function getNumTopicsInForum($forumId) {
                $sql = 'SELECT tbl_forum_topic.id FROM tbl_forum_topic
        WHERE tbl_forum_topic.forum_id = "' . $forumId . '" ';

                $results = $this->getArray($sql);

                return count($results);
        }

        /**
         * Method to get the last topic in a forum
         * @param string $forumId Record Id of the Forum
         * @return string Record Id of Last Topic
         */
        function getLastForumTopic($forumId) {
                $sql = 'SELECT tbl_forum_topic.id FROM tbl_forum_topic
        WHERE tbl_forum_topic.forum_id = "' . $forumId . '" ORDER BY tbl_forum_topic.dateLastUpdated DESC LIMIT 1';

                $results = $this->getArray($sql);

                if (count($results) == 0) {
                        return FALSE;
                } else {
                        return $results[0]['id'];
                }
        }

        /**
         * Method to create dynamic blocks for forum topics
         * @param string $id Record Id of the topic
         * @param string $context Context
         * @param string $categoryName Name of the Category
         */
        private function createDynamicBlocksPost($id, $context, $forumName) {
                $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

                $title = 'Latest Post for Forum: ' . $forumName;

                if ($context == 'root') {
                        // Add Chapter Block
                        $result = $objDynamicBlocks->addBlock('forum', 'dynamicblocks_latestpost', 'renderLatestPost', $id, $title, 'site', NULL, 'small');
                } else {
                        // Add Chapter Block
                        $result = $objDynamicBlocks->addBlock('forum', 'dynamicblocks_latestpost', 'renderLatestPost', $id, $title, 'context', $context, 'small');
                }
        }

        /**
         * Method to create dynamic blocks for forum topics
         * @param string $id Record Id of the topic
         * @param string $context Context
         * @param string $categoryName Name of the Category
         */
        private function createDynamicBlocksView($id, $context, $forumName) {
                $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

                $title = 'Forum: ' . $forumName;

                if ($context == 'root') {
                        // Add Chapter Block
                        $result = $objDynamicBlocks->addBlock('forum', 'dynamicblocks_forumview', 'renderForum', $id, $title, 'site', NULL, 'wide');
                } else {
                        // Add Chapter Block
                        $result = $objDynamicBlocks->addBlock('forum', 'dynamicblocks_forumview', 'renderForum', $id, $title, 'context', $context, 'wide');
                }
        }

        /**
         * update forum lock status
         * 
         * @access public
         * @param string $forum_id forum ID
         * @param string $forum_setting the forum setting to be updated which are
         * forum_context, forum_workgroup, forum_type, forum_name, forum_description, forum_visible
         * forumlocked, ratingsenabled, studentstarttopic, attachments, subscriptions, moderation.
         * @param string $forum_status status the forum will be updated to
         * @return query results
         */
        function updateForum($forum_id,$forum_setting, $forum_status) {
//                if (!empty($forum_status) && !empty($forum_id) && !empty($forum_setting)) {
                        $fields = array(
                            $forum_setting => $forum_status
                        );
                        return $this->update('id', $forum_id, $fields);
//                }
        }
        

}

?>
