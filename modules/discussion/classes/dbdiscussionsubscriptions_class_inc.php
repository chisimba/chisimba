<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* Discussion Subscription Class
*
* This class keeps track of all the discussions users have subscribed to
* Based on their subscription, email notifications are sent to them.
*
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package discussion
* @version 1
*/
class dbdiscussionsubscriptions extends dbtable
{
    
    /**
    * Constructor
    */
    function init()
    {
        parent::init('tbl_discussion_subscribe_discussion');
    }
        
    /**
    * Method to find out if a user is subscribed to a discussion
    *
    * @param string $discussion_id: discussion to get topics from
    * @param string $userId: User Id of the person 
    *
    * @return integer Number of topics user is subscribed to
    */
    function isSubscribedToDiscussion($discussion_id, $userId)
    {
        $sql = 'WHERE discussion_id = "'.$discussion_id.'" AND userid = "'.$userId.'"';
        
        $number = $this->getRecordCount($sql);
        
        if ($number > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    function subscribeUserToDiscussion($discussion_id, $userId)
    {
        return $this->insert(array(
            'discussion_id'=>$discussion_id, 
            'userid'=>$userId, 
            'external'=>'Y', 
            'datecreated'=>strftime('%Y-%m-%d %H:%M:%S', mktime())
        ));
    }
    
    function unsubscribeUserFromDiscussion($discussion_id, $userId)
    {
        $sql = 'DELETE FROM tbl_discussion_subscribe_discussion WHERE discussion_id="'.$discussion_id.'" AND userid="'.$userId.'"';
        return $this->query($sql);
    }
    
    function getUsersSubscribedDiscussion($discussion_id)
    {
        $sql = 'SELECT DISTINCT emailAddress FROM tbl_discussion_subscribe_discussion INNER JOIN tbl_users ON ( tbl_discussion_subscribe_discussion.userid = tbl_users.userid ) WHERE discussion_id = "'.$discussion_id.'"';
        return $this->getArray($sql);
    }
    
    
}
?>