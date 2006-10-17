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
    public $objUser;

	/**
	* Constructor method to instantiate objects and get variables
	*/
    public function init()
    {
        try {
        	$this->objConfig = $this->getObject('altconfig','config');
        	$this->objLanguage = $this->getObject('language','language');
        	$this->objUser = $this->getObject('user', 'security');
        }
        catch (customException $e)
        {
        	customException::cleanUp();
        }
    }
    
    public function requiresLogin() {
    	return FALSE;
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
            	return 'noaction_tpl.php';
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
            		return 'spam_tpl.php';
            		exit();
            	}
            	$text = $this->getParam('comments');
            	try {
            		//load up the mail class
            		$this->objMail = $this->newObject('email', 'mail');
       				//set up the mailer
       				$objMailer = $this->getObject('email', 'mail');
					$objMailer->setValue('to', array('fsiu-dev@uwc.ac.za', $this->objConfig->getsiteEmail(), 'pscott@uwc.ac.za', 'fsiu@uwc.ac.za'));
					$objMailer->setValue('from', $this->objUser->email());
					$objMailer->setValue('fromName', $this->objUser->fullname());
					$objMailer->setValue('subject', $this->objLanguage->languageText("mod_errors_errsubject", "errors"));
					$objMailer->setValue('body', $text . "  " . $hidmsg);
					$objMailer->send();
					return 'thanks_tpl.php';
					break;

            	}
            	catch (customException $e)
            	{
            		customException::cleanUp();
            		exit;
            	}
            	//$this->objMail->
            	//echo $hidmsg . "<br /><br />" . $text;

            	break;
            case 'syserr':
            	$mess = $this->getParam('msg');
            	$mess = urldecode(htmlentities($mess));
            	$this->setVarByRef('mess', $mess);
            	return "syserror_tpl.php";
            	break;

        }
    }

}
?>