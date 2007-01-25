<?php
require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/5ivestepaction.inc';

require_once INSTALL_DIR.'/html_form/html_form.inc';

require_once 'Config.php';

/**

* SystemSetup

*

* Initialises 5ive to be installed.

*

* @author prince mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class CreateConfigs extends Step

{

	/**

	* Set up this step's actions

	*

	* @return void

	* @access public

	*/

	function CreateConfigs()

	{



		$this->actions[] =& new CreateConfigsAction(get_class($this));



		$this->step_title = 'Create System Configuration ';

		$this->conf_required = true;

		$this->title_logo = 'create_system_config.gif';

		$this->help_topic = 'create_system_config';

		$this->can_cancel = false;


		$this->step_help = "Plase allow Chisimba to create database and the configuration files";


	}//end CreateConfigs()





}//end class





/**

* SystemSetupAction

*

* Creates the main.inc defines file

*

*/

class CreateConfigsAction extends FiveStepAction

{



	protected  $_root;

	/**

	* The output of the step01.php script

	* @var string

	*/

	protected $init_output = '';



	/**

	* The config variables to change

	* @var array vars('define_name' => 'value');

	*/

	var $config_vars = Array();



	/**

	* Initialise the 5ive system.

	*

	* @return boolean Whether all the database tables were created or not.

	* @access public

	*/

	function processAction()

	{



		// if ignoring errors (ie the script has run, just want to proceed because the errors found

		// are ignoreable), the checkbox will have been clicked

		if (isset($_POST['ignore_errors'])) {

			return true;

		}



		// make sure that SQ_SYSTEM_ROOT is defined before trying to execute the script.

		if (isset($_SESSION['sys_root'])) {

			$this->_addConfigVar('KEWL_SITEROOT_PATH', $_SESSION['sys_root']);

		} else {

			$this->errors[] = 'The system root could not be found.';

			$this->success = false;

			return false;

		}



		// make sure there is a DSN available for both the primary and secondary connections.

		if (!$this->_setDSN()) {

			$this->success = false;

			return false;

		}



		// set the SQ_CONF_SYSTEM_ROOT_URLS define to $_SESSION['site_url']

		$root_url = isset($_SESSION['site_url']) ? $_SESSION['site_url'] : false;

		if ($root_url === false) {

			$this->success = false;

			return false;

		}



		// if there is already a root url defined, check if it's already defined in the list of

		// root urls.

		if (defined('KEWL_SITE_ROOT')) {

			if (strpos(KEWL_SITE_ROOT, $root_url) === false) {

				$root_url = KEWL_SITE_ROOT."\n$root_url";

			} else {

				$root_url = KEWL_SITE_ROOT;
				$root_url = $_SELF . '../index.php';

			}


		}



		$_SESSION['end_url'] = $root_url;

		$this->_addConfigVar('KEWL_SITE_ROOT', 'http://'. $root_url . '/');



		// was the database (re) created?

		$database_created = isset($_SESSION['create_db']) && $_SESSION['create_db'];



		// if this is a Repair, and the database was recreated in the dbnameaction step,

		// need to delete all the /data stuff and recreate the system's pre-install

		// state.

		if (isset($_SESSION['install_type']) && ($_SESSION['install_type'] == 'repair') && $database_created) {

			if (!$this->_restoreDirectories()) {

				$this->success = false;

				return false;

			}

		}



		// Create the configs

		$this->_createConfig($database_created);



		// Check to see if the configs actually were created.

		if (!$this->isRequired()==true) {

			$this->errors[] = 'Config files were NOT successfully created. The following output was given '.

								'by the config writing script';

			$this->errors[] = $this->init_output;

		}



		// if it was in repair mode, can now change to being in update mode because all the system settings

		// have been updated.

		if (strcmp($_SESSION['install_type'], 'repair') == 0) {

			$_SESSION['install_type'] = 'update';

		}



		return true;



	}//end processAction()





	/**

	* Create the config

	*

	* @param boolean $empty_database Whether the database is empty or not.

	* @return void

	* @access private

	*/

	function _createConfig($empty_database=true)

