<?
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

//Load the library class

require_once($this->getResourcePath('class.phpmailer.php', 'mail'));

//Get the abstract and interface classes
require_once("absendmail_class_inc.php");
require_once("ifsendmail_class_inc.php");

/**
*
* HTML email sending class for Chisimba
*
* This class provides a wrapper for PHPMailer, which is available from
*  http://phpmailer.sourceforge.net/
*
* Features include:
* Class Features:
*  - Send emails with multiple TOs, CCs, BCCs and REPLY-TOs
*  - Redundant SMTP servers
*  - Multipart/alternative emails for mail clients that do not read HTML email
*  - Support for 8bit, base64, binary, and quoted-printable encoding
*  - Uses the same methods as the very popular AspEmail active server (COM) component
*  - SMTP authentication
*  - Native language support
*  - Word wrap, and more!
*
* See documentation in the lib directory of this module for more information
* and credits for the library
*
* @author Derek Keats
* @category Chisimba
* @package mail
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class email extends absendmail implements ifsendmail
{
	/**
	 *
	 * @var string Object $objBaseMail String object to hold the mailer instance
	 *
	 */
	private $objBaseMail;

    /**
    *
    * Standard init method
    *
    */
    function __construct()
    {
    	$this->objBaseMail = new PHPMailer;
	    //Get an instance of the config object
        $objConfig=&$this->newObject('altconfig','config');
        //Get the value of the delimiter
        $method = $objConfig->getValue('MAIL_SEND_METHOD', 'mail');
        switch ($method) {
        	//set up for SMTP
            case "smtp":
            	$this->objBaseMail->IsSMTP();
            	$this->objBaseMail->Host = $objConfig->getValue('MAIL_SMTP_SERVER', 'mail');
            	$smtpAuth = $objConfig->getValue('MAIL_SMTP_REQUIRESAUTH', 'mail');
            	if ($smtpAuth == "true") {
            		$this->objBaseMail->SMTPAuth = true;
            	    $this->objBaseMail->Username = $objConfig->getValue('MAIL_SMTP_USER', 'mail');
            	    $this->objBaseMail->Password = $objConfig->getValue('MAIL_SMTP_PASSWORD', 'mail');
            	}
            	break;

            default:
            	//Sets Mailer to send message using PHP mail() function.
            	$this->objBaseMail->IsMail();
            	$this->objBaseMail->Host = "localhost";

            	//die ("still working on non-SMTP based methods");
                break;

        }
        //Check if we should use HTML mail
        $useHTMLMail = $objConfig->getValue('MAIL_USE_HTML_AS_DEFAULT', 'mail');
        if ($useHTMLMail == "true") {
        	$this->objBaseMail->IsHTML(TRUE);
        } else {
            $this->objBaseMail->IsHTML(FALSE);
        }
        //Set the default wordwrap
		$this->objBaseMail->WordWrap = 50;

    }

    /**
    *
    * Method to send the email according to the method specified
    * by the wrapped class
    *
    */
    public function send()
    {
		$this->objBaseMail->From =  $this->from;
		$this->objBaseMail->FromName = $this->fromName;
		$this->objBaseMail->Subject = $this->subject;
		$this->objBaseMail->Body = $this->body;
		$this->objBaseMail->AltBody = $this->altBody;
		$this->objBaseMail->mailer = $this->mailer;
		//Add the addresses to the mailer
		if (is_array($this->to)) {
			foreach($this->to as $addresses)
			{
				$this->objBaseMail->AddAddress($addresses);
			}

		} else {
		    $this->objBaseMail->AddAddress($this->to);
		}
		//Add the CC addresses to the mailer
		if (isset($this->cc)) {
			if (is_array($this->cc)) {
			    foreach ($this->cc as $address) {
			        $this->objBaseMail->AddCC($address);
			    }
			} else {
			    $this->objBaseMail->AddCC($this->cc);
			}
		}
		//Add the BCC addresses to the mailer
		if (isset($this->bc)) {
			if (is_array($this->bcc)) {
			    foreach ($this->bcc as $address) {
			        $this->objBaseMail->AddBCC($address);
			    }
			} else {
			    $this->objBaseMail->AddBCC($this->bcc);
			}
		}
		//Send it and let us know if it was sent
        if ($this->objBaseMail->send()) {
            return TRUE;
        } else {
            return FALSE;
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

    /**
     *
     * Method to provide access to the properties of the base
     * mailer class.
     *
     * @param string $bmProperty The property to set
     * @param string $value THe value to be set
     *
     */
    public function setBaseMailerProperty($bmProperty, $value)
    {
        $this->objBaseMail->$bmProperty = $value;
    }

    /**
     *
     * Method to clear attachments
     *
     */
    public function clearAttachments()
    {
    	$this->objBaseMail->ClearAttachments();
    }

    /**
     *
     * Method to clear addresses
     *
     */
    public function clearAddresses()
    {
        $this->objBaseMail->ClearAddresses();
    }
}
?>
