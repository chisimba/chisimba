<?php

/**
* Hidden Forum Input class
* 
* @author Tohir Solomons
* @copyright 2005
*/

class hiddeninput
{

    /**
    * @var string $name
    */
    var $name;
    
    /**
    * @var string $value
    */
    var $value;
   
    /**
    * Initialization method
    * 
    * @param string $name optional :sets the name of the hidden form input
    * @param string $value optional :sets the value of the hidden form input
    */
    function hiddeninput($name = null, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    } 
    
    /**
    * Method to set the name of a hidden form input
    * 
    * @param string $name Sets the name of the hidden form input
    */
    function setName($name)
    {
        $this->value = $name;
    } 
    
	/**
    * Method to set the value of a hidden form input
    * 
    * @param string $value Sets the value of the hidden form input
    */
    function setValue($value)
    {
        $this->value = $value;
    } 

    /**
    * Method to return the hidden form input for display on the form
    * @return string $str: the hidden form input element for display
    */
    function show()
    {
        $str = '<input type="hidden" value="' . $this->value . '"';
        $str .= ' name="' . $this->name . '"';
        $str .= ' />';
        return $str;
    } 
} 

?>