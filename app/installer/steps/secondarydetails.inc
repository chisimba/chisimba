<?php



require_once INSTALL_DIR.'/step.inc';



require_once INSTALL_DIR.'/5ivestepaction.inc';

require_once INSTALL_DIR.'/html_form/html_form.inc';

require_once 'DB.php';



/**

* SecondaryDetails

*

* Collects the details about the secondary database

*

* @author prince mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage install

*/



class SecondaryDetails extends Step

{

	

	

	/**

	* Set up the actions

	* 

	* @return void

	* @access public

	*/

	function SecondaryDetails()

	{

		$this->actions[] = new DbSecondUser(get_class($this), 'db2_');

		

		$this->step_title = 'Secondary Database User Details';

		$this->title_logo = 'second_user_details.gif';

		$this->help_topic = 'second_user_details';

		$this->step_help = "The Installer will now setup you system details. Please refer to the help for more info on each section,";

	}//end SecondaryDetails()

	

	

}//end class





/**

* DbSecondUser

*

* Collects information about a second user that has access to the 

* previously defined database.

*

*/

class DbSecondUser extends FiveStepAction 

{

	/**

	* The prefix to uniquely identify this user.

	* @var string

	*/

	var $db_prefix;

	

	

	/**

	* Set the prefix for this stepaction

	* 

	* @param string $pr The prefix to use for saving the info gathered in this action

	* @return void

	* @access public

	*/

	function DbSecondUser($sn, $pr='db_')

	{

		$this->FiveStepAction($sn);

		$this->db_prefix = $pr;

	}//end DbSecondUser()

	

	

	/**

	* Save the second user's details.

	* 

	* @return boolean Whether the user entered can connect to the database or not.

	* @access public

	*/

	function processAction()

	{

		$_SESSION[$this->db_prefix.'user'] = isset($_POST[$this->db_prefix.'user']) ? $_POST[$this->db_prefix.'user'] : '';

		$_SESSION[$this->db_prefix.'pass'] = isset($_POST[$this->db_prefix.'pass']) ? $_POST[$this->db_prefix.'pass'] : '';

		

		if ($_SESSION[$this->db_prefix.'user'] == '') {

			$this->errors[] = 'Cannot connect with empty username.';

			return false;

		}

		

		// set all the details needed to create the dsn to stuff stored in the session 

		$db_type = isset($_SESSION['db_type']) ? $_SESSION['db_type'] : false;

		$db_loc =  isset($_SESSION['db_loc'])  ? $_SESSION['db_loc']  : false;

		$db_user = isset($_SESSION[$this->db_prefix.'user']) ? $_SESSION[$this->db_prefix.'user'] : false;

		$db_pass = isset($_SESSION[$this->db_prefix.'pass']) ? $_SESSION[$this->db_prefix.'pass'] : false;

		$db_name = isset($_SESSION['db_name']) ? $_SESSION['db_name'] : false;

		

		if ($db_type === false || $db_loc === false || $db_user === false || $db_pass === false || $db_name === false) {

			$this->errors[] = 'Could not connect to DSN. One of the needed values was lost.';

			return false;

		}

		

		// try connecting to the dsn using the previously found db details

		$dsn = 	$db_type.'://'.

			$db_user.':'.

			$db_pass.'@'.

			$db_loc.'/'.

			$db_name;

			

		if ($this->_testDSN($dsn)) {

			$_SESSION['dsn2'] = $dsn;	

		} else {

			$this->success = false;

			return false;

		}

	

		return parent::processAction();

		

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

	* Paint out a form for collecting a new username and password

	* 

	* @param

	* @return void

	* @access public

	*/

	function paintAction()

	{

		

		$tpl = new Template(INSTALL_DIR.'/templates/dbuser.tpl');

		// if this is a repair, get all the DSN details from the main.inc file

		if (($_SESSION['install_type'] == 'repair') && !isset($_SESSION[$this->db_prefix.'user'])) {

			require_once $_SESSION['sys_root'].'/data/private/conf/main.inc';

			$dsn_details = DB::parseDSN(SQ_CONF_DB2_DSN);

			$_SESSION[$this->db_prefix.'user'] = $dsn_details['username'];

			$_SESSION[$this->db_prefix.'pass'] = $dsn_details['password'];

		}

		

		ob_start();

			text_box($this->db_prefix.'user',isset($_SESSION[$this->db_prefix.'user'])?$_SESSION[$this->db_prefix.'user']:'');

			$user = ob_get_contents();

		ob_end_clean();

		ob_start();

			password_box($this->db_prefix.'pass',isset($_SESSION[$this->db_prefix.'pass'])?$_SESSION[$this->db_prefix.'pass']:'');

			$pass = ob_get_contents();

		ob_end_clean();

		

		

		$tpl->set('user',$user);

		$tpl->set('pass',$pass);

		

		echo $tpl->fetch();

		

	}//end paintAction()	

	

	

}//end class

?>