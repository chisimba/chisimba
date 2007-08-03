<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run'] 
 * @name   $kewl_entry_point_run 
 */
$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for building a rounded div box for KEWL.nextgen.
*
* The class builds a css style div rounded corners box 
*
* @author    Prince Mbekwa 
* @copyright (c)2004 UWC 
* @package   div class 
* @version   0.1 
*/

class div {
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void   
     * @access public 
     */
	function init(){
		
	}
	
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public 
     */
	function show(){
		$str='<input type="something" value="'.$this->value.'"';
		$str.=' name="'.$this->name.'"';
		$str.=' size="'.$this->size.'"';
		$str.=' width="'.$this->width.'"';
		$str.=' class="'.$this->ccsclass.'"';
		$str.='>';
		return $str;
	}
?>