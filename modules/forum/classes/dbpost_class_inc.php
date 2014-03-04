<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

$this->loadClass('link', 'htmlelements');

/**
 * Forum Posts Table
 * This class controls all functionality relating to the tbl_forum_post table
 * @author Tohir Solomons
 * @copyright (c) 2004 University of the Western Cape
 * @package forum
 * @version 1
 */

/**
 * This class returns arrays of recordset from the database table 'tbl_forum_post' and also helps with the display of topics
 */
class dbPost extends dbTable {

        /**
         *  $var Boolean Variable to flag whether to show the reply-to-post link
         */
        var $repliesAllowed = TRUE;

        /**
         *  $var Boolean Variable to flag whether to show the edit-a-post link
         */
        var $editingPostsAllowed = TRUE;

        /**
         *  $var First level of Indentration
         */
        var $threadDisplayLevel = 1;

        /**
         *  $var Counter to count the number of open div tags (for the collapsible thread system)
         */
        var $numOpenThreadDisplayDivs = 0;

        /**
         *  $var Boolean Variable to flag whether to a forum is locked - affects replying to a post/editing/translating
         */
        var $forumLocked = FALSE;

        /**
         *  $var Array containing values for the rating of forums
         */
        var $forumRatingsArray;

        /**
         *  $var Boolean Variable to flag whether to show the ratings drop down
         */
        var $showRatings = FALSE;

        /**
         *  $var String Variable to hold the type of forum - either context or workgroup
         */
        var $forumtype = 'context';

        /**
         * @var object To handle all post attachments
         */
        var $objPostAttachments;
        
        /**
         *
         * @var object DOM
         */
        var $objDom;
        
        var $showModeration;

        /**
         *
         * @var object
         */
        var $objFileIcons;

        /**
         *
         * @var object To preview post attachments
         */
        var $objFilePreview;

        /**
         *
         * @var object object to be used to retrive post ratings
         */
        var $dbjPostratings;

