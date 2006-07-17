<?php
require_once INSTALL_DIR.'/step.inc';
require_once INSTALL_DIR.'/stepaction.inc';

/**
* CompleteStep
*
*
* The final step in the wizard that notifies the user that things are complete.
*
* @author prince mbekwa
* @version $Id
* @package Installer
*/

class Complete extends Step
{

	/**
	* The step shown when install is completed.
	*
	* @param
	* @return void
	* @access public
	*/
	function Complete()
	{
		$this->actions[] =& new CompleteAction(get_class($this));
		$this->step_title = 'Complete';
		$this->title_logo = 'complete.gif';
		$this->can_cancel = false;

	}//end CompleteStep()


}//end class


/**
* CompleteAction
*
* This action is fired when the user has completed the installation process.
*
* @author prince mbekwa
* @version $Id
* @package Installer
*/

class CompleteAction extends StepAction
{


	/**
	* Show the complete text
	*
	* @return void
	* @access public
	*/
	function paintAction()
	{
		$tpl = new Template(INSTALL_DIR.'/templates/complete.tpl');

		//require_once $_SESSION['sys_root'].'/data/private/conf/main.inc';
		//$tpl->set('login_url', 'http://'.SQ_CONF_SYSTEM_ROOT_URLS.'/'.SQ_CONF_BACKEND_SUFFIX.'/');
		$tpl->set('login_url', 'http://'.$_SESSION['site_url'].'/');
		echo $tpl->fetch();

	}//end paintAction()


}//end class
?>