<?php

/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts

if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}


class modulelinks_dictionary extends object
{

    public function init()
    {
        //$this->loadClass('treenode','tree');
       $this->_objDBEventsCalendar = & $this->newObject('dbeventscalendar','eventscalendar');
       $this->_objDBCategories = & $this->newObject('dbeventscalendarcategories','eventscalendar');
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
       /*
          $catId = $this->_objDBCategories->getCatId('context', $contextCode);
         
          $events =  $this->_objDBEventsCalendar->getAll('WHERE catid="'.$catId.'" ORDER BY event_date' );
          
          $bigArr = array();
         
          foreach ($events as $event)
          {
                $newArr = array();    
              $newArr['menutext'] = $event['title'];
              $newArr['description'] = $event['description'];
              $newArr['itemid'] = $event['id'];
              $newArr['moduleid'] = 'eventscalendar';
              $newArr['params'] = array('month' => date('m',$event['event_date']),'action' => 'events');
              $bigArr[] = $newArr;
          }
          
          return $bigArr;
          */
    }
    
}

?>