<?php

// security check - must be included in all scripts
//if (!$GLOBALS['kewl_entry_point_run'])
//{
//    die("You cannot view this page directly");
//}
// end security check

/**
 * Class to handle and produce a re-usable object to handle Javascript alerts in a
 * standards compliant way.
 * 
 * @author Paul Scott
 * @copyright AVOIR UWC GNU/GPL
 * @package utilities
 * $Id $
 */

class jsalerts extends object
{
	/**
	 * Class to handle JavaScript alerts
	 * 
	 * @author Paul Scott
	 * @access Public
	 * @copyright AVOIR
	 */
	
	/**
	 * Variable to hold the return string
	 *
	 * @var mixed
	 */
	public $return;
	
	/**
	 * Variable to hold the message to construct the alert
	 *
	 * @var mixed
	 */
    public $msg;
    	
	/**
	 * Method to initialize the class
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		
	}
	
	/**
	 * Set the message to be put into the alert
	 *
	 * @param mixed $value
	 */
	public function setMsg($value)
	{
       $this->msg = $value;
    }
    
    /**
     * Method to output the alert to screen
     *
     * @return mixed $return
     */
    public function outPut()
    {
       $this->return  = '<script language="JavaScript">';
       $this->return .= '    alert("'.$this->msg.'")';
       $this->return .= '</script>';
       return $this->return;
    }

} //end class
?>