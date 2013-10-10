<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Class to link the worksheet module to the context
* @author Abdurahim Shariff
* @copyright (c) 2007 UWC
* @package essay
* @version 0.1
*/

class modulelinks_worksheet extends object
{

    public function init()
    {
    	$this->objWorksheet =& $this->getObject('dbworksheet', 'worksheet');
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
        
        $worksheets = $this->objWorksheet->getWorksheetsInContext($contextCode);	
		//print_r($worksheets);
 		foreach ($worksheets as $worksheet)
       {
              $newArr = array();    
              $newArr['menutext'] = $worksheet['name'];
              $newArr['description'] = $worksheet['description'];
              $newArr['itemid'] = $worksheet['id'];
              $newArr['moduleid'] = 'worksheet';
              $newArr['params'] = array('action' => 'selectforanswer','id'=>$worksheet['id']);
              $bigArr[] = $newArr;
              //echo $bigArr;
        }
          
        return $bigArr;
         
    }
    
}

?>
