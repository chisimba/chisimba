<?php
  // security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Forum Topics Read Table
* This class controls all functionality relating to the tbl_forum_topic_read table which records topics the user has views
* @author Tohir Solomons
* @copyright (c) 2004 University of the Western Cape
* @package forum
* @version 1
*/
/**
* This class controls the functionality for marking topics as read when the usr has viewed it. It takes note of the topic
* and the last post in the topic. This allows us to mark a topic as read, but when a reply has been posted to that topic,
* it get marks as Read - but has new replies which you haven't read yet.
*/
class dbtopicread extends dbTable
 {

	/**
	* Constructor method to define the table(default)
	*/
	function init()
	{
		parent::init('tbl_forum_topic_read');
        $this->objTopic = $this->getObject('dbtopic');
    }
    
    /**
    * Method to mark a topic as read. Internally, it first goes and fetch the last post
    * @param string $topic Record Id of the topic
    * @param string $userId Record Id of the User
    */
    function markTopicRead ($topic, $userId)
    {
        // Get list of existing markings for a topic by the user
        $list = $this->getAll(' WHERE topic_id="'.$topic.'" AND userId = "'.$userId.'"');
        
        // Delete these existing markings
        foreach ($list AS $item) {
            $this->delete('id', $item['id']);
        }
        
        // Get details of the topic so as to get the last post
        $topicArray = $this->objTopic->listSingle($topic);
        
        // Mark the topic as read
        $this->insertRead($topic, $userId, $topicArray['last_post']);
        
        return ;
    }
    
    /**
    * Method to insert a record into the database, method is called by above
    * @param string $topic Record Id of the topic
    * @param string $userId Record Id of the user
    * @param string $post_id Record Id of the post
    */
    function insertRead($topic, $userId, $post_id)
    {
        return $this->insert(array('topic_id' => $topic, 'userId' => $userId, 'post_id' => $post_id));
    }
    
}
?>