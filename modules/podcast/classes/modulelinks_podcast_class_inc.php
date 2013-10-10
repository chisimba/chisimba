<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_podcast extends object
{
    public function init()
    {
        $this->loadClass('treenode','tree');
        $this->objPodcast = $this->getObject('dbpodcast');
    }
    
    public function show()
    {
        // Link to Module itself - First Level
        $rootNode = new treenode (array('link'=>$this->uri(NULL, 'podcast'), 'text'=>'Podcast'));
        
        // Get Podcasters - Second Level
        $podcasters = $this->objPodcast->listPodcasters();
        
        // Extra Check
        if (count($podcasters) > 0) {
            
            // Array for References
            $nodesArray = array();
            
            // Loop through Podcasters - second level
            foreach ($podcasters as $podcaster)
            {
                // Create Node
                $node =& new treenode(array('link'=>$this->uri(array('action'=>'byuser', 'id'=>$podcaster['userid'])), 'text'=>$podcaster['firstname'].' '.$podcaster['surname']));
                
                // Create Reference to Node
                $nodesArray['podcaster_'.$podcaster['userid']] =& $node;
                
                // Attach to Root Node
                $rootNode->addItem($node);
            }
            
            // Get List of All Podcasts - Third Level
            $podcasts = $this->objPodcast->getAll();
            
            // Loop
            foreach ($podcasts as $podcast)
            {
                // Create Node
                $node =& new treenode(array('link'=>$this->uri(array('action'=>'viewpodcast', 'id'=>$podcast['id'])), 'text'=>$podcast['title']));
                
                // Attach to Parent Via Reference
                $nodesArray['podcaster_'.$podcaster['userid']]->addItem($node);
            }
            
        }
        
        // Return Root Node
        return $rootNode;
    }

}
?>
