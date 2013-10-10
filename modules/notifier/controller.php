<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
	die("You cannot view this page directly");
}
// end security check
class notifier extends controller
{
	public $objLog;
	public $objLanguage;
	public $objSubsOps;
	public $jruntime = 3600;
	public $jcbkfreq = 1; // callback every second.
	public $jab;
	public $countdown;
	public $objBackground;

	/**
     * Constructor method to instantiate objects and get variables
     */
	public function init()
	{
		try {
			$this->objLanguage = $this->getObject('language', 'language');
			$this->objSubsOps = $this->getObject('notifyops');
			$this->objBackground = $this->getObject('background', 'utilities');
			//Get the activity logger class
			//$this->objLog = $this->newObject('logactivity', 'logger');
			//Log this module call
			//$this->objLog->log();
		}
		catch(customException $e) {
			echo customException::cleanUp();
			die();
		}
	}

	/**
     * Method to process actions to be taken
     *
     * @param string $action String indicating action to be taken
     */
	public function dispatch($action = Null)
	{
		switch ($action) {
			default:
				$this->objSubsOps->startJabber();
				$this->jserver = 'localhost';
				$juser = 'test';
				$jpass = 'test';
				require_once($this->getResourcePath("class_Jabber.php", 'notifier'));
				// create an instance of the Jabber class
				$display_debug_info = false;
				$jab = new Jabber($display_debug_info);
				if (!$jab->connect($this->jserver))
				{
					echo ("Could not connect to the Jabber server!");
				}
				$jab->login($juser, $jpass);
				// browser for transport gateways
				$jab->browse();
				// retrieve this user's roster
				$jab->get_roster();
				// set this user's presence
				$jab->set_presence("","Talk to me bitches and hos!");
				//var_dump($jab);
				var_dump($jab->message('paul@localhost/Gajim',"chat",NULL,"Hello! fucker", NULL, NULL, NULL, TRUE));
				echo "Wazzup"; die();
				break;
			case 'startserver':
				// start the module for 24 hours (86400 seconds) on cron.daily.
				$status = $this->objBackground->isUserConn();
				$callback = $this->objBackground->keepAlive();
				
				$this->objSubsOps->startJabber();
				$this->jserver = 'localhost';
				$this->juser = 'john';
				$this->jpass = 'john';
				// include the Jabber class
				require_once($this->getResourcePath("class_Jabber.php", 'notifier'));
				// create an instance of the Jabber class
				$display_debug_info = false;
				$this->jab = new Jabber($display_debug_info);
				$this->objSubsOps->messenger(&$this->jab);
				$this->jab->set_handler("connected",$this->objSubsOps,"handleConnected");
				$this->jab->set_handler("authenticated",$this->objSubsOps,"handleAuthenticated");
				$this->jab->set_handler("authfailure",$this->objSubsOps,"handleAuthFailure");
				$this->jab->set_handler("heartbeat",$this->objSubsOps,"handleHeartbeat");
				$this->jab->set_handler("error",$this->objSubsOps,"handleError");
				$this->jab->set_handler("message_normal",$this->objSubsOps,"handleMessage");
				$this->jab->set_handler("message_chat",$this->objSubsOps,"handleMessage");
				$this->jab->set_handler("debug_log",$this->objSubsOps,"handleDebug");
				$this->jab->set_handler("rosterupdate",$this->objSubsOps,"handleRosterUpdate");
				// connect to the Jabber server
				if (!$this->jab->connect($this->jserver))
				{
					log_debug("Could not connect to the Jabber server!");
				}
				$this->jab->execute($this->jcbkfreq, $this->jruntime);
				
				$call2 = $this->objBackground->setCallBack("pscott@uwc.ac.za", "Your long thing is done", "The long running process that you requested is finished");
				break;
		}
	}
}
?>