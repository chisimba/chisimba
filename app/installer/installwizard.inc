<?php



/**

* 

*/



require_once dirname(__FILE__).'/wizard.inc';
require_once dirname(__FILE__).'/domtt/domtt_class_inc.php';


define('HELP_URL', 'http://cvs2.uwc.ac.za');



/**

* The install wizard

*

* install specific install wizard. Checks to ensure that the chosen install step doesn't require

* authentication, otherwise authenticates based on information entered by a user at some point.

*

* @author prince mbekwa based on the methods by mnyeholt

* @version $Id

* @package Installer

*/

class InstallWizard extends Wizard

{

	/**

	* The steps that require authorization during install types

	* @var array

	*/

	var $protected_steps;
		

	/**

	* Setup the steps used in the install wizards

	* 

	* @return void

	* @access public

	*/

	public function InstallWizard()

	{

		$GLOBALS['SQ_INSTALL'] = true;

			

		$this->steps = Array('start',

							 'licence',

							 'installdirectory',

							 'systemdetails',

							 'versioncheck',

							 'setuptype',

							 'databasedetails',

							 'databasename',

							 'secondarydatabase',

							 'secondarydetails',

							 'createconfigs',

							 'databasecreate',

							 'complete'

							 );

		

		$this->protected_steps = Array('databasedetails',

										'databasename',

										'secondarydatabase',

										'secondarydetails',

										'systemsetup',

										'databasecreate',

										);

		

		$this->Wizard();

		$this->template = new Template(dirname(__FILE__).'/templates/installer.tpl');

	}//end InstallWizard()

	

	

	/**

	* Overridden to account for the sessions not being started for a install installer unless

	* step 1 has been completed

	* 

	* @return void

	* @access protected

	*/

	public function _startSession()

	{

		// start the session as normal.  

		// copy the existing session details into the new session (if they don't already

		// exist).

		

		parent::_startSession();

		

		

		if (isset($_GET['restart']) && isset($_SESSION)) {

			$this->_killSession();

		}

		

		// save what type of session, and where they're being saved to. This is so that when 

		// the session needs to be destroyed, it's known where to get them from.

		$_SESSION['session_type'] = 'installer';

		$_SESSION['install_save_path'] = session_save_path();

		

		// get the system root out of the currently set session and use it to start the correct session

		$sys_root = isset($_SESSION['sys_root']) ? $_SESSION['sys_root'] : false;

		

		if (($sys_root !== false) && (file_exists($sys_root.'/config/config.xml'))) {

			// check whether the main.xml has been created yet. If so, save the current session

			// details, close the session, and open the install session instead.

			if (file_exists($sys_root.'/conf/config.xml')) {

				$old_session = $_SESSION;

				session_write_close();

				//Get old setting from Main Config file

				

				$this->_importSessionVariables($old_session);

				

				$_SESSION['session_type'] = 'install';

				

				// set up some install specific variables into the session... ?

				if (!isset($_SESSION['dsn'])) $_SESSION['dsn'] = SQ_CONF_DB_DSN;

				if (!isset($_SESSION['dsn2'])) $_SESSION['dsn2'] = SQ_CONF_DB2_DSN;

				

				// get the first system root url entry.

				if (!isset($_SESSION['site_url'])) {

					$urls = explode("\n", SQ_CONF_SYSTEM_ROOT_URLS);

					$_SESSION['site_url'] = $urls[0];

				}

				

				// load the admin email address

				if (!isset($_SESSION['tech_email'])) $_SESSION['tech_email'] = SQ_CONF_TECH_EMAIL;

				if (!isset($_SESSION['root_email'])) $_SESSION['root_email'] = SQ_CONF_DEFAULT_EMAIL;

				if (!isset($_SESSION['sys_name'])) $_SESSION['sys_name'] = SQ_CONF_SYSTEM_NAME;

				if (!isset($_SESSION['sys_owner'])) $_SESSION['sys_owner'] = SQ_CONF_SYSTEM_OWNER;

				if (!isset($_SESSION['debug_level'])) $_SESSION['debug_level'] = SQ_CONF_DEBUG;

				if (!isset($_SESSION['log_path']) && defined('SQ_CONF_LOG_FILE')) $_SESSION['log_path'] = SQ_CONF_LOG_FILE;

								

				// load the dsn details

				$dsn_details = DB::parseDSN($_SESSION['dsn']);

				

				//echo 'dsn is '.$_SESSION['dsn'].', parseDSN is <pre>'.print_r($dsn_details, true).'</pre>';

								

				if (!isset($_SESSION['db_type'])) $_SESSION['db_type'] = $dsn_details['phptype'];

				if (!isset($_SESSION['db_loc']))  $_SESSION['db_loc']  = (strlen($dsn_details['hostspec']) > 0) ? $dsn_details['hostspec'] : $dsn_details['protocol'].'()';

				if (!isset($_SESSION['db_user'])) $_SESSION['db_user'] = $dsn_details['username'];

				if (!isset($_SESSION['db_pass'])) $_SESSION['db_pass'] = $dsn_details['password'];

				if (!isset($_SESSION['db_name'])) $_SESSION['db_name'] = $dsn_details['database'];

			} // end if main.xml exists

				

		}// end if sysroot is found

		

	}//end _startSession()

	

	

