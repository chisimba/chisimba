<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
* 
* Used to create labels for form elements
* 
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @author Tohir Solomons
*
*/
class label {

	var $labelValue;
	var $forId;
	
	function label($labelValue, $forId)
	{
		$this->labelValue=$labelValue;
		$this->forId=$forId;
	}
	
	function show()
	{
		$str='<label ';
		
		if ($this->forId != '') {
			$str.= 'for ="'.$this->forId.'"';
		}
		
		$str.='>';
		$str.=$this->labelValue;
		$str.='</label>';
		return $str;
	}
	function setLabel($labelValue){
		$this->labelValue =$labelValue;		
	}
	
	function setForId($forId){
		$this->forId=$forId;
	}
}
?>