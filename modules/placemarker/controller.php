<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
die("You cannot view this page directly");
}
/**
**
* Controller class for placemarker module. 
*
* @author Paul Scott <pscott@uwc.ac.za>
* @copyright 
* @package placemarker
* @version 0.1
*
*/
class placemarker extends controller
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
		
	}
}
?>