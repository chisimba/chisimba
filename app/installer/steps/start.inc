<?php



require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/stepaction.inc';

/**

* Start

*

* The first step in the installer. Just show some details about 5ive.

*

* @author prince mbekwa

* @version $version$ - 1.0

* @package 5ive

* @subpackage installer

*/

class Start extends Step

{





	/**

	* Set up this step's actions

	*

	* @return void

	* @access public

	*/

	function Start()

	{

		$this->step_title = 'Welcome';

		// start step uses a different logo

		$this->step_logo = 'licence_logo.png';

		$this->title_logo = '';

		$this->actions[] =& new StartAction(get_class($this));

		$this->step_help = "Welcome to the Chisimba installer. This installer is designed to help you configure a fresh download of Chisimba, and install necessary data into the system. If at any time during installation you require help with an item, click the nearest help  icon. This will direct you to the Chisimba website with the latest information about the selected topic.";

	}//end Start()





}//end class





/**

* AliasAction

*

* Give details about what alias to create

*

*/

class StartAction extends StepAction

{





	/**

	* Display a splash screen to the user

	*

	* @return void

	* @access public

	*/

	function paintAction()

	{

		// Whenever we hit this step, make sure the session is killed.

		global $wizard;



		$wizard->killSession();



		$tpl = new Template(INSTALL_DIR.'/templates/startstep.tpl');

		echo $tpl->fetch();

	}//end paintAction()





}//end class

?>

