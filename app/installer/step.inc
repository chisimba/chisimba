<?php
/**
* Step 
*
* A step in a wizard.
*
* @author prince mbekwa
* @version $version$ - 1.0
* @package 5ive
* @subpackage installer
*/
class Step 
{
	
	/**
	* Was this step successful?
	* @var boolean
	*/
	var $success = false;
	
	/**
	* The errors this object has collected
	* @var array
	*/
	var $errors = Array();
	
	/**
	* Defines how many actions to step forward after completing this step
	* @var int
	*/
	var $num_steps = 1;
	
	/**
	* Defines a step to jump to after processing, normally left blank. If there are multiple
	* actions defined for a step, this can cause problems as two steps may define different actions
	* @var string
	*/
	var $next_step = '';
	
	/**
	* The step actions this step can perform.
	* @var array
	*/
	var $actions = Array();

	/**
	* Is this step required? ie can the user skip this step? By default
	* this is false, but by default actions are required so will normally
	* change this to being true.
	* @var type
	*/
	var $required = false;
	
	/**
	* Does this step need to be authenticated before occuring? Note that the authentication function
	* will be responsible for checking if it needs to be authed or not.
	* @var boolean
	*/
	var $auth_required = true;
	
	/**
	* Does this step need to be confirmed before proceeding?
	* @var boolean
	*/
	var $conf_required = false;
	
	/**
	* Can this step be cancelled ?
	* @var boolean
	*/
	var $can_cancel = true;
	
	/**
	* Has this step been confirmed?
	* @var boolean
	*/
	var $confirmed = false;
	
	/**
	* The title of this step
	* @var string
	*/
	var $step_title = 'Step ';
	
	/**
	* A graphical title for the logo
	* @var string
	*/
	var $title_logo = '';
	
	/**
	* Logo to display for the step
	* @var string
	*/
	var $step_logo = 'step_logo.png'; //.png';
	
	/**
	* The name of the help topic for this step
	* @var string
	*/
	var $help_topic = "";
	
	
	/**
	* Rolls back the actions this step performed.  
	* 
	* Make sure the step has been processed by calling isProcessed() first BEFORE just randomly deleting stuff
	* @return void
	* @access public
	*/
	function rollback()
	{
		$success = true;
		for ($i=0; $i<count($this->actions); $i++) {
			$action = &$this->actions[$i];
			if (!$action->rollback()) {
				$this->errors[] = 'Failed to rollback action in step.';
				$success = false;
			}
		}
		return $success;
	}//end rollback()
	
	
	/**
	* Process this step
	* 
	* A step processes each of its step actions one by one. By default, it will perform each step 
	* action one after another. If there needs to be some type of conditional processing of step actions,
	* override this method and change how the steps are processed. Even if there's only one step action
	* for a step, there still needs to be a step action class defined for it.
	* A step must return TRUE for the next step defined in the wizard to be processed. 
	* 
	* @return boolean Do we process the next step after this one? 
	* (true = process next, false = stay on current step)
	* @access public
	*/
	function process()
	{
		// check if the step needs confirmation. If it does, and it has received that confirmation,
		// don't process again.
		if ($this->conf_required) {
			$this->_checkConfirmed();
			if ($this->confirmed) {
				$this->success = true;
				return true;
			}
		}
		
		$success = true;
		// the loops need to be by reference, otherwise information that is set in self::process()
		// will not be available in self::paint.
		for ($i=0; $i<count($this->actions); $i++) {
			$action = &$this->actions[$i];
			// if we processed the action, check to see if it needs to skip ahead a certain
			// number of steps. 
			if ($action->processAction()) {
				if ($action->skip_steps > 0) {
					$this->num_steps += $action->skip_steps;
				}
				if ($action->next_step != '') {
					$this->next_step = $action->next_step;
				}
				
			} else {
				$success = false;
			}
			// if the processing failed, get the errors and merge them in with this object's errors
			$this->errors = array_merge($this->errors, $action->errors);
		}
		
		$this->success = $success;
		
		// if confirmation is needed, always return false
		if ($this->conf_required) {
			return false;
		}
		
		return $this->success;
	}//end process()
	
	
	/**
	* Checks if a step was confirmed
	* 
	* @return void
	* @access private
	*/
	function _checkConfirmed()
	{
		// only check the confirmation if it was the 'next' button that was clicked
		if (isset($_POST['next']) || isset($_POST['next_x'])) {
			if (isset($_POST['confirm_step']) && ($_POST['confirm_step'] == get_class($this))) {
				$this->confirmed = true;
			}
		}
	}//end _checkConfirmed()
	
	
	/**
	* Paint this step.
	* 
	* @return void
	* @access public
	*/
	function paint()
	{
		// if the step requires confirmation, output a hidden variable for confirming this step
		// then draw the confirm screen.
		if ($this->success && $this->conf_required) {
			$out = '<input type="hidden" name="confirm_step" value="'.get_class($this).'" />';
			echo $out;
			// if needed, paint the confirm screen 
			$this->_paintConfirm();
		} else {
			
			// the loops need to be by reference, otherwise information that is set in self::process()
			// will not be available in self::paint.
			for ($i=0; $i<count($this->actions); $i++) {
				$action = &$this->actions[$i];
				$action->paintAction();
			}
		}
		
	}//end paint()
	
	
	/**
	* Paint the confirmation messages.
	* 
	* @return void
	* @access protected
	*/
	function _paintConfirm()
	{
		// paint all the confirmAction screens.
		for ($i=0; $i<count($this->actions); $i++) {
			$action = &$this->actions[$i];
			$action->confirmAction();
		}
	}//end _paintConfirm()
	
	
	/**
	* Does this step need to be processed? Can it be skipped?
	* 
	* @return void
	* @access public
	*/
	function isRequired()
	{
		// the loops need to be by reference, otherwise information that is set in self::process()
		// will not be available in self::paint.
		for ($i=0; $i<count($this->actions); $i++) {
			$action = &$this->actions[$i];
			if ($action->isRequired()) {
				$this->required = true;
			}
		}
		
		return $this->required;
	}
	
	
	/**
	* Check whether this step has been processed
	* 
	* @return boolean Has this step completed all it needs to do in process() ?
	* @access public
	*/
	function isProcessed()
	{
		return false;
	}
	
	
}

?>