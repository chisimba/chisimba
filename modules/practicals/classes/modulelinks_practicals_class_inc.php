<?php

/**
* File modulelinks extends object
*
* @author Yasser Buchana
* @copyright (c) 2007 UWC
* @version 0.1
*/



/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_practical extends object
{

    public function init()
    {
    	$this->objPractical = $this->getObject('dbpracticals', 'practicals');
    	$this->objContext = $this->getObject('dbcontext','context');
    	if($this->objContext->isInContext()){
            $this->contextCode=$this->objContext->getContextCode();
            $this->context=$this->objContext->getTitle();
        }
    }
    /**
     *Do the display
     * @return <type>
     */
     public function show()
    {
        // Link to Module itself - First Level
        $rootNode = new treenode (array('link'=>$this->uri(array('action'=>'practical')), 'text'=>'Practical'));
        
        // Get Practicals - Second Level
        $practicals = $this->objPractical->getPractical($this->contextCode);	 
        
        // Extra Check
        if (count($practicals) > 0) {
            
            // Array for References
            $nodesArray = array();
            
            // Loop through Podcasters - second level
            foreach ($practicals as $practical)
            {
                // Create Node
                $node =& new treenode(array('link'=>$this->uri(array('action'=>'practical')), 'text'=>$practical['name']));
                
                // Create Reference to Node
                $nodesArray['practical'.$practical['id']] =& $node;
                
                // Attach to Root Node
                $rootNode->addItem($node);
            }
            
        }
        
        // Return Root Node
        return $rootNode;
    }
    
    
    
    /**
     * 
     *Method to get a set of links for a context
     *@param string $contextCode
     * @return array
     */
    public function getContextLinks($contextCode)
    { 
       /*
       	$practicals = $this->objPractical->getPractical($this->contextCode);	   
          $bigArr = array();
		
		if(count($practicals) > 1)
		{
		 //var_dump($practicals);
          foreach ($practicals as $practical)
          {
              $newArr = array();    
              $newArr['menutext'] = $practical['name'];
              $newArr['description'] =$practical['description'];
              $newArr['itemid'] = $practical['id'];
              $newArr['moduleid'] = 'practical';
              $newArr['params'] = array('id'=>$practical['id'],'action' => 'view');
              $bigArr[] = $newArr;
          }
          
          return $bigArr;
        } else {
			return FALSE;
		}
         */
    }
    
}

?>