<?php

/**
* Text Input class controls buttons
* 
* @author Wesley Nitsckie 
* @author Megan Watson
* @author Tohir Solomons
* @version $Id$
* @copyright 2003
*/
require_once("htmlbase_class_inc.php");
class textinput extends htmlbase
{

    /**
    * @var integer $size: The width of the text input
    */
    var $size;
   
    

    /**
    * Initialization method to set default values
    * 
    * @param string $name optional :sets the name of the text input
    */
    function textinput($name = null, $value = null, $type=null, $size=null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->cssClass = 'text';
        if (!is_null($type)) {
            $this->fldType=$type;
        }
		else {
	        $this->fldType='text';
		}
   		if (!is_null($size)) {
			$this->size = $size;
		}
     	$this->cssId = 'input_'.$name;
    } 
    /**
    * Method to set the css class
    * 
    * @param string $css 
    * @deprecated  <----------------------------------------------------------
    */
    function setCss($css)
    {
        $this->cssClass = $css;
    } 
    /*
	* Method to set the value of the text box
	* @param string $value 
    * @deprecated <----------------------------------------------------------
	*/
	
	/*
	* Method to set the cssId class 
	* @param string $cssId
	*/
    function setId($cssId)
    {
        $this->cssId = $cssId;
    } 
	
    function setValue($value)
    {
        $this->value = $value;
    } 

    /**
    * Method to return the text input for display on the form
    * @return string $str: the text element for display
    */
    function show()
    {
        $str = '<input type="'.$this->fldType.'" value="' . $this->value . '"';
        $str .= ' name="' . $this->name . '"';

        // only add elements if they have a value
        if ($this->cssClass) {
            $str .= ' class="' . $this->cssClass . '"';
        }
        
        if ($this->size) {
            $str .= ' size="' . $this->size . '"';
        }
        if ($this->cssId) {
            $str .= ' id="' . $this->cssId . '"';
        }
        if ($this->extra) {
            $str .= $this->extra;
        }
        $str .= '>';
        return $str;
    } 
} 

?>
