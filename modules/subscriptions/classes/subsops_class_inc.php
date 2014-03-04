<?php
/* -------------------- string class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}

/**
* Class for working with the subscriptions module. 
* 
* @author Paul Scott
*/
class subsops extends object
{
	public $jserver;
	public $juser;
	public $jpass;
	public $jruntime = 60;
	public $jcbkfreq = 1; // callback every second.
	public $jab;
	public $countdown;
	public $objLanguage;

	public function init()
	{
		$this->objLanguage = $this->getObject('language', 'language');
	}

	public function startJabber()
	{
		$this->jserver = 'localhost';
		$this->juser = 'john';
		$this->jpass = 'john';

		$this->factory();
	}

	public function messenger(&$jab)
	{
		$this->jab = &$jab;
		$this->first_roster_update = true;
		$this->countdown = 0;
	}

	// called when a connection to the Jabber server is established
	public function handleConnected()
	{
		$this->jab->login($this->juser,$this->jpass);
	}

	// called after a login to indicate the the login was successful
	public function handleAuthenticated()
	{
		// browser for transport gateways
		$this->jab->browse();
		// retrieve this user's roster
		$this->jab->get_roster();
		// set this user's presence
		$this->jab->set_presence("","Talk to me bitches!");
	}

	// called after a login to indicate that the login was NOT successful
	public function handleAuthFailure($code, $error)
	{
		log_debug("Jabber server Authentication failure: $error ($code)");
		// set terminated to TRUE in the Jabber class to tell it to exit
		$this->jab->terminated = true;
	}

	// called periodically by the Jabber class to allow us to do our own
	// processing
	public function handleHeartbeat()
	{
		// if the countdown is in progress, determine if we need to take any action
		if ($this->countdown > 0)
		{
			$this->countdown--;
			if ($this->countdown == 9)
			{
				log_debug("Send message");
				$this->jab->composing($this->last_msg_from,$this->last_msg_id);
				$this->jab->composing($this->last_msg_from,$this->last_msg_id,false);
				$this->jab->message($this->last_msg_from,"chat",NULL,"Hello! You said: ".$this->last_message);
				$this->countdown = 0;
			}
		}
		else {
			log_debug("Waiting for incoming message ...");
		}
	}

	// called when an error is received from the Jabber server
	public function handleError($code, $error, $xmlns)
	{
		log_debug("Error: $error ($code)".($xmlns?" in $xmlns":""));
	}

	// called when a message is received from a remote contact
	public function handleMessage($from, $to, $body, $subject, $thread, $id, $extended)
	{
		//echo "Incoming message!\n";
		//echo "From: $from\t\tTo: $to\n";
		//echo "Subject: $subject\tThread; $thread\n";
		//echo "Body: $body\n";
		//echo "ID: $id\n";
		log_debug($extended);
		$this->last_message = $body;
		$this->last_msg_id = $id;
		$this->last_msg_from = $from;
		$this->countdown = 10;
	}

	private function _contact_info($contact)
	{
		return sprintf("Contact %s (JID %s) has status %s and message %s\n",
		$contact['name'],$contact['jid'],$contact['show'],
		$contact['status']);
	}

	public function handleRosterUpdate($jid)
	{
		if ($this->first_roster_update)
		{
			// the first roster update indicates that the entire roster has been
			// downloaded for the first time
			foreach ($this->jab->roster as $k=>$contact)
			{
				log_debug($this->_contact_info($contact));
			}
			$this->first_roster_update = false;
		}
		else {
			// subsequent roster updates indicate changes for individual roster items
			$contact = $this->jab->roster[$jid];
			log_debug("Contact updated: " . $this->_contact_info($contact));
		}
	}

	public function handleDebug($msg, $level)
	{
		log_debug("DBG: $msg");
	}

	public function factory()
	{
		// include the Jabber class
		require_once($this->getResourcePath("class_Jabber.php", 'subscriptions'));
		// create an instance of the Jabber class
		$display_debug_info = false;
		$jab = new Jabber($display_debug_info);

		// create an instance of our event handler class
		$instance = $this->messenger($jab);

		// set handlers for the events we wish to be notified about
		$jab->set_handler("connected",$instance,"handleConnected");
		$jab->set_handler("authenticated",$instance,"handleAuthenticated");
		$jab->set_handler("authfailure",$instance,"handleAuthFailure");
		$jab->set_handler("heartbeat",$instance,"handleHeartbeat");
		$jab->set_handler("error",$instance,"handleError");
		$jab->set_handler("message_normal",$instance,"handleMessage");
		$jab->set_handler("message_chat",$instance,"handleMessage");
		$jab->set_handler("debug_log",$instance,"handleDebug");
		$jab->set_handler("rosterupdate",$instance,"handleRosterUpdate");

		// connect to the Jabber server
		if (!$jab->connect($this->jserver)) 
		{
			log_debug("Could not connect to the Jabber server!\n");
		}

		// now, tell the Jabber class to begin its execution loop
		$jab->execute($this->jcbkfreq, $this->jruntime);

		// Note that we will not reach this point (and the execute() method will not
		// return) until $jab->terminated is set to TRUE.  The execute() method simply
		// loops, processing data from (and to) the Jabber server, and firing events
		// (which are handled by our TestMessenger class) until we tell it to terminate.
		//
		// This event-based model will be familiar to programmers who have worked on
		// desktop applications, particularly in Win32 environments.

		// disconnect from the Jabber server
		// $jab->disconnect();
		return;
	}
}
?>