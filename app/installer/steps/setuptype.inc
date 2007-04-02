<?php







require_once INSTALL_DIR.'/step.inc';



require_once INSTALL_DIR.'/5ivestepaction.inc';

require_once INSTALL_DIR.'/html_form/html_form.inc';
require_once INSTALL_DIR.'/dbhandlers/dbhandler.inc';


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

		$this->step_help = "Select the type of installation you want to do";



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

		if (file_exists($_SESSION['sys_root'].'/modules/config/config.xml')) {

			$this->errors[] = 'It appears the selected system root ('.$_SESSION['sys_root'].') is an already existing Chisimba installation. '.

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

		$main_inc = $_SESSION['sys_root'].'/config/config.xml';
                $dbinc=$_SESSION['sys_root'].'/config/dbdetails_inc.php';

		// check existence of files. If the main.inc file exists, then this isn't a new install

		if (!file_exists($main_inc) || !file_exists($dbinc)) {

			$this->errors[] = 'The selected system root ('.$_SESSION['sys_root'].') is missing one or more of '
                        .'its configuration files. Please do a fresh install before proceeding, '.
                        'or select Modify to attempt to reset an existing installation.';

			$success =  false;

		} else {

			//(defined(SQ_CONF_DB_DSN) && defined(SQ_CONF_DB_DSN2))

			// include the main.inc to get all the defines.

			require_once($dbinc);

			if (defined('KEWL_DB_DSN') && defined('KEWL_DB2_DSN')) {
                            $_SESSION['KEWL_DB_DSN']=KEWL_DB_DSN;
                            $_SESSION['KEWL_DB2_DSN']=KEWL_DB2_DSN;


                            $data=simplexml_load_file($main_inc);
                            if ($data==FALSE){ 
                                $this->errors[]= "Fatal Error! Something is wrong with the XML file that stores the information about the site.. It won't parse properly into an array. Installation cannot proceed until this is fixed.<br />\n";
                                return FALSE;
                            }
                                                

				// test the two dsns

				if (!$this->_testDSN(KEWL_DB_DSN)) {

					$success = false;

				}

				if (!$this->_testDSN(KEWL_DB2_DSN)) {

					$success = false;

				}

				
                                $handler = new DbHandler();
                                print KEWL_DB_DSN;
                                $conn = &$handler->getDbHandler($_SESSION['KEWL_DB_DSN']); 

				// check whether database tables exist. Just select stuff from sq_asset which should have

				// a few results.

				//$sql = 'SELECT count(*) FROM sq_ast WHERE created_userid = 0';

				//$result = $conn->query($sql);
                                $result=$conn->listDbTables();

				//if (DB::isError($result)) {
				if (!is_array($result)) {

					//$this->errors[] = $result->getMessage();
					$this->errors[] = $result->getMessage();

					$success = false;

				} else {

					//$row = $result->fetchRow();

					// ie there weren't any assets that were created by the system user

					//if (!($row[0] > 0)) {
					if (count($result)<40) {

						//$this->errors[] = 'Could not find any assets. Please try running the Repair option.';
						$this->errors[] = 'Some tables are missing. Please try running the Repair Option.';

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

		$main_inc = $_SESSION['sys_root'].'/config/config.xml';

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

		if(!isset($inst_type))
		{
			$inst_type = NULL;
		}
		$inst_type = isset($_SESSION['install_type']) ? $_SESSION['install_type'] : $inst_type;

		if(file_exists($_SESSION['sys_root'].'/config/config.xml'))
		{

		    $hasConfigXML = TRUE;

		    $disable = '';

		} else {

		    $hasConfigXML = FALSE;

		    $disable = ' DISABLED ';

		}



		ob_start();



			radio_button('type', 'install', 'TRUE', null, '');

			$install = ob_get_contents();

			ob_clean();



			radio_button('type', 'update', 	NULL, '', $disable);

			$update = ob_get_contents();

			ob_clean();



			radio_button('type', 'repair', NULL , '', $disable);

			$repair = ob_get_contents();



		ob_end_clean();



		$tpl->set('install', $install);



		$tpl->set('update', $update);

		$tpl->set('repair', $repair);



		echo $tpl->fetch();



	}//end paintAction()





}//end class

?>

