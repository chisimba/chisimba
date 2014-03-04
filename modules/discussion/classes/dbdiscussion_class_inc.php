<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

/**
 * Discussion Table
 * This class controls all functionality relating to the tbl_discussion table
 * @author Tohir Solomons
 * @copyright (c) 2004 University of the Western Cape
 * @package discussion
 * @version 1
 */
class dbdiscussion extends dbTable {

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
                parent::init('tbl_discussion');

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
         * Method to get number of discussions in a context
         *
         * @param string $context: Context Code
         * @return integer number of discussions
         */
        function getNumDiscussions($context = null) {
                $sql = '';
                if (isset($context)) {
                        $sql = 'WHERE discussion_context = \'' . $context . '\' AND discussion_type= \'context\' AND discussion_visible=\'Y\'';
                }

                return $this->getRecordCount($sql);
        }

        /**
         * Method to get list of discussions in a context
         *
         * @param string $context: Context Code
         * @return array list of discussions
         */
        function getContextDiscussions($context = null) {
                $sql = '';
                if (isset($context)) {
                        $sql = 'WHERE discussion_context = \'' . $context . '\' AND discussion_type= \'context\' AND discussion_visible=\'Y\'';
                }

                return $this->getAll($sql);
        }

        /**
         * Method to get list of all discussions in a context whether they are visible or not
         *
         * @param string $context: Context Code
         * @return array list of discussions
         */
        function getAllContextDiscussions($context = null) {
                $sql = '';
                if (isset($context)) {
                        $sql = 'WHERE discussion_context = \'' . $context . '\' AND discussion_type= \'context\'';
                }

                return $this->getAll($sql);
        }

        /**
         * Method to get automatically create a discussion in a context
         *
         * @param string $context: Context Code
         * @param string $title: Title to create the discussion
         */
        function autoCreateDiscussion($context, $title) {

                // confirm there is no discussions in the context
                if ($this->getNumDiscussions($context) == 0) {
                        $discussion_context = $context;
                        $discussion_workgroup = '';
                        $discussion_name = $title . ' Discussion';
                        $discussion_description = $this->objLanguage->code2Txt('mod_discussion_defaultdescription', 'discussion');
                        $enableDefaultDiscussion = 'Y'; // Set to YES

                        $newDiscussionId = $this->insertSingle($discussion_context, $discussion_workgroup, $discussion_name, $discussion_description, $enableDefaultDiscussion);
                } else {
                        $newDiscussionId = NULL;
                }
                return $newDiscussionId;
        }

        /**
         * This function provides a discussion by providing a context
         *  in the module, it will only be called if a discussion exists, so that isn't built in
         *
         * @param string $context: Context Code
         * @return array details of the discussion
         */
        function onlyDiscussion($context) {
                return $this->getRow('discussion_context', $context);
        }

        /**
         * This method gets the list of discussions in a context, and the amount of topics in the discussions
         *
         * @param string $context: Context Code
         * @return array list of discussions
         */
        function showAllDiscussions($context = null) {
                $sql = 'SELECT tbl_discussion.id AS discussion_id, tbl_discussion. *,
        count( DISTINCT topicCountLink.id ) AS topics, count( DISTINCT postCountLink.id ) AS posts
        FROM tbl_discussion LEFT JOIN tbl_discussion_topic AS topicCountLink ON ( topicCountLink.discussion_id = tbl_discussion.id )
        LEFT JOIN tbl_discussion_post AS postCountLink ON ( postCountLink.topic_id = topicCountLink.id )  ';

                $sql .= ' WHERE discussion_visible=\'Y\' ';

                if (isset($context)) {
                        $sql .= '  AND tbl_discussion.discussion_context = \'' . $context . '\' AND discussion_type= \'context\'';
                }

                $sql .= ' GROUP BY tbl_discussion.id';
                $sql .= ' ORDER BY defaultdiscussion, topicCountLink.id DESC  ';

                return $this->getArray($sql);
        }

