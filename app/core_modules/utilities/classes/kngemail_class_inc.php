<?php

/**
 * Class kngemail
 * Class to adapt phpmailer class to nextgen with error handler
 * @author Paul Scott, James Scoble
 */
require("mail_class_inc.php");

class kngemail extends object 
{

	var $mailer = 'sendmail';
	var $wordWrap = '75';
	
        // Init function
	function init()
	{
		$this->objLanguage = &$this->getObject('language','language');
		$this->setup();
	}

        // Setup function for init of Mailer
        function setup($from='user@nextgen',$fromName='Robot',$host='localhost')
        {
		$this->objMailer = new mailer($from, $fromName, $host, $mailer = 'sendmail', $wordWrap = '75');
        }
	
        // Central mail-sending function
	function sendMail($name, $subject, $email, $body, $html = TRUE, $attachment = NULL, $attachment_descrip=NULL)
	{
		if($html != true){
			$this->objMailer->isHTML = false;
		}
		
                if (is_array($email)){
                    foreach ($email as $addr)
                    {
                        $this->objMailer->AddAddress($addr,'');
                    }
                } else {
		    $this->objMailer->AddAddress($email, $name);
                }
		$this->objMailer->Subject = $subject;
		$this->objMailer->Body = $body;
		
		//check if there is an attachment
                if (is_array($attachment)){
                    // Array of attachments
                    foreach ($attachment as $attach)
                    {
                        if (isset($attach['data']) && isset($attach['name'])){
                            $this->objMailer->AddStringAttachment($attach['data'], $attach['name']);
                        }
                    }
                } else if ($attachment != ''){
			//yes there is one, so add it to the message
			$this->objMailer->AddStringAttachment($attachment, $attachment_descrip);  // optional name
		}
		//error handler
		if(!$this->objMailer->Send()){
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
    
    /**
    * Method to set the alternative text for HTML emails
    * @param string $altBody Alternative Text for HTML emails
    * @author Tohir Solomons
    */
    function setAltBody($altBody)
    {
        $this->objMailer->AltBody = $altBody;
    }
	
}//end class
?>
