<?php

/* -------------------- installdirectory class ----------------*/



/**

* This is the installer installdirectory class

*

*/

require_once INSTALL_DIR.'/step.inc';



require_once INSTALL_DIR.'/stepaction.inc';



/**

* InstallDirectory

*

* Finds the base SYSTEM_ROOT directory for this installation of 5ive

*

* @author Prince Mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/



class InstallDirectory extends Step

{





	/**

	* Set the actions for this step

	*

	* @return void

	* @access public

	*/

	function InstallDirectory()

	{

		$this->actions[] =& new InstallDirectoryAction(get_class($this));



		$this->conf_required = true;

		$this->auth_required = false;

		$this->step_title = 'System Root';

		$this->title_logo = 'system_root.gif';

		$this->help_topic = 'system_root';

		$this->step_help = "Enter the path where you want Chisimba to be installed";



	}//end InstallDirectory()





}//end class





/**

* InstallDirectoryAction

*

* Prompt the user for the install directory

*

*

*/

class InstallDirectoryAction extends StepAction

{





	/**

	* Is the directory the user entered accessible by the webserver, and are the directories

	* that need certain permissions available?

	* The following directories should have write permissions :

	* 		SYSROOT/usrfiles

	*		SYSROOT/user_images

	* 		SYSROOT/config/classes

	*

	*

	* @return void

	* @access public

	*/

	function processAction()

	{

		$sys_root = isset($_POST['sys_root']) ? trim($_POST['sys_root']) : false;
		$module_path = isset($_POST['module_path']) ? trim($_POST['module_path']) : false;

		$_SESSION['sys_root'] = $sys_root;
		$_SESSION['module_path'] = $module_path;



		if ($sys_root === false || $sys_root == '') {
			$this->errors[] = 'You must set a value for the system root of Chisimba';
			$this->success = false;
			return false;
		}

		if ($module_path === false || $module_path == '') {
			$this->errors[] = 'You must set a value for the location of the non-core modules';
			$this->success = false;
			return false;
		}



		// change windows \ characters to / separators

		$sys_root = str_replace('\\', '/',$sys_root);
		if (!is_dir($sys_root)) {
			$this->errors[] = 'Could not find the directory specified '.$sys_root;
			$this->success = false;
			return false;
		}

		$module_path = str_replace('\\', '/',$module_path);
		if (!is_dir($module_path)) {
			$this->errors[] = 'Could not find the directory specified '.$module_path;
			$this->success = false;
			return false;
		}




		// Iterate back through the installer's path to check if it's in the

		// system root.

		$install_dir = INSTALL_DIR;



		$last_length = 0;



		while (strlen($install_dir) != $last_length) {

			if (!$install_dir) {

				$this->errors[] = 'Chisimba MUST NOT be located under the install directory (currently located '.

								'at '.INSTALL_DIR.' with system root set to '.$sys_root.'). Please move the '.

								'installer to a location under the webroot.';

				$this->success = false;

				return false;

			}



			$last_length = strlen($install_dir);

			$install_dir = dirname($install_dir);

		}



		// now check if this SYSROOT directory is actually the 5ive sysroot and not just some directory

		if (!$this->_testSysRoot($sys_root)) {

			$this->success = false;

			return false;

		}



		$_SESSION['sys_root'] = $sys_root;

		$this->success = true;

		return true;



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
		if (isset($_SESSION['sys_root']) && $this->_testSysRoot($_SESSION['sys_root'])) {
			return false;
		}

		if (isset($_SESSION['module_path']) && $this->_testSysRoot($_SESSION['module_path'])) {
			return false;
		}

		return true;



	}//end isRequired()





	/**

	* Check whether certain directories exist and are writable

	*

	* @param string $sys_root The alleged system root directory

	* @return boolean Whether this sysroot is writable

	* @access public

	*/

	public function _testSysRoot($sys_root)

