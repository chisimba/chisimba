<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_discussion extends object
{

    public function init()
    {
        $this->loadClass('treenode','tree');
        $this->objDiscussion =& $this->getObject('dbdiscussion', 'discussion');
        $this->objTopics =& $this->getObject('dbtopic', 'discussion');
        $this->objPost =& $this->getObject('dbpost', 'discussion');
    }
    
    public function show()
    {
        $rootNode = new treenode (array('link'=>$this->uri(NULL, 'discussion'), 'text'=>'Discussion', 'preview'=>'sffas'));
        
        $nodesArray = array();
        
        $discussions = $this->objDiscussion->getContextDiscussions();
        
        foreach ($discussions as $discussion)
        {
            $node =& new treenode(array('link'=>$this->uri(array('action'=>'discussion', 'id'=>$discussion['id'])), 'text'=>$discussion['discussion_name']));
            
            $nodesArray['discussion_'.$discussion['id']] =& $node;
            $rootNode->addItem($nodesArray['discussion_'.$discussion['id']]);
            
            $topics = $this->objTopics->showTopicsInDiscussion($discussion['id'], '1');
            
            foreach ($topics as $topic)
            {
                $node =& new treenode(array('link'=>$this->uri(array('action'=>'viewtopic', 'id'=>$topic['topic_id'])), 'text'=>$topic['post_title']));
                
                $nodesArray['topic_'.$topic['topic_id']] =& $node;
                $nodesArray['post_'.$topic['first_post']] =& $node;
                $nodesArray['discussion_'.$topic['discussion_id']]->addItem($nodesArray['topic_'.$topic['topic_id']]);
                
                $posts = $this->objPost->getThread($topic['topic_id']);
                
                foreach ($posts as $post)
                {
                    
                    
                    if ($post['post_parent'] != '0') {
                        $node =& new treenode(array('link'=>$this->uri(array('action'=>'viewtopic', 'id'=>$post['topic_id'], 'post'=>$post['post_id'])), 'text'=>$post['post_title']));
                
                        $nodesArray['post_'.$post['post_id']] =& $node;
                        $nodesArray['post_'.$post['post_parent']]->addItem($nodesArray['post_'.$post['post_id']]);
                    }
                }
            }

        }

        
        return $rootNode;
    }
    
    /**
     * 
     *Method to get a set of links for a context
     *@param string $contextCode
     *@return array
     * @access public
     */
    public function getContextLinks($contextCode)
    {          
        $bigArr = array();

        $discussions = $this->objDiscussion->getContextDiscussions($contextCode);
        
        foreach ($discussions as $discussion)
        {
            
            $discussionArray = array();    
            $discussionArray['menutext'] = $discussion['discussion_name'];
            $discussionArray['description'] = $discussion['discussion_name'];
            $discussionArray['itemid'] = $discussion['id'];
            $discussionArray['moduleid'] = 'discussion';
            $discussionArray['params'] = array('action' => 'discussion','id' => $discussion['id']);
            $bigArr[] = $discussionArray;
            
            $topics = $this->objTopics->showTopicsInDiscussion($discussion['id'], '1');
            
            foreach ($topics as $topic)
            {
                $topicArray = array();    
                $topicArray['menutext'] = 'Topic - '.$topic['post_title'];
                $topicArray['description'] = 'topic description';
                $topicArray['itemid'] = $topic['topic_id'];
                $topicArray['moduleid'] = 'discussion';
                $topicArray['params'] = array('action' => 'viewtopic', 'id' => $topic['topic_id']);
                $bigArr[] = $topicArray;
                
                $posts = $this->objPost->getThread($topic['topic_id']);
                
                foreach ($posts as $post)
                {
                    if ($post['post_parent'] != '0') {
                
                        $postArray = array();    
                        $postArray['menutext'] = 'Post - '.$post['post'];
                        $postArray['description'] = 'Post description';
                        $postArray['itemid'] = $post['topic_id'];
                        $postArray['moduleid'] = 'discussion';
                        $postArray['params'] = array('action' => 'viewtopic', 'post'=>$post['post_id']);
                        $bigArr[] = $postArray;
                    }
                }
            }

        }
        
        return $bigArr;
    }
}
?>
