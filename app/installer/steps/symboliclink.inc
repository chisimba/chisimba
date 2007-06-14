<?php



require_once INSTALL_DIR.'/step.inc';



require_once INSTALL_DIR.'/5ivestepaction.inc';

require_once $_SESSION['sys_root'].'/html_form/html_form.inc';



/**

* SymLinkStep

*

* Prepare actions for creating symlinks

*

* @author prince mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class SymbolicLink extends Step

{

	

	

	/**

	* Create the symlink actions

	* 

	* @return void

	* @access public

	*/

	function SymbolicLink()

	{

		$this->actions[] = new SymLinkAction(get_class($this));

		$this->actions[] = new HtaccessAction(get_class($this));

		$this->step_title = 'Create Symbolic Links';

		$this->title_logo = 'create_symlinks.gif';

		$this->help_topic = 'create_symlinks';

		

	}//end SymbolicLink()

	

	

}//end class





/**

* SymLinkAction

*

* Create Symbolic links to the 5ive install.

*

*/

class SymLinkAction extends FiveStepAction 

{

	

	

	/**

	* Try and create all the needed database tables.

	* 

	* @return boolean Whether all the database tables were created or not.

	* @access public

	*/

	function processAction()

	{

		// first check that there's a directory given. 

		$_SESSION['symlink_root'] = isset($_POST['symlink_root']) ? $_POST['symlink_root'] : '';

		$_SESSION['chroot'] = isset($_POST['chroot']) ? $_POST['chroot'] : false;

		

		if ($_SESSION['symlink_root'] == '') {

			$this->errors[] = 'You must provide a directory for symbolic linking.';

			$this->success = false;

			return $this->success;

		} 

		

		$_SESSION['symlink_root'] = rtrim($_SESSION['symlink_root'], '/');

		

		// create the required symbolic links

		$this->success = $this->_createSymlinks();

		return $this->success;

		

	}//end processAction()

	

	

	/**

	* Create the symlinks needed by 5ive

	* 

	* @return boolean whether the symlinks could be created

	* @access private

	*/

	function _createSymlinks()

	{

		$sys_root = $_SESSION['sys_root'];

		$symlink_root = $_SESSION['symlink_root'];

		

		$sym_dirs = Array();

		

		// if the directory doesn't already exist, try and create it

		if (!is_dir($symlink_root)) {

			if (!$this->_dirIsWritable(dirname($symlink_root))) {

				$this->errors[] = 'Apache must have write access to '.

				dirname($symlink_root).' to create the needed symlinks.';

				return false;

			}

			

			// try and create the directory we're wanting to symbolically link

			if (!create_directory($symlink_root)) {

				$this->errors[] = 'Could not create symlink root directory. Please

				make sure Apache has write access to '.dirname($symlink_root);

				return false;

			}

		}

		

		if (!$this->_dirIsWritable($symlink_root)) {

			$this->errors[] = 'Could not write to directory '.$symlink_root;

			return false;

		}

		

		// now try and link the index.php files up

		

		

		

		foreach ($sym_dirs as $target => $link) {

					

			// if the directory exists already, and it's a fresh install, then there's an error.

			if (file_exists($link) || is_dir($link)) {

				// check if the target is the same as the current target, if so leave the link as is.

				if (readlink($link) != $target ) {

					$this->errors[] = 'Symlink '.$link.' already links to '.readlink($link).', you must delete it first.';

					$this->success = false;

					return $this->success;

				} 

			} else {$result = symlink($target, $link);

				if ($result === false) {

					$this->errors[] = 'Could not create symlink for '.$link.' => '.$target;

					return false;

				}

			}

		}

		

		return true;

		

	}//end _createSymlinks()

	

	

	/**

	* Display information about the creation status to the user

	* 

	* @return void

	* @access public

	*/

	function paintAction()

