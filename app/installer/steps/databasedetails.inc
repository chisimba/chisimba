<?php



require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/5ivestepaction.inc';

require_once INSTALL_DIR.'/html_form/html_form.inc';


/**

* DatabaseDetails

*

* This class contains all the actions needed to connect and verify a connection to

* the database

*

* @author prince mbekwa

* @version $Id

* @package Installer

*/

class DatabaseDetails extends Step

{



	/**

	* Define which actions to use

	*

	* @return void

	* @access public

	*/

	function DatabaseDetails()

	{

		$this->actions[] = new DbDetailsAction(get_class($this));

		
		$this->step_title = 'Database Details';
		$this->title_logo = 'database_details.gif';

		$this->help_topic = 'toc6';

		$this->step_help = "Select the type of database you want to use<p/> The default username is normally \'root\'";


	}//end DatabaseDetails()





}//end class





/**

* DbDetailsAction

*

* Collects the database details for the user.

*

*/



class DbDetailsAction extends FiveStepAction

{

	/**

	* To keep the db details collected unique (and make this action reusable), keep track

	* of what db is being processed by using a db_prefix

	* @var string

	*/

	var $db_prefix = 'db_';





	/**

	* Process the post form data

	*

	* Process the form data from the dbdetails section

	* note that if the db server has anonymous connections turned on, then this step WILL

	* succeed (however it will fail on the next step when it tries to actually manipulate

	* a specific database)

	*

	* @return void

	* @access public

	*/

	function processAction()

	{

		$success = false;

		// Check that the next button was clicked and get all the db details from it.

		$_SESSION[$this->db_prefix.'type'] = isset($_POST[$this->db_prefix.'type']) ? trim($_POST[$this->db_prefix.'type']) : '';

		$_SESSION[$this->db_prefix.'loc'] = isset($_POST[$this->db_prefix.'loc']) ? trim($_POST[$this->db_prefix.'loc']) : '';

		$_SESSION[$this->db_prefix.'user'] = isset($_POST[$this->db_prefix.'user']) ? trim($_POST[$this->db_prefix.'user']) : '';

		$_SESSION[$this->db_prefix.'pass'] = isset($_POST[$this->db_prefix.'pass']) ? trim($_POST[$this->db_prefix.'pass']) : '';



		// check that certain values are filled in correctly

		if ($_SESSION[$this->db_prefix.'user'] == '') {

			$this->errors[] = 'You must provide a database username.';

			return false;

		}



		// try connecting to the database

		// note that if the db server has anonymous connections turned on, then this step WILL

		// succeed (however it will fail on the next step when it tries to actually manipulate

		// a specific database)

		$dsn = $_SESSION[$this->db_prefix.'type'].'://'.$_SESSION[$this->db_prefix.'user'].':'.$_SESSION[$this->db_prefix.'pass'].'@'.$_SESSION[$this->db_prefix.'loc'].'/'; //.$_SESSION[$this->db_prefix.'name'];


		if ($_SESSION[$this->db_prefix.'type'] != '') {

			if ($this->_testDSN($dsn)) {

				$_SESSION['short_dsn'] = $dsn;

				$_SESSION['dsn'] = $dsn;

				$success = true;

			}

		} else {

			$_SESSION['short_dsn'] = $dsn;
			$_SESSION['dsn'] = $dsn;

			$success = true;

		}



		$this->success = $success;





		return parent::processAction();



	}//end processAction()





	/**

	* Return whether this action is required or not

	*

	* if the dsn exists and can be connected to, return true

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

	* Paint out the screen for collecting DB details.

	*

	* If there were errors processing, need to highlight them here

	*

	* @return void

	* @access public

	*/

	function paintAction()

	{

        // if in install - update mode, cannot change the database details

		$tpl = new Template(INSTALL_DIR.'/templates/dbdetails.tpl');



		$conn = 'Untested';

	//	if ($this->isRequired()) {

	//		$conn = '<span style="color: Green">Connected to '.(isset($_SESSION['dsn']) ? $_SESSION['dsn'] : '').'</span>';

	//	}



		ob_start();

            $allowed_db = isset($_SESSION['allowed_db'])? $_SESSION['allowed_db']:NULL;
		
            $db_array = array();

            if($allowed_db['MDB2_Driver_mysql']==TRUE) {
                $db_array['mysql'] = 'MySQL';
            }
            if ($allowed_db['MDB2_Driver_mysqli']==TRUE) {
                $db_array['mysqli'] = 'MySQLi';
            }
            if($allowed_db['MDB2_Driver_pgsql']==TRUE) {
                $db_array['pgsql'] = 'PostgreSQL';
            }
            if(empty($db_array)){
                $db_array[''] = 'No available DB extensions';
              
            }

            combo_box($this->db_prefix.'type',

                    $db_array, false,
					Array(isset($_SESSION[$this->db_prefix.'type'])?$_SESSION[$this->db_prefix.'type']:''));

			$cmb = ob_get_contents();

		ob_end_clean();

		ob_start();

			text_box($this->db_prefix.'loc',isset($_SESSION[$this->db_prefix.'loc'])?$_SESSION[$this->db_prefix.'loc']:'localhost');

			$loc = ob_get_contents();

		ob_end_clean();

		ob_start();

			text_box($this->db_prefix.'name',isset($_SESSION[$this->db_prefix.'name'])?$_SESSION[$this->db_prefix.'name']:'');

			$name = ob_get_contents();

		ob_end_clean();

		ob_start();

			text_box($this->db_prefix.'user',isset($_SESSION[$this->db_prefix.'user'])?$_SESSION[$this->db_prefix.'user']:'');

			$user = ob_get_contents();

		ob_end_clean();

		ob_start();

			password_box($this->db_prefix.'pass',isset($_SESSION[$this->db_prefix.'pass'])?$_SESSION[$this->db_prefix.'pass']:'');

			$pass = ob_get_contents();

		ob_end_clean();





		$tpl->set('type', $cmb);

		$tpl->set('loc',$loc);

		$tpl->set('user',$user);

		$tpl->set('pass',$pass);

		$tpl->set('conn',$conn);



		echo $tpl->fetch();



	}//end paintAction()





}//end class

?>