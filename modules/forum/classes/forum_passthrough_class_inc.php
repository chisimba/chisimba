<?php

class forum_passthrough extends object
{

    /**
    * Constructor
    */
    function init()
    {
        $this->objForum =& $this->getObject('dbforum'); 
        $this->objTopic =& $this->getObject('dbtopic');
        $this->objPost =& $this->getObject('dbpost');
    }
    
    /**
    * Method to determine which context a forum is in
    * @param string $id Record Id of the Forum
    * @return string Context Code
    */
    function getContextFromForum($id)
    {
        $forum = $this->objForum->getForum($id);
        
        if ($forum == FALSE) {
            return FALSE;
        } else {
            return $forum['forum_context'];
        }
    }
    
    /**
    * Method to determine which context a forum is in by ptoviding the topic
    * @param string $id Record Id of the topic
    * @return string Context Code
    */
    function getContextFromTopic($id)
    {
        $forum = $this->objTopic->getTopicForumDetails($id);
        
        if ($forum == FALSE) {
            return FALSE;
        } else {
            return $forum['forum_context'];
        }
    }
    
    /**
    * Method to determine which context a forum is in by ptoviding the post
    * @param string $id Record Id of the post
    * @return string Context Code
    */
    function getContextFromPost($id)
    {
        $forum = $this->objPost->getPostForumDetails($id);
        
        if ($forum == FALSE) {
            return FALSE;
        } else {
            return $forum['forum_context'];
        }
    }

}
?>