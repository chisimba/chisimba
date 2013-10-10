<?php
  // security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Forum Statistics Table
* This class contains SQL statements to generate statistics for the discussion forums
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package forum
* @version 1
*/
class forumstats extends dbTable
 {

	/**
	* Constructor method to define the table(default)
	*/
	function init()
	{
		parent::init('tbl_forum');
    }

    /**
    * Method to get the number of topics and posts in a forum
    *
    * @param string $forum_id Record Id of the forum
    * @param string $context Context the Forum belongs to
    * @return array Statistics for the forum
    */
    function getStats ($forum_id, $context)
    {
        $sql = 'SELECT tbl_forum.id AS forum_id, count(DISTINCT tbl_forum_topic.id) AS topics,
        count(tbl_forum_topic.replies) as posts from  tbl_forum_post
        RIGHT JOIN tbl_forum_topic ON (tbl_forum_post.topic_id = tbl_forum_topic.id)
        RIGHT JOIN tbl_forum ON (tbl_forum_topic.forum_id = tbl_forum.id)
        WHERE forum_visible="Y" AND tbl_forum.forum_context = "'.$context.'" AND tbl_forum.id = "'.$forum_id.'"';

        $sql .= ' GROUP BY tbl_forum.id';

        $results = $this->getArray($sql);

        if (count($results) > 0) {
            return $results[0];
        } else {
            return FALSE;
        }
    }

    /**
    * Method to get the number of tangents in a forum
    *
    * @param string $forum_id Record Id of the forum
    * @return int Tangent Number
    */
    function getTangentsNum($forum_id)
    {
        $sql = 'SELECT count( topic_tangent_parent ) AS tangents FROM tbl_forum_topic WHERE topic_tangent_parent != "0" AND forum_id = "'.$forum_id.'"';

        $sql .= ' GROUP BY tbl_forum_topic.forum_id';

        $results = $this->getArray($sql);

        if (count($results) > 0) {
            return $results[0];
        } else {
            return FALSE;
        }
    }

    /**
    * Method to get the number of users who have made posts in a forum
    *
    * @param string $forum_id Record Id of the forum
    * @param string $context Context the Forum belongs to
    * @return array Statistics for the forum
    */
    function getPosters ($id, $context)
    {
        $sql = 'SELECT count(DISTINCT tbl_forum_post.userId) AS posters from  tbl_forum_post
        RIGHT JOIN tbl_forum_topic ON (tbl_forum_post.topic_id = tbl_forum_topic.id)
        RIGHT JOIN tbl_forum ON (tbl_forum_topic.forum_id = tbl_forum.id)
        WHERE forum_visible="Y" AND tbl_forum.forum_context = "'.$context.'" AND tbl_forum.id = "'.$id.'"';

        $sql .= ' GROUP BY tbl_forum.id';

        $results = $this->getArray($sql);

        if (count($results) > 0) {
            return $results[0];
        } else {
            return FALSE;
        }
    }

    /**
    * Method to get the number of posts a user has made in the forum
    *
    * @param string $forum_id Record Id of the forum
    * @param string $context Context the Forum belongs to
    * @return array List of Users with number of posts they have made
    */
    function getPostersNum ($forum_id, $context)
    {
        $sql = 'SELECT count(DISTINCT(tbl_forum_post.id)) as posts, tbl_users.userId, firstName, surname
        FROM tbl_forum_post
        INNER JOIN tbl_forum_topic ON ( tbl_forum_post.topic_id = tbl_forum_topic.id )
        INNER JOIN tbl_forum ON ( tbl_forum_topic.forum_id = tbl_forum.id )
        INNER JOIN tbl_users ON ( tbl_forum_post.userId = tbl_users.userId )
        WHERE forum_visible = "Y" AND tbl_forum.forum_context = "'.$context.'" AND tbl_forum.id = "'.$forum_id.'"
        GROUP BY tbl_users.userId ORDER BY firstName DESC';

        return $this->getArray($sql);
    }

    /**
    * Method to get the number of topics a user has started in the forum
    *
    * @param string $forum_id Record Id of the forum
    * @param string $context Context the Forum belongs to
    * @return array List of Users with number of topics they have started
    */
    function getPosterTopics ($forum_id, $context)
    {
        $sql = 'SELECT tbl_users.userId, firstName, surname, count( tbl_forum_topic.userId ) AS topics
        FROM tbl_forum_topic
        RIGHT JOIN tbl_forum ON ( tbl_forum_topic.forum_id = tbl_forum.id )
        INNER JOIN tbl_users ON ( tbl_forum_topic.userId = tbl_users.userId )
        WHERE forum_visible = "Y" AND tbl_forum.forum_context = "'.$context.'" AND tbl_forum.id = "'.$forum_id.'"
        GROUP BY tbl_forum_topic.userId
        ORDER BY firstName';
        return $this->getArray($sql);
    }

    /**
    * Method to get the number of tangents a user has started in the forum
    *
    * @param string $forum_id Record Id of the forum
    * @param string $context Context the Forum belongs to
    * @return array List of Users with number of tangents they have started
    */
    function getPosterTangents ($forum_id, $context)
    {
        $sql = 'SELECT tbl_users.userId, firstName, surname, count( tbl_forum_topic.userId ) AS tangents
        FROM tbl_forum_topic
        RIGHT JOIN tbl_forum ON ( tbl_forum_topic.forum_id = tbl_forum.id )
        INNER JOIN tbl_users ON ( tbl_forum_topic.userId = tbl_users.userId )
        WHERE tbl_forum_topic.topic_tangent_parent != "0" AND forum_visible = "Y" AND tbl_forum.forum_context = "'.$context.'" AND tbl_forum.id = "'.$forum_id.'"
        GROUP BY tbl_forum_topic.userId
        ORDER BY firstName';
        return $this->getArray($sql);
    }

    /**
    * Method to get statistics on the ratings that users have given for the posts of otherss
    * It includes the minimum, maximum ratings theu have given, plus the number of posts they have rated.
    *
    * @param string $forum_id Record Id of the forum
    * @return array List of Users with stats on how they rated other posts
    */
    function getUserRateOtherPosts($forum_id)
    {
        $sql = 'SELECT tbl_forum_post_ratings.userId, COUNT( tbl_forum_post_ratings.userId ) AS postsrated, SUM( rating_value ) AS totalvalue, MAX( rating_value ) AS highvalue, MIN( rating_value ) AS minvalue
        FROM tbl_forum_post_ratings
        INNER JOIN tbl_forum_ratings_forum ON ( tbl_forum_post_ratings.rating = tbl_forum_ratings_forum.id )
        WHERE tbl_forum_ratings_forum.forum_id = "'.$forum_id.'"
        GROUP BY tbl_forum_post_ratings.userId';

        return $this->getArray($sql);
    }

    /**
    * Method to get statistics on the ratings that users have received for their posts
    * It includes the minimum, maximum ratings they have given, plus the number of posts on which they have been rated.
    *
    * @param string $forum_id Record Id of the forum
    * @return array List of Users with stats on how they were rated for their posts
    */
    function getUserRateSelfPosts($forum_id)
    {
        $sql = 'SELECT tbl_forum_post.userId, COUNT( tbl_forum_post.userId ) AS postsrated, SUM( rating_value ) AS totalvalue, MAX( rating_value ) AS highvalue, MIN( rating_value ) AS minvalue
        FROM tbl_forum_post_ratings
        INNER JOIN tbl_forum_post ON ( tbl_forum_post_ratings.post_id = tbl_forum_post.id )
        INNER JOIN tbl_forum_ratings_forum ON ( tbl_forum_post_ratings.rating = tbl_forum_ratings_forum.id )
        WHERE tbl_forum_ratings_forum.forum_id = "'.$forum_id.'"
        GROUP BY tbl_forum_post.userId';
        return $this->getArray($sql);
    }

    /**
    * Method to get a the amount of words users have used in their posts
    * It includes the minimum, maximum words they have used, plus the total number of words used.
    *
    * @param string $forum_id Record Id of the forum
    * @return array List of Users with stats on their word count in posts.
    */
    function getUserWordCount($forum_id)
    {
        $sql = 'SELECT tbl_forum_post.userId, COUNT( wordcount ) AS postscounted, SUM( wordcount ) AS totalwords, MAX( wordcount ) AS highvalue, MIN( wordcount ) AS minvalue
        FROM tbl_forum_post_text
        INNER JOIN tbl_forum_post ON ( tbl_forum_post_text.post_id = tbl_forum_post.id )
        INNER JOIN tbl_forum_topic ON ( tbl_forum_topic.id = tbl_forum_post.topic_id )
        WHERE tbl_forum_topic.forum_id = "'.$forum_id.'" AND original_post = "1"  AND wordcount >0
        GROUP BY tbl_forum_post.userId';
        return $this->getArray($sql);
    }

    function getNumContextForums($context = NULL)
    {
        $sql = 'SELECT COUNT(DISTINCT tbl_forum.id) AS forumcount FROM tbl_forum
        WHERE forum_visible="Y" ';

        if ($context != NULL) {
            $sql .= ' AND forum_context ="'.$context.'"';
        }

        $results = $this->getArray($sql);

        return $results[0]['forumcount'];
    }

    /**
    * Method to get the number of topics in all forums for a context.
    * If no context is provided, it checks all forums.
    * Note: Only visible forums are considered
    * @param string $context Context Code to check
    * @return integer Number of topics in that context
    */
    function getNumContextTopics($context = NULL)
    {
        $sql = 'SELECT COUNT(DISTINCT tbl_forum_topic.id) AS topiccount FROM tbl_forum_topic
        INNER JOIN tbl_forum ON (tbl_forum_topic.forum_id = tbl_forum.id)
        WHERE forum_visible="Y" ';

        if ($context != NULL) {
            $sql .= ' AND forum_context ="'.$context.'"';
        }

        //$sql .= ' GROUP BY tbl_forum_topic.id';

        $results = $this->getArray($sql);

        return $results[0]['topiccount'];
    }

    /**
    * Method to get the number of posts in all forums for a context.
    * If no context is provided, it checks all forums.
    * Note: Only visible forums are considered
    * @param string $context Context Code to check
    * @return integer Number of posts in that context
    */
    function getNumContextPosts($context = NULL)
    {
        $sql = 'SELECT COUNT(DISTINCT tbl_forum_post.id) AS postcount FROM tbl_forum_post
        INNER JOIN tbl_forum_topic ON ( tbl_forum_topic.id = tbl_forum_post.topic_id )
        INNER JOIN tbl_forum ON (tbl_forum_topic.forum_id = tbl_forum.id)
        WHERE forum_visible="Y" ';

        if ($context != NULL) {
            $sql .= ' AND forum_context ="'.$context.'"';
        }

        //$sql .= ' GROUP BY tbl_forum_post.id';

        $results = $this->getArray($sql);

        return $results[0]['postcount'];
    }

    /**
    * Method to Show a Sumamry of Stats for All Forums. Used by Thetha
    * Used for the Thetha Forums at UWC
    */
    function show()
    {
        $fieldset = $this->newObject('fieldset', 'htmlelements');
        //$fieldset->setLegend('Forum Statistics');
        //$fieldset->addContent('asfafs');



        $str = 'Our users have posted a total of <strong>'.$this->getNumContextTopics().' topics</strong> having <strong>'.$this->getNumContextPosts().' posts</strong> in <strong>'.$this->getNumContextForums().' forums</strong>.';

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

        return '<h3>Forum Statistics</h3>'.$fieldset->show();
    }


 }

?>