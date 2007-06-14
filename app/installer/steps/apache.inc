<?php



/**

* 

* $Id$

* $Name$

*/



require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/stepaction.inc';

require_once INSTALL_DIR.'/html_form/html_form.inc';



/**

* Apache

*

* This step gives info to the user about creating aliases or symlinks 

* to the system root

*

* @author prince mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class Apache extends Step

{

	

	

	/**

	* Set up this step's actions

	* 

	* @return void

	* @access public

	*/

	function Apache()

	{

		$this->actions[] =& new ApacheAction(get_class($this));

		

		$this->step_title = 'Server Configuration';

		$this->title_logo = 'server_configuration.gif';

		$this->help_topic = 'server_configuration';

		

	}//end Apache()

	

	

}//end class







/**

* ApacheAction

*

* Tells the user what information to enter into their apache.conf file.

*

*/

class ApacheAction extends StepAction

{

	

	

	/**

	* Try and create all the needed database tables.

	* 

	* @return boolean Whether all the database tables were created or not.

	* @access public

	*/

	function processAction()

	{

		$_SESSION['dir_struct'] = isset($_POST['symlink_button']) ? $_POST['symlink_button'] : false;

		

		// if they selected the skip radio button, skip over doing any of this stuff.

		if ($_SESSION['dir_struct'] == 'skip') {

			$this->next_step = 'setuptype';

			$this->success = true;

			return true;

		}

		

		if ($_SESSION['dir_struct'] === false) {

			$this->errors[] = 'You must select a directory linking method.';

			$this->success = false;

			return false;

		}

		

		// figure out which type of loading to do and change the next step to load

		if ($_SESSION['dir_struct'] == 'alias') {

			$this->next_step = 'alias';

			$this->success = true;

			return true;

		} else if ($_SESSION['dir_struct'] == 'symlink') {

			// check if the user is on windows or not

			if (PHP_OS == 'WIN32' || PHP_OS == 'WINNT') {

				$this->errors[] = 'Chisimba cannot be installed with symbolic links on Windows based machines.';

				$this->success = false;

				return false;

			}

			$this->next_step = 'symboliclink';

			$this->success = true;

			return true;

		}

		

		return true;

		

	}//end processAction()

	

	

	/**

	* Display information about the aliases to the user

	* 

	* @return void

	* @access public

	*/

	function paintAction()

	{

		$tpl = new Template(INSTALL_DIR.'/templates/apache.tpl');

		

		// check what type of linking is being done.

		$check_alias = true;

		$check_syms = false;

		$check_skip = false;

		

		

		if(isset($_SESSION['dir_struct'])) {

			switch($_SESSION['dir_struct']) {

				case 'alias' : 

					$check_alias = true;

					$check_skip = false;

				break;

				case 'symlink' :

					$check_syms = true;

					$check_skip = false;

				break;

			}

		}

		

		$syms_disabled = '';

		

		// check if the user is on windows or not

		if (PHP_OS == 'WIN32' || PHP_OS == 'WINNT') {

			$syms_disabled = ' disabled';

		}

		

		

		ob_start();

			radio_button('symlink_button', 'symlink', $check_syms, '', $syms_disabled);

			$symbutton = ob_get_contents();

			ob_clean();

			radio_button('symlink_button', 'alias', $check_alias);

			$aliasbutton = ob_get_contents();

			ob_clean();

			radio_button('symlink_button', 'skip', $check_skip, '', $skip_disabled);

			$skipbutton = ob_get_contents();

		ob_end_clean();

		

		$tpl->set('step', $this->parent_step);

		$tpl->set('symlinks', $symbutton);

		$tpl->set('alias', $aliasbutton);

		$tpl->set('skip', $skipbutton);

		

		echo $tpl->fetch();

		

	}//end paintAction

	

	

}//end class

?>

