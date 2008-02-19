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

		$this->actions[] = new InstallDirectoryAction(get_class($this));



		$this->conf_required = true;

		$this->auth_required = false;

		$this->step_title = 'System Root';

		$this->title_logo = 'system_root.gif';

		$this->help_topic = 'toc3';

		$this->step_help = "Enter the path for your Chisimba home directory.";
		$this->step_help .= "This is usually detected for you by the system, however you can now change it to point elsewhere. ";


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
		$module_path = isset($_POST['module_path']) ? trim($_POST['module_path']) : $sys_root."/packages/";
		$module_URI = isset($_POST['module_URI']) ? trim($_POST['module_URI']) : "packages/";

		$_SESSION['sys_root'] = $sys_root;
		$_SESSION['module_path'] = $module_path;
		$_SESSION['module_URI'] = $module_URI;



		if ($sys_root === false || $sys_root == '') {
			$this->errors[] = 'You must set a value for the system root of Chisimba';
			$this->success = false;
			return false;
		}

		// change windows \ characters to / separators

		$sys_root = str_replace('\\', '/',$sys_root);
                $sys_root.='/';
                $sys_root=str_replace('//','/',$sys_root);
                @chmod($sys_root,0700);
		if (!is_dir($sys_root)) {
			mkdir($sys_root);
			chmod($sys_root,0700);
			$this->errors[] = 'Could not find the directory specified '.$sys_root.' but created it for you';
			$this->success = false;
			return false;
		}

		$module_path = str_replace('\\', '/',$module_path);
                //Add a final '/'
                $module_path.='/';
                $module_path=str_replace('//','/',$module_path);
		if (!is_dir($module_path)) {
			@mkdir($module_path);
			@chmod($module_path, 0700);
			if(is_writeable($module_path))
			{

				//$this->errors[] = 'Could not find the directory specified '.$module_path.' but created it for you';
			}
			else {
				$this->errors[] = 'Could not find the directory specified '.$module_path.'. The installer is unable to create this directory, as it does not have sufficient permissions. Please check that '.$sys_root.' is writeable.';
			}

			$this->success = false;
			return false;
		}


		$module_URI = str_replace('\\', '/',$module_URI);
                //Add a final '/'
                $module_URI.='/';
                $module_URI=str_replace('//','/',$module_URI);
                // again
                $module_URI=str_replace('//','/',$module_URI);
		//if (!is_dir($module_URI)) {
		//	$this->errors[] = 'Could not find the directory specified '.$module_URI;
		//	$this->success = false;
		//	return false;
		//}



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
		$_SESSION['module_path'] = $module_path;
		$_SESSION['module_URI'] = $module_URI;

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

		return true;



	}//end isRequired()



        /**
         * Recursive version of glob
         * Copied over from the Lucene module
         * @return array containing all pattern-matched files.
         *
         * @param string $sDir      Directory to start with.
         * @param string $sPattern  Pattern to glob for.
         * @param int $nFlags      Flags sent to glob.
         */
        private function globr($sDir, $sPattern, $nFlags = NULL)
        {
                //chdir($sDir);
                        $sDir = str_replace('\\','/',$sDir);
                        $sDir = escapeshellcmd($sDir);
                //echo $sDir;
                // Get the list of all matching files currently in the
                // directory.

                $aFiles = glob($sDir.$sPattern, $nFlags);

                // Then get a list of all directories in this directory, and
                // run ourselves on the resulting array.  This is the
                // recursion step, which will not execute if there are no
                // directories.

                foreach (@glob("$sDir/*", GLOB_ONLYDIR) as $sSubDir)
                {
                   // if(is_file($sSubDir))
                    //{
                        $aSubFiles = $this->globr($sSubDir, $sPattern, $nFlags);
                        $aFiles = array_merge($aFiles, $aSubFiles);
                    //}
                }

                // The array we return contains the files we found, and the
                // files all of our children found.

                return $aFiles;
        }//end function



	/**

	* Check whether certain directories exist and are writable

	*

	* @param string $sys_root The alleged system root directory

	* @return boolean Whether this sysroot is writable

	* @access public

	*/

	public function _testSysRoot($sys_root)

	{

                //$sys_root=str_replace('//','/',$sys_root);
		$check_dir = $sys_root;

		$dirs = Array('usrfiles','user_images','usrfiles/users');

		$success = true;

		//make sure we can write to web root
       if(is_dir($sys_root)) {

                //echo $check_dir.'tmpinstallfile';
                //die;

                // Use str_replace to get rid of the //'s
				$fp = @fopen(str_replace('//','/',$check_dir.'/tmpinstallfile'), "w");

				if ($fp==false) {

					$this->errors[] = 'Could not open '.$check_dir.' for writing. Please consult the help guides for your operating system to make this directory writeable. On GNU/Linux systems, this is normally achieved by: chmod -R 0700 '.$sys_root.' and ensuring that the directory is owned by the webserver user (usually apache or www-data)';



					// only output what commands to perform for the first instance

					// of a failed directory check

					if ($check_dir=='modules') {
						//chmod($check_dir, 0700);

						$this->errors[] = 'Please change permissions on the '.$check_dir.' directory and all files below it '.

											'to have write access by the webserver by executing the following ';

						$this->errors[] = '<p><i>chmod 700 '.$check_dir;



						$this->errors[] = 'find '.$check_dir.' -type d | xargs chmod 700';

						$this->errors[] = 'find '.$check_dir.' -type f | xargs chmod 600</i></p>';

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

                // Use str_replace to get rid of the //'s
			$check_dir = str_replace('//','/',$sys_root.'/'.$dir);



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

						$this->errors[] = '<p><i>chmod 700 '.$check_dir;



						$this->errors[] = 'find '.$check_dir.' -type d | xargs chmod 700';

						$this->errors[] = 'find '.$check_dir.' -type f | xargs chmod 600</i></p>';

                                                $this->errors[]=" Also, make sure to change the ownership of the directory to the account used by the webserver. (For example, on Linux Apache typically runs as either 'nobody' or 'www-data')<br />";

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

    			chmod($check_dir,0700);

				$success = true;

				$check_dir = str_replace('//', '/', $check_dir);
				$this->errors[] = '<span class="success">Could not find required directory '.$check_dir.',but the system created it anyway</span>';



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


		$path = $_SERVER['SCRIPT_FILENAME'];

		if (preg_match("/\/installer/i", "$path")) {

			$abspath = str_replace('/installer/index.php',"",$path);

		} else {

			$abspath = str_replace('\installer\index.php',"",$path);

		}


                // non-core modules folder
                // take a smart guess, and check for other options
                $guess=str_replace('/chisimba_framework/app/installer',"",$path).'/chisimba_modules/';


                if (isset($_SESSION['module_path']) && is_dir($_SESSION['module_path'])){

                    $newmodulepath=$_SESSION['module_path'];

                } else if (file_exists($abspath.'/core_modules/chisimba_modules.txt')){

                    $newmodulepath=$abspath.'/core_modules/';

                } else if (is_dir($guess)){

                    $newmodulepath=$guess;

                } else {
                    $newmodulepath=$abspath.'/core_modules/';
                    $this->errors[]="The default value for the module path is supplied, be sure to check that this is accurate!";
                }
                // module URI
                if (isset($_SESSION['module_URI']) && is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$_SESSION['module_URI'])){
                    $module_URI=$_SESSION['module_URI'];
                } else if (($newmodulepath=='')||( strlen($newmodulepath) < strlen($abspath)) ){
                    //$uriSearch=$this->globr($_SERVER['DOCUMENT_ROOT'],'/chisimba_modules.txt',GLOB_NOSORT);
                    //if (count($uriSearch)>0){
                    //    $module_URI=substr($uriSearch[0],strlen($_SERVER['DOCUMENT_ROOT']),-strlen('chisimba_modules.txt'));
                    //    if ($newmodulepath==''){
                    //        $newmodulepath=substr($uriSearch[0],0,-strlen('chisimba_modules.txt'));
                    //    }
                    //} else {
                        $module_URI='';
                    //}
                } else {
                    $module_URI=substr($newmodulepath,strlen($_SERVER['DOCUMENT_ROOT'])).'/';
                }
                $module_URI=str_replace('//','/',$module_URI);

		ob_start();
        if (PHP_OS=="WIN32" || PHP_OS=='WINNT'){
			echo '<input type="text" id="sys_root" name="sys_root"
				value="'.$abspath.'\" size="60"  class="sq-form-field" />';
				$box = ob_get_contents();
                                $box2 = '<input type="text" id="module_path" name="module_path"'
                                .'value="'.$newmodulepath.'\" size="50"  class="sq-form-field" />';

        }else{
        	echo '<input type="text" id="sys_root" name="sys_root"
				value="'.$abspath.'/" size="50"  class="sq-form-field" />';
				$box = ob_get_contents();
                                $box2 = '<input type="text" id="module_path" name="module_path"'
                                .'value="'.$newmodulepath.'" size="50"  class="sq-form-field" />';

        }
		ob_end_clean();



		$tpl->set('install_box',$box);

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

	}//end confirmAction()





}//end class

?>
