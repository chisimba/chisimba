<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_comment extends object
{

    public function init()
    {
    	$this->objcomment =& $this->getObject('dbcomment', 'comment');
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
          
		$bigArr = array();
        
        $comments = $this->objcomment->getComment($tableName, $sourceId);	
	
 		foreach ($comments as $comment)
       {
              $newArr = array();    
              $newArr['menutext'] = $comment['name'];
              $newArr['description'] = '';
              $newArr['itemid'] = $comment['id'];
              $newArr['moduleid'] = 'comment';
              $newArr['params'] = array('action' => 'view','id'=>$comment['id']);
              $bigArr[] = $newArr;
        }
          
        return $bigArr;
         
    }
    
}

?>