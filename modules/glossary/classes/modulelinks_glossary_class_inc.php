<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Class to link the glossary to context
* @author Abdurahim Shariff
* @copyright (c) 2007 UWC
* @package essay
* @version 0.1
*/


class modulelinks_glossary extends object
{

    public function init()
    {
        //$this->loadClass('treenode','tree');
       $this->objGlossary = & $this->newObject('dbglossary','glossary');
    }
    
    public function show()
    {
        
    }
    
    /**
     * 
     *Method to get a set of links for a context
     *@param string $contextCode
     * @return array
     */
    public function getContextLinks($contextCode)
    { 
       
       	$glossarys = $this->objGlossary->fetchAllRecords($contextCode);	   
          $bigArr = array();

          foreach ($glossarys as $glossary)
          {
                $newArr = array();    
              $newArr['menutext'] = $glossary['term'];
              $newArr['description'] = $glossary['definition'];
              $newArr['itemid'] = $glossary['item_id'];
              $newArr['moduleid'] = 'glossary';
              $newArr['params'] = array('id'=>$glossary['item_id'],'action' => 'viewbyletter');
              $bigArr[] = $newArr;
          }
          
          return $bigArr;
         
    }
    
}

?>