<?php
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
 * Class to handle mailmannews elements
 *
 * @author    Paul Scott
 * @copyright GNU/GPL, AVOIR
 * @package   mailmannews
 * @access    public
 */
class mailmansignup extends object
{

	/**
     * Description for public
     * @var    object
     * @access public
     */
	public $objConfig;
	
	public $objMailer;

	/**
     * Standard init function called by the constructor call of Object
     *
     * @param  void  
     * @return void  
     * @access public
     */
	public function init()
	{
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
			$this->sysConfig = $this->getObject('dbsysconfig', 'sysconfig');
			$this->objUser = $this->getObject('user', 'security');
			$this->objMailer = $this->getObject('mailer', 'mail');
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	/**
     * Subscription box form
     *
     * Simple form that takes an email address as input to subscribe user to the newsletter
     *
     * @param  boolean $featurebox Return as a featurebox or as a plain form
     * @return mixed   $ret
     * @access public
     */
	public function subsBox($featurebox = TRUE)
	{
		$this->loadClass('textinput', 'htmlelements');
		$subform = new form('subscribe', $this->uri(array(
		'action' => 'subscribe',
		)));
		$subform->addRule('email', $this->objLanguage->languageText("mod_mailmannews_phrase_emailreq", "mailmannews") , 'required');
		$subform->addRule('email', $this->objLanguage->languageText("mod_mailmannews_phrase_emailinvalid", "mailmannews") , 'email');

		$email = new textinput('email');
		if($this->objUser->isLoggedIn())
		{
			$emailaddy = $this->objUser->email($this->objUser->userId());
			$email->setValue($emailaddy);
		}
		$email->size = 15;
		$subform->addToForm($email->show());
		$this->objSubButton = &new button($this->objLanguage->languageText('word_subscribe', 'mailmannews'));
		$this->objSubButton->setValue($this->objLanguage->languageText('word_subscribe', 'mailmannews'));
		$this->objSubButton->setToSubmit();
		$subform->addToForm($this->objSubButton->show());
		$subform = $subform->show();
		if ($featurebox == FALSE) {
			return $this->objLanguage->languageText("mod_mailmannews_subinstructions", "mailmannews") 
              . "<br />" . $subform;
		} else {
			$objFeatureBox = $this->getObject('featurebox', 'navigation');
			$ret = $objFeatureBox->show(""/*$this->objLanguage->languageText("mod_mailmannews_subscribe", "mailmannews")*/ , $this->objLanguage->languageText("mod_mailmannews_subinstructions", "mailmannews") . "<br />" . $subform);
			return $ret;
		}
	}
	
	/**
	 * Metod to send off the email to the list to subscribe
	 *
	 * @param string $email
	 * @return boolean true on success
	 */
	public function subscribeToMailman($email)
	{
		$maillistAdress = $this->sysConfig->getValue("mailmannews_listjoin","mailmannews");
		
		$this->objMailer->setValue('to', array($maillistAdress));
		$this->objMailer->setValue('from', $email);
		$this->objMailer->setValue('fromName', 'user');
		$this->objMailer->setValue('subject', 'subscribe');
		$this->objMailer->setValue('body', 'subscribe');
		if ($this->objMailer->send()) {
			return TRUE;
			
		} else {
			
			return FALSE;
		}
	}
	
	public function createList()
	{
		$objCurl = $this->getObject('curl', 'utilities');
		return $objCurl->exec('http://mailman.uwc.ac.za/mailman/create');
	}

}
?>