<?php
/**
* Email results class extends object
* @package etd
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Email class for displaying an interface for sending email
* @author Megan Watson
* @copyright (c) 2005 UWC
* @version 0.2
*/

class emailResults extends object
{
    /**
    * @var string Property to set the module name
    */
    private $module = 'etd';

    /**
    * @var string Property to set the Heading
    */
    private $heading = '';

    /**
    * @var string Property to set the subject line
    */
    private $subject = '';

    /**
    * @var string Property to set whether the subject is readonly
    */
    private $readonlySubject = FALSE;

    /**
    * @var string Property to set the name of the recipient
    */
    private $recipientName = '';

    /**
    * @var string Property to set the recipient name as readonly
    */
    private $readonlyRecName = FALSE;

    /**
    * @var string Property to set the recipients email address
    */
    private $recipientEmail = '';

    /**
    * @var string Property to set the recipient email address as readonly
    */
    private $readonlyRecEmail = FALSE;

    /**
    * @var string Property to set the message
    */
    private $message = '';

    /**
    * @var string Property to set the body of the email
    */
    private $emailBody = '';

    /**
    * Constructor method
    */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objMailer = $this->getObject('kngemail','utilities');

        $this->objUser = $this->getObject('user', 'security');
        if($this->objUser->isLoggedIn()){
            $this->email = $this->objUser->email();
            $this->user = $this->objUser->fullname();
        }else{
            $objConfig = $this->getObject('altconfig', 'config');
            $this->email = $objConfig->getsiteEmail();
            $this->user = $objConfig->getinstitutionName();
        }

        $this->objHeading = $this->newObject('htmlheading', 'htmlelements');

