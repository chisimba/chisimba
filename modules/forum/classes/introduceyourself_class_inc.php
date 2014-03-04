<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
/**
* Introduce Yourself
* This class provides forum functionality to the introduceyourself module
* @author Tohir Solomons
* @copyright (c) 2005 University of the Western Cape
* @package forum
* @version 1
*/
class introduceyourself extends object
{
    /**
    * var string $_introTypeId: Type Id for the 'Introduce Your Self' Discussion Type
    */
    var $_introTypeId;
    /**
    * var array $_topic: Record Id for the Introduce Yourself
    */
    var $_topic;
    
    /**
    * Constructor
    */
    function init()
    {
        $this->objForum =& $this->getObject('dbforum');
        $this->objTopic =& $this->getObject('dbtopic');
        $this->objPost =& $this->getObject('dbpost');
        $this->objPostText =& $this->getObject('dbposttext');
        $this->objDiscussionTypes =& $this->getObject('dbdiscussiontypes');
        
        
        $this->objUser =& $this->getObject('user', 'security');
        
        
        
        $this->userId = '1';
        
        $this->_checkTopicExists();
    }
    
    /**
    * Method to check if a Introduce Yourself topic exists, else create one.
    * Sets topic details to a global variable
    */
    function _checkTopicExists()
    {
        // Get the Topic Discussion Type - 'Introduce Yourself'
        $type = $this->objDiscussionTypes->getRow('type_icon', 'introduceyourself');
        $this->_introTypeId = $type['id'];
        
        // Get the Default Forum for a Context - auto creates one if necessary
        $forum_id = $this->objForum->getContextForum(); // Get the Default forum
        
        // Get the Number of Topics that are 'Introduce Yourself' in that forum
        $numIntro = $this->objTopic->getRecordCount(' WHERE forum_id="'.$forum_id.'" AND type_id="'.$this->_introTypeId.'" ');
        
        // IF there are no Introduce Yourself Topics - Create One
        if ($numIntro == '0') {
            //echo 'need to insert';
            $topic_id = $this->objTopic->insertSingle($forum_id, $this->_introTypeId, 0, $this->userId, 'Introduce Your Self');
            $this->objForum->updateLastTopic($forum_id, $topic_id);
        
            $post_parent = 0;
            $post_title = 'Introduce Your Self';
            $post_text = 'Please Introduce yourself';
            $language = 'en';
            $original_post = 1; // YES
            $post_tangent_parent = 0;
            
            $post_id = $this->objPost->insertSingle($post_parent, $post_tangent_parent, $forum_id, $topic_id,  $this->userId);
            
            $this->objPostText->insertSingle($post_id, $post_title, $post_text,  $language, $original_post, $this->userId);
            
            $this->objTopic->updateFirstPost($topic_id, $post_id);
            
            $this->objForum->updateLastPost($forum_id, $post_id);
        }
        
        // Now get the Introduce Yourself Topic Details
        $topics = $this->objTopic->getAll('WHERE forum_id="'.$forum_id.'" AND type_id="'.$this->_introTypeId.'" ');
        
        $this->_topic = $topics[0];
    }
    /**
    * Method to save an introduction into the forum
    * @param string $userId Record Id of the User
    * @param string $message Introduction Message by the User
    */
    function insertIntroduceYourself($userId, $message)
    {
        $post_parent = $this->_topic['first_post'];
        $post_tangent_parent = 0;
        
        $parentPostDetails = $this->objPost->getRow('id', $post_parent);
        
        $forum_id = $this->_topic['forum_id'];
        $topic_id = $this->_topic['id'];
        $type_id = $this->_introTypeId;
        $post_title = 'Introduction by '.$this->objUser->fullname($userId);
        $post_text = $message;
        $language = 'en';
        $original_post = 1;
        $level = $parentPostDetails['level'];
        
        $post_id = $this->objPost->insertSingle($post_parent, $post_tangent_parent, $forum_id, $topic_id,  $userId, $level);
        $this->objPostText->insertSingle($post_id, $post_title, $post_text,  $language, $original_post, $userId);
        
        $this->objTopic->updateLastPost($topic_id, $post_id);
        $this->objForum->updateLastPost($forum_id, $post_id);
    
    }
    
    


}
?>