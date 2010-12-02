<?php

/**
 * HTML Label class for Chisimba using the DOM Object
 *
 * This file contains the label class which is used to generate
 * HTML label elements for form input elements. It was modified after the original
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
 * @package   htmldom
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
 * This file contains the label class which is used to generate
 * HTML label elements for form input items. It was modified after the original
 * HTMLelements input class by Paul Mungai as part of the Chisimba
 * hackathon 2010 11 29. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('htmllabel', 'htmldom')
 * Input class acts as an base class
 * for some commom objects
 *
 * @author Paul Mungai
 * @copyright 2010
 *
 */
class htmlabel extends object {

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $labelValue;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $forId;
    /**
     *
     * Object to hold the dom document
     *
     * @var string Object $objDom
     * @access private
     */
    private $objDom;
    /**
     * Constructor function
     *
     * @param  string $labelValue .
     * @param  string $forId
     * @return void
     * @access public
     */
    public function init() {
        // Instantiate the built in PHP DOM extension and create DOM document.
        $this->objDom = new DOMDocument();
    }
    /**
     * Constructor function
     *
     * @param  string $labelValue .
     * @param  string $forId
     * @return void
     * @access public
     */
    public function label($labelValue=null, $forId=null)
    {
        $this->labelValue=$labelValue;
        $this->forId=$forId;
    }
    /**
     *
     * Standard show function to render the input using the DOM document
     * object
     * Example
     * $htmLabel = $this->getObject('htmlabel', 'htmldom');
     * $htmLabel->label('labelvalue', 'forid');
     * $htmLabel->show();
     *
     * @param <type> $labelValue
     * @param <type> $forId
     * @return <type>
     */
    public function show($caption=null) {
        $label = $this->objDom->createElement('label');
        // Set the label attributes
        if ($this->forId) {
            $label->setAttribute('for', $this->forId);
        }
        if ($this->labelValue) {
            $labelVal = $this->objDom->createTextNode($this->labelValue);
            $label->appendChild($labelVal);
        }
        $label = $this->objDom->appendChild($label);
        $ret = $this->objDom->saveHTML();
        return $ret;
    }

    /**
     *
     * A standard setter. The following params may be set here
     * $labelValue - Set the label value of the label element
     * $forId - Set the forId to use in the label element
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
     * $labelValue - Fetch the label value of the label element
     * $forId - Fetch the forId to use in the label element
     *
     * @param string $param The name of the parameter to set
     * @access public
     */
    public function getValue($param) {
        return $this->$param;
    }

}

?>