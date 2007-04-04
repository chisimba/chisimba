<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML base class
require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

/**
* Button class controls the rendering of buttons on webpage or forms
* @package button
* @category HTML Controls
* @version $Id$
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @author Wesley Nitsckie 
* @author Tohir Solomons
* @example: 
*  $this->objButton=&new button('buttonname');
*  $this->objButton->setValue('Button Value');
*  $this->objButton->setOnClick('alert(\'An onclick Event\')');
*  $this->objButton->setToSubmit();  //If you want to make the button a submit button 
*/
class button extends abhtmlbase implements ifhtml
{

    /**
    * @var string $onsubmit: The javascript to be executed on submit, if any.
    */
    public $onsubmit;

    /**
    * @var bool $issubmitbutton: True | False whether the button is a submit
    * button or not.
    */
    public $issubmitbutton;
    
    /**
    * Initialization method to set default values
    * 
    * @param string $name : name of the button
    * @param string $value optional :value of the button
    * @param string $onclick optional :javascript function that will be called
    */
    public function button($name=null, $value = null, $onclick = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->onclick = $onclick;
        $this->cssClass = 'button';
		$this->cssId = 'input_'.$name;
    } 

    /*
	* Method to set the onclick 
	* event for the button
	* @param string $onclick
	*/
    public function setOnClick($onclick)
    {
        $this->onclick = $onclick;
    } 

    /*
	* Method to set the cssClass class 
	* @param string $cssClass
	*/
    function setCSS($cssClass)
    {
        $this->cssClass = $cssClass;
    }
	
	/*
	* Method to set the cssId class 
	* @param string $cssId
	*/
    public function setId($cssId)
    {
        $this->cssId = $cssId;
    } 

    /*
	* function to set the button to
	* a submit button
	*/
    public function setToSubmit()
    {
        $this->issubmitbutton = true;
    } 
    
    /**
    * Method to show the button
	* @return $str string : Returns the button's html
    */
    public function show()
    {
        $str = '<input';
        $str .= ' value="' . $this->value . '"';
        //check if the buttons is a submit button or a normal button
		if ($this->issubmitbutton) {
            $str .= ' type="submit"';
        } else {
            $str .= ' type="button"';
        } 
        if ($this->name) {
            $str .= ' name="' . $this->name . '"';
        }
		if ($this->cssId) {
            $str .= ' id="' . $this->cssId . '"';
        }		
        if ($this->cssClass) {
            $str .= ' class="' . $this->cssClass . '"';
        } 
        if ($this->onclick) {
            $str .= ' onclick="' . $this->onclick . '"';
        } 
        if ($this->extra) {
            $str .= ' '.$this->extra;
        } 
        $str .= ' />';

        return $str;
    } 
} 

?>
