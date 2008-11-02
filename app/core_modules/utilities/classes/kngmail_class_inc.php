<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * Class kngmail
 * Class to adapt phpmailer class to nextgen with error handler
 *
 * @category  Chisimba
 * @package   <utilities
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
require("mail_class_inc.php");

class kngmail extends object 
{
	/**
    	*@var object $objMailer
    	*/
	var $objMailer;
	/**
        * @var $mailer
        * Property to hold the type of mail given the value sendmail
        */
	var $mailer = 'sendmail';
	/**
        * @var $wordWrap
        * Property to hold the amount of word wrapping given the value of 75
        */
	var $wordWrap = '75';
	
	/**
        * standard init function
        *
        */
	function init($from,$fromName,$host)
	{
		$this->objLanguage = $this->getObject('language','language');
		$this->objMailer = new mailer($from, $fromName, $host, $mailer = 'sendmail', $wordWrap = '75');

	}

	/**
        * Central mail-sending function
        *
	*@param string $name The name of the sender
	*@param string $subject The subject of the email
	*@param string $email The email address
	*@param string $body The content of the email
	*@param string $html 
	*@param string $attachment 
	*@param string $attachment_descrip 
	*@return boolean
        */
	function sendMail($name, $subject, $email, $html = TRUE, $body, $attachment = NULL, $attachment_descrip)
	{
		//set the email address to nothing s that each user doesn't get 6000 mails!
		$email = "";
		
		if($html != TRUE)
		{
			$this->objMailer->isHTML = FALSE;
		}
		
		$this->objMailer->AddAddress($email, $name);
		$this->objMailer->Subject = $subject;
		$this->objMailer->Body = $body;
		
		//check if there is an attachment
		if($attachment != '')
		{
			//yes there is one, so add it to the message
			$this->objMailer->AddStringAttachment($attachment, $attachment_descrip);  // optional name
		}
		//error handler
		if(!$this->objMailer->Send())
		{
			//tell the user there was a problem
			return $this->objLanguage->languageText("word_message_failure");
			//crash and burn dude
			exit;
		}
		else
		{
			//tell the user who mail has been sent to (optional)
			//echo "Mail sent to $email<br><br>";
			//OK mail is sent, so lets clear this recipient and attachment.
	        $this->objMailer->ClearAllRecipients();
	        $this->objMailer->ClearAttachments();
										
		}

		//OK mail is sent, so lets clear this recipient and attachment.
		$this->objMailer->ClearAllRecipients();
		$this->objMailer->ClearAttachments();
		return TRUE;
	}//end function
	
}//end class
?>