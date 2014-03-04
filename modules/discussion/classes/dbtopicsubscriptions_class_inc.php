<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* Topic Subscription Class
*
* This class keeps track of all the topics users have subscribed to
* Based on their subscription, email notifications are sent to them.
*
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package discussion
* @version 1
*/
class dbtopicsubscriptions extends dbtable
{
    
    /**
    * Constructor
    */
    function init()
    {
        parent::init('tbl_discussion_subscribe_topic');
    }
		
	/**
    * Method to find out how many topics (in a discussion) a user is subscribed to by providing the discussion_id and userid
    *
    * @param string $discussion_id: discussion to get topics from
    * @param string $userId: User Id of the person 
    *
    * @return integer Number of topics user is subscribed to
    */
    function getNumTopicsSubscribed($discussion_id, $userId)
	{
        $sql = ' SELECT count( tbl_discussion_subscribe_topic.id ) AS subscribecount
        FROM tbl_discussion_subscribe_topic
        INNER JOIN tbl_discussion_topic ON ( tbl_discussion_subscribe_topic.topic_id = tbl_discussion_topic.id ) 
        WHERE tbl_discussion_topic.discussion_id = "'.$discussion_id.'" AND tbl_discussion_subscribe_topic.userid = "'.$userId.'"';
        
        $number = $this->getArray($sql);
        
        return  $number[0]['subscribecount'];
    }
    
    function subscribeUserToTopic($topic_id, $userId)
    {
        return $this->insert(array(
            'topic_id'=>$topic_id, 
            'userid'=>$userId, 
            'external'=>'Y', 
            'datecreated'=>strftime('%Y-%m-%d %H:%M:%S', mktime())
        ));
    }
    
    /**
    * Method to find out if a user is subscribed to a discussion
    *
    * @param string $discussion_id: discussion to get topics from
    * @param string $userId: User Id of the person 
    *
    * @return integer Number of topics user is subscribed to
    */
    function isSubscribedToTopic($topic_id, $userId)
    {
        $sql = 'WHERE topic_id = "'.$topic_id.'" AND userid = "'.$userId.'"'; 
        
        $number = $this->getRecordCount($sql);
        
        if ($number > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    function getUsersSubscribedTopic($topic_id)
    {
        $sql = 'SELECT DISTINCT emailAddress FROM tbl_discussion_subscribe_topic INNER JOIN tbl_users ON ( tbl_discussion_subscribe_topic.userid = tbl_users.userid ) WHERE topic_id = "'.$topic_id.'"';
        return $this->getArray($sql);
    }
    
    function unsubscribeUserFromTopic($userId,$topic_id){
            $sql = "DELETE FROM tbl_discussion_subscribe_topic WHERE userid='{$userId}' AND topic_id='{$topic_id}'";
            return $this->query($sql);
    }
    
}
?>