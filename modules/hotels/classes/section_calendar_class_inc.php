<?php

class section_calendar extends object
{

    public function init()
    {
		$this->objUser = $this->getObject('user', 'security');
        $this->loadClass('link', 'htmlelements');
		$this->loadClass('htmlheading', 'htmlelements');
		
		$this->objStories = $this->getObject('dbhotelsstories');
        // Load Menu Tools Class
        $this->objMenuTools = $this->getObject('tools', 'toolbar');
		
		// Permissions Module
        $this->objDT = $this->getObject( 'decisiontable','decisiontable' );
        // Create the decision table for the current module
        $this->objDT->create('news');
        // Collect information from the database.
        $this->objDT->retrieve('news');
		
		$this->objIcon = $this->newObject('geticon', 'htmlelements');
    }
    

    public function renderSection($category)
    {
        $year = $this->getParam('year', date('Y'));
        $month = $this->getParam('month', date('m'));
		
		$header = new htmlheading();
        $header->type = 1;
        $header->str = $category['categoryname'];
        
        
        $objCalendar = $this->getObject('calendargenerator', 'calendarbase');
        $output = '';
		
        $objTrimString = $this->getObject('trimstr', 'strings');
        $objDateTime = $this->getObject('dateandtime', 'utilities');
        
        $this->setVar('pageTitle', $category['categoryname']);
        $this->objMenuTools->addToBreadCrumbs(array($category['categoryname']));
        
        $categoryStories = $this->objStories->getMonthStories($category['id'], $month, $year);
        
        if (count($categoryStories) > 0) {
			
			$events = array();
            
            $output = '<ul>';
            
            foreach ($categoryStories as $story)
            {
                $day = explode('-', $story['storydate']);
				$day = $day[2]*1;
				
				
				
				$storyLink = new link ($this->uri(array('action'=>'viewstory', 'id'=>$story['id'])));
                $storyLink->link = $story['storytitle'];
                
                $storyLink->link = $story['storytitle'];
                
                $output .= '<li>'.$objDateTime->formatDateOnly($story['storydate']).' - '.$storyLink->show().'</li>';
                
                $events[$day][] = $storyLink->show();
            }
            
            $newEventsArray = array();
            
            foreach ($events as $eventDay=>$eventsOnDay)
            {
                $newEventsArray[$eventDay] = '';
                
                foreach ($eventsOnDay as $eventOnDay)
                {
                    $newEventsArray[$eventDay] .= '- '.$eventOnDay.'<br />';
                }
                
                //$newEventsArray[$eventDay] .= '</ul>';
            }
            
            $output .= '</ul>';
			
			$objCalendar->setEvents($newEventsArray);
        
        }
		
        
        return $header->show().$objCalendar->show().$output;
    }
    
    /**
    *
    *
    *
    */
    public function renderPage($story, $category)
    {
        $objRender = $this->getObject('renderstory');
		
		return $objRender->render($story, $category);
    }

}
?>