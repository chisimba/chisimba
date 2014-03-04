<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller class for the syncml module that extends the base controller
 *
 * @author Paul Scott <pscott@uwc.ac.za>
 * @copyright AVOIR
 * @package syncml
 * @category chisimba
 * @licence GPL
 */
class syncml extends controller {
	/**
     * Constructor method to instantiate objects and get variables
     *
     * @param void
     * @return string
     * @access public
     */
    public function init()
    {
        try {
            //$this->objUser = $this->getObject('user', 'security');
            //language object
            //$this->objLanguage = $this->getObject('language', 'language');
            //config object
            //$this->objConfig = $this->getObject('altconfig', 'config');
            //proxy object
            //$this->objProxy = $this->getObject('proxyparser', 'utilities');
            //Get the activity logger class
            //$this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            //$this->objLog->log();
        }
        catch(customException $e) {
            //oops, something not there - bail out
            echo customException::cleanUp();
            //we don't want to even attempt anything else right now.
            die();
        }
    }
    
     /**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
    public function dispatch($action = Null)
    {
        switch ($action) {
            default:
            	if(@$_SERVER["CONTENT_TYPE"] != "application/vnd.syncml+xml") {
            			//throw new customException($this->objLanguage->languageText("mod_syncml_syncmlonly", "syncml"));
            			echo "booboo";
            	}
            	else {
            		echo "Yo!";
            	}
            	
            	return 'test_tpl.php';
            	break;
        }
    }
}
?>