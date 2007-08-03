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
* Hidden Forum Input class
* 
* @author    Tohir Solomons
* @copyright 2005
*/
class hiddeninput extends abhtmlbase implements ifhtml
{

    /**
    * @var string $name
    */
    public $name;
    
    /**
    * @var string $value
    */
    public $value;
    
    /**
    * @var string $extra
    */
    public $extra;
   
    /**
    * Initialization method
    * 
    * @param string $name  optional :sets the name of the hidden form input
    * @param string $value optional :sets the value of the hidden form input
    */
    public function hiddeninput($name = null, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    } 
    
    /**
    * Method to set the name of a hidden form input
    * 
    * @param string $name Sets the name of the hidden form input
    */
    public function setName($name)
    {
        $this->value = $name;
    } 
    
    /**
    * Method to return the hidden form input for display on the form
    * @return string $str: the hidden form input element for display
    */
    public function show()
    {
        $str = '<input type="hidden" value="' . $this->value . '"';
        $str .= ' name="' . $this->name . '"';
        
        if ($this->extra) {
            $str .= ' '.$this->extra;
        }
        
        $str .= ' />';
        return $str;
    } 
} 

?>