	{

		$ref = getenv('HTTP_REFERER');
		if (preg_match("/\/installer/i", $ref)) {

			$ksr = str_replace('/installer',"/",$ref);
			$ksr = str_replace('/index.php','',$ksr);
			$ksr = str_replace('app//','app/',$ksr);
		}
		ob_start();

			$SQ_INCLUDE_PATH = defined('SQ_INCLUDE_PATH') ? SQ_INCLUDE_PATH : $_SESSION['sys_root'].'/core/include';

			ini_set('memory_limit', -1);

			error_reporting(E_ALL & ~E_NOTICE);

			define('KEWL_SYSTEM_ROOT',   dirname(dirname(realpath(__FILE__))).'/');

			$this->_addConfigVar('KEWL_INSTITUTION_SHORTNAME',  $_SESSION['sys_owner']);

			$this->_addConfigVar('KEWL_INSTITUTION_NAME',      $_SESSION['sys_owner']);

			$this->_addConfigVar('KEWL_PROXY',     $_SESSION['proxy']);

			$this->_addConfigVar('KEWL_SYSTEMTIMEOUT', 60);

			$this->_addConfigVar('KEWL_DEFAULTICONFOLDER', '/icons/');

			$this->_addConfigVar('KEWL_DEFAULT_SKIN', $_SESSION['defaultskin']);

			$this->_addConfigVar('KEWL_DEFAULT_LANGUAGE', 'english');

			$this->_addConfigVar('KEWL_DEFAULT_LANGUAGE_ABBREV', 'EN');

			$this->_addConfigVar('KEWL_BANNER_EXT', 'jpg');

			$this->_addConfigVar('KEWL_POSTLOGIN_MODULE',  $_SESSION['postLogin']);
                        // Added by jsc
			$this->_addConfigVar('KEWL_SYSTEM_TYPE',  $_SESSION['SysType']);

			$this->_addConfigVar('KEWL_MODULE_PATH',  $_SESSION['module_path']);

			$this->_addConfigVar('KEWL_PRELOGIN_MODULE', 'prelogin');

			$this->_addConfigVar('KEWL_DEFAULT_LAYOUT_TEMPLATE', 'default_layout_tpl.php');

			$this->_addConfigVar('KEWL_LOGIN_TEMPLATE', 'login_tpl.php');

			$this->_addConfigVar('KEWL_LOGGED_IN_TEMPLATE', 'loggedin_tpl.php');

			$this->_addConfigVar('KEWL_TEMPLATE_PATH', $_SESSION['sys_root'].'/templates/');

			$this->_addConfigVar('KEWL_CONTENT_BASEPATH', $_SESSION['contentPath']);

			$this->_addConfigVar('KEWL_CONTENT_PATH', $_SESSION['relContentPath']);

			$this->_addConfigVar('KEWL_SITENAME', $_SESSION['sys_name']);

			$this->_addConfigVar('KEWL_SERVERNAME', $_SESSION['serverName']);

			$this->_addConfigVar('KEWL_MAINSERVERNAME', $_SESSION['main_server']);

			$this->_addConfigVar('KEWL_SERVERNAME_GENERATED', 'TRUE');

			$this->_addConfigVar('KEWL_SKIN_ROOT', 'skins/');

			$this->_addConfigVar('KEWL_SERVERLOCATION', $_SESSION['serverLocation']);

			$this->_addConfigVar('KEWL_SITEROOT', $ksr); //'http://'.$_SESSION['site_url'].'/');


			$this->_addConfigVar('KEWL_ALLOW_SELFREGISTER','TRUE');

			$this->_addConfigVar('KEWL_ENABLE_LOGGING', $_SESSION['log_enable']);

			$this->_addConfigVar('LDAP_USED', 'FALSE');

			$this->_addConfigVar('KEWL_ERROR_REPORTING', 'production'); //$_SESSION['debug_enable']);

			$this->_addConfigVar('KEWL_SYSTEM_OWNER', $_SESSION['sys_owner']);

			$this->_addConfigVar('KEWL_SITEEMAIL', $_SESSION['root_email']);

			$this->_addConfigVar('KEWL_TECH_EMAIL',    $_SESSION['tech_email']);

			$this->_addConfigVar('KEWL_LOG_FILE',    $_SESSION['log_path']);

			$this->_addConfigVar('KEWL_DEFAULT_PAGE_TEMPLATE', 'default_page_xml.php');

			// if this is an install, then none of the config vars are going to be defined, so

			// go through and define them all before trying to save them.

			if ($_SESSION['install_type'] == 'install') {

				foreach ($this->config_vars as $name => $val) {

					if (!defined($name)) {

						define($name, $val);

					}

				}

			}



			$GLOBALS['SQ_INSTALL'] = true;



			// when saving with altered variables, the root user gets sent a message notifying

			// of the changes. If the 5ive session is the current one, make sure the

			// asset types are loaded so this message can be sent.

			if ($_SESSION['session_type'] == 'install' && !$empty_database) {

				$am = &$GLOBALS['SQ_SYSTEM']->am;

				$am->_loadAssetTypes();

			}



			$this->_root = new Config();
			//initial setup of config options
			$options = array('name' => 'Settings');

			//parse the array values into config
			$this->_root->parseConfig($this->config_vars,"PHPArray");
			//lets write the config
			$path = $_SESSION['sys_root'].'/config/';
			if(!file_exists($path))
			{
				mkdir($path);
			}
				
			if(!is_file($path . 'config.xml'))
			{
				touch($path . 'config.xml');
				chmod($path . 'config.xml', 0777);
				}
			$write = $this->_root->writeConfig("{$path}config.xml",'XML', $options);
		    $GLOBALS['SQ_INSTALL'] = false;

			$result = ob_get_contents();

		ob_end_clean();

		$this->init_output = $result;



	}//end _createConfig()