	{

		$tpl = new Template(INSTALL_DIR.'/templates/symlink.tpl');

		

		$symlink_root = isset($_SESSION['symlink_root']) ? $_SESSION['symlink_root'] : null;

		ob_start();

			text_box('symlink_root', $symlink_root, 40);

			$textbox = ob_get_contents();

		ob_end_clean();

		

		$chroot = isset($_SESSION['chroot']) ? $_SESSION['chroot'] : false;

		

		ob_start();

			check_box('chroot',1,$chroot, 'chroot_check();');

			$box = ob_get_contents();

		ob_end_clean();

		

		

		$tpl->set('step', $this->parent_step);

		

		$tpl->set('symlinkbox', $textbox);

		$tpl->set('shared_host', $box);

		

		

		echo $tpl->fetch();

		

	}//end paintAction()

	

}//end class





/**

* HtaccessAction

*

* Create a htaccess file for this 5ive install.

*

* 

*/

class HtaccessAction extends FiveStepAction

{

	

	

	/**

	* Try and create all the needed database tables.

	* 

	* @return boolean Whether all the database tables were created or not.

	* @access public

	*/

	function processAction()

	{

		if ($_SESSION['symlink_root'] == '') {

			$this->errors[] = 'Cannot create .htaccess in empty directory';

			$this->success = false;

			return $this->success;

		} 

		

		

		// check to make sure the directory to write to exists

		if (!$this->_dirIsWritable($_SESSION['symlink_root'])) {

			// remove any old errors, just make up a different one.

			$this->errors = Array();

			$this->errors[] = 'Could not write .htaccess file in '.$_SESSION['symlink_root'].'.';

			$this->errors[] = 'Ensure that this folder has write access before continuing.';

			$this->success = false;

			return $this->success;

		}	

		

		$htaccess_file = $_SESSION['symlink_root'].'/.htaccess';

		// now create the file 

		$fp = @fopen($_SESSION['symlink_root'].'/.htaccess', 'w');

		if ($fp === false) {

			$this->errors[] = 'Could not open '.$htaccess_file.' for writing. ';

			$this->errors[] = 'Please change permissions on the '.$htaccess_file.' file so the webserver has write access.';

			@fclose($fp);

			$this->success = false;

			return false;

		}

		

		// rewrite rules need a base to operate off. In this case, it is everything after the www.domain.com

		// part.

		$base_name = parse_url('http://'.$_SESSION['site_url']);

		// base_name path might be empty (ie top level directory)

		$base_name = @$base_name['path'];

		// if there's an emtpy basename, make it / instead

		if ($base_name == '') {

			$base_name = '/';

		}

		

		if ($_SESSION['chroot']) {

			$link_option = 'SymlinksIfOwnerMatch';

		} else {

			$link_option = 'FollowSymLinks';

		}

			

		$htaccess = "Options +$link_option\r\n".

						"RewriteEngine On\r\n".

						"\r\n".

						"#Set the base uri\r\n".

						"RewriteBase $base_name\r\n".

						"#Now do some rules for redirection\r\n".

						"#if index, don't redirect (again)\r\n".

						"RewriteRule index.php   - [L]\r\n".

						"#don't redirect double-underscore dirs\r\n".

						"RewriteRule __lib       - [L]\r\n".

						"RewriteRule __data      - [L]\r\n".

						"RewriteRule __fudge     - [L]\r\n".

						"#redirect everything else to index.php\r\n".

						"RewriteRule (.*)        index.php/\$1 [L]\r\n";

		

		$success = @fwrite($fp, $htaccess, strlen($htaccess));

		if ($success === false) {

			$this->errors[] = 'Could not write the htaccess file to disk';

			return false;

		}

		return true;

	}//end processAction()

	

	

	

	/**

	* Display information about the creation status to the user

	* 

	* @return void

	* @access public

	*/

	function paintAction()

	{

		// rewrite rules need a base to operate off. In this case, it is everything after the www.domain.com

		// part.

		$base_name = parse_url('http://'.$_SESSION['site_url']);

		// base_name path might be empty (ie top level directory)

		$base_name = @$base_name['path'];

		// if there's an emtpy basename, make it / instead

		if ($base_name == '') {

			$base_name = '/';

		}

		

		

		$tpl = new Template(INSTALL_DIR.'/templates/htaccess.tpl');

		$tpl->set('symlink_dir', $base_name);

		echo $tpl->fetch();

		

	}//end paintAction()

	

	

}//end class

?>

