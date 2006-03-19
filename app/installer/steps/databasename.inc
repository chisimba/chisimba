<?php


require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/5ivestepaction.inc';
require_once INSTALL_DIR.'/html_form/html_form.inc';
require_once INSTALL_DIR.'/dbhandlers/dbhandler.inc';
require_once 'DB.php';

/**
* DatabaseName
*
* This step collects the name of the database that is going to be used. 
* The user can choose to create a new database (possibly overwriting an
* old install) or to enter a current database name.
*
* @author prince mbekwa
* @version $version$ - 1.0
* @package 5ive
* @subpackage installer
*/

class DatabaseName extends Step
{
	
	
	function DatabaseName()
	{
		$this->actions[] =& new DbNameAction(get_class($this));
		$this->step_title = 'Database Name';
		$this->title_logo = 'database_name.gif';
		$this->help_topic = 'database_name';
		
	}//end DatabaseName()
	
	
}//end class


/**
* DbNameAction
*
* This collects the name of the database the user is going to user for
* their 5ive installation. The user can also choose to create a new
* database, which may overwrite an existing install of 5ive.
*
*/
class DbNameAction extends FiveStepAction 
{
	/**
	* To keep the db details collected unique (and make this action reusable), keep track
	* of what db is being processed by using a db_prefix
	* @var string
	*/
	var $db_prefix = 'db_';
	
	
	/**
	* Set the prefix for the db tables this collector uses.
	* 
	* @param string $pr The prefix to use.
	* @return void
	* @access public
	*/
	function DbNameAction($ps, $pr='db_')
	{
		$this->FiveStepAction($ps);
		$this->db_prefix = $pr;
		
	}//end DbDetailsAction
	
	
	/**
	* Save the database name entered by the user
	* 
	* If the database name exists, empty the database if the user has selected to.
	*
	* @return boolean Whether the database name entered is correct and usable
	* @access public
	*/
	function processAction()
	{

		$_SESSION[$this->db_prefix.'name'] = isset($_POST[$this->db_prefix.'name']) ? trim($_POST[$this->db_prefix.'name']) : ''; 
		$_SESSION['create_db'] = isset($_POST['create_db']) ? true : false; 
		
		if ($_SESSION[$this->db_prefix.'name'] == '') {
			$this->errors[] = 'You must provide a database name.';
			$this->success = false;
			return false;
		}
		
		$dsn = $_SESSION['short_dsn'].$_SESSION[$this->db_prefix.'name'];
		
		/* @var  $handler DbHandler */
		// get a reference to the database handler.
		$handler = &DbHandler::getDbHandler($dsn);

		if (!$handler->success) {
			$this->errors[] = 'Could not get database handler for dsn '.$dsn;
			$this->success = false;
			return false;
		}
		
		// check the version of the database to make sure it's compatible
		if (!$handler->checkVersion()) {
			$this->errors[] = 'The database appears to be an unsupported version. This may cause problems with installation.';
			$this->success = false;
		}
		
		// first, check if they've checked that they want to create a new database.
		// if so, go off and try to create the database
		if (isset($_POST['create_db'])) {
			if (!$this->_emptyDatabase($_SESSION[$this->db_prefix.'name'], $handler)) {
				$this->success = false;
				$this->errors[] = 'Could not access database '.$_SESSION[$this->db_prefix.'name'];
				return false;
			} else {
				$this->success = true;

			}
			
		} else {	
			// just want to test that the database entered exists and is connectable.
			// connect to the database using the values collected before
			if ($this->_testDSN($dsn)) {
				$this->success = true;	
			} else {
				$this->success = false;
				return false;
			}
			
			// should the tables be created
			$num_tables = $handler->getNumberOfTables();
			
			if (!$handler->success) {
				$this->errors[] = 'The installer was unable to check if your system currently has db tables created. '.
								'You can continue, but please make sure there are no 5ive tables in the database '.
								'specified.';
				return true;
			}
			// if there were already database tables created, and the install type is 'install', 
			// need to quit.
			if (($num_tables > 0) && ($_SESSION['install_type'] == 'install')) {
				$this->errors[] = 'The database given already contains tables, and must be emptied '.
									'before proceeding with the installation.';
				$this->success = false;
			}
		}		
		// so if after all this success is true, can set the dsn to being short_dsn.db_name
		if ($this->success) {
			$_SESSION['dsn'] = $_SESSION['short_dsn'].$_SESSION[$this->db_prefix.'name'];
		} else {
			$this->errors[] = 'Unable to set the session for some reason'; 
		}
		
		return parent::processAction();
		
	}//end processAction()
	
	
	/**
	* Return whether this action is required or not
	*
	* if they've already selected a sysroot and the sys_root exists, can skip
	* 
	* @return boolean if this action is required to complete the step.
	* @access public
	*/
	function isRequired()
	{
		if (isset($_SESSION['dsn']) && $this->_testDSN($_SESSION['dsn'])) {
			return false;
		}
		
		return true;
		
	}//end isRequired()
	
	
	/**
	* Attempt to connect and create the database using the given DB connection
	* 
	* @param string $db_name The name of the database to create
	* @param object $handler The database handler to use to empty the database.
	* @return boolean was the database successfully emptied
	* @access public
	*/
	function _emptyDatabase($db_name, &$handler)
	{
		// create the appropriate db handler and use it for emptying the database
		
		$db_type = isset($_SESSION[$this->db_prefix.'type'])?$_SESSION[$this->db_prefix.'type']:false;
		
		if ($db_type === false) {
			$this->errors[] = 'Could not identify the database type.';
			return false;
		}
		
		$handler->emptyDatabase();
		
		$this->errors += $handler->errors;

		return $handler->success;
		
	}//end _emptyDatabase()
	
	
	/**
	* Display a form for entering database details.
	* 
	* @return void
	* @access public
	*/
	function paintAction()
	{
		$tpl = new Template(INSTALL_DIR.'/templates/dbname.tpl');
		
		
		ob_start();
			text_box($this->db_prefix.'name',isset($_SESSION[$this->db_prefix.'name'])?$_SESSION[$this->db_prefix.'name']:'');
			$name = ob_get_contents();
		ob_end_clean();
		ob_start();
			check_box("create_db",1,isset($_SESSION['create_db'])?$_SESSION['create_db']:false);
			$create_db = ob_get_contents();
		ob_end_clean();
		
		$tpl->set('db_name',$name);
		$tpl->set('create_db',$create_db);
		
		echo $tpl->fetch();
		
	}//end paintAction()
	
	
}//end class
?>
