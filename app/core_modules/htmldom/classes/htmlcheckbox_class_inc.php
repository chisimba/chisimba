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
 * $htmlInput = $this->getObject('htmlcheckbox', 'htmldom');
 * $htmlInput->setValue('name', 'toaster');
 * $htmlInput->setValue('size', '10');
 * $htmlInput->setValue('value', 'hello there!');
 * $htmlInput->setValue('vtype', 'top');
 * $htmlInput->show();
 *
 * @author Jerusha Wambui
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
    public function show($caption=null) {
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
		if ($this->ischecked) {
            $radio->setAttribute('checked', $this->false);
        }
        if ($this->cssClass) {
            $radio->setAttribute('class', $this->cssClass);
        }
        $checkbox = $this->objDom->appendChild($checkbox);
        $ret = $this->objDom->saveHTML();
        return $ret;
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

}

?>