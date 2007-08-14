<?php

/**
 * This file contains the checkbox class which is used to render
 * and html checkbox
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
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Kariuki wa Njenga <jkariuki@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */




// Include the HTML base class
require_once("abhtmlbase_class_inc.php");

// Include the HTML interface class
require_once("ifhtml_class_inc.php");

/**
 * The checkbox class is used to create HTML
 * checkboxes used in HTML forms
 *
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Kariuki wa Njenga <jkariuki@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @example
 *    $objElement = new checkbox('m','Male',true);  // this will checked
 *    $check = $objElement->show();
 *    $objElement = new checkbox('f','Female');     //this will not be checked
 *    $check .= $objElement->show();
 */
class checkbox  extends abhtmlbase implements ifhtml
{

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
     * Variable used to specify
     * the value of the element
     *
     * @var    string $value
     * @access public
     */
  public $value; //Kariuki added

  /**
   * Constructor for class
   *
   * @param string $name The name of the checkbox
   * @param string $label The text of the accompanying label
   * @param bool $ischecked whether or not the box is checkedd by default
   */
  public function checkbox($name,$label=NULL,$ischecked=false){
  	$this->name=$name;
	$this->ischecked=$ischecked;
	$this->label=$label;
	$this->cssClass='transparentbgnb';
	$this->cssId = 'input_'.$name;
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
  * Function to set the value of a checkbox
  *
  *	@param $value the new value of the checkbox
  * @return void
  * @access public
  */
  public function setValue($value)
  {
  	$this->value=$value;
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