	/**

	* Restores the directory structure for a 5ive install to what it was previous to

	* having the installer run the first time.

	* @todo merge missing modules from cvs

	* @return void

	* @access private

	*/

	function _restoreDirectories()

	{

		// first off, delete the sysroot/data directory.

		$sys_root = isset($_SESSION['sys_root']) ? $_SESSION['sys_root'] : false;

		if ($sys_root === false) {

			$this->errors[] = 'Could not find system root.';

			return false;

		}

		/**

		 * @todo merge missing modules from cvs

		 *

		 */

		return true;



	}//end _restoreDirectories()





	/**

	* Set the DSN using the values collected earlier during the install

	*

	* @return boolean Whether the DSN could be set properly

	* @access private

	*/

	function _setDSN()

	{

		$dsn = isset($_SESSION['dsn']) ? $_SESSION['dsn'] : false;


		if ($dsn === false) {

			$this->errors[] = 'Data Source Name was not set correctly';

			return false;

		}



		if (!$this->_testDSN($dsn)) {

			return false;

		}



		//$this->_addConfigVar('KEWL_DB_DSN', $dsn);



		// check the secondary database connector.

		$dsn2 = isset($_SESSION['dsn2']) ? $_SESSION['dsn2'] : false;



		if ($dsn2 === false) {

			$this->errors[] = 'Secondary DSN was not set correctly';

			return false;

		}



		if (!$this->_testDSN($dsn2)) {

			return false;

		}


		//add the two dsn's to a php file, not the config file
		//create a file to store the info as a heredoc

		$str = <<<EOD
<?php
	define('KEWL_DB_DSN','{$dsn}');
	define('KEWL_DB2_DSN','{$dsn2}');
?>
EOD;

		$path =  $_SESSION['sys_root'].'/config/';
            try {
		if(!file_exists($path)) {
				mkdir($path);
		}
			
		if (!is_file($path . 'dbdetails_inc.php')) {
			touch($path . 'dbdetails_inc.php');
			chmod($path . 'dbdetails_inc.php', 0777);
		}
		$handle = fopen($path . 'dbdetails_inc.php', 'wb');
		fwrite($handle, $str);
		fclose($handle);
                // Error check added by jsc
                if (!file_exists($path.'dbdetails_inc.php')) {
                    throw new Exception ('dbdetails file could not be created.');
                }
            } catch ( Exception $e) {
                print "The installer has encountered a fatal error: <b>".$e->getMessage()."</b><br />\n";
                print "Check that the chisimba folder has the proper write permissions.<br />\n";
                exit;
            }
		//define('KEWL_DB_DSN', $dsn);
		//$this->_addConfigVar('KEWL_DB2_DSN', $dsn2);



		return true;



	}//end _setDSN()





	/**

	* Add values to the config vars array

	*

	* @param string $var_name The name of the variable

	* @param string $value The value to set

	* @return void

	* @access private

	*/

	function _addConfigVar($var_name, $value)

	{

		$this->config_vars[$var_name] = $value;

	}//end _addConfigVar()





	/**

	* Return whether this action is required or not

	*

	* if the sysconfig_properties file exists, then the user can skip this action

	*

	* @return boolean if this action is required to complete the step.

	* @access public

	*/

	function isRequired()

	{

		$main_file = isset($_SESSION['sys_root']) ? $_SESSION['sys_root'].'/conf/sysconfig_properties.xml' : false;
		// if the file exists, try and delete it

		if ($main_file !== false && is_file($main_file)) {

			return false;

		}



		return true;



	}//end isRequired()





	/**

	* Display information about the creation status to the user

	*

	* @return void

	* @access public

	*/

	function paintAction()

	{

		$tpl = new Template(INSTALL_DIR.'/templates/oldstep.tpl');



		$warning_text = '';

		$message = '';

		$message = 	'Chisimba will now try and create the initial setup files. If '.

					'you are running an update, this will save a backup of your '.

					'old configuration file to the same directory. ';



		$warning_text = '<span style="color: #BB2222">Note that from this point on, if you do '.

							'not complete the install you may corrupt '.

							'the 5ive system. </span>';

		$tpl->set('warnings', $warning_text);

		$tpl->set('message', $message);



		echo $tpl->fetch();



	}//end paintAction()





	/**

	* Show the user what config settings were written.

	*

	* @return void

	* @access public

	*/

	function confirmAction()

	{

		echo '<table id="configconf" cellpadding="2" cellspacing="0">';

		echo '<tr><td colspan="2"><h4>The following configuration variables were saved to '.

				$_SESSION['sys_root'].'/config/ </h4></td></tr>';

		foreach($this->config_vars as $varname => $setting) {

			echo '<tr valign="top">';

			echo "<td>$varname</td>";

			echo "<td>$setting</td>";

			echo '</tr>';

		}

		echo '</table>';



	}//end confirmAction()





}//end class
?>
