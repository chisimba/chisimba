<?php



require_once INSTALL_DIR.'/step.inc';

require_once INSTALL_DIR.'/stepaction.inc';
require_once 'I18Nv2/Country.php';
require_once 'I18Nv2/DecoratedList/HtmlSelect.php';
require_once 'I18Nv2/DecoratedList/HtmlEntities.php';




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

