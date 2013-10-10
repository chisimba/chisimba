<?php
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * Class to handle im elements
 *
 * @author    Paul Scott
 * @copyright GNU/GPL, AVOIR
 * @package   blog
 * @access    public
 */
class dasops extends object {

    /**
     * Description for public
     * @var    object
     * @access public
     */
    public $objConfig;

    public $conn;
    // public $objXMPPLog;


    /**
     * Standard init function called by the constructor call of Object
     *
     * @param  void
     * @return void
     * @access public
     */
    public function init() {
        try {

            $this->objConfig = $this->getObject ( 'altconfig', 'config' );
            $this->objLanguage = $this->getObject ( 'language', 'language' );
            // Get the sysconfig variables for the Jabber user to set up the connection.
            $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
            $this->jserver = $this->objSysConfig->getValue ( 'jabberserver', 'im' );
            $this->jport = $this->objSysConfig->getValue ( 'jabberport', 'im' );
            $this->juser = $this->objSysConfig->getValue ( 'jabberuser', 'im' );
            $this->jpass = $this->objSysConfig->getValue ( 'jabberpass', 'im' );
            $this->jstatus = $this->objSysConfig->getValue ( 'jabberstatus', 'im' );
            $this->jshow = $this->objSysConfig->getValue ( 'jabbershow', 'im' );
            $this->jclient = $this->objSysConfig->getValue ( 'jabberclient', 'im' );
            $this->jdomain = $this->objSysConfig->getValue ( 'jabberdomain', 'im' );
			$this->timeLimit = $this->objSysConfig->getValue ( 'imtimelimit', 'im' );
			$this->dasfeedbackemail = $this->objSysConfig->getValue ( 'feedbackemail', 'das' );
            $this->objModules = $this->getObject ( 'modules', 'modulecatalogue' );

            $this->conn = new XMPPHP_XMPP ( $this->jserver, intval ( $this->jport ), $this->juser, $this->jpass, $this->jclient, $this->jdomain, $printlog = FALSE, $loglevel = XMPPHP_Log::LEVEL_ERROR );
        } catch ( customException $e ) {
            echo customException::cleanUp ();
            die ();
        }
    }

