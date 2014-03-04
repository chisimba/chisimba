<?php

/**
* File modulelinks extends object
*
* @author Nonhlanhla Gangeni
* @copyright (c) 2007 UWC
* @version 0.1
*/



/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_essay extends object
{

    public function init()
    {
    	$this->objEssay = $this->getObject('dbessays', 'essay');
    }
    
        
    /**
     * 
     *Method to get a set of links for a context
     *@param string $contextCode
     * @return array
     */
    public function getContextLinks($contextCode)
    { 
     
       	$essays = $this->objEssay->getEssayTopics($contextCode);	   
        $bigArr = array();
		
		// var_dump($essays);
          foreach ($essays as $essay)
          {
                $newArr = array();    
              $newArr['menutext'] = $essay['topic'];
              $newArr['description'] =$essay['name'];
              $newArr['itemid'] = $essay['id'];
              $newArr['moduleid'] = 'essay';
              $newArr['params'] = array('id'=>$essay['id'],'action' => 'view');
              $bigArr[] = $newArr;
          }
          
          return $bigArr;
          //var_dump($bigArr);
         
    }
    
}

?>
