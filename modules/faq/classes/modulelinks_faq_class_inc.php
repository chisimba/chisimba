<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_faq extends object
{
    public function init()
    {
        $this->_objDBFaqEntries = & $this->newObject('dbfaqentries','faq');
        $this->_objDBCategories = & $this->newObject('dbfaqcategories','faq');
        $this->objDbContext = &$this->getObject('dbcontext','context');
        $this->contextCode = $this->objDbContext->getContextCode();
    	// If we are not in a context...
        if ($this->contextCode == null) {
            $this->contextId = "root";
        }
        else {
            $this->contextId = $this->contextCode;
        }
    }
    
    public function show()
    {
		$rootNode = new treenode (array('link'=>$this->uri(NULL, 'faq'), 'text'=>'FAQ'));
        
        $nodesArray = array();
        
        $categories = $this->_objDBCategories->getContextCategories($this->contextId);
        
        foreach ($categories as $category)
        {
            $node =& new treenode(array('link'=>$this->uri(array('action'=>'view', 'id'=>$category['id'])), 'text'=>$category['categoryname']));
            
            $nodesArray['category'.$category['id']] =& $node;
            $rootNode->addItem($nodesArray['category'.$category['id']]);
            
            $entries = $this->_objDBFaqEntries->getEntries($this->contextId, $category['id']);
            
            foreach ($entries as $entry)
            {
                $node =& new treenode(array('link'=>$this->uri(array('action'=>'view', 'id'=>$category['id'])), 'text'=>$entry['question']));
                
                $nodesArray['entry'.$entry['id']] =& $node;
                $nodesArray['category'.$category['id']]->addItem($nodesArray['entry'.$entry['id']]);
            }

        }

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
          $cats = $this->_objDBCategories->getCatId($contextCode);
          $faqs=$this->_objDBFaqEntries->getEntries($contextCode,$cats['id']);
   
          $bigArr = array();
        
          foreach ($faqs as $faq)
          {
                $newArr = array();   
              $newArr['menutext'] = $faq['qn'];
              $newArr['description'] = $faq['categoryid'];
              $newArr['itemid'] = $faq['id'];
              $newArr['moduleid'] = 'faq';
              $newArr['params'] = array('id' => $faq['id'],'action' => 'events');
              $bigArr[] = $newArr;
          }
         
          return $bigArr;
          
          
*/
    }
    
}

?>