	/**

	* Load variables from an array into the current session

	* 

	* @param array $variables The variables to set into the session (if it isn't already set in the array)

	*				Structure is Array( 'session_key' => 'session_value' );

	* @return void

	* @access private

	*/

	private function _importSessionVariables($variables)

	{

		foreach($variables as $session_key => $session_value) {

			if (!isset($_SESSION[$session_key])) {

				$_SESSION[$session_key] = $session_value;

			}

		}

	}//end _importSessionVariables()

	

	

	/**

	* Override to make sure both sessions are killed with great prejudice

	* 

	* @return void

	* @access protected

	*/

	public function _killSession()

	{

		// save some variables out of the session first

		$session_type = $_SESSION['session_type'];

		$sys_root = isset($_SESSION['sys_root']) ? $_SESSION['sys_root'] : false;

		$session_path = isset($_SESSION['install_save_path']) ? $_SESSION['install_save_path'] : false ;

		

		// destroy the current session

		parent::_killSession();

		

		// now make sure that the session being used is the install session (might mean destroying it 

		// twice, but oh well)

		

		// If the session being used was the installer session, must kill the normal session too

		if ($session_type == 'install') {

			if ($session_path !== false) {

				// set the save path to that of the installer

				session_save_path($session_path);

			}

			// now start a new installer session and kill that too.

			session_start();

			

			$_SESSION = Array();

			unset($_SESSION);

			session_destroy();

		}

		

	}//end _killSession()

	

	

	/**

	* If the step needs authorization, do it before painting.

	* 

	* @return void

	* @access private

	*/

	public function _paintStep()

	{

		

		// protected install types are 'update', 'repair'

		$protected = Array('install');

		

		$inst = isset($_SESSION['install_type']) ? $_SESSION['install_type'] : '';

		

		$auth = false;

		

		$auth = isset($_SESSION['step_auth']) ? $_SESSION['step_auth'] : false;

		

		// if the install type is a protected one, and the user isn't authed, redirect to start.

		if (isset($_SESSION['session_type']) && in_array($_SESSION['session_type'], $protected)

			&& $this->active_step->auth_required && ($inst != 'install')) {

			// if authentication failed, redirect the user to the start action.

			if (!$auth) {

				$this->step_number = $this->_getStepByName('systemdetails');

				$this->errors[] = 'Authentication failed';

				$this->_loadStep();

			}

		}

		

		if (get_class($this->active_step) == 'completestep') {

			$status = "Congratulations, you won the install game!";

		} else {

			// figure out what step number we're on.

			$total_steps = count($this->steps);

			

			$current_step = array_search(get_class($this->active_step), $this->steps) + 1;

			

			$status = "Performing step $current_step / $total_steps ";

		}

		// save what the system root is

		//$sys_root = isset($_SESSION['sys_root']) ? $_SESSION['sys_root'] : '';

		

		$this->template->set('current_step', $this->step_number);

		$this->template->set('help_url', 'http://cvs.uwc.ac.za/'.$this->active_step->help_topic);

		$this->template->set('title_logo', $this->active_step->title_logo);

		$this->template->set('extras', './extra');

		$this->template->set('step_name', get_class($this->active_step));

		$this->template->set('center_image', $this->active_step->step_logo);

		$this->template->set('total_steps', $total_steps);

		$helpStr = "<a href=\"#\" onmouseover=\"this.style.color = '#D17E62'; domTT_activate(this, event, 'content', 'Chisimba Installer Help<p>".$this->active_step->step_help."</p>', 'trail', true, 'fade', 'both', 'fadeMax', 87, 'styleClass', 'niceTitle');\" onmouseout=\"this.style.color = ''; domTT_mouseout(this, event);\"><img src=\"./extra/yellow_help_off.png\" border=\"0\"></a>";
		
		$this->template->set('help',$helpStr);  //$this->template->set('help',$this->active_step->getHelpStr());

		

		parent::_paintStep();

		

	}//end _paintStep()

	

}

?>