        /**
         * This method gets a list of discussions in a context, other than the discussion specified.
         * This is used to jump between discussions in a context
         *
         * @param string $discussion_id: Record Id of the discussion
         * @param string $context: Context Code
         * @return array list of discussions
         */
        function otherDiscussions($discussion_id, $context) {
                $sql = 'SELECT tbl_discussion.id AS discussion_id, tbl_discussion.*, count(tbl_discussion_topic.id) AS topics from tbl_discussion_topic RIGHT JOIN tbl_discussion ON (tbl_discussion_topic.discussion_id = tbl_discussion.id) WHERE discussion_visible=\'Y\' AND discussion_type= \'context\' AND tbl_discussion.discussion_context = \'' . $context . '\' AND tbl_discussion.id != \'' . $discussion_id . '\'';

                $sql .= ' GROUP BY tbl_discussion.id';
                $sql .= ' ORDER BY defaultdiscussion, topics DESC  ';

                return $this->getArray($sql);
        }

        /**
         * This method gets the details of a single discussion by providing the discussion_id
         *
         * @param string $discussion_id: Record Id of the discussion
         * @return array list of discussions
         */
        function getDiscussion($discussion_id) {
                return $this->getRow('id', $discussion_id);
        }

        /**
         * This method gets discussion_id of the default discussion with in a context
         * This method also checks that a discussion exists, and creates one if none is present
         *
         * @return string Record Id of the default discussion
         */
        function getContextDiscussion() {
                $discussionNum = $this->getNumDiscussions($this->contextCode);

                if ($discussionNum == 0) {
                        $discussion = $this->autoCreateDiscussion($this->contextCode, $this->contextTitle);
                }
                $discussion = $this->getDefaultDiscussion($this->contextCode);

                return $discussion['id'];
        }

        /**
         * Insert a discussion into into the database
         *
         * @param string $discussion_context:      Context for which this discussion applies
         * @param string $discussion_workgroup:   Workgroup the discussion belongs to
         * @param string $discussion_name:          Name of the Discussion
         * @param string $discussion_description: Description of the discussion
         * @param string $discussion_visible:       Is the discussion visible-
         * @param string $ratingsenabled:      Can users rate posts in the discussion
         * @param string $studentstarttopic: Can students start topics in the discussion
         * @param string $attachments:         Can users upload attachments in the discussion
         * @param string $subscriptions:       Can users subscribe to topics in the discussion
         * @param string $moderation:          Can users moderate posts in the discussion // Under construction
         * @param string $defaultDiscussion:       Is this the default discussion
         */
        function insertSingle($discussion_context, $discussion_workgroup, $discussion_name, $discussion_description, $defaultDiscussion = 'N', $discussion_visible = 'Y', $enablePosts = 'Y', $ratingsenabled = 'N', $studentstarttopic = 'Y', $attachments = 'Y', $subscriptions = 'N', $moderation = 'Y', $discussionlocked = 'N') {
                $this->insert(array(
                    'discussion_context' => $discussion_context,
                    'discussion_workgroup' => $discussion_workgroup,
                    'discussion_name' => $discussion_name,
                    'discussion_description' => $discussion_description,
                    'discussion_visible' => $discussion_visible,
                    'defaultdiscussion' => $defaultDiscussion,
                    'ratingsenabled' => $ratingsenabled,
                    'studentstarttopic' => $studentstarttopic,
                    'attachments' => $attachments,
                    'subscriptions' => $subscriptions,
                    'moderation' => $moderation,
                    'discussionlocked' => $discussionlocked
                ));


                $newDiscussionId = $this->getLastInsertId();

                // Done to Avoid Null Values
                if ($discussion_workgroup == '') {
                        $this->update('id', $newDiscussionId, array('discussion_workgroup' => ''));
                }

                $userId = $this->objUser->userId();
                $objDiscussionDefaultRatings = & $this->getObject('dbdiscussion_default_ratings', 'discussion');
                $objDiscussionRatings = & $this->getObject('dbdiscussion_ratings', 'discussion');

                $defaultRatings = $objDiscussionDefaultRatings->getDefaultList();

                foreach ($defaultRatings as $rating) {
                        $objDiscussionRatings->insertSingle(
                                $newDiscussionId, $rating['rating_description'], $rating['rating_point'], $userId, mktime()
                        );
                }

                // Add Dynamic Block for latest post in discussion
                if ($newDiscussionId != FALSE) {
                        $this->createDynamicBlocksPost($newDiscussionId, $discussion_context, $discussion_name);
                }

                // Add Dynamic Block for the discussion view
                if ($newDiscussionId != FALSE) {
                        $this->createDynamicBlocksView($newDiscussionId, $discussion_context, $discussion_name);
                }

                // Add to Search
                $objIndexData = $this->getObject('indexdata', 'search');

                // Prep Data
                $docId = 'discussion_entry_' . $newDiscussionId;
                $docDate = strftime('%Y-%m-%d %H:%M:%S', mktime());
                $url = $this->uri(array('action' => 'discussion', 'id' => $newDiscussionId), 'discussion');
                $title = $discussion_name;
                $contents = $discussion_name . ': ' . $discussion_description;
                $teaser = $discussion_description;
                $module = 'discussion';
                $userId = $userId;
                $context = $discussion_context;

                // Add to Index
                $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, $context);