        /**
         * Constructor method to define the table(default)
         */
        function init() {
                parent::init('tbl_forum_post');
                $this->objDom = new DOMDocument('utf-8');
                $this->objUserPic = $this->getObject('imageupload', 'useradmin');
                $this->objSkin = $this->getObject('skin', 'skin');
                $this->objTrimStrings = $this->getObject('trimstr', 'strings');
                $this->trimstrObj = $this->getObject('trimstr', 'strings');
                $this->objFileIcons = $this->newObject('fileicons', 'files');
                $this->objFileIcons->size = 'large';
                $this->objFilePreview = $this->getObject('filepreview', 'filemanager');
                $this->dbPostratings = $this->getObject('dbpost_ratings', 'forum');
                $this->objForum = $this->getObject('dbforum');
                $this->objPostAttachments = $this->getObject('dbpostattachments');
                $this->objPostText = $this->getObject('dbposttext');
                $this->objUser = $this->getObject('user', 'security');
                $this->userId = $this->objUser->userId();
                $this->objDateFunctions = $this->getObject('dateandtime', 'utilities');
                $this->objLanguageCode = $this->getObject('languagecode', 'language');
                $this->objLanguage = $this->getObject('language', 'language');
                // Load Forum Subscription classes
                $this->objForumSubscriptions = $this->getObject('dbforumsubscriptions');
                $this->objTopicSubscriptions = $this->getObject('dbtopicsubscriptions');
                $this->objTranslatedDate = $this->getObject('translatedatedifference', 'utilities');
                $this->objDateTime = $this->getObject('dateandtime', 'utilities');
                // Get Context Code Settings
                $this->contextObject = $this->getObject('dbcontext', 'context');
                $this->contextCode = $this->contextObject->getContextCode();
                $this->objWashoutFilters = $this->getObject('washout', 'utilities');
                $this->objIcon = $this->newObject('geticon', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
                $this->objScriptClear = $this->getObject('script', 'utilities');
                $this->showModeration = FALSE;
                $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $this->showFullName = $this->objSysConfig->getValue('SHOWFULLNAME', 'forum');

                if ($this->showFullName == '') {
                        $this->showFullName = TRUE;
                }

                if ($this->showFullName != FALSE) {
                        $this->showFullName = TRUE;
                }
        }

        /**
         * Insert a post into the database
         *
         * @param string $post_parent: Record ID of the Parent Post - that the user is replying to
         * @param string $post_tangent_parent: Record ID of the Tangent Post - that the user is replying to
         * @param string $post_title: Title of Post
         * @param string $post_message: Text of the Post
         * @param string $topic_id: Record ID of the Topic
         * @param string $userId: User ID of person posting the post
         * @param string $dateLastUpdated: Date Post was made
         * @param string $id Id - Optional, used by API
         * @return string $this->getLastInsertId()
         */
        function insertSingle($post_parent, $post_tangent_parent, $forum_id, $topic_id, $userId, $level = 1, $id = NULL) {
                // Interim measure. Alternative, use regexp and replace with space
                //$post_title = strip_tags($post_title);
                //echo $post_parent;

                if ($post_parent == '0') {
                        $lastRightPointer = $this->getLastRightPointer($forum_id);
                        $leftPointer = $lastRightPointer + 1;
                        $rightPointer = $lastRightPointer + 2;
                        $level = 1;
                } else {
                        $lastRightPointer = $this->getPostRightPointer($post_parent);
                        $updateRightSQL = 'UPDATE tbl_forum_post SET rght = rght + 2 WHERE rght > ' . ($lastRightPointer - 1);
                        $this->getArray($updateRightSQL);
                        $updateLeftSQL = 'UPDATE tbl_forum_post SET lft = lft + 2 WHERE lft > ' . ($lastRightPointer - 1);
                        $this->getArray($updateLeftSQL);

                        $leftPointer = $lastRightPointer;
                        $rightPointer = $lastRightPointer + 1;
                        $level += 1;
                }

                $this->insert(array(
                    'id' => $id,
                    'post_parent' => $post_parent,
                    'post_tangent_parent' => $post_tangent_parent,
                    'topic_id' => $topic_id,
                    'post_order' => $this->getLastPostOrder($topic_id),
                    'userId' => $userId,
                    'datecreated' => $this->now(),
                    'lft' => $leftPointer,
                    'rght' => $rightPointer,
                    'level' => $level,
                    'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
                ));

                return $this->getLastInsertId();
        }

        /**
         * This method gets the last right pointer in a forum. Used when a new topic is post,
         * i.e. pointers for topic will be: left = lastright+1; right = lastright+2
         * @param $forum Forum Id to get the last right pointer from
         */
        function getLastRightPointer($forum) {
                $sql = 'SELECT tbl_forum_post.rght FROM tbl_forum_topic  INNER JOIN tbl_forum_post ON (tbl_forum_post.topic_id = tbl_forum_topic.id) WHERE tbl_forum_topic.forum_id = \'' . $forum . '\' ORDER BY rght DESC LIMIT 1';

                $list = $this->getArray($sql);

                if (count($list) == 0) {
                        return 0;
                } else {
                        return $list[0]['rght'];
                }
        }

        /**
         * Method to get the Left and Right Pointer for the first post in a topic.
         * @param string $topic_id Record Id of the topic
         * @return array Left and Right pointer of first post in a topic
         */
        function getTopicPointer($topic_id) {
                $sql = 'SELECT lft, rght FROM tbl_forum_post WHERE topic_id=\'' . $topic_id . '\' AND post_parent = \'0\' ORDER BY level LIMIT 1';

                $array = $this->getArray($sql);

                if (count($array) == 0) {
                        return NULL;
                } else {
                        return $array[0];
                }
        }

        /**
         * Post Order is the order of post with regards to date, irrespective of level.
         * This method gets the last Post Order by providng a topic id
         * @param $topic Record Id of the Topic
         * @return int Order Number of last post +1 (Plus One)
         */
        function getLastPostOrder($topic_id) {
                $sql = 'SELECT post_order FROM tbl_forum_post WHERE topic_id=\'' . $topic_id . '\' ORDER BY post_order DESC LIMIT 1';
                $array = $this->getArray($sql);

                if (count($array) == 0) {
                        return 1;
                } else {
                        return $array[0]['post_order'] + 1;
                }
        }

        /**
         * Method to get the right pointer of a post
         * @param $post_id Record Id of the Post
         * @return int Right Pointer Value
         */
        function getPostRightPointer($post_id) {
                $sql = 'SELECT rght FROM tbl_forum_post WHERE id=\'' . $post_id . '\' LIMIT 1';
                $array = $this->getArray($sql);

                if (count($array) == 0) {
                        return NULL;
                } else {
                        return $array[0]['rght'];
                }
        }

        /**
         * Method to get a thread for a topic by providing the left and right pointers
         * @param int $leftPointer Left Pointer Value
         * @param int $rightPointer Right Pointer Value
         * @param string $topic_id Record Id of the Topic
         *
         * CHECK!! This can be changed to just provide the topic - internal check for pointer
         * @return array List of Posts
         */
        function getThread($topic_id) {
                $pointers = $this->getTopicPointer($topic_id);

                $sql = 'SELECT tbl_forum_post.*, tbl_forum_post_text.*, tbl_users.firstname, tbl_users.surname, tbl_users.username,
        tbl_forum_post_attachment.attachment_id, replypost.id AS replypost, languagecheck.id AS anotherlanguage, tbl_forum_post_ratings.rating FROM tbl_forum_post
        INNER JOIN tbl_forum_post_text ON (tbl_forum_post.id = tbl_forum_post_text.post_id AND tbl_forum_post_text.original_post = "1")
        LEFT  JOIN tbl_users ON ( tbl_forum_post.userId = tbl_users.userId )
        LEFT JOIN tbl_forum_post_attachment ON (tbl_forum_post.id = tbl_forum_post_attachment.post_id)
        LEFT JOIN tbl_forum_post AS replypost ON (tbl_forum_post.id = replypost.post_parent)
        LEFT JOIN tbl_forum_post_text AS languagecheck ON (tbl_forum_post.id = languagecheck.post_id AND languagecheck.original_post="0" AND tbl_forum_post_text.language != languagecheck.language)
        LEFT JOIN tbl_forum_post_ratings ON (tbl_forum_post.id = tbl_forum_post_ratings.post_id)
        WHERE tbl_forum_post.topic_id = \'' . $topic_id . '\' AND tbl_forum_post.lft >= \'' . $pointers['lft'] . '\' AND tbl_forum_post.rght <= \'' . $pointers['rght'] . '\'
        GROUP BY tbl_forum_post.id ORDER BY lft';

                return $this->getArray($sql);
        }

        /**
         * Method to get the list of posts in a topic arranged by the order they were made.
         * Could be ordered by Date
         * @param string $topic Record Id of the Topic
         * @return array List of Posts
         */
        function getFlatThread($topic) {
                $sql = '
                    
                SELECT 
                    tbl_forum_post.id as postid,
                    tbl_forum_post.post_parent,
                    tbl_forum_post.post_tangent_parent,
                    tbl_forum_post.topic_id,
                    tbl_forum_post.post_order,
                    tbl_forum_post.lft,
                    tbl_forum_post.rght,
                    tbl_forum_post.average_ratings,
                    tbl_forum_post.level,
                    tbl_forum_post.userid,
                    tbl_forum_post.datecreated,
                    tbl_forum_post.modifierid,
                    tbl_forum_post.datelastupdated as postlastupdated,
                    tbl_forum_post.post_emailed,
                    tbl_forum_post.puid,

                    tbl_forum_post_text.post_id,
                    tbl_forum_post_text.post_title,
                    tbl_forum_post_text.post_text,
                    tbl_forum_post_text.language,
                    tbl_forum_post_text.original_post,
                    tbl_forum_post_text.readability,
                    tbl_forum_post_text.wordcount,
                    tbl_forum_post_text.userid,
                    tbl_forum_post_text.modifierid,


                    tbl_users.firstname,
                    tbl_users.surname,
                    tbl_users.username,
                    tbl_forum_post_attachment.attachment_id,
                    replypost.id AS replypost,
                    languagecheck.id AS anotherlanguage,
                    tbl_forum_post_ratings.rating
                FROM
                    tbl_forum_post
                        INNER JOIN
                    tbl_forum_post_text ON (tbl_forum_post.id = tbl_forum_post_text.post_id AND tbl_forum_post_text.original_post = \'1\')
                        LEFT JOIN
                    tbl_users ON (tbl_forum_post.userId = tbl_users.userId)
                        LEFT JOIN
                    tbl_forum_post_attachment ON (tbl_forum_post.id = tbl_forum_post_attachment.post_id)
                        LEFT JOIN
                    tbl_forum_post AS replypost ON (tbl_forum_post.id = replypost.post_parent)
                        LEFT JOIN
                    tbl_forum_post_text AS languagecheck ON (tbl_forum_post.id = languagecheck.post_id AND languagecheck.original_post = \'0\' AND tbl_forum_post_text.language != languagecheck.language)
                        LEFT JOIN
                    tbl_forum_post_ratings ON (tbl_forum_post.id = tbl_forum_post_ratings.post_id)
                WHERE tbl_forum_post.topic_id = \'' . $topic . '\' GROUP BY tbl_forum_post.id ORDER BY post_order';
                return $this->getArray($sql);
        }

        /**
         * Method to get the Root (First) Post in a topic: title, text, date, etc.
         * @param string $topic Record Id of the topic
         * @return array Details of the post
         */
        function getRootPost($topic) {
                $sql = '
                SELECT 
                    tbl_forum_post.id as postid,
                    tbl_forum_post.post_parent,
                    tbl_forum_post.post_tangent_parent,
                    tbl_forum_post.topic_id,
                    tbl_forum_post.post_order,
                    tbl_forum_post.lft,
                    tbl_forum_post.rght,
                    tbl_forum_post.average_ratings,
                    tbl_forum_post.level,
                    tbl_forum_post.userid,
                    tbl_forum_post.datecreated,
                    tbl_forum_post.modifierid,
                    tbl_forum_post.datelastupdated,
                    tbl_forum_post.post_emailed,
                    tbl_forum_post.puid,
                    
                    tbl_forum_topic.id as topicid,
                    tbl_forum_topic.forum_id,
                    tbl_forum_topic.type_id,
                    tbl_forum_topic.topic_tangent_parent,
                    tbl_forum_topic.lft,
                    tbl_forum_topic.rght,
                    tbl_forum_topic.first_post,
                    tbl_forum_topic.last_post,
                    tbl_forum_topic.views,
                    tbl_forum_topic.replies,
                    tbl_forum_topic.status,
                    tbl_forum_topic.lockreason,
                    tbl_forum_topic.lockuser,
                    tbl_forum_topic.lockdate,
                    tbl_forum_topic.locktime,
                    tbl_forum_topic.userid,
                    tbl_forum_topic.sticky,
                    tbl_forum_topic.datelastupdated as topicdatelastupdated,
                    
                    tbl_forum_topic.puid,
                    tbl_forum_post_text.post_id,
                    tbl_forum_post_text.post_title,
                    tbl_forum_post_text.post_text,
                    tbl_forum_post_text.language,
                    tbl_forum_post_text.original_post,
                    tbl_forum_post_text.readability,
                    tbl_forum_post_text.wordcount,
                    tbl_forum_post_text.userid,
                    tbl_forum_post_text.modifierid,
                    forum_name,
                    forum_id,
                    tbl_users.firstname,
                    tbl_users.surname,
                    tbl_users.username,
                    tbl_forum_post_attachment.attachment_id,
                    replyPost.id AS replypost,
                    languagecheck.id AS anotherlanguage,
                    tbl_forum_post_ratings.rating,
                    tbl_forum_post.lft as postleft,
                    tbl_forum_post.rght as postright
                FROM
                    tbl_forum_post
                        INNER JOIN
                    tbl_forum_post_text ON (tbl_forum_post.id = tbl_forum_post_text.post_id AND tbl_forum_post_text.original_post = \'1\')
                        INNER JOIN
                    tbl_forum_topic ON (tbl_forum_topic.id = tbl_forum_post.topic_id)
                        INNER JOIN
                    tbl_forum ON (tbl_forum.id = tbl_forum_topic.forum_id)
                        LEFT JOIN
                    tbl_users ON (tbl_forum_post.userId = tbl_users.userId)
                        LEFT JOIN
                    tbl_forum_post_attachment ON (tbl_forum_post.id = tbl_forum_post_attachment.post_id)
                        LEFT JOIN
                    tbl_forum_post AS replyPost ON (tbl_forum_post.id = replyPost.post_parent)
                        LEFT JOIN
                    tbl_forum_post_text AS languagecheck ON (tbl_forum_post.id = languagecheck.post_id AND languagecheck.original_post = \'0\' AND tbl_forum_post_text.language != languagecheck.language)
                        LEFT JOIN
                    tbl_forum_post_ratings ON (tbl_forum_post.id = tbl_forum_post_ratings.post_id)
                WHERE tbl_forum_post.topic_id=\'' . $topic . '\' AND tbl_forum_post.post_parent = \'0\'
                GROUP BY tbl_forum_post.id  LIMIT 1'; //AND tbl_forum_post.level = "1"


                $results = $this->getArray($sql);

                if (count($results) == 0) {
                        return NULL;
                } else {
                        return $results[0];
                }
        }

        /**
         * Method to get the title, text, date, etc of any post, by providing the record id of the post.
         * @param string $post Record Id of the post
         * @return array Details of the post
         */
        function getPostWithText($post) {
                $sql = '
                SELECT 
                    tbl_forum_post.id as postid,
                    tbl_forum_post.post_parent,
                    tbl_forum_post.post_tangent_parent,
                    tbl_forum_post.topic_id,
                    tbl_forum_post.post_order,
                    tbl_forum_post.lft,
                    tbl_forum_post.rght,
                    tbl_forum_post.average_ratings,
                    tbl_forum_post.level,
                    tbl_forum_post.userid,
                    tbl_forum_post.datecreated,
                    tbl_forum_post.modifierid,
                    tbl_forum_post.datelastupdated,
                    tbl_forum_post.post_emailed,
                    tbl_forum_post.puid,
                    tbl_forum_post_text.post_id,
                    tbl_forum_post_text.post_title,
                    tbl_forum_post_text.post_text,
                    tbl_forum_post_text.language,
                    tbl_forum_post_text.original_post,
                    tbl_forum_post_text.readability,
                    tbl_forum_post_text.wordcount,
                    tbl_forum_post_text.userid,
                    tbl_forum_post_text.modifierid,
                    tbl_forum_topic.id as topicid,
                    tbl_forum_topic.forum_id,
                    tbl_forum_topic.type_id,
                    tbl_forum_topic.topic_tangent_parent,
                    tbl_forum_topic.lft,
                    tbl_forum_topic.rght,
                    tbl_forum_topic.first_post,
                    tbl_forum_topic.last_post,
                    tbl_forum_topic.views,
                    tbl_forum_topic.replies,
                    tbl_forum_topic.status,
                    tbl_forum_topic.lockreason,
                    tbl_forum_topic.lockuser,
                    tbl_forum_topic.lockdate,
                    tbl_forum_topic.locktime,
                    tbl_forum_topic.userid,
                    tbl_forum_topic.sticky,
                    tbl_forum_topic.datelastupdated as topicdatelastupdated,
                    tbl_forum_topic.puid,
                    tbl_users.firstname,
                    tbl_users.surname,
                    tbl_users.username,
                    tbl_forum_post_attachment.attachment_id,
                    replyPost.id AS replypost,
                    languagecheck.id AS anotherlanguage,
                    tbl_forum_post_ratings.rating,
                    tbl_forum_post.lft as postleft,
                    tbl_forum_post.rght as postright
                FROM
                    tbl_forum_post
                        INNER JOIN
                    tbl_forum_post_text ON (tbl_forum_post.id = tbl_forum_post_text.post_id)
                        INNER JOIN
                    tbl_forum_topic ON (tbl_forum_post.topic_id = tbl_forum_topic.id)
                        LEFT JOIN
                    tbl_users ON (tbl_forum_post.userId = tbl_users.userId)
                        LEFT JOIN
                    tbl_forum_post_attachment ON (tbl_forum_post.id = tbl_forum_post_attachment.post_id)
                        LEFT JOIN
                    tbl_forum_post AS replyPost ON (tbl_forum_post.id = replyPost.post_parent)
                        LEFT JOIN
                    tbl_forum_post_text AS languagecheck ON (tbl_forum_post.id = languagecheck.post_id AND tbl_forum_post_text.language != languagecheck.language)
                        LEFT JOIN
                    tbl_forum_post_ratings ON (tbl_forum_post.id = tbl_forum_post_ratings.post_id)
                WHERE
                    tbl_forum_post_text.post_id = \'' . $post . '\'
                GROUP BY tbl_forum_post.id
                LIMIT 1; 
            ';
                $results = $this->getArray($sql);
                if (count($results) == 0) {
                        return NULL;
                } else {
                        return $results[0];
                }
        }

        /**
         * Method to get a post in a language
         * At the moment, it works with the record_id not language!
         * @param string $postTextId Record Id of the post text
         * @return array Details of the post
         */
        function getPostInLanguage($postTextId) {
                $sql = 'SELECT tbl_forum_post.*, tbl_forum_post_text.*, tbl_forum_topic.*, tbl_users.firstname, tbl_users.surname, tbl_forum_post.datelastupdated AS datelastupdated, tbl_forum_post_attachment.attachment_id, replyPost.id AS replypost, languagecheck.id AS anotherlanguage
        FROM tbl_forum_post
        INNER JOIN tbl_forum_post_text ON (tbl_forum_post.id = tbl_forum_post_text.post_id )
        INNER JOIN tbl_forum_topic ON (tbl_forum_post.topic_id = tbl_forum_topic.id)
        LEFT  JOIN tbl_users ON ( tbl_forum_post.userId = tbl_users.userId )
        LEFT JOIN tbl_forum_post_attachment ON (tbl_forum_post.id = tbl_forum_post_attachment.post_id)
        LEFT JOIN tbl_forum_post AS replyPost ON (tbl_forum_post.id = replyPost.post_parent)
        LEFT JOIN tbl_forum_post_text AS languagecheck ON (tbl_forum_post.id = languagecheck.post_id AND tbl_forum_post_text.language != languagecheck.language)
        WHERE tbl_forum_post_text.id = \'' . $postTextId . '\' GROUP BY tbl_forum_post.id LIMIT 1';

                //return array('post_text'=>$sql);
                $results = $this->getArray($sql);

                if (count($results) == 0) {
                        return NULL;
                } else {
                        return $results[0];
                }
        }

        /**
         * Method to display a post in a formatted fashion
         * @param array $post The post with all information in an array -> text, title, date, author, etc.
         * @param boolean $showMargin A flag to indicate whether the post should be shown with indented margins (threaded margin)
         * @param string $topic_id ID of the topic the reply belongs to
         * @param boolean $makeContractible A flag to indicate whether to include javascript to expand/contract threads
         * @return string The post in a formatted version.
         */
        function displayPost($post, $showMargin = FALSE, $makeContractible = FALSE) {
                //INNER POSTS
                //get all posts
                $statement = "SELECT * FROM tbl_forum_post WHERE post_parent = '{$post['post_id']}'";
                $innerPosts = $this->getArray($statement);
                //values to be used in query string
                $topicInfo = $this->getRow('id', $post['topic_id'], 'tbl_forum_topic');
                if ($showMargin) {
                        $margin = 'style="margin-left: ' . ($post['level'] * 40 - 40) . 'px;"';
                } else {
                        $margin = NULL;
                }
                $return = '';

                if ($makeContractible) {
                        if ($post['level'] <= $this->threadDisplayLevel) {
                                $times = $this->numOpenThreadDisplayDivs - $post['level'] + 1;

                                for ($i = 1; $i <= $times; $i++) {
                                        $return .= '</div>';
                                        $this->numOpenThreadDisplayDivs -=1;
                                }
                        }


                        $this->appendArrayVar('headerParams', '
<style type="text/css" title="text/css">
.switchcontent{display:none;}
</style>
                    ');
                }

                $this->objIcon->setIcon('delete');
                $this->objIcon->align = 'right';
                // Set up Alt Text
                $moderatePostIcon = $this->objIcon->show();
                $contentDiv ='<div class="forumProfileImg" >' . $this->objUser->getUserImage($post['userid']);
                $contentDiv .= "</div>";
                $this->threadDisplayLevel = $post['level'];
                $return .= $contentDiv;
                //$return .= $post['post_parent'].' '.$post['post_id']; - just for testing
                $return .= '<div id="' . $post['post_id'] . '" class="newForumContainer" ' . $margin . '>' . "\r\n";
                $return .= '<div class="newForumTopic parent">' . "\r\n";
                // Check if user can edit post
                if ($this->editingPostsAllowed && $post['replypost'] == NULL && $this->checkOkToEdit($post['datecreated'], $post['userid'], $post['replypost'])) {

                        // Check whether to start a paragraph or pipe
                        if ($this->repliesAllowed) {
                                $return .= '  ';
                        } else {
                                $return .= '<p>';
                        }
                }
                //get the date and time difference
                $dateDiff = $this->objTranslatedDate->getDifference_no_html($post['datecreated'], $this->now(), 'd');
                //testing date and time
                $dateFrom = strtotime($post['datecreated']);
                $dateDiff = strtotime($this->now()) - $dateFrom;
//                echo '<br/>'.$formetedDate.'<br/>'.  strtotime($this->now()) ;
                if ($this->showModeration || $this->objUser->userId() == $post['userid']) {
                        if ($topicInfo['status'] == 'OPEN') {
                                $objDateTime = $this->getObject('dateandtime', 'utilities');
                                $date = $objDateTime->secondsInDay($dateDiff);
//                                echo $date/60;
                                if (count($innerPosts) == 0) {
                                        if ($dateDiff < 1800) {
//                        if($post['datelastupdated']){
                                                $tposteditLink = new link('javascript:void(0)');
                                                $this->objIcon->setIcon('edit');
                                                $tposteditLink->link = $this->objIcon->show();
                                                $tposteditLink->cssClass = "postEditClass {$post['post_id']}";
                                                $tposteditLink->cssId = $post['postid'];
                                                $tposteditLink->title = $this->objLanguage->languageText('mod_forum_moderatepost', 'forum');
                                                $deleteLink = new link($this->uri(array('action' => 'moderatepost', 'id' => $post['post_id'])));
                                                $deleteLink->link .= $moderatePostIcon;
                                                $deleteLink->title = $this->objLanguage->languageText('mod_forum_moderatepost', 'forum');
                                                $return .= $tposteditLink->show();
                                        }
                                }
                        }
//                        if ($this->objUser->userId() == $post['userid']) {
//                        }
                }

                // Check if the contractible layers should be implemented
                if ($makeContractible) {
                        $return .= '<img src="modules/forum/resources/contract.gif" align="right" onclick="expandcontent(\'' . $post['post_id'] . '\')"  style="cursor:hand; cursor:pointer; padding-right: 20px;" />';
                }
//=========Decorating the date============
//                                echo '<pre>'; var_dump($post); echo '</pre>';die();
                $Date = date('Y M d', mktime(0, 0, 0, substr($post['datecreated'], 5, 2), substr($post['datecreated'], 8, 2), substr($post['datecreated'], 0, 4)));
                $year = '<div class="date-year">' . date("Y", strtotime($post['datecreated'])) . '</div>';

                $month = '<div class="date-month" >' . date("M", strtotime($post['datecreated'])) . '</div>';
                $day = '<div class="date-day" >' . date("d", strtotime($post['datecreated'])) . '</div>';
                $dateSpan = '<div class="date-wrapper" >' . $day . '' . $month . '' . $year . '</div>';


                if ($this->showFullName) {
                        // Start of the Title Area
                        $return .= '<div>' . $dateSpan . '<span class="strong">' . $this->objTrimStrings->strTrim($post['post_title'], 50) . '</span><br/> <strong>' . $this->objLanguage->languageText('word_by', 'system') . ': ' . $post['firstname'] . ' ' . $post['surname'] . '</strong><br/>' . strtolower($this->objLanguage->languageText('word_at', 'system')) . ' ' . $this->objDateTime->formatTime($post['datecreated']) . ' (' . $this->objTranslatedDate->getDifference($post['datecreated']) . ')' . ' <strong>' . '</strong>  <br/> </div>';
                } else {
                        // Start of the Title Area
                        $return .= '<div class="forumTopicTitle"><strong>' . stripslashes($post['post_title']) . '</strong><br />by ' . $post['username'] . ' - ' . $this->objDateTime->formatDateOnly($post['datecreated']) . $this->objLanguage->languageText('word_at', 'system') . $this->objDateTime->formatTime($post['datecreated']) . ' (' . $this->objTranslatedDate->getDifference($post['datecreated']) . ') </div>';
                }
                // Ebd Title Area
                $return .= '</div>';


                // Start of the content area
                $return .= '<div class="newForumContent" id="' . $post['post_id'] . '" style="display:block">' . "\r\n";
                if ($post['post_tangent_parent'] != '0' && $post['level'] == 1) {
                        $tangentParent = $this->getPostWithText($post['post_tangent_parent']);
                        $return .= '<div class="forumTangentIndent">';
                        $link = & $this->getObject('link', 'htmlelements');
                        $link->href = $this->uri(array('action' => 'thread', 'id' => $tangentParent['topic_id'], 'type' => $this->forumtype));
                        $link->link = stripslashes($tangentParent['post_title']);
                        $link->anchor = $tangentParent['post_id'];

                        $return .= '<strong>' . $this->objLanguage->languageText('mod_forum_topicisatangentto', 'forum') . ' ' . $link->show() . $this->objLanguage->languageText('word_by', 'system') . $tangentParent['firstname'] . ' ' . $tangentParent['surname'] . '</strong><br /><br />';
                        $return .= $this->objScriptClear->removeScript($tangentParent['post_text']);
                        $return .= '</div>';
                }
                $return .= '<div id="loading_' . $post['post_id'] . '"></div>';
                $tmpText = $this->objWashoutFilters->parseText(
                        $this->objScriptClear->removeScript(// Apply Script Removal Filters
                                stripslashes(// Remove Slashes
                                        $post['post_text']
                )));
                /**
                 * removing database paragraph tags
                 * @todo Find a cleaner wy of doing this
                 */
                //value to be replaced
                $search = array(
                    '<p>',
                    '</p>'
                );
                //replacement values
                $replacers = array(
                    '',
                    ''
                );
                $tmpText = str_replace($search, $replacers, $tmpText);
                $return .= "<div id='{$post['postid']}' class='postText' > {$tmpText}</div>";

                // Check if the post has attachments
                if ($post['attachment_id'] != NULL) {
                        $attachments = $this->objPostAttachments->getAttachments($post['post_id']);
                        // By Pass if attachment has been deleted.
                        if (count($attachments) != 0) {
                                $return .= '<br /><br />';
                                foreach ($attachments AS $attachment) {
                                        $files = $this->objPostAttachments->downloadAttachment($attachment['id']);
                                        if (count($files) > 0) {
                                                $this->objFiles = $this->getObject('dbfile', 'filemanager');
                                                $attachment_path = $this->objFiles->getFilePath($files[0]['id']);
                                                $downloadlink = new link($attachment_path);
                                                $downloadlink->cssClass = 'forumDownload';
//                                                $downloadlink->link = $attachment['filename'];
                                                $wrapperDiv = "<div class='file-preview' id='{$attachment['id']}' >";
//                                                $this->objIcon->setIcon('download');
                                                $downloadlink->link .= $this->objLanguage->languageText('word_download','system');
                                                $previewLink = new link('javascript:void(0);');
                                                $previewLink->cssClass = 'forumViewAttachment';
                                                $previewLink->link = $this->objLanguage->languageText('phrase_viewattachment','system');
                                                $previewLink->cssId = $attachment['id'];
//                                                $wrapperDiv .= $downloadlink->show();
//                                                $wrapperDiv .= $previewLink->show();
                                                $wrapperDiv .= '<br/><br/>';
                                                $wrapperDiv .= $this->objFilePreview->previewFile($files[0]['id']);
                                                $wrapperDiv .= "</div>";
                                                $return .= $this->objFileIcons->getFileIcon($attachment['filename']).'' . $attachment['filename']."<br/><br/> {$downloadlink->show()} {$previewLink->show()}".$wrapperDiv . '<br />';
                                        }
                                }
                        }
                }

                // Check if allowed to show ratings
                // $this->repliesAllowed is a boolean that takes into consideration many things
                // Is the forum unlocked? Is the post unlocked
                if ($this->showRatings && $this->repliesAllowed) {
                        // Start Ratings
                        $this->loadclass('dropdown', 'htmlelements');
                        $dropdown = new dropdown($post['post_id']);

                        if ($post['rating'] == NULL) {
                                $dropdown->addOption('n/a', $this->objLanguage->languageText('mod_forum_selectarating', 'forum') . '...');
                        }

                        foreach ($this->forumRatingsArray as $rating) {
                                $dropdown->addOption($rating['id'], $rating['rating_description']);
                        }

                        if (isset($post['rating']) && $post['rating'] != NULL) {
                                $dropdown->setSelected($post['rating']);
                        }

//                        $return .= '<div align="right">' . $dropdown->show() . '</div>';
                        // End Ratings
                }


                $forumID = "<span class='forumid' id='{$topicInfo['forum_id']}' ></span>";
                if (count($innerPosts) >= 1) {
                        foreach ($innerPosts as $innerPost) {
                                $dLink = "";
//                        $sqlState = "SELECT * FROM tbl_forum_post_text WHERE post_id = '{$innerPost['id']}'";
//            $postInfo = $this->getArray($sqlState);
                                $postInfo = $this->getPostWithText($innerPost['id']);
                                $return .= "<br/>";
                                //wrapper   
                                if ($this->showModeration || $this->objUser->userId() == $innerPost['userid'] || $this->objUser->isCourseAdmin()) {
//            $deleteLink->link = ;
//                                if($this->objUser->isCourseAdmin()){echo "is";}
                                        $confimLink = new link('#');
                                        $confimLink->link = $this->objLanguage->languageText('word_yes', 'system');
                                        $confimLink->cssId = $postInfo['post_id'];
                                        $confimLink->cssClass = "postDeleteConfirm";

                                        $declineLink = new link('#');
                                        $declineLink->link = $this->objLanguage->languageText('word_no', 'system');
                                        $declineLink->cssId = $postInfo['post_id'];
                                        $declineLink->cssClass = "postDeleteCancel";

                                        $postEditLink = new link('javascript:void(0)');
                                        $this->objIcon->setIcon('edit');
                                        $postEditLink->link = $this->objIcon->show();
                                        $postEditLink->title = $this->objLanguage->languageText('mod_forum_editpost', 'forum');
                                        $postEditLink->cssClass = "postEditClass {$postInfo['post_id']}";
                                        $postEditLink->cssId = $postInfo['post_id'];
                                        $deleteLink = new link('#'/* $this->uri(array('action' => 'moderatepost', 'id' => $postInfo[0]['post_id'])) */);
                                        $deleteLink->link = $moderatePostIcon;
                                        $deleteLink->title = $this->objLanguage->languageText('phrase_delete_post', 'system');
                                        $deleteLink->cssId = $postInfo['post_id'];
                                        $deleteLink->cssClass = "postDeleteLink";
                                        $threadReply = new link('javascript:void(0);');
                                        $threadReply->cssClass = "threadReplyLink";
                                        $threadReply->cssId = "{$postInfo['post_id']}";
                                        $this->objIcon->setIcon('reply');
                                        $threadReply->link = $this->objIcon->show();
                                        if ($this->objUser->userId() == $innerPost['userid']) {
                                                $dLink .= $postEditLink->show();
                                        }
                                        $deleteConfirm = "<div id='{$postInfo['post_id']}' class='deleteconfirm' ><div><p>{$this->objLanguage->languageText('mod_forum_confirmdeletepost', 'forum')}<br/><br/><br/>{$confimLink->show()} &nbsp;&nbsp;&nbsp;&nbsp;{$declineLink->show()}</p></div></div>";
                                        if ($this->objUser->isCourseAdmin($this->contextCode) || $this->objUser->userId() == $innerPost['userid']) {
                                                $dLink .= $deleteConfirm . $deleteLink->show();
                                        }
                                }

                                //new ratings object 
                                $ratingsDiv = "<div class='ratingsWrapper' ><hr/>";
                                $upperLink = new link('#');
                                $upperLink->cssClass = "ratings up";
                                $upperLink->cssId = $innerPost['id'];
                                $upperLink->link = "";
                                $lowerLink = new link('#');
                                $lowerLink->cssClass = "ratings down";
                                $lowerLink->cssId = $innerPost['id'];
                                $lowerLink->link = "";
                                $numberOfVotes = $this->dbPostratings->getPostRatings($innerPost['id']);
                                $displaySpan = "<span class='numberindicator' >{$upperLink->show()}";
                                if ($numberOfVotes > 0) {
                                        $displaySpan .= "<br/>" . $numberOfVotes . '<br/>' . $lowerLink->show();
                                } else {
                                        $displaySpan .= "<br/><span class='number' >" . $numberOfVotes . "</span><br/>";
                                }
                                $displaySpan .= '</span>';
                                $ratingsDiv .= $displaySpan . "</div>";

//=========Decorating the date============
//                                $Date = date('Y M d', mktime(0, 0, 0, substr($postInfo['datelastupdated'], 5, 2), substr($postInfo['datelastupdated'], 8, 2), substr($postInfo['datelastupdated'], 0, 4)));
//                                $year = '<div class="date-year-inner">' . substr($Date, 0, 4) . '</div>';
//                                $month = '<div class="date-month-inner" >' . substr($Date, 5, 3) . '</div>';
//                                $day = '<div class="date-day-inner" >' . substr($Date, 9, 2) . '</div>';
//                                $dateSpan = '<div class="date-wrapper-inner" >' . $day . '' . $month . '' . $year . '</div>';
                                //get parent info
                                $conteiner = "\r\n" . '<div class="all-wrapper" > <div class="forumProfileImg" >' . $this->objUser->getUserImage($innerPost['userid']) . '</div> <div class="innerReplyDiv" >' . '</div><div id="' . $postInfo['post_id'] . '" class="newForumContainer parent" >' . $dLink . '<div class="newForumTopic Inner" >' . $dateSpan . ' <span class="strong"> ' . $this->objLanguage->languageText('word_re', 'system') . ': ' . $this->objTrimStrings->strTrim($postInfo['post_title'], 50) . '</span> <br />' . $postInfo['firstname'] . ' ' . $postInfo['surname'] . '<br/>' . $this->objTranslatedDate->getDifference($postInfo['datecreated']) . ' </div>
                <div class="postText"  id="' . $postInfo['post_id'] . '" >' . $this->objWashoutFilters->parseText($postInfo['post_text']) . '<span class="' . $forumID . '" ></span></div>';
//                                $return .= $conteiner;
                                //get inner post details
                                $innerAttachments = $this->objPostAttachments->getAttachments($postInfo['post_id']);

                                foreach ($innerAttachments AS $attachment) {
                                        $files = $this->objPostAttachments->downloadAttachment($attachment['id']);
                                        if (count($files) > 0) {
                                                $this->objFiles = $this->getObject('dbfile', 'filemanager');
                                                //$this->objFiles->getFullFilePath($files[0]['id']);
                                                $attachment_path = $this->objFiles->getFilePath($files[0]['id']);
//                                        $objIcon->setIcon('download');
                                                $downloadlink = new link($attachment_path);
                                                $downloadlink->cssClass = "forumDownload";
                                                $downloadlink->target = '_blank';
                                                $this->objIcon->setIcon('download');
                                                $downloadlink->link = $this->objLanguage->languageText('phrase_downloadattachment', 'system');
                                                $this->objIcon->setIcon('view');
                                                $viewLink = new link('javascript:void(0);');
                                                $viewLink->title = $this->objLanguage->languageText('phrase_viewattachment', 'system');
                                                $viewLink->cssClass = "forumViewAttachment";
                                                $viewLink->cssId = $attachment['id'];
                                                $viewLink->link = $this->objLanguage->languageText('phrase_viewattachment', 'system');
                                                $conteiner .= $this->objFileIcons->getFileIcon($attachment['filename']) . "&nbsp;&nbsp; <label >{$attachment['filename']}</label> <br/><br/>" . $downloadlink->show() . '&nbsp; &nbsp;' . $viewLink->show() . '<br/> ' . "<div class='file-preview' id='{$attachment['id']}' >" . $this->objFilePreview->previewFile($attachment['attachment_id']) . '</div><br />';
                                                //header('Content-Disposition: attachment; filename="' . $files[0]['filename'] . '"');
                                                //readfile($location);
                                                //--header('Location:'.$location); // Todo - Force Download
                                        }
                                }
                                if ($this->showRatings) {
                                        if ($innerPost['userid'] != $this->objUser->userId()) {
                                                $conteiner .= '<span class="ratings" >&nbsp;' . $ratingsDiv . '</span>';
                                        }
                                }
                                $return .='
                </div></div>
                <br/> <br/> <br/>' . $conteiner;
                        }
                }
                //Check if replies allowed
                if ($this->repliesAllowed) {
                        /**
                         * @DISPLAYPOSTREPLYFORM
                         */
                        // Get the Post
                        $post = $this->getPostWithText($post['post_id']);
                        // Get details of the Forum
                        $forum = $this->objForum->getForum($post['forum_id']);



                        // Do not show form if forum is locked
                        if ($forum['forumlocked'] == 'Y') {
                                return NULL;
                        } else {
                                // Generate Temporary Id
                                $temporaryId = $this->objUser->userId() . '_' . mktime();

                                // Set Mode to New
                                $mode = 'new';

                                // Check if Title has Re: attached to it
                                if (substr($post['post_title'], 0, 3) == 'Re:') {
                                        // If it does, simply strip slashes
                                        $defaultTitle = stripslashes($post['post_title']);
                                        $originalTitle = stripslashes($post['post_title']);
                                } else {
                                        // Else strip slashes AND append Re: to the title
                                        $defaultTitle = 'Re: ' . stripslashes($post['post_title']);
                                        $originalTitle = 'Re: ' . stripslashes($post['post_title']);
                                }

                                // If result of server-side validation, change default title to posted one
                                if ($mode == 'fix') {
                                        // Select Posted Title
                                        $defaultTitle = $details['title'];
                                }

                                // Load Classes Needed
                                $this->loadClass('form', 'htmlelements');
                                $this->loadClass('textinput', 'htmlelements');
                                $this->loadClass('textarea', 'htmlelements');
                                $this->loadClass('button', 'htmlelements');
                                $this->loadClass('dropdown', 'htmlelements');
                                $this->loadClass('label', 'htmlelements');
                                $this->loadClass('radio', 'htmlelements');
                                $this->loadClass('htmlheading', 'htmlelements');
                                $this->loadClass('iframe', 'htmlelements');


                                // Start of Form
                                $postReplyForm = new form('postReplyForm', $this->uri(array('action' => 'savepostreply', 'type' => $this->forumtype)));
                                $postReplyForm->displayType = 3;
                                $postReplyForm->addRule('title', $this->objLanguage->languageText('mod_forum_addtitle', 'forum'), 'required');


                                $addTable = $this->newObject('htmltable', 'htmlelements');
                                $addTable->width = '99%';
                                $addTable->align = 'center';
                                $addTable->cellpadding = 10;


                                $addTable->startRow();
                                $subjectLabel = new label($this->objLanguage->languageText('word_subject', 'system') . ':', 'input_title');
                                $addTable->addCell($subjectLabel->show(), 100);

                                $titleInput = new textinput('title');
                                $titleInput->size = 50;

                                $titleInput->value = htmlspecialchars($defaultTitle);

                                $addTable->addCell($titleInput->show());

                                $addTable->endRow();

                                // type of post
                                $addTable->startRow();

                                $addTable->addCell('<nobr>' . $this->objLanguage->languageText('mod_forum_typeofreply', 'forum') . ':</nobr>', 100);

                                $objElement = new radio('replytype');
                                $objElement->addOption('reply', $this->objLanguage->languageText('mod_forum_postasreply', 'forum'));
                                $objElement->addOption('tangent', $this->objLanguage->languageText('mod_forum_postastangent', 'forum'));
                                //$objElement->addOption('moderate','Post Reply as Moderator');

                                if ($mode == 'fix') {
                                        $objElement->setSelected($details['replytype']);
                                } else {
                                        $objElement->setSelected('reply');
                                }
                                $objElement->setBreakSpace('<br />');

                                $objElement->extra = ' onclick="clearForTangent()"';


                                $addTable->addCell($objElement->show());

                                $addTable->endRow();

                                $addTable->startRow();

                                $languageLabel = new label($this->objLanguage->languageText('word_language', 'system') . ':', 'input_language');
                                $addTable->addCell($languageLabel->show(), 100);

                                $languageList = new dropdown('language');
                                $languageCodes = & $this->getObject('languagecode', 'language');

                                // Sort Associative Array by Language, not ISO Code
                                asort($languageCodes->iso_639_2_tags->codes);

                                foreach ($languageCodes->iso_639_2_tags->codes as $key => $value) {
                                        $languageList->addOption($key, $value);
                                }

                                $languageList->setSelected($languageCodes->getISO($this->objLanguage->currentLanguage()));

                                $addTable->addCell($languageList->show());

                                $addTable->endRow();

                                $addTable->startRow();

                                $addTable->addCell($this->objLanguage->languageText('word_message') . ':', 140);

                                $editor = &$this->newObject('htmlarea', 'htmlelements');
                                $editor->setName('message');
                                $editor->setContent('');
                                $editor->setRows(20);
                                $editor->setColumns('100');

                                $objContextCondition = &$this->getObject('contextcondition', 'contextpermissions');
                                $this->isContextLecturer = $objContextCondition->isContextMember('Lecturers');

                                if ($this->contextCode == '') {
                                        $editor->context = FALSE;
                                } else if ($this->isContextLecturer || $objContextCondition->isAdmin()) {
                                        $editor->context = TRUE;
                                } else {
                                        $editor->context = FALSE;
                                }

                                $addTable->addCell($editor->show());

                                $addTable->endRow();

                                // ------------------------------

                                $addTable->startRow();

                                $addTable->addCell(' ');

                                $submitButton = new button('submitbutton', $this->objLanguage->languageText('word_submit'));
                                $submitButton->cssClass = 'save';
//                                $submitButton->extra = ' onclick="SubmitForm()"';
//                                }
                        }

//                        $postReplyForm->addToForm($addTable);
                        // IE is not getting values from hidden textinputs...hence we pass them via session vars
                        $this->setSession('temporaryId', $temporaryId);

//            return $this->showTangentJavaScript($defaultTitle).$postReplyForm->show();

                        /**
                         * @END
                         */
//                        $this->loadClass('textarea', 'htmlelements');
                        $textArea = new textarea('commentsArea');
                        $link = new link('javascript:void(0)'/* $this->uri(array('action' => 'postreply', 'id' => $post['post_id'], 'type' => $this->forumtype)) */);
                        $textArea->cssClass = "miniReply";
                        $textArea->cssId = $post['post_id'];
                        $textArea->extra = "placeholder='{$this->objLanguage->languageText('mod_forum_postreply', 'forum')}'";
                        $link->cssClass = "buttonLink postReplyLink";
                        $link->cssId = $post['post_id'];
                        $link->link = $this->objLanguage->languageText('mod_forum_postreply', 'forum');

                        /**
                         * @var object The attachment link
                         */
                        $attachmentLink = new link("javascript:void(0);");
                        $this->objIcon->setIcon('attachment');
                        $attachmentLink->cssClass = "attachmentLink buttonLink";
                        $attachmentLink->link = 'Add attachment';

//                        $this->loadClass('linkparser', 'htmlelements');
//                        $randerObject = $this->getObject('linkparser', 'htmlelements');

                        $attachmentObject = $this->getObject('selectfile', 'filemanager');
                        $attachmentObject->showClearInputJavaScript();
                        $attachmentObject->cssClass = "popUp";
                        //wrap the atachment object in a div
                        $divAttachmentWrapper = "<div class='attachmentwrapper' > <br/> &nbsp;&nbsp;&nbsp;" . $attachmentObject->show() . "</div>";
                        $return .= '</div><br/><div class="clone" id="' . $post['postid'] . '" > <div class="innerReplyDiv" >' . $forumID . '<span class="topicid" id="' . $post['topic_id'] . '"  ></span></div><div  ><span class="level" id="' . $post['level'] . '" ></span><span class="forumid" id="' . $topicInfo['forum_id'] . '" ></span><span class="lang" id="' . $post['language'] . '" ></span><span class="lft" id="' . $post['lft'] . '" ></span><div class=" Inner" ><strong><span class="posttitle" id=" ' . $post['post_title'] . '" ></span></div><div class="content" ></div>';
                        //add the attachment link if attachments are enabled in the forum
                        if ($forum['attachments'] == 'Y') {
                                $return .= $divAttachmentWrapper . $attachmentLink->show();
                        }
                        $return .= '<br/>' . $link->show() . '</div><br/>' . '
                </div> <br/> <br/></div></div>';
                }
                $return .= "</div>";

//                $return .= '<hr />';
                // Check if other languages exist
                if (isset($post['anotherlanguage']) && $post['anotherlanguage'] != '') {
                        $link = new link('javascript:loadTranslation(\'' . $post['post_id'] . '\', \'' . $post['language'] . '\');');
                        $link->link = $this->objLanguageCode->getLanguage($post['language']) . ' (' . strtoupper($post['language']) . ')';

                        $return .= $this->objLanguage->languageText('mod_forum_postmadein', 'forum') . ' <strong>' . $link->show() . '</strong>. ';

                        // Start text
                        $return .= $this->objLanguage->languageText('mod_forum_alsoavailablein', 'forum') . ' ';

                        // Get list of languages
                        $languages = $this->objPostText->getPostLanguages($post['post_id'], $post['language']);

                        $comma = '';

                        // Loop through the languages
                        foreach ($languages as $language) {
                                $link = new link('javascript:loadTranslation(\'' . $post['post_id'] . '\', \'' . $language['language'] . '\');');
                                $link->href = $this->uri(array('action' => 'viewtranslation', 'id' => $language['id'], 'type' => $this->forumtype));
                                $link->link = $this->objLanguageCode->getLanguage($language['language']) . ' (' . strtoupper($language['language']) . ')';

                                $return .= $comma . $link->show();
                                $comma = ', ';
                        }

                        // Add a full stop for courtesy
                        $return .= '. ';
                } else {
                        
                }

                if ($this->forumLocked == FALSE) {
                        $translateLink = & $this->getObject('link', 'htmlelements');
                        $translateLink->href = $this->uri(array('action' => 'translate', 'id' => $post['post_id'], 'type' => $this->forumtype));
                        $translateLink->link = $this->objLanguage->languageText('mod_forum_translatepost', 'forum');
                }


                $return .= '</div>' . "\r\n"; // End newForumContent
                $return .= '</div>' . "\r\n"; // End newForumContainer
                // Start of the contractible area for children of the posts
                if ($makeContractible) {
                        $return .= '<div id="' . $post['post_id'] . '_child" style="display:block; ">' . "\r\n";
                        $this->numOpenThreadDisplayDivs +=1;
                }

                // Load scriptaclous since we can no longer guarantee it is there
                $scriptaculous = $this->getObject('scriptaculous', 'prototype');
                $this->appendArrayVar('headerParams', $scriptaculous->show('text/javascript'));
                // Load JavaScript Function
                $this->appendArrayVar('headerParams', $this->getTranslationAjaxScript());
                return $return . $this->getjavascriptFile('effects.js', 'forum');
//         }
        }

        /**
         * This function checks whether it is OK for the user to edit his/her post
         * Criteria includes:
         * - User had to post this post
         * - Post has to be under 30 minutes old
         *
         * In this module, we also check that No One has replied to the post yet, but that check is done elsewhere with SQL
         *
         * @param $postDate Date the Post was made
         * @param $postUser User who made the Post
         *
         * @return boolean True if it is ok to edit| False
         */
        function checkOkToEdit($postDate, $postUser) {
                $diff = $this->objDateFunctions->dateDifference($postDate, strftime('%Y-%m-%d %H:%M:%S', mktime()));

                if ($diff['d'] == 0 && $diff['h'] == 0 && $diff['m'] < 30 && $postUser == $this->userId) {
                        return TRUE;
                } else {
                        return FALSE;
                }
        }

        /**
         * Method to Display a thread with indentations
         * @param string $topic_id Record Id of the Topic
         * @return string Thread
         */
        function displayThread($topic_id) {
                $posts = $this->getThread($topic_id);

                $thread = '';

                if (count($posts) == 1) {
                        $showExpandable = FALSE;
                } else {
                        $showExpandable = TRUE;
                }

                foreach ($posts AS $post) {
                        $thread .= $this->displayPost($post, TRUE, $showExpandable);
                }

                // Close any open Divs
                if ($this->numOpenThreadDisplayDivs > 0) {
                        for ($i = 1; $i <= $this->numOpenThreadDisplayDivs; $i++) {
                                $thread .= '</div>';
                        }
                }
                return $thread;
        }

        /**
         * Method to display a topic in a flat format
         * @param string $topic_id Record Id of the topic
         * @return Formatted thread ready for display.
         */
        function displayFlatThread($topic_id) {
                $posts = $this->getFlatThread($topic_id);

                $thread = '';

                if (count($posts) == 1) {
                        $showExpandable = FALSE;
                } else {
                        $showExpandable = FALSE;
                }

                foreach ($posts AS $post) {
                        $thread .= $this->displayPost($post, FALSE, $showExpandable, $topic_id);
                }

                // Close any open Divs
                if ($this->numOpenThreadDisplayDivs > 0) {
                        for ($i = 1; $i <= $this->numOpenThreadDisplayDivs; $i++) {
                                $thread .= '</div>';
                        }
                }
                return $thread;
        }

        /**
         * Method to get all posts in a topic, and convert them into a tree-like format
         * @param string $topic_id Record Id of the Topic
         * @param string $highlightPostId Record Id of the Post to highlight
         * @return string The entire tree in a string format.
         */
        function generateTopicPostsTree($topic_id, $highlightPostId) {
                // Get all posts for the topic
                $threadArray = $this->getThread($topic_id);

                $this->loadClass('treemenu', 'tree');
                $this->loadClass('treenode', 'tree');
                $this->loadClass('dhtml', 'tree');
                $this->loadClass('htmllist', 'tree');

                if ($threadArray[0]['post_id'] == $highlightPostId) {
                        $highlightPost = TRUE;
                } else {
                        $highlightPost = FALSE;
                }

                $treeMenu = & new treemenu();
                $rootNode = & new treenode($this->generateNodeText($threadArray[0], $highlightPost));

                // start an array
                $nodeArray = array();

                // Reference the array element with the record id
                $nodeArray[$threadArray[0]['post_id']] = & $rootNode;

                // Remove the first item
                array_shift($threadArray);

                // In the forum's case, each topic has only one root.
                // This approach of adding children is done in that way.
                // Loop through the posts
                foreach ($threadArray as $thread) {
                        if ($thread['post_id'] == $highlightPostId) {
                                $highlightPost = TRUE;
                        } else {
                                $highlightPost = FALSE;
                        }

                        $thisnode = & new treenode($this->generateNodeText($thread, $highlightPost));
                        $nodeArray[$thread['post_parent']]->addItem($thisnode);

                        // Reference the array element with the record id
                        $nodeArray[$thread['post_id']] = & $thisnode;
                }

                $treeMenu->addItem($rootNode);

                $tree = &new htmllist($treeMenu, array('images' => $this->objSkin->getSkinURL() . 'treeimages/imagesAlt2'), FALSE);
                //$this->appendArrayVar('headerParams', '<script src="modules/tree/resources/TreeMenu.js" language="JavaScript" type="text/javascript"></script>');

                return $tree->getMenu();
        }

        /**
         * Method to generate a tree node text for the forum
         * Has more to do with the formatting of the array.
         * @param array $post Post with all the fields in an array
         * @param boolean $highlightPost Flag on whether to highlight the post or not
         * @return array Properly formatted array ready to be used as a node
         */
        function generateNodeText($post, $highlightPost = FALSE) {
                $text = stripslashes($this->trimstrObj->strTrim(strip_tags(trim($post['post_text'])), 60));

                // Additional Filter to remove line breaks
                $text = str_replace("\r\n", ' ', $text);

                if ($this->objDateTime->formatDateOnly($post['datelastupdated']) == date('j F Y')) {
                        $datefield = $this->objLanguage->languageText('mod_forum_todayat', 'forum') . ' ' . $this->objDateTime->formatTime($post['datelastupdated']);
                } else {
                        $datefield = $this->objDateTime->formatDateOnly($post['datelastupdated']) . ' - ' . $this->objDateTime->formatTime($post['datelastupdated']);
                }

                if ($highlightPost) {
                        $cssClass = 'confirm';

                        if ($this->showFullName) {
                                $treenode = '<strong> ' . $post['firstname'] . ' ' . $post['surname'] . '</strong> -  <em>' . trim($text) . '</em> (' . $datefield . ')';
                        } else {
                                $treenode = '<strong> ' . $post['username'] . '</strong> -  <em>' . trim($text) . '</em> (' . $datefield . ')';
                        }
                } else {
                        $cssClass = NULL;
                        $link = & $this->getObject('link', 'htmlelements');
                        $link->href = $this->uri(array('action' => 'viewtopic', 'id' => $post['topic_id'], 'post' => $post['post_id']));
                        $link->link = $text;

                        if ($this->showFullName) {
                                $treenode = '<strong> ' . $post['firstname'] . ' ' . $post['surname'] . '</strong> -  <em>' . $link->show() . '</em> (' . $datefield . ')';
                        } else {
                                $treenode = '<strong> ' . $post['username'] . '</strong> -  <em>' . $link->show() . '</em> (' . $datefield . ')';
                        }
                }

                $icon = & $this->getObject('geticon', 'htmlelements');
                $icon->setIcon('gotopost', NULL, 'modules/forum');

                return array('text' => $treenode, 'cssClass' => $cssClass, 'icon' => 'gotopost.gif');
        }

        /**
         * Method to get the details of the last post in a forum
         * @param string forum Record Id of the forum
         * @return array
         */
        function getLastPost($forum) {
                $sql = 'SELECT tbl_forum_post_text. * , tbl_forum_post.topic_id, tbl_users.firstname, tbl_users.surname, tbl_users.username
        FROM tbl_forum_post INNER JOIN tbl_forum_post_text ON ( tbl_forum_post_text.post_id = tbl_forum_post.id AND tbl_forum_post_text.original_post=\'1\')
        INNER JOIN tbl_forum_topic ON ( tbl_forum_post.topic_id = tbl_forum_topic.id )
        LEFT  JOIN tbl_users ON ( tbl_forum_post.userId = tbl_users.userId )
        WHERE tbl_forum_topic.forum_id = \'' . $forum . '\'
        ORDER BY tbl_forum_post.datelastupdated DESC LIMIT 1';

                $results = $this->getArray($sql);

                if (count($results) == 1) {
                        return $results[0];
                } else {
                        return FALSE;
                }
        }

        /**
         * gets the last n posts in a forum
         * @param type $forum
         * @param type $limit
         * @return type 
         */
        function getLastNPosts($forum, $limit = 10) {
                $sql = 'SELECT distinct tbl_forum_post_text. * , tbl_forum_post.topic_id, tbl_users.firstname, tbl_users.surname, tbl_users.username
        FROM tbl_forum_post INNER JOIN tbl_forum_post_text ON ( tbl_forum_post_text.post_id = tbl_forum_post.id AND tbl_forum_post_text.original_post=\'1\')
        INNER JOIN tbl_forum_topic ON ( tbl_forum_post.topic_id = tbl_forum_topic.id )
        LEFT  JOIN tbl_users ON ( tbl_forum_post.userId = tbl_users.userId )
        WHERE tbl_forum_topic.forum_id = \'' . $forum . '\'
        ORDER BY tbl_forum_post.datelastupdated DESC LIMIT 10';

                return $this->getArray($sql);
        }

        /**
         * Method to get the last 10 posts in a workgroup
         * All formatting is done here
         * @param string $workgroup Record Id of the Workgroup
         * @param string $content Context Code of the Workgroup
         * @param int $limit Number of Records to get
         */
        function getWorkGroupPosts($workgroup, $context, $limit = 10) {
                // Get the workgroups forum record id
                $workgroupForum = $this->objForum->getWorkgroupForum($context, $workgroup);

                // If none exists, return a message saying no records
                // no forum is automatically created here. left until someone actually visits the forum
                if ($workgroupForum == NULL) {
                        return '<div class="noRecordsMessage">' . $this->objLanguage->languageText('mod_forum_nopostsinforum', 'forum') . '</div>';
                } else {
                        // If forum exists, get the last '10' posts or whatever limit is specified
                        $sql = 'SELECT tbl_forum_post_text. * , tbl_forum_post.topic_id, tbl_users.firstname, tbl_users.surname
            FROM tbl_forum_post INNER JOIN tbl_forum_post_text ON ( tbl_forum_post_text.post_id = tbl_forum_post.id AND tbl_forum_post_text.original_post=\'1\')
            INNER JOIN tbl_forum_topic ON ( tbl_forum_post.topic_id = tbl_forum_topic.id )
            LEFT  JOIN tbl_users ON ( tbl_forum_post.userId = tbl_users.userId )
            WHERE tbl_forum_topic.forum_id = \'' . $workgroupForum . '\'
            ORDER BY tbl_forum_post.dateLastUpdated DESC LIMIT ' . $limit;

                        $results = $this->getArray($sql);

                        // If there are no posts, return a message saying no recordss
                        if (count($results) == 0) {
                                return '<div class="noRecordsMessage">' . $this->objLanguage->languageText('mod_forum_nopostsinforum', 'forum') . '</div>';
                        } else {

                                // Else prepare a table to display results
                                $objTable = $this->getObject('htmltable', 'htmlelements');
                                $objTable->cellpadding = 5;
                                $objTable->cellspacing = 1;

                                $objTable->startHeaderRow();
                                $objTable->addHeaderCell($this->objLanguage->languageText('mod_forum_topicconversation', 'forum'));
                                $objTable->addHeaderCell($this->objLanguage->languageText('word_message'), '60%');
                                $objTable->addHeaderCell($this->objLanguage->languageText('word_author'), 100);
                                $objTable->endHeaderRow();

                                // Load Link class
                                $link = $this->loadClass('link', 'htmlelements');

                                // loop through posts
                                foreach ($results as $post) {
                                        $objTable->startRow();
                                        // title is link to the posts
                                        $titleLink = new link($this->uri(array('action' => 'viewtopic', 'id' => $post['topic_id'], 'type' => 'workgroup', 'post' => $post['post_id']), 'forum'));
                                        $titleLink->link = $post['post_title'];
                                        $titleLink->anchor = $post['post_id'];
                                        $objTable->addCell($titleLink->show());


                                        // Chop the post to 130 characters to give user a glimpse of the post
                                        $text = stripslashes($this->trimstrObj->strTrim(strip_tags(trim($post['post_text'])), 130));
                                        // Additional Filter to remove line breaks
                                        $text = str_replace("\r\n", ' ', $text);

                                        $objTable->addCell($text);
                                        $objTable->addCell('<nobr>' . $post['firstname'] . ' ' . $post['surname'] . '</nobr>');
                                        $objTable->endRow();
                                }

                                // Return the finished table
                                return $objTable->show();
                        }
                }
        }

        /**
         * Method to display a post reply form
         * This is being standardised as a function to prevent duplication,
         * @param string $postId Record Id of the Post
         * @param boolean $showCancel Flag whether to show a Cancel button or not.
         * @return string Completed Form for posting a reply
         */
        function showPostReplyForm($postId, $showCancel = TRUE) {
                // Get the Post
                $post = $this->getPostWithText($postId);
                // Get details of the Forum
                $forum = $this->objForum->getForum($post['forum_id']);


                // Do not show form if forum is locked
                if ($forum['forumlocked'] == 'Y') {
                        return NULL;
                } else {
                        // Generate Temporary Id
                        $temporaryId = $this->objUser->userId() . '_' . mktime();

                        // Set Mode to New
                        $mode = 'new';

                        // Check if Title has Re: attached to it
                        if (substr($post['post_title'], 0, 3) == 'Re:') {
                                // If it does, simply strip slashes
                                $defaultTitle = stripslashes($post['post_title']);
                                $originalTitle = stripslashes($post['post_title']);
                        } else {
                                // Else strip slashes AND append Re: to the title
                                $defaultTitle = 'Re: ' . stripslashes($post['post_title']);
                                $originalTitle = 'Re: ' . stripslashes($post['post_title']);
                        }

                        // If result of server-side validation, change default title to posted one
                        if ($mode == 'fix') {
                                // Select Posted Title
                                $defaultTitle = $details['title'];
                        }

                        // Load Classes Needed
                        $this->loadClass('form', 'htmlelements');
                        $this->loadClass('textinput', 'htmlelements');
                        $this->loadClass('textarea', 'htmlelements');
                        $this->loadClass('button', 'htmlelements');
                        $this->loadClass('dropdown', 'htmlelements');
                        $this->loadClass('label', 'htmlelements');
                        $this->loadClass('radio', 'htmlelements');
                        $this->loadClass('htmlheading', 'htmlelements');
                        $this->loadClass('iframe', 'htmlelements');


                        // Start of Form
                        $postReplyForm = new form('postReplyForm', $this->uri(array('action' => 'savepostreply', 'type' => $this->forumtype)));
                        $postReplyForm->displayType = 3;
                        $postReplyForm->addRule('title', $this->objLanguage->languageText('mod_forum_addtitle', 'forum'), 'required');


                        $addTable = $this->newObject('htmltable', 'htmlelements');
                        $addTable->width = '99%';
                        $addTable->align = 'center';
                        $addTable->cellpadding = 10;


                        $addTable->startRow();
                        $subjectLabel = new label($this->objLanguage->languageText('word_subject', 'system') . ':', 'input_title');
                        $addTable->addCell($subjectLabel->show(), 100);

                        $titleInput = new textinput('title');
                        $titleInput->size = 50;

                        $titleInput->value = htmlspecialchars($defaultTitle);

                        $addTable->addCell($titleInput->show());

                        $addTable->endRow();

                        // type of post
                        $addTable->startRow();

                        $addTable->addCell('<nobr>' . $this->objLanguage->languageText('mod_forum_typeofreply', 'forum') . ':</nobr>', 100);

                        $objElement = new radio('replytype');
                        $objElement->addOption('reply', $this->objLanguage->languageText('mod_forum_postasreply', 'forum'));
                        $objElement->addOption('tangent', $this->objLanguage->languageText('mod_forum_postastangent', 'forum'));
                        //$objElement->addOption('moderate','Post Reply as Moderator');

                        if ($mode == 'fix') {
                                $objElement->setSelected($details['replytype']);
                        } else {
                                $objElement->setSelected('reply');
                        }
                        $objElement->setBreakSpace('<br />');

                        $objElement->extra = ' onclick="clearForTangent()"';


                        $addTable->addCell($objElement->show());

                        $addTable->endRow();

                        $addTable->startRow();

                        $languageLabel = new label($this->objLanguage->languageText('word_language', 'system') . ':', 'input_language');
                        $addTable->addCell($languageLabel->show(), 100);

                        $languageList = new dropdown('language');
                        $languageCodes = & $this->getObject('languagecode', 'language');

                        // Sort Associative Array by Language, not ISO Code
                        asort($languageCodes->iso_639_2_tags->codes);

                        foreach ($languageCodes->iso_639_2_tags->codes as $key => $value) {
                                $languageList->addOption($key, $value);
                        }

                        $languageList->setSelected($languageCodes->getISO($this->objLanguage->currentLanguage()));

                        $addTable->addCell($languageList->show());

                        $addTable->endRow();

                        $addTable->startRow();

                        $addTable->addCell($this->objLanguage->languageText('word_message') . ':', 140);

                        $editor = &$this->newObject('htmlarea', 'htmlelements');
                        $editor->setName('message');
                        $editor->setContent('');
                        $editor->setRows(20);
                        $editor->setColumns('100');

                        $objContextCondition = &$this->getObject('contextcondition', 'contextpermissions');
                        $this->isContextLecturer = $objContextCondition->isContextMember('Lecturers');

                        if ($this->contextCode == '') {
                                $editor->context = FALSE;
                        } else if ($this->isContextLecturer || $objContextCondition->isAdmin()) {
                                $editor->context = TRUE;
                        } else {
                                $editor->context = FALSE;
                        }

                        $addTable->addCell($editor->show());

                        $addTable->endRow();

                        // ------------------

                        if ($forum['attachments'] == 'Y') {
                                $addTable->startRow();

                                /*                $attachmentsLabel = new label($this->objLanguage->languageText('mod_forum_attachments', 'forum').':', 'attachments');
                                  $addTable->addCell($attachmentsLabel->show(), 100);

                                  $attachmentIframe = new iframe();
                                  $attachmentIframe->width='100%';
                                  $attachmentIframe->height='100';
                                  $attachmentIframe->frameborder='0';
                                  $attachmentIframe->src= $this->uri(array('module' => 'forum', 'action' => 'attachments', 'id'=>$temporaryId, 'forum' => $forum['id'], 'type'=>$this->forumtype));

                                  $addTable->addCell($attachmentIframe->show());
                                 */

                                $attachmentsLabel = new label($this->objLanguage->languageText('mod_forum_attachments', 'forum') . ':', 'attachments');
                                $addTable->addCell($attachmentsLabel->show(), 120);

                                $form = new form('saveattachment', $this->uri(array('action' => 'saveattachment')));

                                $objSelectFile = $this->newObject('selectfile', 'filemanager');
                                $objSelectFile->name = 'attachment';
                                $form->addToForm($objSelectFile->show());


                                $hiddenTypeInput = new textinput('discussionType');
                                $hiddenTypeInput->fldType = 'hidden';
                                $hiddenTypeInput->value = $post['type_id'];
                                $form->addToForm($hiddenTypeInput->show());


                                $hiddenTangentInput = new textinput('parent');
                                $hiddenTangentInput->fldType = 'hidden';
                                $hiddenTangentInput->value = $post['post_id'];
                                $form->addToForm($hiddenTangentInput->show());

                                $topicHiddenInput = new textinput('topic');
                                $topicHiddenInput->fldType = 'hidden';
                                $topicHiddenInput->value = $post['topic_id'];
                                $form->addToForm($topicHiddenInput->show());

                                $hiddenForumInput = new textinput('forum');
                                $hiddenForumInput->fldType = 'hidden';
                                $hiddenForumInput->value = $forum['id'];
                                $form->addToForm($hiddenForumInput->show());

                                $hiddenTemporaryId = new textinput('temporaryId');
                                $hiddenTemporaryId->fldType = 'hidden';
                                $hiddenTemporaryId->value = $temporaryId;
                                $form->addToForm($hiddenTemporaryId->show());

                                $addTable->addCell($form->show());
                                $addTable->endRow();
                        }

                        // ------------------------------
                        // Show Forum Subscriptions if enabled

                        if ($forum['subscriptions'] == 'Y') {
                                // Get the number of topics a user is subscribed to
//                                $numTopicSubscriptions = $this->objTopicSubscriptions->getNumTopicsSubscribed($post['forum_id'], $this->objUser->userId());
//
//                                // Check whether the user is subscribed to the current topic
//                                $topicSubscription = $this->objTopicSubscriptions->isSubscribedToTopic($post['topic_id'], $this->objUser->userId());
//
//                                // Check whether the user is subscribed to the current forum
//                                $forumSubscription = $this->objForumSubscriptions->isSubscribedToForum($post['forum_id'], $this->objUser->userId());
//
//                                $addTable->startRow();
//                                $addTable->addCell($this->objLanguage->languageText('mod_forum_emailnotification', 'forum', 'Email Notification') . ':');
////                                $subscriptionsRadio = new radio('subscriptions');
////                                $subscriptionsRadio->addOption('nosubscriptions', $this->objLanguage->languageText('mod_forum_donotsubscribetothread', 'forum', 'Do not subscribe to this thread'));
////                                $subscriptionsRadio->addOption('topicsubscribe', $this->objLanguage->languageText('mod_forum_notifytopic', 'forum', 'Notify me via email when someone replies to this thread'));
////                                $subscriptionsRadio->addOption('forumsubscribe', $this->objLanguage->languageText('mod_forum_notifyforum', 'forum', 'Notify me of ALL new topics and replies in this forum.'));
////                                $subscriptionsRadio->setBreakSpace('<br />');
//
//                                if ($forumSubscription) {
//                                        $subscriptionsRadio->setSelected('forumsubscribe');
//                                        $subscribeMessage = $this->objLanguage->languageText('mod_forum_youaresubscribedtoforum', 'forum', 'You are currently subscribed to the forum, receiving notification of all new posts and replies.');
//                                } else if ($topicSubscription) {
//                                        $subscriptionsRadio->setSelected('topicsubscribe');
//                                        $subscribeMessage = $this->objLanguage->languageText('mod_forum_youaresubscribedtotopic', 'forum', 'You are already subscribed to this topic.');
//                                } else {
//                                        $subscriptionsRadio->setSelected('nosubscriptions');
//                                        $subscribeMessage = $this->objLanguage->languageText('mod_forum_youaresubscribedtonumbertopic', 'forum', 'You are currently subscribed to [NUM] topics.');
//                                        $subscribeMessage = str_replace('[NUM]', $numTopicSubscriptions, $subscribeMessage);
//                                }
//
//                                $div = '<div class="forumTangentIndent">' . $subscribeMessage . '</div>';
//
//                                $addTable->addCell($subscriptionsRadio->show() . $div);
//                                $addTable->endRow();
                        }

                        // ------------------------------

                        $addTable->startRow();

                        $addTable->addCell(' ');

//                        $submitButton = new button('submitbutton', $this->objLanguage->languageText('word_submit'));
//                        $submitButton->cssClass = 'save';
//                        $submitButton->extra = ' onclick="SubmitForm()"';
                        //$submitButton->setToSubmit();
//                        if ($showCancel) {
//                                $cancelButton = new button('cancel', $this->objLanguage->languageText('word_cancel'));
//                                $returnUrl = $this->uri(array('action' => 'thread', 'id' => $post['topic_id'], 'type' => $this->forumtype));
//                                $cancelButton->setOnClick("window.location='$returnUrl'");
//
//                                $addTable->addCell($submitButton->show() . ' / ' . $cancelButton->show());
//                        } else {
//
//
//                                $addTable->addCell($submitButton->show());
//                        }
//                        $addTable->endRow();
//
//                        $postReplyForm->addToForm($addTable);
                        // IE is not getting values from hidden textinputs...hence we pass them via session vars
//                        $this->setSession('temporaryId', $temporaryId);
//                        return $this->showTangentJavaScript($defaultTitle) . $postReplyForm->show();
                }
        }

        /**
         * Method to show the javascript used for warning the user if the title is the same when replying with a tangent
         *
         * @string $title Tit
         */
        function showTangentJavaScript($title) {
                $script = "
<script type=\"text/javascript\">
function clearForTangent()
{
    postTitle = \"" . addslashes($title) . "\";


    if (document.forms[\"postReplyForm\"].replytype[1].checked)
    {

        if (document.forms[\"postReplyForm\"].title.value == \"" . addslashes($title) . "\".split(\"'\").join(\"\'\"))
        {
            alert ('" . $this->objLanguage->languageText('mod_forum_tangentsowntitles', 'forum') . " \"" . addslashes($title) . "\".\\n" . $this->objLanguage->languageText('mod_forum_changetitle', 'forum') . ".');
            document.forms[\"postReplyForm\"].title.value = '';
            document.forms[\"postReplyForm\"].title.focus();


        }
    }

    if (document.forms[\"postReplyForm\"].replytype[0].checked)
    {
        if (document.forms[\"postReplyForm\"].title.value == '')
        {
            document.forms[\"postReplyForm\"].title.value = postTitle.split(\"'\").join(\"\'\");
            document.forms[\"postReplyForm\"].title.focus();
        }
    }


}
</script>

        ";

                return $script;
        }

        /**
         * Method to get the Record Id of the first post in a topic
         * @param string $topic_id Record Id of the Topic
         * @return string Id if it exists, else FALSE
         */
        function getIdFirstPostInTopic($topic_id) {
                $sql = 'SELECT id FROM tbl_forum_post WHERE topic_id=\'' . $topic_id . '\' ORDER BY post_order LIMIT 1';
                $results = $this->getArray($sql);

                if (count($results) > 0) {
                        return $results[0]['id'];
                } else {
                        return FALSE;
                }
        }

        /**
         * Method to move a post from one tangent to another.
         * @param string $originalTopic Record Id of the Topic which needs to be updated
         * @param string $newTopic Topic to whom the tangents will be moved to
         */
        function movePostTangent($originalTopic, $newTopic) {
                $originalPostId = $this->getIdFirstPostInTopic($originalTopic);
                $newPostId = $this->getIdFirstPostInTopic($newTopic);

                return $this->update('id', $originalPostId, array('post_tangent_parent' => $newPostId));
        }

        /**
         * Method to remove a tangent setting from a post
         * @param string $topic Record Id of the Topic
         */
        function removeTangentParent($topic) {
                $post = $this->getIdFirstPostInTopic($topic);

                return $this->update('id', $post, array('post_tangent_parent' => '0'));
        }

        /**
         * Method to get the forum details of a post
         * @param string $post_id Record Id of the post
         * @return array Details of the forum
         */
        function getPostForumDetails($post_id) {
                $sql = 'SELECT tbl_forum.* FROM tbl_forum_post
        INNER JOIN tbl_forum_topic ON ( tbl_forum_post.topic_id = tbl_forum_topic.id)
        INNER JOIN tbl_forum ON ( tbl_forum_topic.forum_id = tbl_forum.id)
        WHERE tbl_forum_topic.id = \'' . $post_id . '\' GROUP BY tbl_forum_post.id LIMIT 1';

                $forum = $this->getArray($sql);

                if (count($forum) == 1) {
                        return $forum[0];
                } else {
                        return FALSE;
                }
        }

        /**
         * Function to Check that the Left and Right Values of a Tree are in tact.
         * This is related to the modified preorder tree traversal algorithm.
         * If the topic is 'broken', it calls the function to fix it.
         * It does a whole series of checks to ensure the topic is not broken
         *
         * @param string $topic Record Id of the Topic
         */
        function detectBrokenTopic($topic) {

                // Get Root Post
                $rootPost = $this->getRootPost($topic);

                // Start Broken Detection
                // Left value of first post MUST be one
                if ($rootPost['postleft'] != 1) {
                        // If not, topic is broken - start fixing tree
                        $this->rebuildTopic($topic);
                        return;
                        break;
                }

                // Left value of first post MUST be one
                if ($rootPost['post_order'] != 1) {
                        // If not, topic is broken - start fixing tree
                        $this->rebuildTopic($topic);
                        return;
                        break;
                }

                // Right value of first post can never be zero
                if ($rootPost['postright'] == 0) {
                        // If not, topic is broken - start fixing tree
                        $this->rebuildTopic($topic);
                        return;
                        break;
                }

                // Right value of first post can never be less than left value
                if ($rootPost['postright'] < $rootPost['postleft']) {
                        // If not, topic is broken - start fixing tree
                        $this->rebuildTopic($topic);
                        return;
                        break;
                }

                // Right value of first post can never be an odd number
                if ($rootPost['postright'] % 2 != 0) {
                        // If not, topic is broken - start fixing tree
                        $this->rebuildTopic($topic);
                        return;
                        break;
                }

                // Get Num Children
                $numChildren = $this->getRecordCount(' WHERE topic_id = "' . $topic . '"');

                // Right Value of First Post divided by two equals the number of topics in the post.
                if ($rootPost['postright'] / 2 != $numChildren) {
                        // If not, topic is broken - start fixing tree
                        $this->rebuildTopic($topic);
                        return;
                        break;
                }

                // Check if there are any duplicate Left Values
                $leftSql = 'SELECT t1.id  FROM tbl_forum_post t1, tbl_forum_post t2 WHERE (t1.lft = t2.lft) AND t1.topic_id = \'' . $topic . '\' AND t2.topic_id = \'' . $topic . '\' AND t1.id != t2.id GROUP BY t1.lft';
                $leftResults = $this->getArray($leftSql);

                if (count($leftResults) > 0) {
                        // If not, topic is broken - start fixing tree
                        $this->rebuildTopic($topic);
                        return;
                        break;
                }

                // Check if there are any duplicate Right Values
                $rightSql = 'SELECT t1.id  FROM tbl_forum_post t1, tbl_forum_post t2 WHERE (t1.rght = t2.rght) AND t1.topic_id = \'' . $topic . '\' AND t2.topic_id = \'' . $topic . '\' AND t1.id != t2.id GROUP BY t1.lft';
                $rightResults = $this->getArray($rightSql);

                if (count($rightResults) > 0) {
                        // If not, topic is broken - start fixing tree
                        $this->rebuildTopic($topic);
                        return;
                        break;
                }

                // If not, topic is not broken
                return;
        }

        /**
         * Method to start a process of rebuilding a forum topic tree
         * @param string $topic Record Id of the Topic
         */
        function rebuildTopic($topic) {
                // Get Root Post
                $rootPost = $this->getRootPost($topic);

                // Check if Valid Topic
                if ($rootPost != FALSE) {
                        $this->_rebuild_tree($rootPost['post_id'], 1, 1);
                }

                return;
        }

        /**
         * Method to Rebuild a Tree
         *
         * This function recursives itself to update the left and right values of a tree
         *
         * @access private
         * @param string $parent Record Id of the Parent post
         * @param int $left Left Value of the Parent
         * @param int $level Level of the Post
         */
        function _rebuild_tree($parent, $left, $level) {
                // the right value of this node is the left value + 1
                $right = $left + 1;

                // get all children of this node
                $result = $this->getAll(' WHERE  post_parent=\'' . $parent . '\'');

                foreach ($result as $row) {
                        $right = $this->_rebuild_tree($row['id'], $right, $level + 1);
                }

                //echo 'Id - '.$parent.', left - '.$left.', right - '.$right.'<br />';

                $this->update('id', $parent, array('lft' => $left, 'rght' => $right, 'level' => $level));

                // return the right value of this node + 1
                return $right + 1;
        }

        /**
         * Method to Build a get the Replies to a post and build them as a tree
         * JavaScript automatically sent to header
         * @param string $post_id Record Id of the Post
         * @return string DHTML Treee
         */
        function buildChildTree($post_id) {
                $post = $this->getRow('id', $post_id);

                $childPosts = $this->getChildPostsSQL($post['topic_id'], $post['lft'], $post['rght']);

                $this->loadClass('treemenu', 'tree');
                $this->loadClass('treenode', 'tree');
                $this->loadClass('dhtml', 'tree');
                $this->loadClass('htmllist', 'tree');

                $treeMenu = new treemenu();

                $nodeArray = array();

                foreach ($childPosts as $childPost) {
                        $node = & new treenode($this->generateNodeText($childPost));
                        $nodeArray[$childPost['post_id']] = & $node;

                        if ($childPost['post_parent'] == $post_id) {
                                $treeMenu->addItem($node);
                        } else {
                                if (array_key_exists($childPost['post_parent'], $nodeArray)) {
                                        $nodeArray[$childPost['post_parent']]->addItem($node);
                                }
                        }
                }

                $tree = &new htmllist($treeMenu, array('images' => $this->objSkin->getSkinURL() . 'treeimages/imagesAlt2'), FALSE);

                //$this->appendArrayVar('headerParams', '<script src="modules/tree/resources/TreeMenu.js" language="JavaScript" type="text/javascript"></script>');
                return $tree->getMenu();
        }

        function getChildPostsSQL($topic, $left, $right) {
                $sql = 'SELECT tbl_forum_post.*, tbl_forum_post_text.*, tbl_users.firstname, tbl_users.surname FROM tbl_forum_post
        INNER JOIN tbl_forum_post_text ON ( tbl_forum_post_text.post_id = tbl_forum_post.id AND tbl_forum_post_text.original_post=\'1\')
        LEFT  JOIN tbl_users ON ( tbl_forum_post.userId = tbl_users.userId )
        WHERE topic_id=\'' . $topic . '\' AND lft>' . $left . ' AND rght<' . $right . ' ORDER BY lft';

                return $this->getArray($sql);
        }

        function deletePostAndReplies($post_id) {
                $post = $this->getRow('id', $post_id);

                $childPosts = $this->getChildPostsSQL($post['topic_id'], $post['lft'], $post['rght']);

                foreach ($childPosts as $childPost) {
                        $this->delete('id', $childPost['post_id']);
                }

                $this->delete('id', $post_id);

                $this->rebuildTopic($post['topic_id']);

                return;
        }

        /**
         * Method to return the javascript used for ajax translation of posts
         * @return string
         */
        function getTranslationAjaxScript() {
                return '<script type="text/javascript">
//<![CDATA[

function loadTranslation(post, lang) {
    var url = \'index.php\';
    var pars = \'module=forum&action=loadtranslation&id=\'+post+\'&lang=\'+lang;
    var myAjax = new Ajax.Request( url, {method: \'get\', parameters: pars, onLoading:function(response)
    {
        Effect.SlideUp($(\'text_\'+post));
        $(\'loading_\'+post).innerHTML = \'<span class="dim"><img src="skins/_common/icons/loader.gif" /> Loading Translation</span><br /><br />\';
        Effect.Appear($(\'loading_\'+post), {queue:\'end\'});
    } , onComplete: function(response) {
        Effect.Appear($(\'text_\'+post), {queue:\'end\'});
        $(\'text_\'+post).innerHTML = response.responseText;
        Effect.SlideUp($(\'loading_\'+post));
    }} );
}

//]]>
</script>';
        }

        /**
         * Method to get the last post in a topic
         * @param string $topicid Record Id of the Topic
         * @return string Record Id of the last post
         */
        function getLastTopicPost($topicid) {
                $results = $this->getAll(' WHERE topic_id = "' . $topicid . '" ORDER BY tbl_forum_post.dateLastUpdated DESC LIMIT 1');

                if (count($results) == 0) {
                        return FALSE;
                } else {
                        return $results[0]['id'];
                }
        }

        /**
         * Method to get the amount of posts in a topic
         * @param string $topicid Record Id of the Topic
         * @return int Number of Posts
         */
        function getNumPostsInTopic($topicid) {
                return $this->getRecordCount(' WHERE topic_id = "' . $topicid . '"');
        }

        /**
         * Insert a post into the database
         *
         * @param string $post_parent: Record ID of the Parent Post - that the user is replying to
         * @param string $post_tangent_parent: Record ID of the Tangent Post - that the user is replying to
         * @param string $post_title: Title of Post
         * @param string $post_message: Text of the Post
         * @param string $topic_id: Record ID of the Topic
         * @param string $userId: User ID of person posting the post
         * @param string $dateLastUpdated: Date Post was made
         * @return string $this->getLastInsertId()
         */
        function insertSingleAPI($post_parent, $post_tangent_parent, $forum_id, $topic_id, $userId, $level = 1) {
                // Interim measure. Alternative, use regexp and replace with space
                //$post_title = strip_tags($post_title);
                //echo $post_parent;

                if ($post_parent == '0') {
                        // $lastRightPointer = $this->getLastRightPointer($forum_id);
                        //  $leftPointer = $lastRightPointer+1;
                        //  $rightPointer = $lastRightPointer+2;
                        $level = 1;
                } else {
                        /*  $lastRightPointer = $this->getPostRightPointer($post_parent);
                          $updateRightSQL = 'UPDATE tbl_forum_post SET rght = rght + 2 WHERE rght > '.($lastRightPointer-1);
                          $this->getArray($updateRightSQL);
                          $updateLeftSQL = 'UPDATE tbl_forum_post SET lft = lft + 2 WHERE lft > '.($lastRightPointer-1);
                          $this->getArray($updateLeftSQL);

                          $leftPointer = $lastRightPointer;
                          $rightPointer = $lastRightPointer+1; */
                        $level += 1;
                }

                $this->insert(array(
                    'post_parent' => $post_parent,
                    'post_tangent_parent' => $post_tangent_parent,
                    'topic_id' => $topic_id,
                    'post_order' => $this->getLastPostOrder($topic_id),
                    'userId' => $userId,
                    'datecreated' => $this->now(),
                    'lft' => null,
                    'rght' => null,
                    'level' => $level,
                    'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
                ));

                return $this->getLastInsertId();
        }
        
        /**
         * Save eMail to be sent at later stage
         * 
         * @param string $postParent
         * @param string $post_title
         * @param string $post_text
         * @param string $forum_name
         * @param string $user_id
         * @param string $replyUrl address top be included in the notification email
         * @return boolean TRUE/FALSE
         */
        function saveMailJob($postParent, $post_title, $post_text, $forum_name, $user_id, $replyUrl){
                $fields = array(
                    'post_parent'=>$postParent,
                    'post_title'=>$post_title,
                    'post_text'=>$post_text,
                    'forum_name'=>$forum_name,
                    'user_id'=>$user_id,
                    'reply_url'=>$replyUrl,
                    'sent'=>FALSE
                );
                $result = $this->insert($fields,'tbl_forum_mailjobs');
                return $result;
        }

}

?>