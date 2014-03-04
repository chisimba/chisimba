<?php
/**
 * Mailman Newsletter Controller
 *
 * controller class for mailman newsletter package
 *
 * PHP version 5
 *
 * The license text...
 *
 * @category  Chisimba
 * @package   mailmannews
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Pual Scott
 * @license   gpl
 * @version   $Id: controller.php 24809 2012-12-09 12:04:20Z dkeats $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * Controller class for the mailman newsletter module
 *
 * Controller class for the mailman newsletter module
 *
 * @category  Chisimba
 * @package   mailmannews
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   gpl
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */
class mailmannews extends controller
{

    /**
     * Description for public
     * @var    unknown
     * @access public
     */
	public $objLanguage;

    /**
     * Description for public
     * @var    unknown
     * @access public
     */
	public $objConfig;

	public $objMailmanSignup;
	public $objUser;

	/**
     * Constructor method to instantiate objects and get variables
     */
    public function init()
    {
        try {
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objConfig = $this->getObject('altconfig', 'config');
            $this->objMailmanSignup = $this->getObject('mailmansignup');
            $this->objUser = $this->getObject('user', 'security');
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
                // A simple editor and a post to the list to send a newsletter
                return 'sendletter_tpl.php';
                break;

            case 'sendnews':
                $subject = $this->getParam('posttitle');
                $content = $this->getParam('postcontent');
                $this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
                $objMailer = $this->getObject('mailer', 'mail');

                $emailadd = $this->sysConfig->getValue('mailmannews_listsend', 'mailmannews');

                $objMailer->setValue('to', array($emailadd));
			    $objMailer->setValue('from', 'noreply');
			    $objMailer->setValue('fromName', $this->objLanguage->languageText("mod_mailmannews_emailfromname", "mailmannews"));
			    $objMailer->setValue('subject', $subject);
			    $objMailer->setValue('body', strip_tags($content));
			    if ($objMailer->send()) {
		   		    $this->nextAction('');
			    } else {
			        return 'error_tpl.php';
			    }
                break;

            case 'createlist':
            	$this->requiresLogin(FALSE);
            	return 'subscribe_tpl.php';
            	break;

            case 'subscribe':
            	$this->requiresLogin(FALSE);
            	$email = $this->getParam('email');
            	if($this->objMailmanSignup->subscribeToMailman($email) === TRUE)
            	{
            		$this->nextAction(NULL, NULL, '_default');
            		//return 'welcome_tpl.php';
            	}
            	else {
            		return 'error_tpl.php';
            	}
            	//echo $email; die();
        }
    }

    /**
    * Overide the login object in the parent class
    *
    * @param  void
    * @return bool
    * @access public
    */
	public function requiresLogin($action)
	{
       return FALSE;
	}
}
?>