	{

		$check_dir = $sys_root;

		$dirs = Array('usrfiles','user_images');

		$success = true;

		//make sure we can write to web root
       if(is_dir($sys_root)) {

				$fp = @fopen($check_dir.'/tmpinstallfile', "w");

				if ($fp==false) {

					$this->errors[] = 'Could not open '.$check_dir.' for writing. ';



					// only output what commands to perform for the first instance

					// of a failed directory check

					if ($dir=='modules') {

						$this->errors[] = 'Please change permissions on the '.$check_dir.' directory and all files below it '.

											'to have write access by the webserver by executing the following ';

						$this->errors[] = '<p><i>chmod 777 '.$check_dir;



						$this->errors[] = 'find '.$check_dir.' -type d | xargs chmod 755';

						$this->errors[] = 'find '.$check_dir.' -type f | xargs chmod 666</i></p>';

					}

					$success = false;



					@fclose($fp);
					return $success;
				}
		}else{

			$success = false;
			return $success;

		}

		foreach($dirs as $dir) {

			$check_dir = $sys_root.'/'.$dir;



			// if it is a directory, check whether it is writable

			if (is_dir($check_dir)) {



				// create a tmp file to see if there's write access.

				$fp = @fopen($check_dir.'/tmpinstallfile', "w");

				if ($fp===false) {

					$this->errors[] = 'Could not open '.$check_dir.' for writing. ';



					// only output what commands to perform for the first instance

					// of a failed directory check

					if ($dir=='usrfiles' || $dir=='user_images') {

						$this->errors[] = 'Please change permissions on the '.$check_dir.' directory and all files below it '.

											'to have write access by the webserver by executing the following ';

						$this->errors[] = '<p><i>chmod 777 '.$check_dir;



						$this->errors[] = 'find '.$check_dir.' -type d | xargs chmod 777';

						$this->errors[] = 'find '.$check_dir.' -type f | xargs chmod 666</i></p>';

					}

					$success = false;
					return $success;


					@fclose($fp);

				} else {

					@fclose($fp);

					// delete the temp file that was just created

					unlink($check_dir.'/tmpinstallfile');

				}

			} else {

				//create the dir anyway

			    mkdir($check_dir);

    			chmod($check_dir,0777);

				$success = true;

				$this->errors[] = 'Could not find required directory '.$check_dir.',but the system created it anyway';



			}

		}



		return $success;



	}//end _testSysRoot()





	/**

	* Prompt the user for the directory and display the current status of permissions

	* needed

	*

	* @param

	* @return void

	* @access public

	*/

	function paintAction()

	{

		$tpl = new Template(INSTALL_DIR.'/templates/installdir.tpl');

		$source_dir = isset($_SESSION['sys_root']) ? $_SESSION['sys_root'] : INSTALL_DIR;



		$path = getcwd();

		if (preg_match("/\/installer/i", "$path")) {

			$abspath = str_replace('/installer',"",$path);

		} else {

			$abspath = str_replace('\installer',"",$path);

		}



		ob_start();
        if (PHP_OS=="WIN32" || PHP_OS=='WINNT'){
			echo '<input type="text" id="sys_root" name="sys_root"
				value="'.$abspath.'\" size="50"  class="sq-form-field" />';
				$box = ob_get_contents();
                                $box2 = '<input type="text" id="module_path" name="module_path"'
                                .'value="'.$abspath.'\modules\" size="50"  class="sq-form-field" />';
        }else{
        	echo '<input type="text" id="sys_root" name="sys_root"
				value="'.$abspath.'/" size="50"  class="sq-form-field" />';
				$box = ob_get_contents();
                                $box2 = '<input type="text" id="module_path" name="module_path"'
                                .'value="'.$abspath.'/modules/" size="50"  class="sq-form-field" />';
        }
		ob_end_clean();



		$tpl->set('install_box',$box);
		$tpl->set('module_path',$box2);



		echo $tpl->fetch();



	}//end paintAction()





	/**

	* Show the user what they selected for the system root.

	*

	* @return void

	* @access public

	*/

	function confirmAction()

	{

		echo '<p>System Root has been set to '.$_SESSION['sys_root'].'</p>';
		echo '<p>Non-core Module path has been set to '.$_SESSION['module_path'].'</p>';

	}//end confirmAction()





}//end class

?>

