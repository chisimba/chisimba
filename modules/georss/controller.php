<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
class georss extends controller
{
    public $objLog;
    public $objLanguage;
    public $objUser;    
    
    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
        	$this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objGeoRssOps = $this->getObject('georssops');
            //Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            //Log this module call
            $this->objLog->log();
        }
        catch(customException $e) {
            echo customException::cleanUp();
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
                $this->requiresLogin();
            	$file = $this->getResourcePath('georss.xml');
            	@chmod($file, 0777);
            	if(filemtime($file) > 43200)
            	{
            		// go and update the feed...
            		$objCurl = $this->getObject('curl', 'utilities');
					$data = $objCurl->exec('http://ws.geonames.org/rssToGeoRSS?feedUrl=http://www.timeslive.co.za/?service=rss');
					
					file_put_contents($this->getResourcePath('georss.xml'), $data);
            	}
            	
            	return 'main_tpl.php';
            	break; 
        }
    }
    
    public function requiresLogin() {
        return FALSE;
    }
}
?>
