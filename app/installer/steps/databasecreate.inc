<?php





require_once INSTALL_DIR.'/step.inc';

require_once 'xml2array_class_inc.php';

require_once INSTALL_DIR.'/stepaction.inc';

require_once INSTALL_DIR.'/html_form/html_form.inc';

require_once INSTALL_DIR.'/dbhandlers/dbhandler.inc';



/**

* DatabaseCreate

*

* This step creates all the tables needed in the database.

*

* @author prince mbekwa

* @version $version$ - 1.0

* @package installer

* @subpackage installer

*/

class DatabaseCreate extends Step

{


    /**

	* Set up this step's actions

	*

	* @return void

	* @access public

	*/

	function DatabaseCreate()

	{

		$this->actions[] = new DbCreateAction(get_class($this));

		$this->step_title = 'Create Database Tables';

		$this->conf_required = false;

		$this->title_logo = 'create_database_tables.gif';

		$this->help_topic = 'toc11';

		$this->can_cancel = false;


        $this->step_help = "Chisimba will now create the database structures. This step may take some time. Please do not interupt the installation at this time";
	}//end DatabaseCreate()





}//end class





/**

* DbCreateAction

*

* Creates all the tables needed for the 5ive database. Takes the place of 'step_02.php'

*

*/

class DbCreateAction extends StepAction

{



	/**

	* The output of the step02.php script

	* @var string

	*/

	var $init_output = '';

	/**
	 * The output of register file outputs
	 * @var array
	 */

	var $registerdata = array();

	var $moduleAdmin = null;

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

			$this->errors[] = 'The system root could not be found, aborting.';

			$this->success = false;

			return false;

		}

	// Sql tables live in the modules that own them.

    $this->_installDb32('core_modules/security/sql/tbl_users.sql');
    $this->_installDb32('core_modules/security/sql/tbl_userloginhistory.sql');
    $this->_installDb32('core_modules/security/sql/tbl_loggedinusers.sql');
    $this->_installDb32('core_modules/modulecatalogue/sql/tbl_modules.sql');
    $this->_installDb32('core_modules/modulecatalogue/sql/tbl_modules_dependencies.sql');
    $this->_installDb32('core_modules/modulecatalogue/sql/tbl_modules_owned_tables.sql');
    $this->_installDb32('core_modules/systext/sql/tbl_systext_system.sql');
    $this->_installDb32('core_modules/systext/sql/tbl_systext_text.sql');
    $this->_installDb32('core_modules/systext/sql/tbl_systext_abstract.sql');
    $this->_installDb32('core_modules/language/sql/tbl_languagetext.sql');
    $this->_installDb32('core_modules/language/sql/tbl_langs_avail.sql');
    $this->_installDb32('core_modules/language/sql/tbl_languagelist.sql');
    $this->_installDb32('core_modules/language/sql/tbl_english.sql');
    $this->_installDb32('core_modules/language/sql/tbl_language_modules.sql');
    $this->_installDb32('core_modules/toolbar/sql/tbl_menu_category.sql');
    $this->_installDb32('core_modules/sysconfig/sql/tbl_sysconfig_properties.sql');


    //we need tbl_groupadmin_groupuser as well.
    $this->_installDb32('core_modules/groupadmin/sql/tbl_groupadmin_groupuser.sql');
