<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
ini_set("max_execution_time", -1);
// end security check
class codesniffer extends controller
{
    public $objMail;
    public $objLog;
    public $objLanguage;
    public $objSysConfig;
    public $objConfig;

    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            // Get the activity logger class
            $this->objLog = $this->newObject('logactivity', 'logger');
            // Log this module call
            $this->objLog->log();
            //sys-config object
            $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            //$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
            $this->reportPath = $this->objSysConfig->getValue('report_path', 'codesniffer');
            $this->codePath = $this->objSysConfig->getValue('code_path', 'codesniffer');
            $this->objConfig = $this->getObject('altconfig', 'config');
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
            	$this->requiresLogin(FALSE);
                    try {
                    	$codepath = $this->objConfig->getSiteRootPath();
                        exec("phpcs --report=full --standard=Chisimba $this->codePath > $this->reportPath/report.txt");
                        
                        $objMailer = $this->getObject('mailer', 'mail');
			            $objMailer->setValue('to', array('nextgen-online@mailman.uwc.ac.za'));
			            $objMailer->setValue('from', 'noreply@uwc.ac.za');
			            $objMailer->setValue('fromName', 'CodeSniffer');
			            $objMailer->setValue('subject', 'Code QA Report ('.date('r').')');
			            $objMailer->setValue('body', 'Text file attached...');
			            $objMailer->attach($this->reportPath.'/report.txt', 'code_report.txt');
			            if ($objMailer->send()) {
		   		            // unlink the file and clean up
		   		            echo $this->dumpText('Success!');
			            	die();
		   		            //return TRUE;
			            } else {
			            	echo $this->dumpText('Something went wrong...');
		   		            return FALSE;
			            }
                    }
                    catch(customException $e) {
                        customException::cleanUp();
                    }
                }
        }
        
        public function requiresLogin($bool) 
        {
        	return $bool;
        }
    }
?>