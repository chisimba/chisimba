<?php

/* -------------------- security class extends module ---------------- */
// security check - must be included in all scripts
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
if (!
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
 * $radiobutton = $this->getObject('htmlradio', 'htmldom');
 * $radiobutton->setValue('name', 'radioname');
 * $radiobutton->setValue('value', '1');
 * $radiobutton->setValue('type', 'radio');
 * $radiobutton->show();
 *
 * @author Jerusha Wambui
 * @copyright 2010
 *
 */
class htmlradio extends object
{
 

    /**
     * Selected radio button
     * @var    boolean $selected
     * @access public 
     */
    public $selected;
    /**
     * String to hold a space
     * @var    string $breakSpace
     * @access public
     */
    public $breakSpace = '';
    /**
     * Holds the number of table columns
     * @var    integer $tableColumns
     * @access public
     */
    public $tableColumns = 3;
    /**
     * Class Constructor
     * @param string $name : The name of the radio group
     */
    private $name;
    /**
     * Class Constructor
     * @param string $options : The number of options the radio group
     */
    private $options;
    /**
     * Class Constructor
     * @param string $label : The label of options the radio group
     */
    private $label;
    /**
     * Class Constructor
     * @param string $cssClass : The class of the radio button
     */
    private $cssClass;
    /**

     * Class Constructor
     * @param string $checked : set an option as checked in the radio group
     */
    private $checked;
    /**
     *
     * Object to hold the dom document
     *
     * @var string Object $objDom
     * @access private
     */
    private $objDom;

    /**
     *
     * Intialiser for the htmldom BUTTON object
     *
     * @access public
     * @return void
     *
     */
    public function init() {
        // Instantiate the built in PHP DOM extension and create DOM document.
        $this->objDom = new DOMDocument();
    }

    /**
     *
     * Standard createRadio function to render the radio using the DOM document
     * object
     *
     *
     * @param <type> $name
     * @param <type> $value
     * @param <type> $type
     * @return <type>
     */
    public function createRadio($caption=null) {
        $radio = $this->objDom->createElement('input');
        if ($this->name) {
            $radio->setAttribute('name', $this->name);
        }
        if ($this->type) {
            $radio->setAttribute('type', $this->type);
        }
        if ($this->value) {
            $radio->setAttribute('value', $this->value);
        }
        if ($this->checked) {
            $radio->setAttribute('checked', $this->checked);
        }
        if ($this->cssClass) {
            $radio->setAttribute('class', $this->cssClass);
        }
        // If there is an onclick event specified, add it as an attribute.
        $radio = $this->objDom->appendChild($radio);
        $ret = $this->objDom->saveHTML();
        //die($ret);
        return $ret;
    }

    /**
     * Method that adds a options to
     * the radio group
     * @param string $label : The label that goes with the option
     * @param string $value : The value for a give option

     */
    public function addOption($value, $label) {
        $this->options[$value] = $label;
    }
    /**
     * Method to show the option group
     */
    public function show() {
        if (strtolower($this->breakSpace) == 'table') {
            return $this->showTable();
        } else {
            return $this->showNormal();
        }
    }

    /**
     * Method to show the option group with the given breakspace (not table)
     */
    public function showNormal() {
        $str = '';

        $breakSpace = '';
        if (empty($this->options)) {
            $option = array(1,2);
            $this->options = $option;
        }

        foreach ($this->options as $opt => $lbl) {
            //Create Space
            $str .= $breakSpace;
            // If no CSS Id is given, it takes the default value of input_$opt for accessibility
            // If CSS Id is given, it takes the default value of input_$opt for accessibility, as well as CSS one           
            if ($this->cssId) {
                $cssId = 'input_' . $this->name . $opt . ' ' . $this->cssId;
            } else {
                $cssId = 'input_' . $this->name . $opt;
            }

            // Cleanup to the CSS Id to make it W3C Compliant
            // At the moment, it checks for \ and /
            $cssId = preg_replace('/(\/|\\\)/', '_', $cssId);

            $this->cssId = $cssId;
            //Create Radio Button for this option
            $thisRadio = $this->createRadio();
            //Create label for the option
            $label = new label($lbl, $cssId);
            $label = $label->show();

            $breakSpace = $this->breakSpace;
            $str .= $thisRadio . $label;
        }

        return $str;
    }

    /**
     * Method to show the option group for a table
     */
    public function showTable() {
        $table = new htmltable();
        $table->startRow();
        $table->cellpadding = 1;

        $counter = 0;

        $equalColumns = (100 - (100 % $this->tableColumns)) / $this->tableColumns;

        foreach ($this->options as $opt => $lbl) {
            $counter++;

            // If no CSS Id is given, it takes the default value of input_$opt for accessibility
            // If CSS Id is given, it takes the default value of input_$opt for accessibility, as well as CSS one
            if ($this->cssId) {
                $cssId = 'input_' . $this->name . $opt . ' ' . $this->cssId;
            } else {
                $cssId = 'input_' . $this->name . $opt;
            }

            // Cleanup to the CSS Id to make it W3C Compliant
            // At the moment, it checks for \ and /
            $cssId = preg_replace('/(\/|\\\)/', '_', $cssId);

            $this->cssId = $cssId;
            //Create Radio Button for this option
            $thisRadio = $this->createRadio();
            //Create label for the option
            $label = new label($lbl, $cssId);
            $label = $label->show();

            $str = $thisRadio . $label;

            $table->addCell($str, $equalColumns . '%');

            if ($counter % $this->tableColumns == 0) {
                $table->endRow();
                $table->startRow();
            }
        }


        if ($counter % $this->tableColumns == 0) {
            $table->endRow();
        } else {
            for ($i = 1; $i < ($counter % $this->tableColumns); $i++) {
                $table->addCell('&nbsp;', $equalColumns . '%');
            }
            $table->endRow();
        }

        return $table->show();
    }

/**
 *
 * A standard setter. The following params may be set here
 * $size - Set the size of the input element
 * $class - A CSS class to use in the input element
 * $value - Set the value of the input element
 * $type  - Set the vtype (Vertical Orientation) of the input element
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
     * $type  - Fetch the vtype (Vertical Orientation) of the input element
     *
     * @param string $param The name of the parameter to set
     * @access public
     */
    public function getValue($param) {
        return $this->$param;
    }

}
?>