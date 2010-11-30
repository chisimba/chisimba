<?php

/**
 * This file contains the calendar class for Chisimba
 * It uses the DOM Object to render an html calendar for date selection within forms
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
 * @category  Chisimba
 * @package   htmldom
 * @author    Paul Mungai <paul.mungai@wits.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
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
 * The calendar class is used to render a text input
 * field for date selections, along with a button
 * which when clicked provides a popup graphical
 * calendar for more user friendly date selection
 * within HTML forms.
 *
 * @category  Chisimba
 * @package   htmldom
 * @author    Paul Mungai <paul.mungai@wits.ac.za>
 * @copyright 2010 AVOIR
 * @link      http://avoir.uwc.ac.za
 */
class htmlcalendar extends object {

    /**
     * variable used to store the css
     * class of the element
     *
     * @var    string $css
     * @access private
     */
    private $css;
    /**
     * The name of the calendar window
     * which will pop up
     *
     * @var    string $windowName
     * @access private
     */
    private $windowName;
    /**
     * Holds the value of the input element, and is set using
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
     * Holds the month value of the input element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $mth
     * @access private
     *
     */
    private $mth;
    /**
     * Holds the day value of the input element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $day
     * @access private
     *
     */
    private $day;
    /**
     * Holds the year value of the input element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $year
     * @access private
     *
     */
    private $year;
    /**
     * Holds the id value of the input element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $id
     * @access private
     *
     */
    private $id;
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
     * Intialiser for the htmldom Calendar object
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
     * Method to render the textinput and popup window
     *
     * @return string The html of the textinput with onclick calendar popup
     */
    public function show() {
        $input = $this->objDom->createElement('input');
        // Set the input attributes
        if ($this->name) {
            $input->setAttribute('name', $this->name);
        }
        if ($this->type) {
            $input->setAttribute('type', $this->type);
        }
        if ($this->id) {
            $input->setAttribute('id', $this->id);
        }
        if ($this->mth && $this->day && $this->year) {

            if (checkdate($this->mth, $this->day, $this->year)) {
                $this->value = $this->mth . '/' . $this->day . '/' . $this->year;
            }
        }
        if ($this->css) {
            $input->setAttribute('class', $this->css);
        }
        if (empty($this->windowName)) {
            $this->windowName = "win";
        }

        //Create ahref dom object
        $ahref = $this->objDom->createElement('a');
        // Set the ahref attributes
        $ahref->setAttribute('href', '#');
        $ahref->setAttribute('onclick', "window.open('core_modules/htmlelements/classes/cal.php','" . $this->windowName . "','width=350,height=200'); return false\");");

        //Create image dom object
        $img = $this->objDom->createElement('img');
        // Set the image attributes
        $img->setAttribute('src', "core_modules/htmlelements/resources/images/schedule_ico.gif");
        //Add img as child to ahref
        $ahref->appendChild($img);
        //Append objects
        $this->objDom->appendChild($input);
        $this->objDom->appendChild($ahref);
        $ret = $this->objDom->saveHTML();
        return $ret;
    }
    /**
     *
     * A standard setter. The following params may be set here
     * $windowName - Set the popup window name
     * $name - Set input name
     * $value - Set the value of the input element
     * $css  - Set the css of the input element
     * $id - Set the id of the input element
     * $mth - Set the month value
     * $day  - Set the day value
     * $year  - Set the year value
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
     * $windowName - Fetch the popup window name
     * $name - Fetch input name
     * $value - Fetch the value of the input element
     * $css  - Fetch the css of the input element
     * $id - Fetch the id of the input element
     * $mth - Fetch the month value
     * $day  - Fetch the day value
     * $year  - Fetch the year value
     *
     * @param string $param The name of the parameter to fetch
     * @access public
     */
    public function getValue($param) {
        return $this->$param;
    }
}
?>