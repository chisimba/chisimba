<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
class odt extends controller
{
    public $objLog;
    public $objLanguage;
    public $objOdt;

    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
        	$this->objOdt = $this->getObject('opendoc');
        	$this->objLanguage = $this->getObject('language', 'language');
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
            case 'test':
            	$this->objOdt->setup('/var/www/test.odt');
            	foreach ($this->objOdt->getChildren() as $child) 
            	{
    				//var_dump($child); 
            		//strip headings
    				//if ($child instanceof OpenDocument_Heading) {
        			//	$child->delete();
    				//}
				}
				
				echo ($this->objOdt->toHtml($this->objOdt));

            	
            break;
        }
    }
}
?>