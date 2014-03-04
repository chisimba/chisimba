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


class modulelinks_assignment extends object
{

    public function init()
    {
    	$this->objAssignment = $this->getObject('dbassignment', 'assignment');
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
        $rootNode = new treenode (array('link'=>$this->uri(array('action'=>'assignment')), 'text'=>'Assignment'));
        
        // Get Assignments - Second Level
        $assignments = $this->objAssignment->getAssignment($this->contextCode);	 
        
        // Extra Check
        if (count($assignments) > 0) {
            
            // Array for References
            $nodesArray = array();
            
            // Loop through Podcasters - second level
            foreach ($assignments as $assignment)
            {
                // Create Node
                $node =& new treenode(array('link'=>$this->uri(array('action'=>'assignment')), 'text'=>$assignment['name']));
                
                // Create Reference to Node
                $nodesArray['assignment'.$assignment['id']] =& $node;
                
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
       	$assignments = $this->objAssignment->getAssignment($this->contextCode);	   
          $bigArr = array();
		
		if(count($assignments) > 1)
		{
		 //var_dump($assignments);
          foreach ($assignments as $assignment)
          {
              $newArr = array();    
              $newArr['menutext'] = $assignment['name'];
              $newArr['description'] =$assignment['description'];
              $newArr['itemid'] = $assignment['id'];
              $newArr['moduleid'] = 'assignment';
              $newArr['params'] = array('id'=>$assignment['id'],'action' => 'view');
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