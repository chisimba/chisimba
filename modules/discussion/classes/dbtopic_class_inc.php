<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Discussion Topics Table
 * This class controls all functionality relating to the tbl_discussion_topic table
 * @author Tohir Solomons
 * @copyright (c) 2004 University of the Western Cape
 * @package discussion
 * @version 1
 */
/**
 * This class controls the functionality for topics and tangents in a discussion discussion
 */
class dbtopic extends dbTable {

    /**
     * Constructor method to define the table(default)
     */
    function init() {
        parent::init('tbl_discussion_topic');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->objPost =& $this->getObject('dbpost');
        $this->objDiscussion = & $this->getObject('dbDiscussion');

        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        $this->objDateTime =& $this->getObject('dateandtime', 'utilities');
        // Get Context Code Settings
        $this->contextObject =& $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->contextObject->getContextCode();
    }

    /**
     * Method to retrieve a single Record
     *
     * @param string $id: ID of the Topic
     * @return array Details of the topic
     */
    function listSingle($id) {
        return $this->getRow('id', $id);
    }


    /**
     * Insert a topic into the database
     *
     * @param string $discussion_id: Record ID of the Discussion post is being made into
     * @param string $type_id: Type of topic
     * @param string $first_post: Record Id of the first post
     * @param string $topic_tangent_parent: Record Id of tangent parent
     * @param string $userId: User ID of person starting the topic
     * @param string $dateLastUpdated: Date topic was started
     * @param string $id Id - Optional, used by API
     */
    function insertSingle($discussion_id, $type_id, $topic_tangent_parent, $userID, $postTitle=NULL, $id=NULL) {
        if ($topic_tangent_parent == 0) {
            $lastRightPointer = $this->getLastRightPointer($discussion_id);
            $leftPointer = $lastRightPointer+1;
            $rightPointer = $lastRightPointer+2;
            $level = 1;
        }
        // provide support for tangents

        $this->insert(array(
                'id'              => $id,
                'discussion_id'        => $discussion_id,
                'type_id'         => $type_id,
                'views'           => 0,
                'replies'         => 0,
                'topic_tangent_parent'   => $topic_tangent_parent,
                'lft'                => $leftPointer,
                'rght'                => $rightPointer,
                'userId'          => $userID,
                'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
        ));

        return $this->getLastInsertId();
    }

