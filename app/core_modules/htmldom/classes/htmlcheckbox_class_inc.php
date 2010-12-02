<?php

/**
 * Input class acts as an base class for some commom objects
 * eg. buttons , text ,radio buttons ,check boxes
 * This file contains the input class which is used to generate
 * HTML input elements for forms. It was modified after the original
 * HTMLelements input class by Paul Mungai as part of the Chisimba
 * hackathon 2010 11 29. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('htmlinput', 'htmldom')
 * Input class acts as an base class
 * for some commom objects
 * Example
 * $radiobutton = $this->getObject('htmlcheckbox', 'htmldom');
 * $radiobutton->setValue('name', 'checkboxname');
 * $radiobutton->setValue('value', '1');
 * $radiobutton->setValue('type', 'checkbox');
 * $radiobutton->show();
 *
 * @author Jerusha Wambui
 * @copyright 2010
 *
 */
if (!
        /**
         * Description for $GLOBALS
         * @global unknown $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Input class acts as an base class for some commom objects
 * eg. buttons , text ,radio buttons ,check boxes
 * This file contains the input class which is used to generate
 * HTML input elements for forms. It was modified after the original
 * HTMLelements input class by Paul Mungai as part of the Chisimba
 * hackathon 2010 11 29. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('htmlinput', 'htmldom')
 * Input class acts as an base class
 * for some commom objects
 * Example
 * $htmlInput->setValue('name', 'toaster');
 * $htmlInput->setValue('size', '10');
 * $htmlInput->setValue('label', 'checkbox1');
 * $htmlInput->setValue('value', 'hello there!');
 * $htmlInput->setValue('ischecked', '');
 * $str = $htmlInput->show();
 * $htmlInput = $this->getObject('htmlcheckbox', 'htmldom');
 * $htmlInput->setValue('name', 'toaster');
 * $htmlInput->setValue('size', '10');
 * $htmlInput->setValue('label', 'checkbox1');
 * $htmlInput->setValue('value', 'hello there!');
 * $htmlInput->setValue('ischecked', 'checked');
 * $str .= $htmlInput->show();
 *@author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Kariuki wa Njenga <jkariuki@uwc.ac.za>
 * @author Jerusha Wambui<jerusha@uonbi.ac.ke>
 * @copyright 2010
 *
 */
class htmlcheckbox extends object {

   /**
     * Variable used to determine
     * whether or not the box is
     * checked by default
     *
     * @var    boolean $ischecked
     * @access public
     */
  public $ischecked;

    /**
     * Holds the value of the input element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $value
     * @access private
     *
     */
  
    private $value;
    /**
     * Holds the name of the input element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $name
     * @access private
     *
     */
    private $name;
    /**
     * Holds the type value of the input element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $type
     * @access private
     *
     */
    private $type;
    
    /**
     *
     * Intialiser for the htmldom INPUT object
     *
     * @access public
     * @return void
     *
     */
	  /**
     * Class Constructor
     * @param string $cssClass : The class of the radio button
     */
   
   
    private $cssClass;
    /**

     * Class Constructor
     * @param string $label : set an option as checked in the radio group
     */
    private $label;
	  /**
     *
     * Object to hold the dom document
     *
     * @var string Object $objDom
     * @access private
     */
    private $objDom;
	
    public function init() {
        // Instantiate the built in PHP DOM extension and create DOM document.
        $this->objDom = new DOMDocument();
    }

    /**
     *
     * Standard show function to render the input using the DOM document
     * object
     *
     *
     * @param <type> $name
     * @param <type> $value
     * @param <type> $type
     * @return <type>
     */
    public function createCheckbox($caption=null) {
        $checkbox = $this->objDom->createElement('input');
        // Set the input attributes
        if ($this->name) {
            $checkbox->setAttribute('name', $this->name);
        }
        if ($this->value) {
            $checkbox->setAttribute('value', $this->value);
        }
        if ($this->type) {
            $checkbox->setAttribute('type', $this->type);
        }
		if ($this->label) {
            $checkbox->setAttribute('label', $this->label);
        }
		if ($this->ischecked) {
            $radio->setAttribute('checked', $this->checked);
        }
        if ($this->cssClass) {
            $radio->setAttribute('class', $this->cssClass);
        }
        $checkbox = $this->objDom->appendChild($checkbox);
        $ret = $this->objDom->saveHTML();
        return $ret;
    }

	/**
   * Method to set the text of
   * the accompanying label
   *
   * @param string $label value to be displayed
   * @return void
   * @access public
   */
  public function setLabel($label)
  {
      $this->label=$label;
  }

  /**
  * Method to set the css class of
  * the element as defined in the
  * main css document
  *
  * @param $cssClass string The css class to be associated with the checkbox
  */
  public function setCSS($cssClass)
  {
      $this->cssClass=$cssClass;
  }

  /**
   * Method to set the DOM Id of the elements
   *
   * @param string $cssId the Id
   * @return void
   * @access public
   */
   public function setId($cssId)
    {
        $this->cssId = $cssId;
    }

  /**
  * Method to set the checkbox to checked or unchecked
  *
  * @param $isChecked boolean toggles between checked and unchecked stated
  * @return void
  * @access public
  */
  public function setChecked($isChecked)
  {
      $this->ischecked=$isChecked;
  }
    /**
     *
     * A standard setter. The following params may be set here
     * $size - Set the size of the input element
     * $class - A CSS class to use in the input element
     * $value - Set the value of the input element
     * $vtype  - Set the vtype (Vertical Orientation) of the input element
     *
     *
     * @param string $param The name of the parameter to set
     * @param string $value The value to set the parameter to
     * @access public
     */
    public function setValue($param, $value) {
        $this->$param = $value;
    }

    /**
     * A standard fetcher. The following params may be fetched here
     * $size - Fetch the size of the input element
     * $class - Fetch the CSS class to use in the input element
     * $value - Fetch the value of the input element
     * $vtype  - Fetch the vtype (Vertical Orientation) of the input element
     *
     * @param string $param The name of the parameter to set
     * @access public
     */
    public function getValue($param) {
        return $this->$param;
    }
	/**
  * Method to render the checkbox as HTML code
  *
  * @return string the HTML of the checkbox
  * @access public
  */
  public function show()
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
        $str.=' checked="checked" ';
    }
    if ($this->value) {
         $str.= ' value="'.$this->value.'"';
    }
    if($this->extra){
        $str.=' '.$this->extra;
    }
    $str.=' />';
    //This position of the label will depend on the form's display type
    //$str.=$this->label;
    return $str;
  }
}

?>