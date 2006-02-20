<?php

/**
 * Input class acts as an base class
 * for some commom objects 
 * eg. buttons , text ,radio buttons ,check boxes
 *
 * @version $Id$
 * @copyright 2003 
 **/


 class input {
 	var $size;
	var $value;
	var $name;
	var $width;
	var $css;
	
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
	function getName(){
		return $this->$name;
	}
 	
	function getSize(){
		return $this->$size;
	}
 	
	function getValue(){
		return $this->$value;
	}
	
	function getCSS(){
		return $this->css;
	}
	
	function getvType(){
		return $this->vtype;
	}

/**************************************************************
*         SET METHODS                                         *
* *************************************************************/
	function setName($name){
		$this->name=$name;
	} 
 	function setSize($size){
		$this->size;
	}
	function setValue($value){
		$this->value=$value;
	}
	
	function setCss($css){
		$this->class=$css;
	}
	
	function setvType($vtype){
		$this->vtype=$vtype;
	}
 }
?>