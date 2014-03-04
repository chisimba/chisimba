<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Include the HTML interface class


/**
 * Class to control the Icalendar object
*/
class icalendar extends object 
{
    
    /**
     * Constructor
     * 
     */
    public function init()
    {
        
    }
    
    
    /**
     * Method to get the Calendar()
     * 
     * @return string
     */
    public function getCalendar()
    {
        /*$objIFrame = $this->newObject('iframe', 'htmlelements');
        $objIFrame->src = '/modules/ical/resources/phpicalendar/index.php';
        return $objIFrame->show();
        */
        
        $str = '<iframe width="100%" height="600px" src="modules/ical/resources/phpicalendar/index.php" frameborder="0"  name="ical"  id="ical"  scrolling="auto"  ></iframe>';
        return $str;
    }
}

?>