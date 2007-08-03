<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
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