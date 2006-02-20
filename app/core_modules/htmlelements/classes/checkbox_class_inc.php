<?php

/**
* CheckBox class for outputting dropdown menu. 
* 
* @package button
* @category HTML Controls
* @version $Id$
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @example 
*		$objElement = new checkbox('m','Male',true);  // this will checked
*		$check= $objElement->show();
*		$objElement = new checkbox('f','Female'); //this will not be checked
*		$check .= $objElement->show();
*	
* @author Smtegha
* @author Tohir Solomons
* @author Kariuki wa Njenga
*/
require_once("htmlbase_class_inc.php");

class checkBox extends htmlbase
{
  var $ischecked; 
  
  
  var $value; //Kariuki added

  
  /**
  * Class Constructor
  * @param string $name : The name of the dropdown
  */
  function checkbox($name,$label=NULL,$ischecked=false){
  	$this->name=$name;
	$this->ischecked=$ischecked;
	$this->label=$label;
	$this->cssClass='transparentbgnb';
	$this->cssId = 'input_'.$name;
  }
  /*
  *Method to set the label 
  *@param string $label :value to be displayed
  */
  function setLabel($label)
  {
  	$this->label=$label;
  }
  
  /**
  * Method to set the 
  * cssClass
  * @param $cssClass string : The css class associated with the checkbox
  */ 
  function setCSS($cssClass)
  {
  	$this->cssClass=$cssClass;
  }
  
  /*
	* Method to set the cssId class 
	* @param string $cssId
	*/
    function setId($cssId)
    {
        $this->cssId = $cssId;
    } 
  
  /**
  * Method to set the checkbox to checked or unchecked
  * @param $isChecked  boolean : toggles between checked and unchecked stated
  */
  function setChecked($isChecked)
  {
  	$this->ischecked=$isChecked;
  }
  
  
  /**
  *  Function to set the value of a checkbox
  *	 @param $value : the new value of the checkbox
  */
  function setValue($value)
  {
  	$this->value=$value;
  }
  
  /**
  * Method that outputs the checkbox
  * @return $str string : the output of the checkbox
  */ 
  function show()
  {
  	$str='<input type="checkbox"';
	if($this->name){
		$str.=' name="'.$this->name.'"';	
	}	
	if($this->cssClass){
		$str.=' class="'.$this->cssClass.'"';	
	}
	if ($this->cssId) {
            $str .= ' id="' . $this->cssId . '"';
    }
	if($this->ischecked){
		$str.=' CHECKED';
	}
	if ($this->value) {
	 	$str.= 'value="'.$this->value.'"';
	}
	if($this->extra){
		$str.=$this->extra;	
	}
	$str.='>';
	//This position of the label will depend on the form's display type
	//$str.=$this->label;
	return $str;
  }
}
?>
