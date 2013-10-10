<?php
  // security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Discussion Statistics Table
* This class contains SQL statements to generate statistics for the discussion discussions
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package discussion
* @version 1
*/
class discussionstats extends dbTable
 {

	/**
	* Constructor method to define the table(default)
	*/
	function init()
	{
		parent::init('tbl_discussion');
    }

    /**
    * Method to get the number of topics and posts in a discussion
    *
    * @param string $discussion_id Record Id of the discussion
    * @param string $context Context the Discussion belongs to
    * @return array Statistics for the discussion
    */
    function getStats ($discussion_id, $context)
    {
        $sql = 'SELECT tbl_discussion.id AS discussion_id, count(DISTINCT tbl_discussion_topic.id) AS topics,
        count(tbl_discussion_topic.replies) as posts from  tbl_discussion_post
        RIGHT JOIN tbl_discussion_topic ON (tbl_discussion_post.topic_id = tbl_discussion_topic.id)
        RIGHT JOIN tbl_discussion ON (tbl_discussion_topic.discussion_id = tbl_discussion.id)
        WHERE discussion_visible="Y" AND tbl_discussion.discussion_context = "'.$context.'" AND tbl_discussion.id = "'.$discussion_id.'"';

        $sql .= ' GROUP BY tbl_discussion.id';

        $results = $this->getArray($sql);

        if (count($results) > 0) {
            return $results[0];
        } else {
            return FALSE;
        }
    }

    /**
    * Method to get the number of tangents in a discussion
    *
    * @param string $discussion_id Record Id of the discussion
    * @return int Tangent Number
    */
    function getTangentsNum($discussion_id)
    {
        $sql = 'SELECT count( topic_tangent_parent ) AS tangents FROM tbl_discussion_topic WHERE topic_tangent_parent != "0" AND discussion_id = "'.$discussion_id.'"';

        $sql .= ' GROUP BY tbl_discussion_topic.discussion_id';

        $results = $this->getArray($sql);

        if (count($results) > 0) {
            return $results[0];
        } else {
            return FALSE;
        }
    }

    /**
    * Method to get the number of users who have made posts in a discussion
    *
    * @param string $discussion_id Record Id of the discussion
    * @param string $context Context the Discussion belongs to
    * @return array Statistics for the discussion
    */
    function getPosters ($id, $context)
    {
        $sql = 'SELECT count(DISTINCT tbl_discussion_post.userId) AS posters from  tbl_discussion_post
        RIGHT JOIN tbl_discussion_topic ON (tbl_discussion_post.topic_id = tbl_discussion_topic.id)
        RIGHT JOIN tbl_discussion ON (tbl_discussion_topic.discussion_id = tbl_discussion.id)
        WHERE discussion_visible="Y" AND tbl_discussion.discussion_context = "'.$context.'" AND tbl_discussion.id = "'.$id.'"';

        $sql .= ' GROUP BY tbl_discussion.id';

        $results = $this->getArray($sql);

        if (count($results) > 0) {
            return $results[0];
        } else {
            return FALSE;
        }
    }

    /**
    * Method to get the number of posts a user has made in the discussion
    *
    * @param string $discussion_id Record Id of the discussion
    * @param string $context Context the Discussion belongs to
    * @return array List of Users with number of posts they have made
    */
    function getPostersNum ($discussion_id, $context)
    {
        $sql = 'SELECT count(DISTINCT(tbl_discussion_post.id)) as posts, tbl_users.userId, firstName, surname
        FROM tbl_discussion_post
        INNER JOIN tbl_discussion_topic ON ( tbl_discussion_post.topic_id = tbl_discussion_topic.id )
        INNER JOIN tbl_discussion ON ( tbl_discussion_topic.discussion_id = tbl_discussion.id )
        INNER JOIN tbl_users ON ( tbl_discussion_post.userId = tbl_users.userId )
        WHERE discussion_visible = "Y" AND tbl_discussion.discussion_context = "'.$context.'" AND tbl_discussion.id = "'.$discussion_id.'"
        GROUP BY tbl_users.userId ORDER BY firstName DESC';

        return $this->getArray($sql);
    }

    /**
    * Method to get the number of topics a user has started in the discussion
    *
    * @param string $discussion_id Record Id of the discussion
    * @param string $context Context the Discussion belongs to
    * @return array List of Users with number of topics they have started
    */
    function getPosterTopics ($discussion_id, $context)
    {
        $sql = 'SELECT tbl_users.userId, firstName, surname, count( tbl_discussion_topic.userId ) AS topics
        FROM tbl_discussion_topic
        RIGHT JOIN tbl_discussion ON ( tbl_discussion_topic.discussion_id = tbl_discussion.id )
        INNER JOIN tbl_users ON ( tbl_discussion_topic.userId = tbl_users.userId )
        WHERE discussion_visible = "Y" AND tbl_discussion.discussion_context = "'.$context.'" AND tbl_discussion.id = "'.$discussion_id.'"
        GROUP BY tbl_discussion_topic.userId
        ORDER BY firstName';
        return $this->getArray($sql);
    }

    /**
    * Method to get the number of tangents a user has started in the discussion
    *
    * @param string $discussion_id Record Id of the discussion
    * @param string $context Context the Discussion belongs to
    * @return array List of Users with number of tangents they have started
    */
    function getPosterTangents ($discussion_id, $context)
    {
        $sql = 'SELECT tbl_users.userId, firstName, surname, count( tbl_discussion_topic.userId ) AS tangents
        FROM tbl_discussion_topic
        RIGHT JOIN tbl_discussion ON ( tbl_discussion_topic.discussion_id = tbl_discussion.id )
        INNER JOIN tbl_users ON ( tbl_discussion_topic.userId = tbl_users.userId )
        WHERE tbl_discussion_topic.topic_tangent_parent != "0" AND discussion_visible = "Y" AND tbl_discussion.discussion_context = "'.$context.'" AND tbl_discussion.id = "'.$discussion_id.'"
        GROUP BY tbl_discussion_topic.userId
        ORDER BY firstName';
        return $this->getArray($sql);
    }

    /**
    * Method to get statistics on the ratings that users have given for the posts of otherss
    * It includes the minimum, maximum ratings theu have given, plus the number of posts they have rated.
    *
    * @param string $discussion_id Record Id of the discussion
    * @return array List of Users with stats on how they rated other posts
    */
    function getUserRateOtherPosts($discussion_id)
    {
        $sql = 'SELECT tbl_discussion_post_ratings.userId, COUNT( tbl_discussion_post_ratings.userId ) AS postsrated, SUM( rating_value ) AS totalvalue, MAX( rating_value ) AS highvalue, MIN( rating_value ) AS minvalue
        FROM tbl_discussion_post_ratings
        INNER JOIN tbl_discussion_ratings_discussion ON ( tbl_discussion_post_ratings.rating = tbl_discussion_ratings_discussion.id )
        WHERE tbl_discussion_ratings_discussion.discussion_id = "'.$discussion_id.'"
        GROUP BY tbl_discussion_post_ratings.userId';

        return $this->getArray($sql);
    }

    /**
    * Method to get statistics on the ratings that users have received for their posts
    * It includes the minimum, maximum ratings they have given, plus the number of posts on which they have been rated.
    *
    * @param string $discussion_id Record Id of the discussion
    * @return array List of Users with stats on how they were rated for their posts
    */
    function getUserRateSelfPosts($discussion_id)
    {
        $sql = 'SELECT tbl_discussion_post.userId, COUNT( tbl_discussion_post.userId ) AS postsrated, SUM( rating_value ) AS totalvalue, MAX( rating_value ) AS highvalue, MIN( rating_value ) AS minvalue
        FROM tbl_discussion_post_ratings
        INNER JOIN tbl_discussion_post ON ( tbl_discussion_post_ratings.post_id = tbl_discussion_post.id )
        INNER JOIN tbl_discussion_ratings_discussion ON ( tbl_discussion_post_ratings.rating = tbl_discussion_ratings_discussion.id )
        WHERE tbl_discussion_ratings_discussion.discussion_id = "'.$discussion_id.'"
        GROUP BY tbl_discussion_post.userId';
        return $this->getArray($sql);
    }

    /**
    * Method to get a the amount of words users have used in their posts
    * It includes the minimum, maximum words they have used, plus the total number of words used.
    *
    * @param string $discussion_id Record Id of the discussion
    * @return array List of Users with stats on their word count in posts.
    */
    function getUserWordCount($discussion_id)
    {
        $sql = 'SELECT tbl_discussion_post.userId, COUNT( wordcount ) AS postscounted, SUM( wordcount ) AS totalwords, MAX( wordcount ) AS highvalue, MIN( wordcount ) AS minvalue
        FROM tbl_discussion_post_text
        INNER JOIN tbl_discussion_post ON ( tbl_discussion_post_text.post_id = tbl_discussion_post.id )
        INNER JOIN tbl_discussion_topic ON ( tbl_discussion_topic.id = tbl_discussion_post.topic_id )
        WHERE tbl_discussion_topic.discussion_id = "'.$discussion_id.'" AND original_post = "1"  AND wordcount >0
        GROUP BY tbl_discussion_post.userId';
        return $this->getArray($sql);
    }

    function getNumContextDiscussions($context = NULL)
    {
        $sql = 'SELECT COUNT(DISTINCT tbl_discussion.id) AS discussioncount FROM tbl_discussion
        WHERE discussion_visible="Y" ';

        if ($context != NULL) {
            $sql .= ' AND discussion_context ="'.$context.'"';
        }

        $results = $this->getArray($sql);

        return $results[0]['discussioncount'];
    }

    /**
    * Method to get the number of topics in all discussions for a context.
    * If no context is provided, it checks all discussions.
    * Note: Only visible discussions are considered
    * @param string $context Context Code to check
    * @return integer Number of topics in that context
    */
    function getNumContextTopics($context = NULL)
    {
        $sql = 'SELECT COUNT(DISTINCT tbl_discussion_topic.id) AS topiccount FROM tbl_discussion_topic
        INNER JOIN tbl_discussion ON (tbl_discussion_topic.discussion_id = tbl_discussion.id)
        WHERE discussion_visible="Y" ';

        if ($context != NULL) {
            $sql .= ' AND discussion_context ="'.$context.'"';
        }

        //$sql .= ' GROUP BY tbl_discussion_topic.id';

        $results = $this->getArray($sql);

        return $results[0]['topiccount'];
    }

    /**
    * Method to get the number of posts in all discussions for a context.
    * If no context is provided, it checks all discussions.
    * Note: Only visible discussions are considered
    * @param string $context Context Code to check
    * @return integer Number of posts in that context
    */
    function getNumContextPosts($context = NULL)
    {
        $sql = 'SELECT COUNT(DISTINCT tbl_discussion_post.id) AS postcount FROM tbl_discussion_post
        INNER JOIN tbl_discussion_topic ON ( tbl_discussion_topic.id = tbl_discussion_post.topic_id )
        INNER JOIN tbl_discussion ON (tbl_discussion_topic.discussion_id = tbl_discussion.id)
        WHERE discussion_visible="Y" ';

        if ($context != NULL) {
            $sql .= ' AND discussion_context ="'.$context.'"';
        }

        //$sql .= ' GROUP BY tbl_discussion_post.id';

        $results = $this->getArray($sql);

        return $results[0]['postcount'];
    }

    /**
    * Method to Show a Sumamry of Stats for All Discussions. Used by Thetha
    * Used for the Thetha Discussions at UWC
    */
    function show()
    {
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        //$fieldset->setLegend('Discussion Statistics');
        //$fieldset->addContent('asfafs');



        $str = 'Our users have posted a total of <strong>'.$this->getNumContextTopics().' topics</strong> having <strong>'.$this->getNumContextPosts().' posts</strong> in <strong>'.$this->getNumContextDiscussions().' discussions</strong>.';

        $objOnline = $this->getObject('loggedinusers', 'security');

        $onlineUsers = $objOnline->getRecordCount();

        $str .= ' There are currently <strong>'.$onlineUsers.' users online</strong>.';

        if ($onlineUsers > 0) {
            $str .= '<br /><strong>Users currently online:</strong> ';

            $list = $objOnline->getListOnlineUsers();

            $comma = '';

            foreach ($list as $member) {
                $str .= $comma.$member['username'];

                $comma = ', ';
            }

            $str .= '</p>';
        }


        $fieldset->addContent($str);

        return '<h3>Discussion Statistics</h3>'.$fieldset->show();
    }


 }

?>