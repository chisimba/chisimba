<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_readinglist extends object
{

    public function init()
    {
       $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->loadClass('treenode','tree');
        
        $this->userId = $this->objUser->userId();
    }
    
    public function show()
    {
      $read = $this->objLanguage->languageText('mod_readinglist_readinglist', 'readinglist');
        $rootNode = new treenode (array('link'=>$this->uri(NULL, 'readinglist'), 'text'=>$read, 'preview'=>''));
        
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
      	$objReading = $this->getObject('dbreadinglist', 'readinglist');
      	$objDBContext = $this->getObject('dbcontext', 'context');
	    $read = $objReading->listAll($objDBContext->getContextCode());
        
      
	       $bigArr = array();
         
         if(count($read) > 0)
         {
         
          foreach ($read as $r)
          {
                $newArr = array();    
              $newArr['menutext'] = $r['title'];
              $newArr['description'] = $r['publisher'];
              $newArr['itemid'] = $r['id'];
              $newArr['moduleid'] = 'readinglist';
              $newArr['params'] = array('id' => $r['id'],'action' => 'additionals');
              $bigArr[] = $newArr;
          }
          
          return $bigArr;
          } else {
			return false;
		}
	        
          
    }
    
}

?>