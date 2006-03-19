<?php
/**
* StepAction
*
* A single action in a step
*
* @author prince mbekwa
* @version $version$ - 1.0
* @package 5ive
* @subpackage installer
*/
class StepAction
{
	/**
	* The name of the parent step of this action.
	* @var string
	*/
	var $parent_step = '';
	
	/**
	* Store whether the action completed successfully. By default all actions are 'true'
	* @var boolean
	*/
	var $success = true;
	
	/**
	* The errors this object has collected
	* @var array
	*/
	var $errors = Array();
	
	/**
	* The number of steps to skip on completion of this action (if any). BE 
	* VERY CAREFUL WITH THIS. If there is more than one action in a step, 
	* this could have really really strange consecquences for the other steps.
	* @var int
	*/
	var $skip_steps = 0;
	
	/**
	* Defines a step to jump to after processing, normally left blank
	* @var string
	*/
	var $next_step = '';
	
	/**
	* Is this action required (can it be skipped?)
	* @var boolean
	*/
	var $required = true;
	
	
	/**
	* Save the name of the parent step if given
	*
	* @param string $step_name The name of the parent step
	* @access public
	*/
	function StepAction($step_name='')
	{
		$this->parent_step = $step_name;

	}//end StepAction
	
	
	/**
	* Perform this step action
	* 
	* @return boolean Did the action complete successfully?
	* @access public
	*/
	function processAction()
	{
		return $this->success;
	}//end performAction()
	
	
	/**
	* Paint the step action
	* 
	* @return void
	* @access public
	*/
	function paintAction()
	{
	}//end paintAction()
	
	
	/**
	* If a step requires confirmation, paint out the confirm screen
	* 
	* @return void
	* @access public
	*/
	function confirmAction()
	{
	}//end confirmAction()
	
	
	/**
	* Rollback whatever this action did in processAction
	* 
	* @return boolean Whether rollback completed successfully or not.
	* @access public
	*/
	function rollback()
	{
		return true;
	}//end rollback()
	
	
	/**
	* Return whether this action is required or not
	* 
	* @return boolean if this action is required to complete the step.
	* @access public
	*/
	function isRequired()
	{
		return $this->required;
	}//end isRequired()
	
	
}//end class

?>