//    $this->_installDb32('core_modules/groupadmin/sql/tbl_groupadmin_group_groupfk.sql');
    $this->_installDb32('core_modules/groupadmin/sql/tbl_groupadmin_group.sql');

    //tbl_permissions_acl
    $this->_installDb32('core_modules/permissions/sql/tbl_permissions_acl.sql');
	$this->_installDb32('core_modules/permissions/sql/tbl_permissions_acl_description.sql');

	//decision tables
	$this->_installDb32('core_modules/decisiontable/sql/tbl_decisiontable_action_rule.sql');
	$this->_installDb32('core_modules/decisiontable/sql/tbl_decisiontable_action.sql');
	$this->_installDb32('core_modules/decisiontable/sql/tbl_decisiontable_condition.sql');
	$this->_installDb32('core_modules/decisiontable/sql/tbl_decisiontable_conditiontype.sql');
	$this->_installDb32('core_modules/decisiontable/sql/tbl_decisiontable_decisiontable_action.sql');
	$this->_installDb32('core_modules/decisiontable/sql/tbl_decisiontable_decisiontable_rule.sql');
	$this->_installDb32('core_modules/decisiontable/sql/tbl_decisiontable_decisiontable.sql');
	$this->_installDb32('core_modules/decisiontable/sql/tbl_decisiontable_rule_condition.sql');
	$this->_installDb32('core_modules/decisiontable/sql/tbl_decisiontable_rule.sql');

	//OK now lets dump in the default data from the xml files...
	//get an array of the directories that we need to look through for the default files...
	$defaults = array('security' => 'core_modules/security/sql/users.xml',
					  'modulecatalogue' => 'core_modules/modulecatalogue/sql/defaultdata.xml',
					  'languagemodules' => 'core_modules/language/sql/langmodules.xml',
					  'toolbar' => 'core_modules/toolbar/sql/defaultdata.xml',
					  'sysconfig' => 'core_modules/sysconfig/sql/defaultdata.xml',
					  'groupadmin' => 'core_modules/groupadmin/sql/groupadmin.xml',
					  'groupadmin2' => 'core_modules/groupadmin/sql/data.xml',
					  'permissions' => 'core_modules/permissions/sql/defaultdata.xml',
					  'decisiontable' => 'core_modules/decisiontable/sql/defaultdata.xml',
					  'language' => 'core_modules/language/sql/langlist.xml',
					  'systext' => 'core_modules/systext/sql/systextabstract.xml',
					  'systexttext' => 'core_modules/systext/sql/systexttext.xml',
					  'systextsystem' => 'core_modules/systext/sql/systextsystem.xml'
					  );

	$handler = new DbHandler();
	$conn = &$handler->getDbHandler($_SESSION['dsn']);
	$output = '';
	foreach($defaults as $default)
	{
		
		$xmlfile = isset($_SESSION['sys_root']) ? $_SESSION['sys_root'].$default : false;
		//echo $xmlfile;
		if(is_file($xmlfile))
		{
			$x = new xml2array();
			$arr = $x->xmlFileToArray($xmlfile, FALSE, TRUE);

			//get the table name
			$toplevel = array_keys($arr);
			$table = $toplevel[0];
			//echo $table;
			//now get the data
			$data = $arr[$table];
			//check if data is an array for more than 1 entry
			if(is_array($data[0]))
			{
				foreach($data as $entries)
				{
					//insert to the db
					//print_r($entries);
					$output = $conn->insert($entries, $table);

				}
			}
			else {
				$output = $conn->insert($data, $table);
			}


		}

	}
		
		return true;
	}//end processAction()

	function firstRegister()
    {
    	$XPost = "";
     	$url = "http://localhost/5ive/app/index.php?module=modulecatalogue&action=firsttimeregistration";

     // initialize curl handle
     $ch = curl_init();
     // set url to post to
     curl_setopt($ch, CURLOPT_URL,$url);
     // return into a variable
     curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
     // times out after 4s
     curl_setopt($ch, CURLOPT_TIMEOUT, 4);
     // add POST fields
     curl_setopt($ch, CURLOPT_POSTFIELDS, $XPost);
     // run the whole process
     $result = curl_exec($ch);
     $result = utf8_encode($result);

     return $result;
    }



	/**

	* Installs the database for 5ive systems

	*/

    private function _installDb32($parth)

	{
		try {
				$sqlfile= isset($_SESSION['sys_root']) ? $_SESSION['sys_root'].$parth : false;

	     		if (is_file($sqlfile)) {
	     			$handler = new DbHandler();
	     			$conn = &$handler->getDbHandler($_SESSION['dsn']);
					
					include_once($sqlfile);
					$tablecreatereturn = $conn->createTables($tablename,$fields,$options);
					//echo $tablecreatereturn;
        			if ($tablecreatereturn ==false) {
        				$this->errors[] = 'Could not create table'.$tablename;
        			}
					if(!isset($indexes))
					{
						$indexes = NULL;
					}
        			if ($indexes!=null) {
        				$tableindex = $conn->createTableIndex($tablename,$name,$indexes);
	        			if (!$tableindex) {
	        				$this->errors[] = 'Could not create table indexes for '.$tablename;

	        			}

        			}
        			$this->init_output[] = $tablename.' successfuly created';
        			$sqlfile = null;
        			$fields =null;
        			$tablename = null;
        			$indexes = null;
        			return true;
				}else{

					$this->init_output[] = 'could not read file'.$sqlfile;
				}
		}catch (Exception $e){
			$this->errorCallback('Caught exception: '.$e->getMessage());
    		exit();
		}


	}//end _installDb32

	/**

	* Deletes a directory and all subdirectories

	*

	* @param string $dir the directory to delete

	* @return void

	* @access private

	*/

	function _rmDashRf($dir)

	{

		if (!is_dir($dir)) {

			return;

		}

		$handle = opendir($dir);

		while (false !== ($file = readdir($handle))) {

			if ($file != "." && $file != "..") {

				if (is_dir($dir.'/'.$file)) {

					$this->_rmDashRf($dir.'/'.$file);

				} else {

					unlink($dir.'/'.$file);

				}

			}

		}



		closedir($handle);

		// now remove this directory

		if (is_dir($dir)) {

			rmdir($dir);

		}



	}//end _rmDashRf()





	/**

	* Empty a directory of files.

	*

	* @param string $dir The directory name (note: NO TRAILING SLASH)

	* @param boolean $recurse Whether to recurse subdirectories

	* @return void

	* @access private

	*/

	function _emptyDirectory($dir, $recurse = false)

	{

		if (!is_dir($dir)) {

			return;

		}

		$handle = opendir($dir);

		while (false !== ($file = readdir($handle))) {

			if ($file != "." && $file != "..") {

				if (is_dir($dir.'/'.$file) && $recurse) {

					$this->_emptyDirectory($dir.'/'.$file, $recurse);

				} else if (is_file($dir.'/'.$file)){

					unlink($dir.'/'.$file);

				}

			}

		}



		closedir($handle);



	}//end _emptyDirectory()





	/**

	* Return whether this action is required or not

	*

	* If the table_columns.inc file exists the user can skip this action.

	*

	* @return boolean if this action is required to complete the step.

	* @access public

	*/

	function isRequired()

	{

		$main_file = isset($_SESSION['sys_root']) ? $_SESSION['sys_root'].'/dbhandlers/default_modules.txt' : false;



		// if it exists, this action isn't required.

		if (is_file($main_file)) {

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



		// if this is an install setup type, or it was set that db tables should be

		// created, the message is that 5ive will create tables.

		if (($_SESSION['install_type'] == 'install') || (isset($_SESSION['create_db']) && $_SESSION['create_db'])) {

			$message = 	'Chisimba will now create the database tables needed by the system. Note

					 that this step may take some time, so do not interrupt the processing of this step after

					 clicking the "Next" button.';

		}

		else {

			$message = 'Chisimba will now cache the current database table columns for quicker '.

						'database access';

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

		foreach($this->init_output as $msg) {
			echo $msg."<br/>";
		}



	}//end confirmAction()


/**
     * The error callback function, defers to configured error handler
     *
     * @param string $exception
     * @return void
     */
    public function errorCallback($exception) {
    	echo $exception;
    }


}//end class

?>