<?php

/**
 * Class kngmail
 * Class to adapt phpmailer class to nextgen with error handler
 */
require("mail_class_inc.php");

class kngmail extends object 
{
	var $objMailer;
	var $mailer = 'sendmail';
	var $wordWrap = '75';
	
	function init($from,$fromName,$host)
	{
		$this->objLanguage = &$this->getObject('language','language');
		$this->objMailer = new mailer($from, $fromName, $host, $mailer = 'sendmail', $wordWrap = '75');

	}
	
	function sendMail($name, $subject, $email, $html = true, $body, $attachment = null, $attachment_descrip)
	{
		//set the email address to nothing s that each user doesn't get 6000 mails!
		$email = "";
		
		if($html != true)
		{
			$this->objMailer->isHTML = false;
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
		return true;
	}//end function
	
}//end class
?>