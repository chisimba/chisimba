<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_announcements extends object
{
    public function init()
    {
        $this->loadClass('treenode','tree');
        $this->objAnnouncements = $this->getObject('dbAnnouncements');
    }
    
    public function show()
    {
        // Link to Module itself - First Level
        $rootNode = new treenode (array('link'=>$this->uri(NULL, 'announcements'), 'text'=>'Announcements'));
        
        // Get Announcements - Second Level
        $announces = $this->objAnnouncements->getAll("ORDER BY createdOn");
        
        // Extra Check
        if (count($announces) > 0) {
            
            // Array for References
            $nodesArray = array();
            
            // Loop through Podcasters - second level
            foreach ($announces as $announce)
            {
                // Create Node
                $node =& new treenode(array('link'=>$this->uri(array('action'=>'', 'id'=>$announce['id'])), 'text'=>$announce['title']));
                
                // Create Reference to Node
                $nodesArray['announcements'.$announce['id']] =& $node;
                
                // Attach to Root Node
                $rootNode->addItem($node);
            }
            
        }
        
        // Return Root Node
        return $rootNode;
    }

}
?>