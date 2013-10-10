<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_rubric extends object
{

    public function init()
    {
       //the rubric
       $this->objRubric =& $this->getObject('dbrubrictables', 'rubric');      
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
        
         $rubrics = $this->objRubric->listAll($contextCode);	
		
		if(count($rubrics) > 0)
		{
			
			 foreach ( $rubrics as $rubric)
	      	 {
              $newArr = array();    
              $newArr['menutext'] = $rubric['title'];
              $newArr['description'] = $rubric['description'];
              $newArr['itemid'] = $rubric['id'];
              $newArr['moduleid'] = 'rubric';
              $newArr['params'] = array('action' => 'viewtable','tableid'=>$rubric['id']);
              $bigArr[] = $newArr;
        	}
       		return $bigArr;
       	} else {
			return FALSE;
		}
    }
    
}

?>