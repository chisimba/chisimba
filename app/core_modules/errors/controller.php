<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* error module Controller
*
* @author Paul Scott
* @copyright (c) 2004 University of the Western Cape
* @package httpclient
* @version 1
*/
class errors extends controller
{
    public $objLog;
    public $objConfig;
    public $objLanguage;
    public $objMail;

	/**
	* Constructor method to instantiate objects and get variables
	*/
    public function init()
    {
        try {
        	$this->objConfig = $this->getObject('altconfig','config');
        	$this->objLanguage = $this->getObject('language','language');
        }
        catch (customException $e)
        {
        	customException::cleanUp();
        }
    }

   /**
	* Method to process actions to be taken
    *
    * @param string $action String indicating action to be taken
	*/
    public function dispatch($action=Null)
    {
        switch ($action)
        {
            default:
            	return 'noaction_tpl.php';//die($this->objLanguage->languagetext("mod_errors_noerr", "errors"));
            	break;
            case 'dberror':
            	$devmsg = $this->getParam('devmsg');
            	$usrmsg = $this->getParam('usrmsg');
            	$this->setVarByRef('devmsg',$devmsg);
            	$this->setVarByRef('usrmsg',$usrmsg);
            	return 'dberror_tpl.php';
            	break;

            case 'errormail':
            	$hidmsg = $this->getParam('error');
            	if(empty($hidmsg))
            	{
            		//possible spam usage!!!
            		return 'spam_tpl.php'; //die($this->objLanguage->languageText("mod_errors_spammeralert", "errors"));
            		exit();
            	}
            	$text = $this->getParam('comments');
            	//load up the mail class
            	//$this->objMail = $this->getObject('email', 'mail');
            	//$this->objMail->
            	//echo $hidmsg . "<br /><br />" . $text;

            	break;

        }
    }

}
?>