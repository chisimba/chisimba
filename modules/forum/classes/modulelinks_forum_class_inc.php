<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_forum extends object
{

    public function init()
    {
        $this->loadClass('treenode','tree');
        $this->objForum =& $this->getObject('dbforum', 'forum');
        $this->objTopics =& $this->getObject('dbtopic', 'forum');
        $this->objPost =& $this->getObject('dbpost', 'forum');
    }
    
    public function show()
    {
        $rootNode = new treenode (array('link'=>$this->uri(NULL, 'forum'), 'text'=>'Forum', 'preview'=>'sffas'));
        
        $nodesArray = array();
        
        $forums = $this->objForum->getContextForums();
        
        foreach ($forums as $forum)
        {
            $node =& new treenode(array('link'=>$this->uri(array('action'=>'forum', 'id'=>$forum['id'])), 'text'=>$forum['forum_name']));
            
            $nodesArray['forum_'.$forum['id']] =& $node;
            $rootNode->addItem($nodesArray['forum_'.$forum['id']]);
            
            $topics = $this->objTopics->showTopicsInForum($forum['id'], '1');
            
            foreach ($topics as $topic)
            {
                $node =& new treenode(array('link'=>$this->uri(array('action'=>'viewtopic', 'id'=>$topic['topic_id'])), 'text'=>$topic['post_title']));
                
                $nodesArray['topic_'.$topic['topic_id']] =& $node;
                $nodesArray['post_'.$topic['first_post']] =& $node;
                $nodesArray['forum_'.$topic['forum_id']]->addItem($nodesArray['topic_'.$topic['topic_id']]);
                
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

        $forums = $this->objForum->getContextForums($contextCode);
        
        foreach ($forums as $forum)
        {
            
            $forumArray = array();    
            $forumArray['menutext'] = $forum['forum_name'];
            $forumArray['description'] = $forum['forum_name'];
            $forumArray['itemid'] = $forum['id'];
            $forumArray['moduleid'] = 'forum';
            $forumArray['params'] = array('action' => 'forum','id' => $forum['id']);
            $bigArr[] = $forumArray;
            
            $topics = $this->objTopics->showTopicsInForum($forum['id'], '1');
            
            foreach ($topics as $topic)
            {
                $topicArray = array();    
                $topicArray['menutext'] = 'Topic - '.$topic['post_title'];
                $topicArray['description'] = 'topic description';
                $topicArray['itemid'] = $topic['topic_id'];
                $topicArray['moduleid'] = 'forum';
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
                        $postArray['moduleid'] = 'forum';
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
