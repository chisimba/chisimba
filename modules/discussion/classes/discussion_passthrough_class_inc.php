<?php

class discussion_passthrough extends object
{

    /**
    * Constructor
    */
    function init()
    {
        $this->objDiscussion =& $this->getObject('dbdiscussion'); 
        $this->objTopic =& $this->getObject('dbtopic');
        $this->objPost =& $this->getObject('dbpost');
    }
    
    /**
    * Method to determine which context a discussion is in
    * @param string $id Record Id of the Discussion
    * @return string Context Code
    */
    function getContextFromDiscussion($id)
    {
        $discussion = $this->objDiscussion->getDiscussion($id);
        
        if ($discussion == FALSE) {
            return FALSE;
        } else {
            return $discussion['discussion_context'];
        }
    }
    
    /**
    * Method to determine which context a discussion is in by ptoviding the topic
    * @param string $id Record Id of the topic
    * @return string Context Code
    */
    function getContextFromTopic($id)
    {
        $discussion = $this->objTopic->getTopicDiscussionDetails($id);
        
        if ($discussion == FALSE) {
            return FALSE;
        } else {
            return $discussion['discussion_context'];
        }
    }
    
    /**
    * Method to determine which context a discussion is in by ptoviding the post
    * @param string $id Record Id of the post
    * @return string Context Code
    */
    function getContextFromPost($id)
    {
        $discussion = $this->objPost->getPostDiscussionDetails($id);
        
        if ($discussion == FALSE) {
            return FALSE;
        } else {
            return $discussion['discussion_context'];
        }
    }

}
?>