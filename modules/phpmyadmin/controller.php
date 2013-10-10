<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for Chisimba for the phpMyAdmin module
*
* @author Derek Keats
* @package timeline
*
*/
class phpmyadmin extends controller
{
   
    /**
    * @var $objLog String object property for holding the 
    * logger object for logging user activity
    */
    public $objLog;

    /**
     * Intialiser for the controller
     *
     * @param byref $ string $engine the engine object
     */
    public function init()
    {
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
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
    	$objConfig = $this->getObject('dbsysconfig', 'sysconfig');
    	$src = $objConfig->getValue('mod_phpmyadmin_url', 'phpmyadmin');
    	//header ("Location: $url");
    	$this->setVarByRef("src", $src);
    	return "default_tpl.php";
    }
}
?>