    /**
     * Insert a topic into the lucene search
     *
     * @param string $topicId: Record ID of the topic post is being made into
     * @param string $postTitle: Title of the topic
     * @param string $postContent: Content of the topic post
     * @param string $userId: User ID of person starting the topic
     * @param string $discussionId: The id of the discussion which the topic is posted in
     */
    function insertTopicSearch($topicId, $postTitle, $postContent, $userId, $discussionId) {
        $discussion = $this->objDiscussion->getRow('id', $discussionId);
        // Add to Search
        $objIndexData = $this->getObject('indexdata', 'search');

        // Prep Data
        $docId = 'discussion_topic_'.$topicId;
        $docDate = strftime('%Y-%m-%d %H:%M:%S', mktime());
        $url = $this->uri(array('action'=>'viewtopic', 'id'=>$topicId), 'discussion');
        $title = $postTitle;
        $contents = $discussion['discussion_name'].': '.$postTitle;
        $teaser = $postContent;
        $module = 'discussion';
        $userId = $userId;
        $context = $discussion['discussion_context'];

        // Add to Index
        $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, $context);
    }

    /**
     * When starting a new post, u need to know the id of topic, to set for the post, and id of the post for the topic
     *
     * This function is called after topic has been created, then post, and then updates topic
     *
     * @param string $topic_id: Record ID of the Topic
     * @param string $post_id: Record ID of the Post
     */
    function updateFirstPost($topic_id, $post_id) {
        $this->update('id', $topic_id, array(
                'first_post'    => $post_id,
                'last_post'    => $post_id
        ));

        return;
    }

    /**
     * Everytime there is a reply to a topic, this function updates the topic to the last post it
     *
     * @param string $topic_id: Record ID of the Topic
     * @param string $post_id: Record ID of the Post
     *
     * @return array Details of the topic
     */
    function updateLastPost($topic_id, $post_id) {
        // Get topic to get number of replies thus far
        $record = $this->listSingle($topic_id);

        $this->update('id', $topic_id, array(
                'last_post'     => $post_id,
                'replies'       => $record['replies'] + 1
        ));

        return;
    }


    // can't be used by tangents
    /**
     * Show topics in discussion, type of topic, first post, last post, by providing the discussion_id
     *
     * @param string $discussion_id: Record ID of the Discussion
     * @param string $userId: User Id to check whether the discussion has been read
     * @param date $archiveDate: Date to get posts from
     * @param string $order: Column to Order by
     * @param string $direction: Direction to order by - either asc or desc
     * @param string $additionalWhere: Any Additional SQL that needs to go into the WHERE SECTION
     * @param string $limit: Limit clause for the SQL
     *
     * @return array Details of the topics
     */
    function showTopicsInDiscussion($discussion_id, $userId, $archiveDate = NULL, $order=NULL, $direction=NULL, $additionalWhere = NULL, $limit = NULL) {
        $sql = 'SELECT tbl_discussion_topic.id AS topic_id,tbl_discussion_topic.*, tbl_discussion_topic.status AS topicstatus, tbl_users.firstname, tbl_users.surname, tbl_users.username, tbl_discussion_discussiontype.*, tbl_discussion_post_text.post_title, tbl_discussion_topic_read.id AS readtopic, tbl_discussion_topic_read.post_id AS lastreadpost, lastPostUser.firstname AS lastfirstname, lastPostUser.surname AS lastsurname, lastPostUser.username AS lastusername, post2.datelastupdated AS lastdate, tangentCheck.id AS tangentcheck'

                .' FROM tbl_discussion_topic'

                // Inner Joing Post to get the details
                .' INNER JOIN tbl_discussion_post ON (tbl_discussion_topic.first_post = tbl_discussion_post.id AND tbl_discussion_post.post_parent=0)'
                // Get User who started the topic
                .' LEFT  JOIN tbl_users ON ( tbl_discussion_topic.userId = tbl_users.userId ) '
                // Get the type of topic
                .' INNER JOIN tbl_discussion_discussiontype ON (tbl_discussion_topic.type_id = tbl_discussion_discussiontype.id)'
                // Get the title of the topic
                .' INNER JOIN tbl_discussion_post_text ON (tbl_discussion_post.id = tbl_discussion_post_text.post_id AND tbl_discussion_post_text.original_post = \'1\')'
                // Check if the user has read this topic alreadt
                .' LEFT JOIN tbl_discussion_topic_read ON (tbl_discussion_topic.id = tbl_discussion_topic_read.topic_id AND tbl_discussion_topic_read.userId = \''.$userId.'\') '
                // Check if this topic has a tangent
                .' LEFT  JOIN tbl_discussion_topic as tangentCheck ON ( tangentCheck.topic_tangent_parent = tbl_discussion_topic.id ) '
                // Get details of the last post
                .' INNER JOIN tbl_discussion_post AS post2 ON (tbl_discussion_topic.last_post = post2.id)'
                // Get user who did the last post
                .' LEFT  JOIN tbl_users as lastPostUser ON ( post2.userId = lastPostUser.userId ) '
                // Restrict to current discussion and topics that aren't tangents
                .' WHERE tbl_discussion_topic.discussion_id=\''.$discussion_id.'\' AND tbl_discussion_topic.topic_tangent_parent = \'0\'  ';
        //OR tbl_discussion_topic.topic_tangent_parent = 0)
        if ($archiveDate != NULL) {
            $sql .= ' AND tbl_discussion_topic.dateLastUpdated > \''.$archiveDate.' 00:00:00\'';
        }

        $sql .= $additionalWhere;

        // Return single rows
        $sql .= ' GROUP  BY tbl_discussion_topic.id';

        if (strtolower($direction) == 'asc') {
            $direction = NULL;
        } else {
            $direction = ' DESC ';
        }

        // Order By
        $sql .= ' ORDER BY sticky DESC, ';

        // Determine the ordering
        switch ($order) {
            case 'author': $sql .= ' firstName '.$direction;
                break;
            case 'lastpost': $sql .= ' lastdate '.$direction;
                break;
            case 'read': $sql .= ' readtopic '.$direction;
                break;
            case 'replies': $sql .= ' replies '.$direction;
                break;
            case 'status': $sql .= ' topicstatus '.$direction;
                break;
            case 'topic': $sql .= ' post_title '.$direction;
                break;
            case 'type': $sql .= ' type_name '.$direction;
                break;
            case 'views': $sql .= ' tbl_discussion_topic.views '.$direction;
                break;
            default :  $sql .= ' tbl_discussion_topic.dateLastUpdated DESC';
                break;
        }

        $sql .= $limit;
        
        return $this->getArray($sql);
    }

    /**
     * Method to get the last right pointer for a topic in discussion. Usually called when inserting a new topic
     * @param string $discussion Record Id of the Discussion
     * @return int Right Pointer Value
     */
    function getLastRightPointer($discussion) {
        $sql = 'SELECT tbl_discussion_topic.rght FROM tbl_discussion_topic WHERE tbl_discussion_topic.discussion_id = \''.$discussion.'\'  ORDER BY rght DESC LIMIT 1';

        $list =$this->getArray($sql);

        if (count($list) == 0) {
            return 0;
        } else {
            return $list[0]['rght'];
        }
    }

    /**
     * Method to get the list of tangents for a topic
     * @param string $topic Record Id of the topic
     * @return array List of tangents
     */
    function getTangents($topic) {
        $sql = 'SELECT tbl_discussion_topic. * , tbl_discussion_post_text.post_title, tbl_users.firstname, tbl_users.surname,tbl_users.username,lastPostUser.firstName AS lastFirstName, lastPostUser.surname AS lastSurname, lastPostUser.username AS lastusername, post2.dateLastUpdated AS lastdate
                FROM tbl_discussion_topic
                INNER JOIN tbl_discussion_post ON ( tbl_discussion_post.topic_id = tbl_discussion_topic.id AND tbl_discussion_post.post_parent="0")
                INNER JOIN tbl_discussion_post_text ON ( tbl_discussion_post_text.post_id = tbl_discussion_post.id )
                INNER JOIN tbl_discussion_post AS post2 ON (tbl_discussion_topic.last_post = post2.id)
                LEFT JOIN tbl_users ON ( tbl_discussion_topic.userId = tbl_users.userId )
                LEFT  JOIN tbl_users as lastPostUser ON ( post2.userId = lastPostUser.userId )
                WHERE tbl_discussion_topic.topic_tangent_parent = \''.$topic.'\'
GROUP BY tbl_discussion_topic.id                ';
        return $this->getArray($sql);
    }

    /**
     *
     *
     *
     */
    function showTangentsTable($topic) {
        $tangents = $this->getTangents($topic);

        if (count($tangents) == 0) {
            return NULL;
        } else {

            $this->loadClass('link', 'htmlelements');
            $this->loadClass('htmlheading', 'htmlelements');

            $header = new htmlheading();
            $header->type=3;
            $header->str = 'Tangents';

            $table = $this->newObject('htmltable', 'htmlelements');
            $table->cellpadding = 5;
            $table->cellspacing = 1;
            $table->startHeaderRow();
            $table->addHeaderCell($this->objLanguage->languageText('mod_discussion_topicconversation', 'discussion'));
            $table->addHeaderCell($this->objLanguage->languageText('word_author'), NULL, NULL, 'center');
            $table->addHeaderCell($this->objLanguage->languageText('word_replies', 'system'), NULL, NULL, 'center');
            $table->addHeaderCell($this->objLanguage->languageText('word_views', 'system'), NULL, NULL, 'center');
            $table->addHeaderCell($this->objLanguage->languageText('mod_discussion_lastpost', 'discussion'), NULL, NULL, 'center');
            $table->endHeaderRow();

            $row = 'odd';
            foreach ($tangents AS $tangent) {
                $table->startRow();

                $titleLink = new link($this->uri(array('action'=>'viewtopic', 'id'=>$tangent['id'])));
                $titleLink->link = $tangent['post_title'];

                $table->addCell($titleLink->show(), NULL, NULL, NULL, $row);
                $table->addCell($tangent['firstname'].' '.$tangent['surname'], NULL, NULL, 'center', $row);
                $table->addCell($tangent['replies'], NULL, NULL, 'center', $row);
                $table->addCell($tangent['views'], NULL, NULL, 'center', $row);

                $objIcon = $this->getObject('geticon', 'htmlelements');
                $objIcon->setIcon('gotopost', NULL, 'icons/discussion/');

                $lastPostLink = new link ($this->uri(array('action'=>'viewtopic', 'id'=>$tangent['id'], 'post'=>$tangent['last_post'])));
                $lastPostLink->link = $objIcon->show();

                if ($this->objDateTime->formatDateOnly($tangent['lastdate']) == date('j F Y')) {
                    $datefield = $this->objLanguage->languageText('mod_discussion_todayat', 'discussion').' '.$this->objDateTime->formatTime($tangent['lastdate']);
                } else {
                    $datefield = $this->objDateTime->formatDateOnly($tangent['lastdate']).' - '.$this->objDateTime->formatTime($tangent['lastdate']);
                }

                $table->addCell($datefield.'<br />'.$tangent['lastfirstname'].' '.$tangent['lastsurname'].$lastPostLink->show(), Null, 'center', 'right', $row.' smallText');

                $table->endRow();

                $row = $row=='odd' ? 'even' : 'odd';
            }

            return $header->show().$table->show();
        }
    }

    /**
     * Method to display the form that allows users to switch between views. A drop down is shown
     * @param string $topic_id Record Id of the topic
     * @param string $defaultSelected Which view should be set as the default selected in the drop down
     * @return string Form with list of views in a drop down
     */
    function showChangeDisplayTypeForm($topic_id, $defaultSelected = NULL) {
        // Start String
        $string = '<p><strong>Display Modes</strong>: ';

        // Flat View
        if ($defaultSelected == 'flatview') {
            $this->objIcon->setIcon('flat_view', NULL, 'icons/discussion/');
            $string .= $this->objIcon->show().' '.'Flat View';
        } else {
            $this->objIcon->setIcon('flat_view_disabled', NULL, 'icons/discussion/');
            $link = new link ($this->uri(array('action'=>'flatview', 'id'=>$topic_id)));
            $link->link = 'Flat View';
            $string .= $this->objIcon->show().' '.$link->show();
        }

        // Spacer
        $string .= ' &nbsp; ';

        // Single View
        if ($defaultSelected == 'singlethreadview') {
            $this->objIcon->setIcon('single_view', NULL, 'icons/discussion/');
            $string .= $this->objIcon->show().' '.'Single View';
        } else {
            $this->objIcon->setIcon('single_view_disabled', NULL, 'icons/discussion/');
            $link = new link ($this->uri(array('action'=>'singlethreadview', 'id'=>$topic_id)));
            $link->link = 'Single View';
            $string .= $this->objIcon->show().' '.$link->show();
        }

        // Spacer
        $string .= ' &nbsp; ';

        // Threaded View
        if ($defaultSelected == 'thread') {
            $this->objIcon->setIcon('threaded_view', NULL, 'icons/discussion/');
            $string .= $this->objIcon->show().' '.'Threaded View';
        } else {
            $this->objIcon->setIcon('threaded_view_disabled', NULL, 'icons/discussion/');
            $link = new link ($this->uri(array('action'=>'thread', 'id'=>$topic_id)));
            $link->link = 'Threaded View';
            $string .= $this->objIcon->show().' '.$link->show();
        }

        // Spacer
        $string .= ' &nbsp; ';

        // Freemind View
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the mathml module is registered and set params
        $isRegistered = $objModule->checkIfRegistered('mindmap');
        // Freemind View
        if($isRegistered) {
            if ($defaultSelected == 'viewtopicmindmap') {
                $this->objIcon->setIcon('mindmap_view', NULL, 'icons/discussion/');
                $string .= $this->objIcon->show().' '.'Mind Map View';
            } else {
                $this->objIcon->setIcon('mindmap_view_disabled', NULL, 'icons/discussion/');
                $link = new link ($this->uri(array('action'=>'viewtopicmindmap', 'id'=>$topic_id)));
                $link->link = 'Mind Map View';
                $string .= $this->objIcon->show().' '.$link->show();
            }
        }
        // Return String
        return $string.'</p>';
    }

    /**
     * Method to increment the views column each time a topic is viewed
     * @param string $topic Record Id of The Topic
     */
    function updateTopicViews($topic_id) {
        $topicDetails = $this->getRow('id', $topic_id);

        $viewNum = $topicDetails['views'] + 1;

        return $this->update('id', $topic_id, array('views'=>$viewNum));
    }

    /**
     * Method to get information about a topic
     * @param string $topic_id Record Id of the topic
     * @return array Details of the topic
     */
    function getTopicDetails($topic_id) {
        $sql = 'SELECT tbl_discussion_topic.*, tbl_discussion_discussiontype.*, tbl_discussion_topic.id AS topic_id, tbl_discussion_topic.status AS topicstatus, post_title FROM tbl_discussion_topic INNER JOIN tbl_discussion_post_text ON ( tbl_discussion_topic.first_post = tbl_discussion_post_text.post_id AND tbl_discussion_post_text.original_post = "1" ) INNER JOIN tbl_discussion_discussiontype ON (tbl_discussion_topic.type_id = tbl_discussion_discussiontype.id) WHERE tbl_discussion_topic.id = \''.$topic_id.'\' GROUP BY tbl_discussion_topic.id LIMIT 1';

        $topic = $this->getArray($sql);

        if (count($topic) == 1) {
            return $topic[0];
        } else {
            return FALSE;
        }
    }

    /**
     * Method to get the discussion details of a topic
     * @param string $topic_id Record Id of the topic
     * @return array Details of the discussion
     */
    function getTopicDiscussionDetails($topic_id) {
        $sql = 'SELECT tbl_discussion.* FROM tbl_discussion_topic
        INNER JOIN tbl_discussion ON ( tbl_discussion_topic.discussion_id = tbl_discussion.id)
        WHERE tbl_discussion_topic.id = \''.$topic_id.'\' GROUP BY tbl_discussion_topic.id LIMIT 1';

        $discussion = $this->getArray($sql);

        if (count($discussion) == 1) {
            return $discussion[0];
        } else {
            return FALSE;
        }
    }

    /**
     * Method to change a topic status between locked and unlocked
     * @param string $topic_id Record Id of the topic
     * @param string $status Either OPEN or CLOSE enum
     * @param string $reason Reason for making the change
     * @param string $userId Record if of the user making the change
     */
    function changeTopicStatus($topic_id, $status, $reason, $userId) {
        return $this->update('id', $topic_id,
                array('status' => $status,
                'lockReason'=> $reason,
                'lockuser'=>$userId,
                'lockdate'=>strftime('%Y-%m-%d %H:%M:%S', mktime())
        ));
    }

    /**
     * Method to delete a topic
     * @param string $id Record Id of the Topic
     */
    function deleteTopic($id) {
        $topic = $this->getRow('id', $id);
        //$topic = $this->listSingle($id);
        // To do: rearrange left and right values

        // Delete lucene search entry
        $objIndexData = $this->getObject('indexdata', 'search');
        $objIndexData->removeIndex('discussion_topic_'.$id);

        return $this->delete('id', $id);
    }

    /**
     * Method to delete tangents of a topic
     * @param string $id Record Id of the Topic
     */
    function deleteTangents($id) {
        //$topic = $this->listSingle($id);
        // To do: rearrange left and right values

        if ($id != '0') { // Prevent Deleting with value 0 - this will delete all topics - need to use above method
            return $this->delete('topic_tangent_parent', $id);
        } else {
            return FALSE;
        }
    }

    /**
     * Method to move Tangents from one Topic to another
     *
     * @param string $originalTopic Original Topic holding the Topic
     * @param string $newTopic New Topic tangents should go under;
     */
    function moveTangentsToAnotherTopic($originalTopic, $newTopic) {
        $this->beginTransaction();
        $this->update('topic_tangent_parent', $originalTopic, array('topic_tangent_parent'=>$newTopic));
        $this->objPost->movePostTangent($originalTopic, $newTopic);
        $this->commitTransaction();

        return;
    }

    /**
     * Method to Convert a Topic into a Tangent
     *
     * @param string $topic Record Id of the Topic
     */
    function moveTangentToRootTopic($topic) {
        $this->update('id', $topic, array('topic_tangent_parent'=>'0'));

        $this->beginTransaction();
        $this->update('id', $topic, array('topic_tangent_parent'=>'0'));
        $this->objPost->removeTangentParent($topic);
        $this->commitTransaction();

        return;
    }

    /**
     * Method to change All Tangents of a specified Topic into Topics
     *
     * @param string $topic Record Id of the Topic
     */
    function moveAllTangentsToRootTopic($topic) {
        $tangents = $this->getAll(' WHERE topic_tangent_parent=\''.$topic.'\'');

        $this->beginTransaction();
        $this->update('topic_tangent_parent', $topic, array('topic_tangent_parent'=>'0'));

        foreach ($tangents as $tangent) {
            $this->objPost->removeTangentParent($tangent['id']);
        }

        $this->commitTransaction();

        return;
    }

    /**
     * Method to Convert a Topic into a Tangent
     *
     * @param string $topic Record Id of the Topic
     * @param string $topicRootTangent Topic underwhich to place the tangent
     */
    function moveTopicToTangent($topic, $topicRootTangent) {

        $this->beginTransaction();
        $this->update('id', $topic, array('topic_tangent_parent'=>$topicRootTangent));
        $this->objPost->movePostTangent($topic, $topicRootTangent);
        $this->commitTransaction();

        return;
    }

    /**
     * Function to Make a Topic Sticky
     * @param string $id Record Id of the topic
     */
    function makeTopicSticky($id) {
        return $this->update('id', $id, array('sticky'=>'1'));
    }

    /**
     * Function to remove the stickiness of a topic
     * @param string $id Record Id of the topic
     */
    function removeTopicSticky($id) {
        return $this->update('id', $id, array('sticky'=>'0'));
    }

    /**
     * Function to get the number of topics in a discussion
     * @param string $discussion_id Record Id of the Discussion
     * @param boolean $includeTangents Flag whether to include tangents in count or not
     * @return int Number of Topics in that discussion
     */
    function getNumTopicsInDiscussion($discussion_id, $includeTangents=TRUE) {
        $sql = ' WHERE discussion_id=\''.$discussion_id.'\'';

        if (!$includeTangents) {
            $sql .= ' AND topic_tangent_parent=\'0\' ';
        }

        return $this->getRecordCount($sql);
    }

    /**
     * Function to get the number of topics in a discussion
     * @param string $discussion_id Record Id of the Discussion
     * @param int $page Current Page
     * @param int $limit Number of Records per Page
     * @return string Paging
     */
    function prepareTopicPagingLinks($discussion_id, $page=1, $limit) {
        $numPages = $this->getNumDiscussionPages($discussion_id, $limit, FALSE);

        if ($numPages == 1) {
            $paging = '';
        } else {

            $paging = '<p align="right">Page '.$page.' of '.$numPages.'</u>: &nbsp; ';

            for ($i=1; $i <= $numPages; $i++) {
                if ($i == $page) {

                    // if ($numPages < 5) {
                    // $paging .= ' <span class="confirm"><strong> Page'.$i.'</strong></span> &nbsp; ';
                    // } else {
                    $paging .= ' <span class="confirm"><strong>'.$i.'</strong></span> &nbsp; ';
                    //}
                } else {
                    $link = new link($this->uri(array('action'=>'discussion', 'id'=>$discussion_id, 'page'=>$i)));

                    // if ($numPages < 5) {
                    // $link->link = 'Page '.$i;
                    // } else {
                    $link->link = $i;
                    //}

                    $paging .= ' '.$link->show().' &nbsp; ';
                }
            }

            $paging .= '&nbsp; </p>';
        }

        return $paging;
    }

    /**
     * Function to gets the number of pages a discussion has depending on the amount of topics a page has
     *
     * It has two functions:
     * 1) To calculate the number of pages for paging
     * 2) To determine if there are hacking via URL
     *
     * @param string $discussion_id Record Id of the Discussion
     * @param int $limit Number of Records per Page
     * @param boolean $includeTangents Flag whether to include tangents in count or not
     * @return int Number of Pages
     */
    function getNumDiscussionPages($discussion_id, $limit, $includeTangents=TRUE) {
        $totalTopics = $this->getNumTopicsInDiscussion($discussion_id, $includeTangents);

        // Remove Remainder, and divider
        // Check to prevent blank values causing a PHP fatal error
        if (is_numeric($totalTopics) && is_numeric($limit)) {
            $numPages = ($totalTopics - ($totalTopics % $limit)) / $limit;
        } else {
          return 0;
        }

        // If there is a remainder, add another page
        if ($totalTopics % $limit > 0) {
            $numPages++;
        }

        return $numPages;
    }

    /**
     * Method to move a topic to another discussion
     * @param string $id Record Id of the Topic
     * @param string $discussion Record Id of the new discussion
     * @return boolean result of move
     */
    function switchTopicDiscussion($id, $discussion) {
        return $this->update('id', $id, array('discussion_id' => $discussion));
    }

    /**
     * Method to update the last post and replies value of a topic after deleting a post
     * @param string $topic_id Record Id of the Topic
     */
    function updateTopicAfterDelete($topic_id) {
        $lastPost = $this->objPost->getLastTopicPost($topic_id);
        $numPosts = $this->objPost->getNumPostsInTopic($topic_id);

        if ($numPosts == 0) {
            $numPosts = 1;
        }

        if ($lastPost != '' && $lastPost != FALSE) {
            $this->update('id', $topic_id, array('last_post'=>$lastPost, 'replies'=>$numPosts-1));
        }
    }

    /**
     * Insert a topic into the database
     *
     * @param string $discussion_id: Record ID of the Discussion post is being made into
     * @param string $type_id: Type of topic
     * @param string $first_post: Record Id of the first post
     * @param string $topic_tangent_parent: Record Id of tangent parent
     * @param string $userId: User ID of person starting the topic
     * @param string $dateLastUpdated: Date topic was started
     */
    function insertSingleAPI($discussion_id, $type_id, $topic_tangent_parent, $userID) {
        if ($topic_tangent_parent == 0) {
            $level = 1;
        }
        // provide support for tangents

        return $this->insert(array(
                'discussion_id'        => $discussion_id,
                'type_id'         => $type_id,
                'views'           => 0,
                'replies'         => 0,
                'topic_tangent_parent'   => $topic_tangent_parent,
                'lft'                => null,
                'rght'                => null,
                'userId'          => $userID,
                'dateLastUpdated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
        ));

    }

}
?>
