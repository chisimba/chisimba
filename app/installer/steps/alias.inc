<?php



/**

* 

* $Id$

* $Name$

*/



require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/stepaction.inc';





/**

* Alias

*

* Give the user detailed instructions on creating aliases for 5ive.

*

* @author prince mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class Alias extends Step

{

	

	

	/**

	* Set up this step's actions

	* 

	* @return void

	* @access public

	*/

	function Alias()

	{

		$this->actions[] =& new AliasAction(get_class($this));

		$this->step_title = 'Apache Aliases';

		$this->title_logo = 'apache_aliases.gif';

		$this->help_topic = 'apache_aliases';

	}//end Alias()

	

	

}//end class





/**

* AliasAction

*

* Give details about what alias to create

*

* @author prince mbekwa	

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class AliasAction extends StepAction

{

	

	

	/**

	* Try and create all the needed database tables.

	* 

	* @return boolean Whether all the database tables were created or not.

	* @access public

	*/

	function processAction()

	{

		$this->next_step = 'setuptype';

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

		$tpl = new Template(INSTALL_DIR.'/templates/alias.tpl');

		

		$base_name = parse_url('http://'.$_SESSION['site_url']);

		// base_name path might be empty (ie top level directory)

		$base_name = @$base_name['path'];

		$web_root = $base_name;

		// if the base name is empty, make it the root

		if (strlen($base_name) == 0) {

			$web_root = '/';

		}

		

		$tpl->set('system_root', isset($_SESSION['sys_root']) ? $_SESSION['sys_root'] : '');

		$tpl->set('web_path', $base_name);

		$tpl->set('web_root', $web_root);

		

		

		echo $tpl->fetch();

	}//end paintAction

	

	

}//end class

?>

