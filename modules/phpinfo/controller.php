<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
*
* Controller class for Chisimba for the phpinfo
*
* @author Derek Keats
* @package timeline
*
*/
class phpinfo extends controller
{

    /**
    * @var $objLog String object property for holding the
    * logger object for logging user activity
    */
    public $objLog;

    /**
     * Intialiser for the stories controller
     *
     * @param byref $ string $engine the engine object
     */
    public function init()
    {
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        // Set up event message
        $this->eventDispatcher->addObserver ( array ($this, 'modaccess' ) );
        //Log this module call
        $this->objLog->log();
    }


    /**
     *
     * The standard dispatch method for
     *
     */
    public function dispatch()
    {
        $this->eventDispatcher->post($this, "test");
    	return "phpinfo_tpl.php";
    }

    public function modaccess($notification) {
        if ($notification->getNotificationName () == 'test') {
            log_debug("PHPInfo module was accessed");
        }
        else {
            log_debug("nothing to see here");
        }

    }
}
?>
