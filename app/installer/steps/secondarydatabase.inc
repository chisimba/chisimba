<?php
require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/5ivestepaction.inc';
require_once INSTALL_DIR.'/html_form/html_form.inc';

/**
* SecondaryDatabase
*
* A user may wish to create a secondary database for oracle etc
*
* @author prince mbekwa
* @version $version$ - 1.0
* @package 5ive
* @subpackage installer
*/
class SecondaryDatabase extends Step
{
	
	
	function SecondaryDatabase()
	{
		$this->actions[] =& new SecondaryDatabaseAction(get_class($this));
		$this->step_title = 'Secondary User';
		$this->title_logo = 'create_second_user.gif';
		$this->help_topic = 'create_second_user';
		
	}//end SecondaryDatabase()
	
	
}//end class


/**
* SecondaryDatabaseAction
*
* Prompts the user to choose whether to enter secondary db details
* or to use the previously entered ones. Note that this action
* will skip over multiple actions if a certain value is set.
*
*/
class SecondaryDatabaseAction extends FiveStepAction
{
	
	
	/**
	* By default, this action will skip over the 'substeps' of this step
	* 
	* @return void
	* @access public
	*/
	function SecondaryDatabaseAction($sn)
	{
		$this->FiveStepAction($sn);
		$this->skip_steps = 1;
	}//end SecondaryDatabaseAction()
	
	
	/**
	* Check whether the user selected to use the previously entered database details or not.
	* 
	* @return boolean true Whether we use the previously defined values or not.
	* step.
	* @access public
	*/
	function processAction()
	{
		$_SESSION['use_old'] = isset($_POST['use_old']) ? $_POST['use_old'] : false;
			
		// if the user selected to use the old details, set them up here
		if ($_SESSION['use_old']) {
			
			$_SESSION['dsn2'] = $_SESSION['dsn'];
			
			return true;
		} else {
			$_SESSION['use_old'] = false;
			// we don't want to skip over the next step which is defining the alternate user
			$this->skip_steps = 0;
			return true;
		}
	
		return false;
		
	}//end processAction()
	
	
	/**
	* Return whether this action is required or not
	*
	* If the secondary dsn exists and is connectable, can skip this step
	* 
	* @return boolean if this action is required to complete the step.
	* @access public
	*/
	function isRequired()
	{
		if (isset($_SESSION['dsn2']) && $this->_testDSN($_SESSION['dsn2'])) {
			return false;
		}
		
		return true;
		
	}//end isRequired()
	
	
	/**
	* Show information about why 5ive might need a secondary database.
	* 
	* @return void
	* @access public
	*/
	function paintAction()
	{
		
		$tpl = new Template(INSTALL_DIR.'/templates/seconddb.tpl');
		$conn = 'Untested';
		if (!$this->isRequired()) {
			$conn = '<span style="color: Green">Connected to '.(isset($_SESSION['dsn']) ? $_SESSION['dsn'] : '').'</span>';
		}
		
		$db_type = isset($_SESSION['db_type']) ? $_SESSION['db_type'] : '';
		$db_loc  = isset($_SESSION['db_loc']) ? $_SESSION['db_loc'] : '';
		$db_user = isset($_SESSION['db_user']) ? $_SESSION['db_user'] : '';
		$db_pass = isset($_SESSION['db_pass']) ? $_SESSION['db_pass'] : '';
		$db_name = isset($_SESSION['db_name']) ? $_SESSION['db_name'] : '';
		
		ob_start();
			check_box('use_old',1,isset($_SESSION['use_old'])?$_SESSION['use_old']:false);
			$use_old = ob_get_contents();
		ob_end_clean();
		
		
		$tpl->set('db_type',$db_type);
		$tpl->set('db_loc',$db_loc);
		$tpl->set('db_user',$db_user);
		$tpl->set('db_pass',$db_pass);
		$tpl->set('db_name',$db_name);
		$tpl->set('use_old',$use_old);
		$tpl->set('conn',$conn);
		
		echo $tpl->fetch();
		
	}//end paintAction()
	
	
}
?>