                return $newDiscussionId;
        }

        /**
         * This method gets discussion_id of the default discussion by providing a context
         *
         * @param string $context context code of the current context
         */
        function getDefaultDiscussion($context) {
                $sql = 'SELECT id FROM tbl_discussion WHERE discussion_context= \'' . $context . '\'  AND discussion_type= \'context\' AND defaultdiscussion=\'Y\'';

                $list = $this->getArray($sql);

                if (count($list) > 0) {
                        return $list[0];
                } else {
                        return;
                }
        }

        /**
         * This method sets a discussion as the default discussion for a context
         *
         * @param string $discussion record Id of the discussion
         * @param string $context Context Code
         */
        function setDefaultDiscussion($discussion, $context) {
                $this->update('discussion_context', $context, array(
                    'defaultdiscussion' => 'N'
                ));

                $this->update('id', $discussion, array(
                    'defaultdiscussion' => 'Y',
                    'discussion_visible' => 'Y'
                ));

                return;
        }

        /**
         * Update settings of a discussion
         *
         * @param string $discussion_id:      Context for which this discussion applies
         * @param string $discussion_name:          Name of the Discussion
         * @param string $discussion_description: Description of the discussion
         * @param string $discussion_visible:       Is the discussion visible-
         * @param string $ratingsenabled:      Can users rate posts in the discussion
         * @param string $studentstarttopic: Can students start topics in the discussion
         * @param string $attachments:         Can users upload attachments in the discussion
         * @param string $subscriptions:       Can users subscribe to topics in the discussion
         * @param string $moderation:          Can users moderate posts in the discussion // Under construction
         * @param string $archiveDate:        Date to start Archiving from // NULL if archiving is disabled
         */
        function updateSingle($discussion_id, $discussion_name, $discussion_description, $discussion_visible, $discussion_locked, $ratingsenabled, $studentstarttopic, $attachments, $subscriptions, $moderation, $archiveDate) {

                $this->update('id', $discussion_id, array(
                    'discussion_name' => $discussion_name,
                    'discussion_description' => $discussion_description,
                    'discussion_visible' => $discussion_visible,
                    'discussionlocked' => $discussion_locked,
                    'ratingsenabled' => $ratingsenabled,
                    'studentstarttopic' => $studentstarttopic,
                    'attachments' => $attachments,
                    'subscriptions' => $subscriptions,
                    'moderation' => $moderation,
                    'archivedate' => $archiveDate
                ));

                // Update dynamic block to display the latest post
                $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');
                //if ($discussion['discussion_context'] == 'root') {
                $objDynamicBlocks->updateTitle('discussion', 'dynamicblocks_latestpost', 'renderLatestPost', $discussion_id, 'site', 'Latest Post for Discussion: ' . $discussion_name);
                //} else {
                //    $objDynamicBlocks->updateTitle('discussion', 'dynamicblocks_latestpost', 'renderLatestPost', $discussion_id, 'context', 'Latest Post for Discussion: '.$discussion_name);
                //}
                // Update dynamic block to display topic list
                //if ($discussion['discussion_context'] == 'root') {
                $objDynamicBlocks->updateTitle('discussion', 'dynamicblocks_discussionview', 'renderDiscussion', $discussion_id, 'site', 'Discussion: ' . $discussion_name);
                //} else {
                //    $objDynamicBlocks->updateTitle('discussion', 'dynamicblocks_discussionview', 'renderDiscussion', $discussion_id, 'context', 'Discussion: '.$discussion_name);
                //}
                // Add to Search
                $objIndexData = $this->getObject('indexdata', 'search');

                // Prep Data
                $docId = 'discussion_entry_' . $discussion_id;
                $docDate = strftime('%Y-%m-%d %H:%M:%S', mktime());
                $url = $this->uri(array('action' => 'discussion', 'id' => $discussion_id), 'discussion');
                $title = $discussion_name;
                $contents = $discussion_name . ': ' . $discussion_description;
                $teaser = $discussion_description;
                $module = 'discussion';
                $userId = $this->objUser->userId();
                @$context = $discussion_context;

                // Add to Index
                $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, $context);
        }

        /**
         * Method to update the visibility of a discussion
         *
         * @param string $discussion_id Record Id of the Discussion
         * @param string $discussion_visbile Discussion Visibility either Y or N
         *
         * @return Result of SQL Update
         */
        function updateDiscussionVisibility($discussion_id, $discussion_visible) {
                $discussion_visible = strtoupper($discussion_visible);

                if ($discussion_visible == 'Y' || $discussion_visible == 'N') {
                        return $this->update('id', $discussion_id, array('discussion_visible' => $discussion_visible));
                } else {
                        return FALSE;
                }
        }

        /**
         * Method to check if a discussion is locked
         * @param string $discussionId Record Id of the discussion
         * @return boolean True if the discussion is unlocked or false
         */
        function checkIfDiscussionLocked($discussionId) {
                $discussion = $this->getDiscussion($discussionId);

                if (count($discussion) > 0 && $discussion['discussionlocked'] == 'Y') {
                        return TRUE;
                } else {
                        return FALSE;
                }
        }

        /**
         * Method to update the last posted topic in a discussion
         * @param string $discussion_id Record Id of the discussion
         * @param string $topic_id Record Id of the topic
         */
        function updateLastTopic($discussion_id, $topic_id) {
                $discussion = $this->getRow('id', $discussion_id);

                $topicsNum = $discussion['topics'] + 1;

                return $this->update('id', $discussion_id, array('lasttopic' => $topic_id, 'topics' => $topicsNum));
        }

        /**
         * Method to updated the last posted post/reply in a discussion
         * @param string $discussion_id Record Id of the discussion
         * @param string $post-id Record Id of the post
         */
        function updateLastPost($discussion_id, $post_id) {
                $discussion = $this->getRow('id', $discussion_id);

                $postNum = $discussion['post'] + 1;

                return $this->update('id', $discussion_id, array('lastpost' => $post_id, 'post' => $postNum));
        }

        /**
         *
         *
         */
        function getWorkgroupDiscussion($context, $workgroup) {
                $sql = 'SELECT id FROM tbl_discussion WHERE discussion_context= \'' . $context . '\'  AND discussion_workgroup= \'' . $workgroup . '\'';

                $list = $this->getArray($sql);

                if (count($list) > 0) {
                        return $list[0]['id'];
                } else {
                        return NULL;
                }
        }

        /**
         * Method to get automatically create a discussion in a Workgroup
         *
         * @param string $context: Context Code
         * @param string $title: Title to create the discussion
         */
        function autoCreateWorkgroupDiscussion($context, $workgroup, $title) {

                // confirm there is no discussions in the workgroup
                if ($this->getWorkgroupDiscussion($context, $workgroup) == NULL) {
                        $discussion_context = $context;
                        $discussion_workgroup = $workgroup;
                        $discussion_name = $title . ' Discussion';
                        $discussion_description = $this->objLanguage->code2Txt('mod_discussion_defaultdescription', 'discussion');
                        $defaultDiscussion = 'Y'; // Set to YES

                        $discussion_visible = 'Y';
                        $enablePosts = 'Y';
                        $ratingsenabled = 'N';
                        $studentstarttopic = 'Y';
                        $attachments = 'Y';

                        $newDiscussionId = $this->insertSingle($discussion_context, $discussion_workgroup, $discussion_name, $discussion_description, $defaultDiscussion, $discussion_visible, $enablePosts, $ratingsenabled, $studentstarttopic, $attachments);
                } else {
                        $newDiscussionId = NULL;
                }
                return $newDiscussionId;
        }

        /**
         * Method to delete a discussion
         * @param string $id Record Id of the Discussion
         * @return boolean Result of Delete
         */
        function deleteDiscussion($id) {
                $discussion = $this->getRow('id', $id);

                $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

                // Delete dynamic block for latest post
                if ($discussion['discussion_context'] == 'root') {
                        $objDynamicBlocks->removeBlock('discussion', 'dynamicblocks_latestpost', 'renderLatestPost', $id, 'site');
                } else {
                        $objDynamicBlocks->removeBlock('discussion', 'dynamicblocks_latestpost', 'renderLatestPost', $id, 'context');
                }

                // Delete dynamic block for topic list
                if ($discussion['discussion_context'] == 'root') {
                        $objDynamicBlocks->removeBlock('discussion', 'dynamicblocks_discussionview', 'renderDiscussion', $id, 'site');
                } else {
                        $objDynamicBlocks->removeBlock('discussion', 'dynamicblocks_discussionview', 'renderDiscussion', $id, 'context');
                }

                // Delete lucene search entry
                $objIndexData = $this->getObject('indexdata', 'search');
                $objIndexData->removeIndex('discussion_entry_' . $id);

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
         * @param string $discussion_id Record Id of the Discussion
         * @sort string Column to sort by
         *
         * @return string Completed URI
         */
        function discussionSortLink($discussion_id, $sort, $textLink) {
                $this->loadClass('link', 'htmlelements');

                $icon = $this->getObject('geticon', 'htmlelements');

                $direction = $this->orderDirection($sort);
                $link = new link($this->uri(array('action' => 'discussion', 'id' => $discussion_id, 'order' => $sort, 'direction' => $direction)));

                $link->link = $textLink;
                $link->title = $this->objLanguage->languageText('sort_by', 'discussion', 'Sort by') . ' ' . $textLink;

                if ($this->order == $sort) {
                        if ($direction == 'asc') {
                                $image = 'mvup';
                                $alt = $this->objLanguage->languageText('current_sort_descending', 'discussion', 'Current Sort - Descending');
                        } else {
                                $image = 'mvdown';
                                $alt = $this->objLanguage->languageText('current_sort_ascending', 'discussion', 'Current Sort - Ascending');
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
         * Method to update the last post and number of topics/posts in a discussion after deleting a topic/post
         * @param string $discussion_id Record Id of the Discussion
         * @return boolean Result of Update
         */
        function updateDiscussionAfterDelete($discussion_id) {

                $lastPost = $this->getLastDiscussionPost($discussion_id);
                $numPosts = $this->getNumPostsInDiscussion($discussion_id);

                $lastPost = ($lastPost == FALSE) ? NULL : $lastPost;

                $lastTopic = $this->getLastDiscussionTopic($discussion_id);
                $numTopic = $this->getNumTopicsInDiscussion($discussion_id);

                $lastTopic = ($lastTopic == FALSE) ? NULL : $lastTopic;

                $this->update('id', $discussion_id, array('lastpost' => $lastPost, 'post' => $numPosts, 'lasttopic' => $lastTopic, 'topics' => $numTopic));
        }

        /**
         * Method to get the number of posts in a discussion
         * @param string $discussionId Record Id of the Discussion
         * @return int Number of Posts
         */
        function getNumPostsInDiscussion($discussionId) {
                $sql = 'SELECT tbl_discussion_post.id FROM tbl_discussion_post
        INNER JOIN tbl_discussion_topic ON ( tbl_discussion_post.topic_id = tbl_discussion_topic.id)
        WHERE tbl_discussion_topic.discussion_id = "' . $discussionId . '" ';

                $results = $this->getArray($sql);

                return count($results);
        }

        /**
         * Method to get the last post in a discussion
         * @param string $discussionId Record Id of the Discussion
         * @return string Record Id of Last Post
         */
        function getLastDiscussionPost($discussionId) {
                $sql = 'SELECT tbl_discussion_post.id FROM tbl_discussion_post
        INNER JOIN tbl_discussion_topic ON ( tbl_discussion_post.topic_id = tbl_discussion_topic.id)
        WHERE tbl_discussion_topic.discussion_id = "' . $discussionId . '" ORDER BY tbl_discussion_post.dateLastUpdated DESC LIMIT 1';

                $results = $this->getArray($sql);

                if (count($results) == 0) {
                        return FALSE;
                } else {
                        return $results[0]['id'];
                }
        }

        /**
         * Method to get the number of topics in a discussion
         * @param string $discussionId Record Id of the Discussion
         * @return int Number of Topics
         */
        function getNumTopicsInDiscussion($discussionId) {
                $sql = 'SELECT tbl_discussion_topic.id FROM tbl_discussion_topic
        WHERE tbl_discussion_topic.discussion_id = "' . $discussionId . '" ';

                $results = $this->getArray($sql);

                return count($results);
        }

        /**
         * Method to get the last topic in a discussion
         * @param string $discussionId Record Id of the Discussion
         * @return string Record Id of Last Topic
         */
        function getLastDiscussionTopic($discussionId) {
                $sql = 'SELECT tbl_discussion_topic.id FROM tbl_discussion_topic
        WHERE tbl_discussion_topic.discussion_id = "' . $discussionId . '" ORDER BY tbl_discussion_topic.dateLastUpdated DESC LIMIT 1';

                $results = $this->getArray($sql);

                if (count($results) == 0) {
                        return FALSE;
                } else {
                        return $results[0]['id'];
                }
        }

        /**
         * Method to create dynamic blocks for discussion topics
         * @param string $id Record Id of the topic
         * @param string $context Context
         * @param string $categoryName Name of the Category
         */
        private function createDynamicBlocksPost($id, $context, $discussionName) {
                $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

                $title = 'Latest Post for Discussion: ' . $discussionName;

                if ($context == 'root') {
                        // Add Chapter Block
                        $result = $objDynamicBlocks->addBlock('discussion', 'dynamicblocks_latestpost', 'renderLatestPost', $id, $title, 'site', NULL, 'small');
                } else {
                        // Add Chapter Block
                        $result = $objDynamicBlocks->addBlock('discussion', 'dynamicblocks_latestpost', 'renderLatestPost', $id, $title, 'context', $context, 'small');
                }
        }

        /**
         * Method to create dynamic blocks for discussion topics
         * @param string $id Record Id of the topic
         * @param string $context Context
         * @param string $categoryName Name of the Category
         */
        private function createDynamicBlocksView($id, $context, $discussionName) {
                $objDynamicBlocks = $this->getObject('dynamicblocks', 'blocks');

                $title = 'Discussion: ' . $discussionName;

                if ($context == 'root') {
                        // Add Chapter Block
                        $result = $objDynamicBlocks->addBlock('discussion', 'dynamicblocks_discussionview', 'renderDiscussion', $id, $title, 'site', NULL, 'wide');
                } else {
                        // Add Chapter Block
                        $result = $objDynamicBlocks->addBlock('discussion', 'dynamicblocks_discussionview', 'renderDiscussion', $id, $title, 'context', $context, 'wide');
                }
        }

        /**
         * update discussion lock status
         * 
         * @access public
         * @param string $discussion_id discussion ID
         * @param string $discussion_setting the discussion setting to be updated which are
         * discussion_context, discussion_workgroup, discussion_type, discussion_name, discussion_description, discussion_visible
         * discussionlocked, ratingsenabled, studentstarttopic, attachments, subscriptions, moderation.
         * @param string $discussion_status status the discussion will be updated to
         * @return query results
         */
        function updateDiscussion($discussion_id,$discussion_setting, $discussion_status) {
//                if (!empty($discussion_status) && !empty($discussion_id) && !empty($discussion_setting)) {
                        $fields = array(
                            $discussion_setting => $discussion_status
                        );
                        return $this->update('id', $discussion_id, $fields);
//                }
        }
        

}

?>