    public function sendMessage($to, $message) {
        try {
            //$this->conn->connect();
            //$this->conn->processUntil('session_start');
            //$this->conn->presence();
            // send the message
            $this->conn->message ( $to, $message );
            // disconnect
        //$this->conn->disconnect();
        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
    }

    /**
     * Method to display the login box for prelogin blog operations
     *
     * @param  bool   $featurebox
     * @return string
     */
    public function loginBox($featurebox = FALSE) {
        $objLogin = $this->getObject ( 'logininterface', 'security' );
        $objRegister = $this->getObject ( 'block_register', 'security' );
        if ($featurebox == FALSE) {
            return $objLogin->renderLoginBox ( 'im' ) . "<br />" . $objRegister->show ();
        } else {
            $objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
            return $objFeatureBox->show ( $this->objLanguage->languageText ( "word_login", "system" ), $objLogin->renderLoginBox ( 'im' ) . "<br />" . $objRegister->show () );
        }
    }

    public function showMassMessageBox($featurebox = FALSE, $editor = FALSE, $module="das") {
        if ($editor == FALSE) {
            $form = $this->massmessageform ( FALSE );
        } else {
            $form = $this->massmessageform ( TRUE );
        }
        if ($featurebox == FALSE) {
            return $form->show ();
        } else {
            $objFeatureBox = $this->getObject ( 'featurebox', 'navigation' );
            return $objFeatureBox->show ( $this->objLanguage->languageText ( "mod_im_massmsg", "im" ), $form->show () );
        }
    }

    /**
	* Method to post a bulk massage
	* to all subscribed users
	*/
	public function sendToSubscribers()
	{
		$objFB = $this->getObject('featurebox', 'navigation');
		 $ajax = "<p class=\"im_source\" id=\"massalldiv\">Ready...</p>
            
			 <script charset=\"utf-8\">
                            new Ajax.InPlaceEditor('massalldiv', 'index.php', {rows:10,cols:13, callback: function(form, value) { return 'module=das&action=sendtoall&message=' + escape(value) }})
                        </script>
			";

		return '<p>'.$objFB->show("Send a message to subscribers", $ajax).'</p>';
	}
	
	/**
	* Method to post a massage
	*/
	public function massMessage()
	{
		$objFB = $this->getObject('featurebox', 'navigation');
		 $ajax = "<p class=\"im_source\" id=\"massdiv\">Ready...</p>
            
			 <script charset=\"utf-8\">
                            new Ajax.InPlaceEditor('massdiv', 'index.php', {rows:10,cols:13, callback: function(form, value) { return 'module=das&action=massmessage&msg=' + escape(value) }})
                        </script>
			";

		return '<p>'.$objFB->show("Send a message to this session's subscribers", $ajax).'</p>';
	}
	
	
	
    private function massmessageform($editor = FALSE) {
        try {
            $this->loadClass ( 'form', 'htmlelements' );
            $this->loadClass ( 'textinput', 'htmlelements' );
            $this->loadClass ( 'textarea', 'htmlelements' );
            $this->loadClass ( 'button', 'htmlelements' );
            //$this->loadClass('htmlarea', 'htmlelements');
            $this->loadClass ( 'dropdown', 'htmlelements' );
            $this->loadClass ( 'label', 'htmlelements' );
            $objCaptcha = $this->getObject ( 'captcha', 'utilities' );
        } catch ( customException $e ) {
            customException::cleanUp ();
            exit ();
        }
        $required = '<span class="warning"> * ' . $this->objLanguage->languageText ( 'word_required', 'system', 'Required' ) . '</span>';
        $cform = new form ( 'massmsg');
	$cform->action = $this->uri ( array ('action' => 'massmessage' ,'module' => 'das') ) ;
        $cfieldset = $this->getObject ( 'fieldset', 'htmlelements' );
        $ctbl = $this->newObject ( 'htmltable', 'htmlelements' );
        $ctbl->cellpadding = 5;

        //textarea for the message
        $commlabel = new label ( $this->objLanguage->languageText ( 'mod_im_message', 'im' ) . ':', 'input_comminput' );
        $ctbl->startRow ();
        $ctbl->addCell ( $commlabel->show () );
        $ctbl->endRow ();
        $ctbl->startRow ();
        if ($editor == TRUE) {
            //echo "start";
            $comm = $this->getObject ( 'htmlarea', 'htmlelements' );
            $comm->setName ( 'msg' );
            $comm->height = 200;
            $comm->width = '100%';
            $comm->setBasicToolBar ();
            $ctbl->addCell ( $comm->showFCKEditor () );
        } else {
            $comm = new textarea ( );
            $comm->setName ( 'msg' );
            $comm->height = 200;
            $comm->width = '100%';
            $ctbl->addCell ( $comm->show () );
        }
        $ctbl->endRow ();

        //end off the form and add the buttons
        $this->objCButton = &new button ( $this->objLanguage->languageText ( 'mod_im_send', 'im' ) );
        $this->objCButton->setValue ( $this->objLanguage->languageText ( 'mod_im_send', 'im' ) );
        $this->objCButton->setToSubmit ();

        $cfieldset->addContent ( $ctbl->show () );
        $cform->addToForm ( $cfieldset->show () );
        $cform->addToForm ( $this->objCButton->show () );

        return $cform;
    }

    public function parseSysMessages($pl) {
        // first check the body for system commands...
        log_debug ( $pl ['body'] );

    }


		/**
	* Method to evoke the python script to start the session
	*/
	public function startSession()
	{
		$username = $this->juser.'@'.$this->jdomain;
		$password = $this->jpass;
		$dbname = $this->objSysConfig->getValue ( 'imdbname', 'im' );
		$dbusername = $this->objSysConfig->getValue ( 'imdbusername', 'im' );
		$dbhost = $this->objSysConfig->getValue ( 'imdbhost', 'im' );
		$dbpassword = $this->objSysConfig->getValue ( 'imdbpassword', 'im' );
		$pathToScript = $this->objSysConfig->getValue ( 'imdaemonpath', 'im' );
		$exeString = "python $pathToScript/messagehandler.py $username $password $dbhost $dbusername $dbpassword $dbname";
		exec($exeString. " > /dev/null &");
		
	}

	/**
	* Method to kill the python script
	*/
	public function endSession($username)
	{

		//return exec("killall python");
		$pids = $this->getPID($username);
		if(count($pids) > 0)
		{
			foreach($pids as $pid)
			{			
				return exec("kill ".$pid);
			}
		}
	}

	/**
	* Method to check if the script is running
	*/
	public function isScriptRunning($param)
	{

		if(count($this->getPID($username)) > 0)
		{
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	* Method to get PID of processes
	* @author James Scoble
	* @param string $param
	* @returns array
	*/
	function getPID($param)
	{
		 exec("ps aux", $result);
	
		 $r2=array();
		 foreach ($result as $line)
		 {
			
			if (strpos($line,$param))
			{				
				$l2=substr($line,strpos($line,' '),-1);
				$l2=trim($l2);
				$l2=substr($l2,0,strpos($l2,' '));
				$l2=trim($l2);
				$r2[]=$l2;
			}
		 }
		 return $r2;
	}

	/**
	* Method to config the session
	*/
	public function getConfigBlock()
	{
		$str = "";
		$form = $this->newObject('form', 'htmlelements');
		$this->loadClass('button', 'htmlelements');
		$this->loadClass('textinput', 'htmlelements');
		/*$daspassword = $this->newObject('textinput', 'htmlelements');
		$idletime = $this->newObject('textinput', 'htmlelements');
		$domain = $this->newObject('textinput', 'htmlelements');
		*/
		$form->action = $this->uri(array('action' => 'savesettings'));
		$form->setDisplayType(2);
		
		$button = new  button();
		$button->setToSubmit();
		$button->setValue('Save');
		$dasusername = new textinput('dasusername', $this->juser, null, 10);
		$daspassword = new textinput('daspassword', $this->jpass,null, 10);
		$dasfeedback = new textinput('dasfeedbackemail', $this->dasfeedbackemail,null, 10);
		$daspassword->setType('password');
		$domain = new textinput('domain', $this->jdomain, null, 10);
		$idletime = new textinput('idletime', $this->timeLimit, null, 10);

		$dasusername->label = 'Username';
		$daspassword->label = 'Password';
		$dasfeedback->label = 'Feedback email';
		$domain->label = 'Domain  @';
		$idletime->label = 'Idle Time';
		$button->label = '&nbsp;';

		$form->addToForm($dasusername);
		$form->addToForm($domain);
		$form->addToForm($daspassword);
		$form->addToForm($idletime);
		$form->addToForm($dasfeedback);
		$form->addToForm($button);		

		return $form->show();
	
	}
	
	/**
	 * Method to get the status block
	 * 
	 */
	public function getStatusBlock()
	{
		$str = "";
		$form = $this->newObject('form', 'htmlelements');
		$this->loadClass('button', 'htmlelements');
		$this->loadClass('textinput', 'htmlelements');
		$this->loadClass('dropdown', 'htmlelements');
		
		$form->action = $this->uri(array('action' => 'savestatus'));
		$form->setDisplayType(2);
		
		$button = new  button();
		$button->setToSubmit();
		$button->setValue('Save');
		$dasstatus = new textinput('status', $this->jstatus, null, 10);
		//$dasshow = new textinput('show', $this->jshow,null, 10);
		$dasshow = new dropdown('show');
		
		$dasshow->addOption('', '<img src="http://sitepointstatic.com/forums/images/statusicon/user_offline.gif">Available');
		$dasshow->addOption('away', 'Away');
		$dasshow->addOption('dnd', 'Do Not Disturb');
		$dasshow->addOption('unavailable', 'Invisible');
		$dasshow->addOption('unavailable', 'Offline');
		
		$dasshow->setSelected($this->jshow);
		
		$dasstatus->label = 'Message';
		$dasshow->label = 'Status';
		
		$form->addToForm($dasstatus);
		$form->addToForm($dasshow);
		$form->addToForm($button);		

		return $form->show();
		
	}
	
	/**
	* This method send an email to the user config jabber email
	*@param string $personId
	*@return boolean
	*/
	public function sendFeedBack($personId)
	{
		//get the conversation
		$objViewRender = $this->getObject('viewrender', 'das');
		$body = $objViewRender->getCurrentSessionMessages($personId);
		
		//get the service email
		$to = $this->dasfeedbackemail; //'$this->wnitsckie@uwc.ac.za, wesleynitsckie@gmail.com';//$this->juser.'@'.$this->jdomain;
		$subject = "Feedback from ".$this->objConfig ->getSiteName();
		$from  = $this->juser.'@'.$this->jserver;
		
		//set mailer
		//$objEmail = $this->getObject('mailer', 'mail');
		//$objEmail->setBaseMailerProperty('to', $to);
		//$objEmail->setBaseMailerProperty('subject', $subject);
		//$objEmail->setBaseMailerProperty('body', $body);
		//send the mail
		//return $objEmail->send();
		
		//return mail($to, $subject,$body);		
		
		//$to      = 'wesleynitsckie@gmail.com';
		//$subject = 'the subject';
		//$message = 'hello';
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$from . "\r\n" .
			'Reply-To: '.$from . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

		return mail($to, $subject, $body, $headers);
	}
	
	/**
	* Method to evoke the python script to start the session
	*/
	public function sendToAll($message)
	{
		$username = $this->juser.'@'.$this->jdomain;
		$password = $this->jpass;
		$pathToScript = $this->objSysConfig->getValue ( 'imdaemonpath', 'im' );
		$exeString = "python $pathToScript/bulk_messager.py $username $password \"$message\"";
		exec($exeString. " > /dev/null &");
		
	}
	
}
?>
