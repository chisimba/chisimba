<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
die("You cannot view this page directly");
}
/**
**
* Controller class for unihr module. 
*
* @author 
* @copyright 
* @package unihr
* @version 0.1
*
*/
class unihr extends controller
{
	public function init()
	{
		
	}
	
	/**
	*
	* Standard controller dispatch method. The dispatch method calls any
	* methods involving logic and hands of the results to the template for
	* display.
	*
	* @access public
	*
	*/
	public function dispatch()
	{
		echo "This module is under heavy construction! Will be up soon";
		die();
	}
}
?>
