<?php
/**
 * 
 * A general mail sending class
 * 
 * A general mail sending class that wraps the mail functionality of
 * PEAR::Mail. 
 * 
 * 
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
* sendmail abstract
*/
require_once("absendmail_class_inc.php");
    


/**
*
* A general mail sending class
* 
* A general mail sending class that wraps the mail functionality of
* PEAR::Mail. 
*
*
* @author    Derek Keats
* @category  Chisimba
* @package   mail
* @copyright AVOIR
* @licence   GNU/GPL
*
*/
class mailer extends absendmail 
{
    public $objConfig;
    private $sendMethod;
    private $useHTMLMail;
    private $host;
    private $smtpAuth;
    private $port;
    private $username;
    private $password;
    
    /**
     * 
     * Standard init, load PEAR::Mail and set up some parameters
     * @access Public
     * @return VOID
     * 
     */
    public function init()
    {
        require_once($this->getPearResource('Mail.php'));
        require_once($this->getPearResource('Mail/mime.php'));
        
        $this->objConfig=$this->newObject('dbsysconfig','sysconfig');
        //Get the value of the delimiter
        $this->sendMethod = $this->objConfig->getValue('MAIL_SEND_METHOD', 'mail');
        //Check if we should use HTML mail
        $this->useHTMLMail = $this->objConfig->getValue('MAIL_USE_HTML_AS_DEFAULT', 'mail');
        switch ($this->sendMethod) {
            //set up for SMTP
            case "smtp":
                $this->host = $this->objConfig->getValue('MAIL_SMTP_SERVER', 'mail');
                $this->smtpAuth = $this->booleanFromTxt(
                  $this->objConfig->getValue('MAIL_SMTP_REQUIRESAUTH', 'mail')
                );
                $this->port = $this->objConfig->getValue('MAIL_SMTP_PORT', 'mail');
                $this->username = $this->objConfig->getValue('MAIL_SMTP_USER', 'mail');
                $this->password = $this->objConfig->getValue('MAIL_SMTP_PASSWORD', 'mail');
                break;
            default:
                break;
        }
    }
    
    /**
     * Convert a string to boolean
     * 
     * @param string $txt Text to evaluat (usually 'true' or 'false' in text)
     * @access private
     * @return boolean
     * 
     */
    private function booleanFromTxt($txt)
    {
        $txt = strtolower($txt);
        if ($txt == 'true') {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function send()
    {
        $fullFrom = "'" . $this->fromName . "' <" . $this->from . ">";
        $fullFrom =  $this->from;
        $cc = NULL;
        $bcc = NULL;
        
        if (is_array($this->to)) {
            $entries = count($this->to);
            if ($entries > 1) {
                $to = "";
                $counter = 0;
                foreach ($this->to as $address) {
                   $chk = $counter + 1;
                   if ($chk == $entries) {
                       $to .= $address;
                   } else {
                       $to .= $address . ',';
                   }
                   $counter ++;
                }
            } else {
                $to = $this->to[0];
            }
        } else {
            $to = $this->to;
        }
        $recipients = $to;
        
        // Add CC
        if (isset($this->cc)) {
            if (is_array($this->cc)) {
                $cc = "";
                $counter = 0;
                foreach ($this->cc as $address) {
                   $chk = $counter + 1;
                   if ($chk == $entries) {
                       $cc .= $address;
                   } else {
                       $cc .= $address . ',';
                   }
                   $counter ++;
                }
            } else {
                $cc = $this->cc;
            }
            $recipients = $recipients . "," . $cc;
        }
        
        // Add CC
        if (isset($this->bcc)) {
            if (is_array($this->bcc)) {
                $bcc = "";
                $counter = 0;
                foreach ($this->bcc as $address) {
                   $chk = $counter + 1;
                   if ($chk == $entries) {
                       $bcc .= $address;
                   } else {
                       $bcc .= $address . ',';
                   }
                   $counter ++;
                }
            } else {
                $bcc = $this->cc;
            }
            $recipients = $recipients . "," . $bcc;
        }
        
        // To Blind CC (aka, BCC) an address, simply add the address to the 
        //    $recipients, but not to any of the $headers.
        $headers = array (
            'From' => $fullFrom,
            'Return-Path'   => $this->from,
            'To' => $to,
            'Cc' => $cc,
            'Subject' => $this->subject
        );
        
        $smtp = Mail::factory(
            'smtp',
            array (
              'host' => $this->host,
              'port' => $this->port,
              'auth' => $this->smtpAuth,
              'username' => $this->username,
              'password' => $this->password
            )
        );
        
        $mail = $smtp->send($recipients, $headers, $this->body);
        if (PEAR::isError($mail)) {
            echo("<p>" . $mail->getMessage() . "</p>");
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
    *
    * Method to attach a file
    *
    */
    public function attach($file, $name=NULL)
    {
       $this->objBaseMail->AddAttachment($file);
    }

}
?>