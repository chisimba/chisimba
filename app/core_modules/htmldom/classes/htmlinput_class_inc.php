<?php

/**
 * Input class for Chisimba using the DOM Object
 *
 * Input class acts as an base class for some commom objects
 * This file contains the input class which is used to generate
 * HTML input elements for forms. It was modified after the original
 * HTMLelements input class by Paul Mungai as part of the Chisimba
 * hackathon 2010 11 29. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('htmlinput', 'htmldom')
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
 * @package   htmlelements
 * @author    Paul Mungai <paul.mungai@wits.ac.za>
 * @copyright 2010, University of the Western Cape & AVOIR Project
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
 * $htmlInput = $this->getObject('htmlinput', 'htmldom');
 * $htmlInput->setValue('name', 'toaster');
 * $htmlInput->setValue('size', '10');
 * $htmlInput->setValue('value', 'hello there!');
 * $htmlInput->setValue('vtype', 'top');
 * $htmlInput->show();
 *
 * @author Paul Mungai
 * @copyright 2010
 *
 */
class htmlinput extends object {

    /**
     * Holds the size of the input element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $size
     * @access private
     *
     */
    private $size;
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
     * Holds the vtype value of the input element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $vtype
     * @access private
     *
     */
    private $vtype;
    /**
     * Holds the css value of the input element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $css
     * @access private
     *
     */
    private $css;

    /**
     *
     * Intialiser for the htmldom INPUT object
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
     * Standard show function to render the input using the DOM document
     * object
     *
     *
     * @param <type> $size
     * @param <type> $name
     * @param <type> $value
     * @param <type> $css
     * @param <type> $vtype
     * @return <type>
     */
    public function show($caption=null) {
        $input = $this->objDom->createElement('input');
        // Set the input attributes
        if ($this->name) {
            $input->setAttribute('name', $this->name);
        }
        if ($this->size) {
            $input->setAttribute('size', $this->size);
        }
        if ($this->value) {
            $input->setAttribute('value', $this->value);
        }
        if ($this->css) {
            $input->setAttribute('css', $this->css);
        }
        if ($this->vtype) {
            $input->setAttribute('vtype', $this->vtype);
        }
        $input = $this->objDom->appendChild($input);
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