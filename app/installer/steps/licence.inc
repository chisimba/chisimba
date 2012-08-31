<?php

require_once INSTALL_DIR.'/step.inc';
require_once INSTALL_DIR.'/stepaction.inc';

/**
* Start
* 
* 
* The first step in the wizard that introduces the user to the installer
* 
* @author prince mbekwa
* @version $Id
* @package Installer
*/

class Licence extends Step
{
	
	/**
	* Get the user to accept the licence
	* 
	* @param
	* @return void
	* @access public
	*/
	function Licence() 
	{
		
		$this->actions[] = new LicenceAction(get_class($this));
		
		$this->step_title = 'Licence Agreement';
		$this->auth_required = false;
		$this->title_logo = 'licence_agreement.gif';
		$this->help_topic = 'toc2';
		$this->step_help = "Please read the licence agreement and click the next button";
		
	}//end Licence()
	
	
}//end class


/**
* LicenceAction
*
* This action is fired when the user starts the install process
* 
* @author Prince Mbekwa
* @version $Id
* @package KNG installer
*/

class LicenceAction extends StepAction
{
	
	
	/**
	* Check to make sure that the user has checked the 'iagree' checkbox.
	* 
	* @return boolean Whether it is okay to proceed to the next step of the installer or not
	* @access public
	*/
	function processAction()
	{
		//if (isset($_POST) && isset($_POST['agree'])) {
			$_SESSION['licence_agree'] = true;
			$this->success = true;
			return parent::processAction();
		//} else {
			//$this->errors[] = 'You cannot proceed with the install unless you accept the terms of the licence agreement';
		//	$this->success = false;
		//	return false;
		//}
		
	}//end processAction()
	
	
	/**
	* Return whether this action is required or not
	*
	* if they've already agreed to the licence, skip is allowed.
	* 
	* @return boolean if this action is required to complete the step.
	* @access public
	*/
	function isRequired()
	{
		//if (isset($_SESSION['licence_agree'])) {
		//	return false;
		//}
		
		return true;
		
	}//end isRequired()
	

	/**
	* Show the licence and agree box
	* 
	* @return void
	* @access public
	*/
	function paintAction()
	{
		$tpl = new Template(INSTALL_DIR.'/templates/licence.tpl');
		
		$licence_agree = ' ';
		if (isset($_SESSION['licence_agree']) && $_SESSION['licence_agree']) {
			$licence_agree = ' checked ';
		}
		ob_start();
			//echo '<input type="checkbox" name="agree" id="agree" value="1" '.$licence_agree.'  class="sq-form-field" />';
			$agree_box = ob_get_contents();
		ob_end_clean();
		
		$tpl->set('licence_check', $agree_box);
		echo $tpl->fetch();
		
	}//end paintAction()
	
	/**
	 * Method to get the help string
	 */
	function getHelpStr()
	{
	    
	    return 'THEREASDFASDFASDFASDFASDFSADF';
	}
	
	
}//end class
?>