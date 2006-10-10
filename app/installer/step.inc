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
require_once dirname(__FILE__).'/customexception.inc';

class Step 
{
	
	/**
	* Was this step successful?
	* @public boolean
	*/
	public $success = false;
	
	/**
	* The errors this object has collected
	* @public array
	*/
	public $errors = Array();
	
	/**
	* Defines how many actions to step forward after completing this step
	* @public int
	*/
	public $num_steps = 1;
	
	/**
	* Defines a step to jump to after processing, normally left blank. If there are multiple
	* actions defined for a step, this can cause problems as two steps may define different actions
	* @public string
	*/
	public $next_step = '';
	
	/**
	* The step actions this step can perform.
	* @public array
	*/
	public $actions = Array();

	/**
	* Is this step required? ie can the user skip this step? By default
	* this is false, but by default actions are required so will normally
	* change this to being true.
	* @public type
	*/
	public $required = false;
	
	/**
	* Does this step need to be authenticated before occuring? Note that the authentication public function
	* will be responsible for checking if it needs to be authed or not.
	* @public boolean
	*/
	public $auth_required = true;
	
	/**
	* Does this step need to be confirmed before proceeding?
	* @public boolean
	*/
	public $conf_required = false;
	
	/**
	* Can this step be cancelled ?
	* @public boolean
	*/
	public $can_cancel = true;
	
	/**
	* Has this step been confirmed?
	* @public boolean
	*/
	public $confirmed = false;
	
	/**
	* The title of this step
	* @public string
	*/
	public $step_title = 'Step ';
	
	/**
	* A graphical title for the logo
	* @public string
	*/
	public $title_logo = '';
	
	/**
	* Logo to display for the step
	* @public string
	*/
	public $step_logo = 'step_logo.png'; //.png';
	
	/**
	* The name of the help topic for this step
	* @public string
	*/
	public $help_topic = "";
	
	/**
     * Has an error been generated?
     *
     * @access private
     * @var string
     */
    private $_hasError = FALSE;

    /**
     * Where was the error generated?
     *
     * @access private
     * @var string
     */
    private $_errorField = '';
	/**
     * The global error message
     *
     * @access private
     * @var string
     */
    private $_errorMessage = '';
	/**
	* Rolls back the actions this step performed.  
	* 
	* Make sure the step has been processed by calling isProcessed() first BEFORE just randomly deleting stuff
	* @return void
	* @access public
	*/
	public public function rollback()
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
	public function process()
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
	public function _checkConfirmed()
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
	public function paint()
	{
		// if the step requires confirmation, output a hidden publiciable for confirming this step
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
	public function _paintConfirm()
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
	public function isRequired()
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
	public function isProcessed()
	{
		return false;
	}
	
	/**
     * Method to instantiate the pear error handler callback
     *
     * @access public
     * @param string $error
     * @return void (die)
     */
    public function _pearErrorCallback($error)
    {
        // TODO: note $error->getMessage() returns a shorter and friendlier but
        //       less informative message, for production should use getMessage
        //TODO: note 2: Appending the getUserinfo method from the PEAR
        //      error stack will give you the same detail as toString()
        //      but it will look decent and not confuse the crap out of users
        //      that being said, we should still go for just getMessage() in prod

        $msg = $error->getMessage() . ': ' . $error->getUserinfo();
        $errConfig = $this->_objConfig->geterror_reporting();

        if($errConfig == "developer")
        {
            $usermsg = $msg;
        }
        else {
            $usermsg = $error->getMessage();
        }

        $this->setErrorMessage($usermsg);
        $this->putMessages();
        log_debug(__LINE__ . "  " . $msg);

        die($this->diePage());
    }

    /**
     * Method to return a nicely formatted error page for DB errors
     *
     * @todo fix this function up for multilingual and prettiness
     * @access public
     * @param void
     * @return string
     */
    public function diePage()
    {
        $uri = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
        $message = '<style type="text/css" media="screen">
                    @import url("skins/echo/main.css");
                 </style>

                <div class="featurebox"><h1> An Error has been encountered</h1>
                '.$this->_errorMessage;
        $message .= '<a href='.$uri.'>Back</a></div>';
        return $message;
    }
     /**
    * Method to set the global error message, and an error field if appropriate
    *
    * @access public
    * @param $errormsg string The error message
    * @param $field string The name of the field the error applies to (optional)
    * @return FALSE
    */
    public function setErrorMessage($errormsg, $field = NULL)
    {
        if (!$this->_hasError) {
            $this->_errorMessage = $errormsg;
            $this->_hasError = TRUE;
        }
        if ($field) {
            $this->_errorField = $field;
        }
        // error return code if needed by caller
        return FALSE;
    }
	
}

?>