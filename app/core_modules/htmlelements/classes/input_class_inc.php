<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

/**
 * Input class acts as an base class
 * for some commom objects 
 * eg. buttons , text ,radio buttons ,check boxes
 *
 * @version $Id$
 * @copyright 2003 
 **/
 class input implements ifhtml
 {
 	public $size;
	public $value;
	public $name;
	public $width;
	public $css;
	
	/**
     * Initialization method to set default values
     *
	function input ($name,$value,$size,$width){
		$this->name=$name;
		$this->value=$value;
		if(!$size){
			$this->size=10;
		}else{
			$this->size=$size;
		}
		if(!$width){
			$this->width=10;
		}else{
			$this->width=$width;
		}
	}
	*/
	
/**************************************************************
*         GET METHODS                                         *
* *************************************************************/
	public function getName(){
		return $this->$name;
	}
 	
	public function getSize(){
		return $this->$size;
	}
 	
	public function getValue(){
		return $this->$value;
	}
	
	public function getCSS(){
		return $this->css;
	}
	
	public function getvType(){
		return $this->vtype;
	}

/**************************************************************
*         SET METHODS                                         *
* *************************************************************/
	public function setName($name){
		$this->name=$name;
	} 
 	public function setSize($size){
		$this->size;
	}
	public function setValue($value){
		$this->value=$value;
	}
	
	public function setCss($css){
		$this->class=$css;
	}
	
	public function setvType($vtype){
		$this->vtype=$vtype;
	}
 }
?>