<?php
/**
 * This file contains the button class which is used to generate
 * HTML button elements for forms
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

// Include the HTML base class

/**
 * Description for require_once
 */
require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
 * Button class controls the rendering of buttons on webpages or forms
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @example:
 *            $this->objButton=new button('buttonname');
 *            $this->objButton->setValue('Button Value');
 *            $this->objButton->setOnClick('alert(\'An onclick Event\')');
 *            $this->objButton->setToSubmit();  //If you want to make the button a submit button
 */
class button extends abhtmlbase implements ifhtml
{

    /**
    * @var string $onsubmit: The javascript to be executed on submit, if any.
    */
    public $onsubmit;

    /**
    * @var bool $issubmitbutton: True | False whether the button is a submit
    *           button or not.
    */
    public $issubmitbutton;

    /**
    * Initialization method to set default values
    *
    * @param string $name    : name of the button
    * @param string $value   optional :value of the button
    * @param string $onclick optional :javascript function that will be called
    */
    public function button($name=null, $value = null, $onclick = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->onclick = $onclick;
        $this->cssClass = 'button';
		//$this->cssId = 'input_'.$name;
    }

    /**
	 * Method to set the action for the onclick event
	 * for the button
	 *
	 * @param string $onclick
     * @return void
     * @access public
     */
    public function setOnClick($onclick)
    {
        $this->onclick = $onclick;
    }

    /**
	 * Method to set the cssClass private variable
	 * which determines the DOM class of the button as
	 * definied in the CSS
	 *
	 * @param string $cssClass the class
     * @return void
     * @access public
     */
    function setCSS($cssClass)
    {
        $this->cssClass = $cssClass;
    }

	/**
	 * Method to set the cssId private member
	 * which determines the DOM id of the button
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
	 * Method used to set the button as
	 * a submit button for a form
     *
     * @return void
     * @access public
     */
    public function setToSubmit()
    {
        $this->issubmitbutton = true;
    }

    /**
    * Method to render the button as an HTML string
    *
	* @return string Returns the button's html
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