<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
ini_set("max_execution_time", -1);
// end security check
class hrwizard extends controller
{
    public $objLog;
    public $objLanguage;
    public $objHrOps;
    public $recarr;

    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
        	$this->objHrOps = $this->getObject('hrwizardops');
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
            	return 'message_tpl.php';
            	break;
            	
            case 'uploaddatafile':
            	$file = $this->getParam('zipfile');
            	//file id is returned, so lets go and get the actual file for parsing...
            	$pdfzip = $this->objHrOps->unpackPdfs($file);
            	return 'upload2_tpl.php';
            	break;
            	
            case 'uploadcsvfile':
            	$csv = $this->getParam('csvfile');
            	$this->recarr = $this->objHrOps->parseCSV($csv);
            	$this->setVarByRef('msg', $this->recarr);
            	return 'message_tpl.php';
            	break;
            	
            case 'addmessage':
            	$subject = $this->getParam('subject');
            	$bodytext = $this->getParam('bodytext');
            	$file = $this->getParam('zipfile');
            	$csv = $this->getParam('csvfile');
            
            	$pdfzip = $this->objHrOps->unpackPdfs($file);
            	$this->recarr = $this->objHrOps->parseCSV($csv);
            	//print_r($this->recarr);
            	$ret = $this->objHrOps->sendMails($this->recarr, nl2br($bodytext), $subject);
            	$this->setVarByRef('ret', $ret);
            	return "done_tpl.php";
            	break;
            	
            	
        }
    }
}
?>