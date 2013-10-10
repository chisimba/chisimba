<?PHP
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for Chisimba for the module VLC	
* which uses the vlc library to wrap and 
* create an online vlc player
*
* @author Prince Mbekwa
* @package vlc
*
*/


class vlc extends controller {
	
	
	 /**
     * Intialiser for the stories controller
     *
     * @param byref $ string $engine the engine object
     */
    public function init()
    {
        //Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('altconfig', 'config');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        //$this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the _MODULECODE module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
    	$src =  "http://localhost:8080";    	
    	$this->setVarByRef("src", $src);
    	return "default_tpl.php";
    }
   
	
	
}
?>