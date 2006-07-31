<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

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
class label implements ifhtml
{

	public $labelValue;
	public $forId;
	
	public function label($labelValue, $forId)
	{
		$this->labelValue=$labelValue;
		$this->forId=$forId;
	}
	
	public function show()
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
	public function setLabel($labelValue){
		$this->labelValue =$labelValue;		
	}
	
	public function setForId($forId){
		$this->forId=$forId;
	}
}
?>