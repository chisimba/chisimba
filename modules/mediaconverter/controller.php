<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Controller class for the  mediaconverter module that extends the base controller
 *
 * @author Paul Scott <pscott@uwc.ac.za>
 * @copyright AVOIR
 * @package mediaconverter
 * @category chisimba
 * @license GPL
 */
class mediaconverter extends controller 
{
	
	public $objMedia;
	public $objUser;
	public $objLanguage;
	public $objConfig;
	public $objLog;
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
        	$this->objMedia = $this->getObject('media');
            $this->objUser = $this->getObject('user', 'security');
            //language object
            $this->objLanguage = $this->getObject('language', 'language');
            //config object
            $this->objConfig = $this->getObject('altconfig', 'config');
            //proxy object
            //$this->objProxy = $this->getObject('proxyparser', 'utilities');
            //Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            $this->objLog->log();
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
            	$file = "/var/www/Swizz.3gp";
            	echo $this->objMedia->convert3gp2flv($file);
            	//echo "test done!";
            	//return 'test_tpl.php';
            	break;
        }
    }
}
?>