        $this->loadClass('link', 'htmlelements');
        $this->loadClass( 'button', 'htmlelements' );
        $this->loadClass( 'textinput', 'htmlelements');
        $this->loadClass( 'label', 'htmlelements' );
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
    }

    /**
    * Method to set the module name. Default is etd.
    *
    * @access public
    * @param string $module The name of the module using the class.
    * @return
    */
    public function setModuleName($module)
    {
        $this->module = $module;
    }

    /**
    * Method to set the heading for the email page
    *
    * @access public
    * @param string $heading The heading.
    * @return
    */
    public function setHeading($heading)
    {
        $this->heading = $heading;
    }

    /**
    * Method to set the subject of the email.
    *
    * @access public
    * @param string $subject The email subject.
    * @param string $readonly Set the subject input as readonly
    * @return
    */
    public function setSubject($subject, $readonly = FALSE)
    {
        $this->subject = $subject;
        $this->readonlySubject = $readonly;
    }

    /**
    * Method to set the recipients name and email address
    *
    * @access public
    * @param string $name The name of the recipient
    * @param string $email The recipients email address
    * @param string $readonly Allow/prevent editing of the recipients details.
    * @return
    */
    public function setRecipient($name, $email, $readonly = TRUE)
    {
        $this->recipientName = $name;
        $this->readonlyRecName = $readonly;
        $this->recipientEmail = $email;
        $this->readonlyRecEmail = $readonly;
    }

    /**
    * Method to set a message below the email body, eg Resource attached.
    *
    * @access public
    * @param string $message The message.
    * @return
    */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
    * Method to set the email body.
    *
    * @access public
    * @param string $body The body.
    * @return
    */
    public function setBody($body)
    {
        $this->emailBody = $body;
    }
    
    /**
    * Method to create and send the email.
    *
    * @access public
    * @param string $fromEmail Senders email address
    * @param string $fromUser Senders name
    * @return
    */
    public function sendEmail($fromEmail = NULL, $fromUser = NULL)
    {
        $body = $this->getSession('emailbody');
        $this->unsetSession('emailbody');

        $name = $this->getParam('name', $this->user);
        $email = $this->getParam('address', $this->email);
        $subject = $this->getParam('subject', '');
        $body = $this->getParam('comment', '').'<p>'.$body.'</p>';
        
        $this->email($name, $subject, $email, $body, $fromEmail, $fromUser);
        return TRUE;
    }

    /**
    * Method to send the email via the kng mailer.
    *
    * @access public
    * @param string $name Recipient name
    * @param string $subject Email subject
    * @param string $email Recipient email address
    * @param string $body Email body
    * @param string $fromEmail Senders email address
    * @param string $fromUser Senders name
    * @return
    */
    public function email($name, $subject, $email, $body, $fromEmail = NULL, $fromUser = NULL)
    {
        if(is_null($fromEmail)){
            $fromEmail = $this->email;
        }
        if(is_null($fromUser)){
            $fromUser = $this->user;
        }

        $this->objMailer->setup($fromEmail, $fromUser);
        $this->objMailer->sendMail($name, $subject, $email, $body);
        return TRUE;
    }

    /**
    * Method to set a session variable containing the body of the email.
    *
    * @access public
    * @param string $body The body of the email
    * @return
    */
    public function setEmailBody($body)
    {
        $this->setSession('emailbody', $body);
    }

    /**
    * Method to display a page for inputting an email address to email a search result set.
    *
    * @access public
    * @return string html
    */
    public function showEmail()
    {
        if(empty($this->heading)){
            $this->heading = $this->objLanguage->languageText('phrase_emailresults');
        }
        if(empty($this->subject)){
            $this->subject = $this->objLanguage->languageText('phrase_searchresults');
        }
        $name = $this->objLanguage->languageText('word_to');
        $emailAdd = $this->objLanguage->languageText('phrase_emailaddress');
        $subject = $this->objLanguage->languageText('word_subject');
        $comment = $this->objLanguage->languageText('word_email');
        $send = $this->objLanguage->languageText('word_send');
        $errEmail = $this->objLanguage->languageText('mod_etd_entervalidemail', 'etd');

        $address = ''; $receiver = '';
        if(isset($this->recipientEmail) && !empty($this->recipientEmail)){
            $address = $this->recipientEmail;
            $receiver = $this->recipientName;            
        }else if($this->objUser->isLoggedIn()){
            $address = $this->email;
            $receiver = $this->user;
        }
        
        $this->objHeading->str = $this->heading;
        $this->objHeading->type = 1;
        $str = $this->objHeading->show();

        // Receiver
        $objLabel = new label($name.':&nbsp;&nbsp;', 'input_name');
        $objInput = new textinput('name', $receiver, '', 53);
        if($this->readonlyRecName){
            $objInput->extra = ' readonly="READONLY" ';
        }
        $str .= '<p>'.$objLabel->show().'<br />'.$objInput->show().'</p>';

        // Email address
        $objLabel = new label($emailAdd.':&nbsp;&nbsp;', 'input_address');
        $objInput = new textinput('address', $address, '', 53);
        if($this->readonlyRecEmail){
            $objInput->extra = ' readonly="READONLY" ';
        }

        $str .= '<p>'.$objLabel->show().'<br />'.$objInput->show().'</p>';

        // Subject
        $objLabel = new label($subject.':&nbsp;&nbsp;', 'input_subject');
        $objInput = new textinput('subject', $this->subject, '', 53);
        if($this->readonlySubject){
            $objInput->extra = ' readonly="READONLY" ';
        }

        $str .= '<p>'.$objLabel->show().'<br />'.$objInput->show().'</p>';

        // Email body / comment
        $objLabel = new label($comment.':&nbsp;', 'input_comment');
        $objText = new textarea('comment', $this->emailBody, 10, 70);

        $str .= '<p>'.$objLabel->show().'<br />'.$objText->show().'</p>';

        // Message
        if(isset($this->message) && !empty($this->message)){
            $str .= '<p>'.$this->message.'</p>';
        }

        // Send button
        $objButton = new button('send', $send);
        $objButton->setToSubmit();

        $str .= '<p>'.$objButton->show().'</p>';

        $objForm = new form('sendemail', $this->uri(array('action' => 'sendemail'), $this->module));
        $objForm->addToForm($str);
        
        // Validation - requires an email address to send
        $objForm->addRule('address', $errEmail, 'required');
        $objForm->addRule('address', $errEmail, 'email');

        return $objForm->show();
    }
}
?>