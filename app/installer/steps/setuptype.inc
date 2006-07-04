<?php







require_once INSTALL_DIR.'/step.inc';



require_once INSTALL_DIR.'/5ivestepaction.inc';

require_once INSTALL_DIR.'/html_form/html_form.inc';


/**

* SetupType

*

* This step determines what type of install is being performed, and 

* sets some variables that can be used for later install steps.

*

* @author prince mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class SetupType extends Step

{

	

	

	/**

	* Set up this step's actions

	* 

	* @return void

	* @access public

	*/

	function SetupType()

	{

		$this->actions[] =& new SetupTypeAction(get_class($this));

		$this->step_title = 'Setup Type';

		$this->title_logo = 'setup_type.gif';

		$this->help_topic = 'setup_type';

		

	}//end SetupType()

	

	

}//end class





/**

* SetupTypeAction

*

* Asks the user what sort of install they want to perform. Will set some values 

* to be used later in the install (ie checking if there's database tables already

* defined for an upgrade etc). There are currently 3 types of install;

* 

*	- Install 		:- 	Installs a fresh checkout of 5ive. Assumes that there's been 

						no previous installation of the given system root.

	- Reconfigure	:- 	Used to rerun the package management steps which should reset 

						packages to a cleanish state.

	- Modify 		:- 	Lets a user 'reinstall' over an existing install of 5ive. Eventually a 

						'repair' install type becomes an 'upgrade' install type.

*

* @author prince mbekwa		

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class SetupTypeAction extends FiveStepAction 

{

	

	

	/**

	* Check what install type was selected.

	* 

	* @return boolean Whether all the database tables were created or not.

	* @access public

	*/

	function processAction()

	{

		if (isset($_POST['type'])) {

			$_SESSION['install_type'] = $_POST['type'];

			

			switch ($_POST['type']) {

				case 'install': $this->success = $this->_installSystem(); break;

				case 'update': $this->success = $this->_updateSystem(); break;

				case 'repair': $this->success = $this->_repairSystem(); break;

				default : $this->success = false; break;

			}

		} else {

			$this->errors[] = 'You must select an install type';

			$this->success = false;

		}

		

		return $this->success;

		

	}//end processAction()

	

	

	/**

	* Make sure the system is in a state ready for a clean install (ie no files exist yet)

	* 

	* If installing, the following file must NOT exist

	*	- SYSTEM_ROOT/modules/config/config.xml

	* @return void

	* @access private

	*/

	function _installSystem()

	{

		$success = true;

		// check existence of files. If the main.inc file exists, then this isn't a new install

		if (file_exists($_SESSION['sys_root'].'/modules/config/main.xml')) {

			$this->errors[] = 'It appears the selected system root ('.$_SESSION['sys_root'].') is an already existing 5ive install. '.

								'Please go back and select the upgrade or modify options for this installation.';

			$success =  false;

		}

		

		return $success;

		

	}//end _installSystem()

	

	

	/**

	* Make sure the current system is in an okay state to upgrade. Check that the main.xml

	* file is in the sysroot, that a db connection can be made using the DSN values, 

	* that database tables exist already. 

	* 

	* @return void

	* @access private

	*/

	function _updateSystem()

	{

		$success = true;

		$main_inc = $_SESSION['sys_root'].'/modules/config/main.xml';

		// check existence of files. If the main.inc file exists, then this isn't a new install

		if (!file_exists($main_inc)) {

			$this->errors[] = 'The selected system root ('.$_SESSION['sys_root'].') does not have a '.

								'main configuration file. Please do a fresh install before proceeding, '.

								'or select Modify to attempt to reset an existing installation.';

			$success =  false;

		} else {

			//(defined(SQ_CONF_DB_DSN) && defined(SQ_CONF_DB_DSN2))

			// include the main.inc to get all the defines.

			require_once($main_inc);

			if (defined('SQ_CONF_DB_DSN') && defined('SQ_CONF_DB2_DSN')) {

				// test the two dsns

				if (!$this->_testDSN(SQ_CONF_DB_DSN)) {

					$success = false;

				}

				if (!$this->_testDSN(SQ_CONF_DB2_DSN)) {

					$success = false;

				}

				$conn = DB::connect(SQ_CONF_DB_DSN);

				// check whether database tables exist. Just select stuff from sq_asset which should have

				// a few results.

				$sql = 'SELECT count(*) FROM sq_ast WHERE created_userid = 0';

				$result = $conn->query($sql);

				if (DB::isError($result)) {

					$this->errors[] = $result->getMessage();

					$success = false;

				} else {

					$row = $result->fetchRow();

					// ie there weren't any assets that were created by the system user

					if (!($row[0] > 0)) {

						$this->errors[] = 'Could not find any assets. Please try running the Repair option.';

						$success = false;

					}

				}

				

			} else {

				$this->errors[] = 'Could not find the DSN names required to upgrade. Please run Repair.';

				$success = false;

			}

		}

		

		if ($success) {

			$this->next_step = 'createconfigs';

		}

		

		return $success;

		

	}//end _upgradeSystem()

	

	

	/**

	* Nuke some stuff so that the system can be repaired

	* 

	* @return boolean Whether the system is able to be repaired

	* @access private

	*/

	function _repairSystem()

	{

		$success = true;

		$main_inc = $_SESSION['sys_root'].'/modules/config/main.xml';

		// check existence of files. If the main.inc file exists, then this isn't a new install

		if (!file_exists($main_inc)) {

			$this->errors[] = 'The selected system root ('.$_SESSION['sys_root'].') does not have a '.

								'main configuration file. Please do a fresh install before proceeding';

			$success =  false;

		} 

		

		return $success;

		

	}//end _repairSystem()

	

	

	/**

	* Ask what install type to use

	* 

	* @return void

	* @access public

	*/

	function paintAction()

	{

		$tpl = new Template(INSTALL_DIR.'/templates/setuptype.tpl');

				

		$inst_type = isset($_SESSION['install_type']) ? $_SESSION['install_type'] : $inst_type;

		

				

		ob_start();

		

			radio_button('type', 'install', 	$inst_type == 'install', '', '');

			$install = ob_get_contents();

			ob_clean();

			

			radio_button('type', 'update', 	$inst_type == 'update', '', '');

			$update = ob_get_contents();

			ob_clean();

			

			radio_button('type', 'repair', 	$inst_type == 'repair', '', '');

			$repair = ob_get_contents();

			

		ob_end_clean();

		

		$tpl->set('install', $install);

		

		$tpl->set('update', $update);

		$tpl->set('repair', $repair);

		

		echo $tpl->fetch();

			

	}//end paintAction()

	

	

}//end class

?>

