<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* Forum Subscription Class
*
* This class keeps track of all the forums users have subscribed to
* Based on their subscription, email notifications are sent to them.
*
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package forum
* @version 1
*/
class dbforumsubscriptions extends dbtable
{
    
    /**
    * Constructor
    */
    function init()
    {
        parent::init('tbl_forum_subscribe_forum');
    }
        
    /**
    * Method to find out if a user is subscribed to a forum
    *
    * @param string $forum_id: forum to get topics from
    * @param string $userId: User Id of the person 
    *
    * @return integer Number of topics user is subscribed to
    */
    function isSubscribedToForum($forum_id, $userId)
    {
        $sql = 'WHERE forum_id = "'.$forum_id.'" AND userid = "'.$userId.'"';
        
        $number = $this->getRecordCount($sql);
        
        if ($number > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    function subscribeUserToForum($forum_id, $userId)
    {
        return $this->insert(array(
            'forum_id'=>$forum_id, 
            'userid'=>$userId, 
            'external'=>'Y', 
            'datecreated'=>strftime('%Y-%m-%d %H:%M:%S', mktime())
        ));
    }
    
    function unsubscribeUserFromForum($forum_id, $userId)
    {
        $sql = 'DELETE FROM tbl_forum_subscribe_forum WHERE forum_id="'.$forum_id.'" AND userid="'.$userId.'"';
        return $this->query($sql);
    }
    
    function getUsersSubscribedForum($forum_id)
    {
        $sql = 'SELECT DISTINCT emailAddress FROM tbl_forum_subscribe_forum INNER JOIN tbl_users ON ( tbl_forum_subscribe_forum.userid = tbl_users.userid ) WHERE forum_id = "'.$forum_id.'"';
        return $this->getArray($sql);
    }
    
    
}
?>