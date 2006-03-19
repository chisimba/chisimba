<?php


require_once INSTALL_DIR.'/step.inc';

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
* @package 5ive 
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
		$this->actions[] =& new DbCreateAction(get_class($this));
		$this->step_title = 'Create Database Tables';
		$this->conf_required = true;
		$this->title_logo = 'create_database_tables.gif';
		$this->help_topic = 'create_database_tables';
		$this->can_cancel = false;
		
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
		
		// now include the step to process
		ob_start();
			//require_once $_SESSION['sys_root'].'/installer/step_02.php';
			
			$this->init_output .= ob_get_contents();
		ob_end_clean();
		
		
		
		return true;
		
	}//end processAction()
	
	
	/**
	* Installs the database for 5ive systems
	*/
	function _installDb32()
	{
		
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
		$main_file = isset($_SESSION['sys_root']) ? $_SESSION['sys_root'].'/data/private/db/table_columns.inc' : false;
	
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
			$message = 	'5ive will now try and create the database tables needed by the system. Note
					 that this step may take some time, so do not interrupt the processing of this step after
					 clicking the "Next" button.';
		}
		else {
			$message = '5ive will now cache the current database table columns for quicker '.
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
		echo $this->init_output;
		
	}//end confirmAction()
	
	
}//end class
?>
