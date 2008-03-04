<?php

/**
 * Class textinput extends abhtmlbase implements ifhtml
 *
 * This class defines the text input objects used by forms in the interface.
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
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
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
* Text Input class controls buttons
*
* @author    Wesley Nitsckie
* @author    Megan Watson
* @author    Tohir Solomons
* @version   $Id$
* @copyright 2003
*/
class textinput extends abhtmlbase implements ifhtml
{

    /**
    * @var integer $size: The width of the text input
    */
    public $size;



    /**
    * Initialization method to set default values
    *
    * @param string $name optional :sets the name of the text input
    */
    public function textinput($name = null, $value = null, $type=null, $size=null)
    {
        $this->name = $name;
        $this->value = htmlentities($value);
        $this->cssClass = 'text';
        if (!is_null($type)) {
            $this->fldType=$type;
        }
		else {
	        $this->fldType='text';
		}
   		if (!is_null($size)) {
			$this->size = $size;
		}
     	$this->cssId = 'input_'.$name;
    }
    /**
    * Set the name.
    * @param string Name
    */
    public function setName($name)
    {
        $this->name = $name;
    }
    /**
    * Set the type.
    * @param string Type
    */
    public function setType($type)
    {
        $this->fldType=$type;
    }
    /**
    * Method to set the css class
    *
    * @param      string $css
    * @deprecated <----------------------------------------------------------
    */
    public function setCss($css)
    {
        $this->cssClass = $css;
    }
    /*
	* Method to set the value of the text box
	* @param string $value
    * @deprecated <----------------------------------------------------------
	*/

	/*
	* Method to set the cssId class
	* @param string $cssId
	*/

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $cssId Parameter description (if any) ...
     * @return void
     * @access public
     */
    public function setId($cssId)
    {
        $this->cssId = $cssId;
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $value Parameter description (if any) ...
     * @return void
     * @access public
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
    * Method to return the text input for display on the form
    * @return string $str: the text element for display
    */
    public function show()
    {
        $str = '<input type="'.$this->fldType.'" value="' . $this->value . '"';
        $str .= ' name="' . $this->name . '"';

        // only add elements if they have a value
        if ($this->cssClass) {
            $str .= ' class="' . $this->cssClass . '"';
        }

        if ($this->size) {
            $str .= ' size="' . $this->size . '"';
        }
        if ($this->cssId) {
            $str .= ' id="' . $this->cssId . '"';
        }
        if ($this->extra) {
            $str .= ' '.$this->extra;
        }
        $str .= ' />';
        return $str;
    }
}

?>
