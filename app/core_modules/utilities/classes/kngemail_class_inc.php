<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * Class kngemail
 * Class to adapt phpmailer class to nextgen with error handler
 *
 * @category  Chisimba
 * @author Paul Scott, James Scoble
 * @package   utilities
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
require("mail_class_inc.php");

class kngemail extends object 
{
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
    function init()
    {
        $this->objLanguage = $this->getObject('language','language');
        $this->setup();
    }

    /**
        * Setup function for init of Mailer
        *
        */
        function setup($from='user@nextgen',$fromName='Robot',$host='localhost')
        {
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
    function sendMail($name, $subject, $email, $body, $html = TRUE, $attachment = NULL, $attachment_descrip=NULL)
    {
        if($html != TRUE){
            $this->objMailer->isHTML = FALSE;
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
        return TRUE;
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