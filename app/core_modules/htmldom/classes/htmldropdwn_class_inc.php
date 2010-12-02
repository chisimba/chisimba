<?php

/**
 *
 * A dropdown class using DOM extension
 *
 * This file contains the dropdown class which is used to generate
 * HTML dropdown elements for forms. It was modified after the original
 * HTMLelements button class by Nguni Phakela as part of the Chisimba
 * hackathon 2010 11 29. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('html5dropdown', 'htmldom')
 *
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */
// security check - must be included in all scripts
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
 *
 * @category  Chisimba
 * @package   htmldom
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @author    Nguni Phakela <nonkululeko.phakela@dkeats.com>
 *
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: button_class_inc.php 16438 2010-01-22 15:38:42Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @example:
 *       $objButt = $this->newObject('htmlbutton', 'htmldom');
 *       $objButt->setValue("cssId","what_a_long_id_hey");
 *       $objButt->setValue('onclick','javascript:alert("Some alert");');
 *       $str = $objButt->show("TEST SUCCESSFUL YAY YAY YIPPEE");
 */
class htmldropdwn extends object {

    /**
     * Holds the name of the select, and is set using
     * $this->setValue()
     *
     * @var string $name
     * @access private
     *
     */
    private $name;
    /**
     * Holds the value of the options
     *
     * @var string $name
     * @access private
     *
     */
    private $value;
    /**
     * Holds the onChange javascript event for the select, and is set using
     * $this->setValue()
     *
     * @var string $name
     * @access private
     *
     */
    private $onchange;
    /**
     * Holds the CSS id for the select, and is set using
     * $this->setValue()
     *
     * @var string $name
     * @access private
     *
     */
    private $cssId;
    /**
     * Holds the CSS class for the button, and is set using
     * $this->setValue().
     * 
     * @var string $name
     * @access private
     *
     */
    private $cssClass;
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
     * @var string $selected: The value that selected
     */
    private $selected = NULL;
    /**
     *
     * @var array $options: holds the options for the combo box
     */
    public $options = array();
    /**
     *
     * @var string $size: Defines the number of visible items in the dropdown list
     */
    private $size = 1;
    /**
     *
     * @var boolean $multiple: When set allows multiple entries to be selected
     */
    private $multiple = FALSE;

    /**
     *
     * Intialiser for the htmldom SELECT object
     *
     * @access public
     * @return void
     *
     */
    public function init() {
        //Instantiate the built in PHP DOM extension and create DOM document.
        $this->objDom = new DOMDocument();
    }

    /**
     *
     * Standard show function to render the select using the DOM document
     * object
     *
     * @param <type> $caption
     * @param <type> $name
     * @param <type> $value
     * @param <type> $onclick
     * @return <type>
     */
    public function show($option=null) {
        $select = $this->objDom->createElement('select');

        foreach ($option as $key => $value) {
            $caption = $value;
            $this->options = $this->objDom->createElement('option');
            $this->options->setAttribute('class', 'dropdown');
            $this->options->setAttribute('value', $key);
            $select->appendChild($this->options);
            $text = $this->objDom->createTextNode($caption);
            $this->options->appendChild($text);
        }

        if ($this->cssClass) {
            $select->setAttribute('class', $this->cssClass);
        }

        // If there is a name, then use it.
        if ($this->name) {
            $select->setAttribute('name', $this->name);
        }
        // If a css id is set, then add it as an attribute
        if ($this->cssId) {
            $select->setAttribute('id', $this->cssId);
        }
        // If there is an onclick event specified, add it as an attribute.
        if ($this->onchange) {
            $select->setAttribute('onchange', $this->onchange);
        }
        if ($this->size > 1) {
            $select->setAttribute('size', $this->size);
        }
        if ($this->multiple == TRUE) {
            $select->setAttribute('multiple', 'multiple');
        }

        $options = $select->getElementsByTagName('option');
        foreach ($options as $value) {
            if ($value->hasAttributes()) {
                $count = 0;
                foreach ($value->attributes as $attr) {
                    $value = $attr->nodeValue;
                    if ($this->selected == $attr->nodeValue) {
                        $options->item($count)->setAttribute("selected", "selected");
                    }
                    $count++;
                }
            }
        }

        $this->objDom->appendChild($select);
        $ret = $this->objDom->saveHTML();
        return $ret;
    }

    /**
     *
     * A standard setter. The following params may be set here
     * $name - The name used in the select
     * $size - The size of the select
     * $onchange - A javascript that is executed on changing the select option
     * $cssClass - A CSS class to use in the select
     * $cssId - A CSS id to use in the select
     * 
     * @param string $param The name of the parameter to set
     * @param string $value The value to set the parameter to
     * @access public
     */
    public function setValue($param, $value) {
        $this->$param = $value;
    }

    /**
      * Method to set the selected value
      *
      * @param  $value string : The value that you want selected
      * @access public
      * @return void
      */
    public function setSelected($value) {
        $this->selected = $value;
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