<?php
/**
 * Class htmltextarea extends object
 *
 * Textarea class to use to make textarea inputs
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
 * @author Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: htmltextarea_class_inc.php 19909 2010-12-1 15:43:46 nguni52 $
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
* textarea class to use to make textarea inputs.
*
* @package   htmlTextarea
* @category  HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @version   $Id:
* @author    Wesley Nitsckie
* @author    Megan Watson
* @author    Tohir Solomons
 *@author    Nguni Phakela
* @example   $str = $this->newObject("htmltextarea", "htmldom");
*            $str->setValue("cols","10");
*            $str->setValue("rows","20");
*            $str->setValue("name","comments");
*            $str->setValue("cssId","commentsID");
*            $str = $str->show();
* 
*/
 class htmltextarea extends object
 {
    /**
      * Holds the name of the textarea, and is set using $this->setValue()
      *
      * @var string $name
      * @access private
      *
      */
    private $name;
     /**
      *
      * @var string $cols: The number of columns the textarea will have
      * @access private
      */
    private $cols;

    /**
      *
      * @var string $rows: The number of rows the textarea will have
     *  @access private
      */
    private $rows;

    /**
      *
      * @var string $autoGrow Whether or not to autogrow the textarea
      *  using jQuery
      */
    private $autoGrow=FALSE;

    /**
      *
      * Object to hold the dom document
      *
      * @var string Object $objDom
      * @access private
      */
    private $objDom;

    /**
      * Holds the CSS id for the textarea, and is set using $this->setValue().
      * Value is returned using $this->getValue();
      *
      * @var string $name
      * @access private
      *
      */
    private $cssId;

    /**
      *
      * Holds the CSS class for the textarea, and is set using $this->setValue().
      * @var string $cssClass:
      * @access private
      */
    private $cssClass;

    /**
      *
      * Specifies that a textarea should be disabled, and is set using $this->setValue().
      * @var string $cssClass:
      * @access private
      */
    private $disabled=FALSE;

    /**
      *
      * Specifies that a text-area should be read-only, and is set using $this->setValue().
      * @var string $cssClass:
      * @access private
      */
    private $readonly=FALSE;

    /**
     *
     * Intialiser for the htmldom TEXTAREA object
     *
     * @access public
     * @return void
     *
     */
    /**
     * Holds the onClick javascript event for the button, and is set using
     * $this->setValue()
     *
     * @var string $name
     * @access private
     *
     */
    private $onclick;



    public function init()
    {
        // Instantiate the built in PHP DOM extension and create DOM document.
        $this->objDom = new DOMDocument();
        $this->rows=4;
        $this->cols=50;
    }

    /**
    * Method to show the textarea
    * @return string The formatted link
    */
    public function show()
    {
        $textarea = $this->objDom->createElement('textarea');

        // If the number of columns is set, then use them
        if($this->rows){
            $textarea->setAttribute('rows',$this->rows);
        }
        // If the number of rows is set, then use them
        if($this->cols){
            $textarea->setAttribute('cols',$this->cols);
        }
        // If there is a name, then use it.
        if ($this->name) {
            $textarea->setAttribute('name',$this->name);
        }
        // If a css id is set, then add it as an attribute
        if ($this->cssId) {
            $textarea->setAttribute('id',$this->cssId);
        }
        // If a css class is set, then add it as an attribute
        if ($this->cssClass) {
            $textarea->setAttribute('class',$this->cssClass);
        }
        // If there is an onclick event specified, add it as an attribute.
        if ($this->onclick) {
            $textarea->setAttribute('onclick',$this->onclick);
        }
        if($this->disabled) {
            $textarea->setAttribute('disabled',$this->disabled);
        }
        if($this->readonly) {
            $textarea->setAttribute('disabled',$this->readonly);
        }

        $textarea = $this->objDom->appendChild($textarea);
        $ret = $this->objDom->saveHTML();

        return $ret;
    }

    /**
     *
     * A standard setter. The following params may be set here
     * $size - Set the size of the textarea element
     * $id - Set a css id to use in the textarea element
     * $class - A CSS class to use in the textarea element
     * $value - Set the value of the textarea element
     *
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
     *
     *
     * @param string $param The name of the parameter to set
     * @access public
     */
    public function getValue($param) {
        return $this->$param;
    }
 }

?>