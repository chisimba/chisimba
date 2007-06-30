<?php





require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/stepaction.inc';

require_once $_SESSION['sys_root'].'/installer/html_form/html_form.inc';

//require_once $_SESSION['sys_root'].'/installer/install.inc';



/**

* DatabasePopulate

*

* This step populates the database tables

*

* @author prince mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class DatabasePopulate extends Step

{





	/**

	* Set up this step's actions

	*

	* @return void

	* @access public

	*/

	function DatabasePopulate()

	{

		$this->actions[] = new DbPopulateAction(get_class($this));

		$this->step_title = 'Populate Database Tables';

		$this->conf_required = false;

		$this->title_logo = 'populate_database_tables.gif';

		$this->help_topic = 'populate_database_tables';

		$this->can_cancel = false;



	}//end DatabasePopulate()





}//end class





/**

* DbPopulateAction

*

* Populate all the tables needed for the 5ive database. Takes the place of 'step_03.php'

*

*/

class DbPopulateAction extends StepAction

{



	/**

	* The output of the step02.php script

	* @var string

	*/

	var $init_output = Array();



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

			$_GET['SYSTEM_ROOT'] = $_SESSION['sys_root'];

		} else {

			$this->errors[] = 'The system root could not be found.';

			$this->success = false;

			return false;

		}



		// process 'step3'

		if (!$this->_step03()) {

			$this->success = false;

			return false;

		}



		return true;



	}//end processAction()





	/**

	* Find out whether there needs to be any reconstructing of the database (ie empty tables). If so,

	* will delete the existing system_asset_config file that defines all the system assets that are currently

	* listed as installed.

	*

	* @return boolean Whether or not to repopulate the database

	* @access private

	*/

	function _repairCheck()

	{

		$ret = false;



		ini_set('memory_limit', -1);

		error_reporting(E_ALL);

		$SYSTEM_ROOT = $_SESSION['sys_root'];



		if (empty($SYSTEM_ROOT) || !is_dir($SYSTEM_ROOT)) {

			$this->errors[] = 'Could not find the system root at '.$SYSTEM_ROOT;

			return $ret;

		}



		// Let everyone know we are installing

		$GLOBALS['SQ_INSTALL'] = true;

		$db = &$GLOBALS['SQ_SYSTEM']->db;



		$sql = '';

		switch ($db->phptype) {

			case 'pgsql':

			case 'mysql': $sql = 'select assetid from sq_ast'; break;

			default : break;

		}



		$result = $db->query($sql);

		if (DB::isError($result)) {

			$this->errors[] = $result->getMessage();

			return false;

		}



		// if there WEREN'T any rows in the database, need to make sure the main system_asset config file

		// is nuked so that assets can be recreated (system_asset config file tells the system what assets are

		// installed)

		if ($result->numRows() <= 0) {

			$ret = true;

			// get the system_asset_config object and find out what its config_file path is so that

			// it can be deleted

			require_once SQ_INCLUDE_PATH.'/system_asset_config.inc';

			$sys_asset_cfg = new System_Asset_Config();



			// if the config file exists, then delete it so that the assets will be recreated

			if (file_exists($sys_asset_cfg->config_file)) {

				@unlink($sys_asset_cfg->config_file);

			}



			unset($sys_asset_cfg);

		}



		return $ret;



	}//end _repairCheck()





	/**

	* Step 03.php script wrapped into a function

	*

	* @return void

	* @access public

	*/

	function _step03()

	{

		$output = '';

		$success = true;



		// buffer the output because it doesn't all need to be sent to the user.

		ob_start();



		ini_set('memory_limit', -1);

		set_time_limit(300);



		error_reporting(E_ALL);

		$SYSTEM_ROOT = $_SESSION['sys_root'];



		if (empty($SYSTEM_ROOT) || !is_dir($SYSTEM_ROOT)) {

			$this->errors[] = 'Could not find the system root at '.$SYSTEM_ROOT;

			return;

		}



		// Need to configure all the stuff in the asset manager stuff before running this stuff.

		// Normally, the init is included with SQ_INSTALL set to false before this stuff happens,

		// so need to make sure that whatever is being set if SQ_INSTALL was false is set.

		$am = &$GLOBALS['SQ_SYSTEM']->am;

		// make sure the asset types are loaded.

		$am->_loadAssetTypes();



		// Let everyone know we are installing

		$GLOBALS['SQ_INSTALL'] = true;





		if (!regenerate_configs()) {

			$success = false;

			$this->errors[] = 'Could not regenerate configs';

		} else {

			$this->init_output[] = 'Regenerated Configs';

		}





		// if the user deleted database tables during a modify, then this will

		// make sure that all the configs that say those tables still exist

		// are deleted too.

		$repair_populate = $this->_repairCheck();



		// refactor this stuff out as soon as possible!!

		if (!uninstall_asset_types()) {

			$success = false;

			$this->errors[] = 'Could not uninstall asset types';

		} else {

			$this->init_output[] = 'Performed Asset Uninstall Check.';

		}

		if (!uninstall_packages()) {

			$success = false;

			$this->errors[] = 'Could not uninstall packages';

		} else {

			$this->init_output[] = 'Performed Package Uninstall Check.';

		}

		if (!install_core()) {

			return false;

			$this->errors[] = 'Could not install core';

		} else {

			$this->init_output[] = 'Installed Core';

		}

		if (!install_packages()) {

			$success = false;

			$this->errors[] = 'Could not install packages';

		} else {

			$this->init_output[] = 'Installed Packages';

		}

		if (!install_authentication_types()) {

			$success = false;

			$this->errors[] = 'Could not install authentication types';

		} else {

			$this->init_output[] = 'Installed Authentication Types';

		}

		if (!generate_global_preferences()) {

			$success = false;

			$this->errors[] = 'Could not generate global preferences';

		} else {

			$this->init_output[] = 'Generated Global Preferences';

		}

		if (!install_event_listeners()) {

			$success = false;

			$this->errors[] = 'Could not install event listeners.';

		} else {

			$this->init_output[] = 'Installed Event Listeners';

		}



		// have to repeat the packageSetup if this is a fresh install, or if the db tables had

		// been repopulated.

		if (($_SESSION['install_type'] == 'install') || $repair_populate) {

			install_packages();

		}



		//$output = ob_get_contents();

		ob_end_clean();



		return $success;



	}//end _step03()





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



		// if this is an install setup type, or it was set that db tables should be

		// created, the message is that 5ive will create tables.

		if (($_SESSION['install_type'] == 'install') || (isset($_SESSION['create_db']) && $_SESSION['create_db'])) {

			$message = 	'Chisimba will now try and populate the database tables needed by the system. Note

					 that this step may several minutes to complete, so do not interrupt the processing of this step after

					 clicking the "Next" button.';

		}

		else {

			$message = 'Chisimba will now ensure recently installed packages and assets are correctly '.

						'inserted into the database, and that any packages marked for deletion are removed.';

		}



		$tpl->set('warnings', $warning_text);

		$tpl->set('message', $message);



		echo $tpl->fetch();



	}//end paintAction()





	/**

	* Show the user what database stuff was written

	*

	* @return void

	* @access public

	*/

	function confirmAction()

	{

		echo '<table id="poptable" cellpadding="2" cellspacing="0">';

		echo '<tr valign="top">';

		echo "<td><h4>The following actions were performed successfully.</h4></td>";

		echo '</tr>';

		foreach($this->init_output as $line) {

			echo '<tr valign="top">';

			echo "<td>$line</td>";

			echo '</tr>';

		}

		echo '</table>';



	}//end confirmAction()





}//end class

?>