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
// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
* 
* Used to create labels for form elements
* 
* @category  HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @author    Tohir Solomons
*            
*/
class label implements ifhtml
{

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
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $labelValue Parameter description (if any) ...
     * @param  unknown $forId      Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function label($labelValue, $forId)
	{
		$this->labelValue=$labelValue;
		$this->forId=$forId;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return string Return description (if any) ...
     * @access public
     */
	public function show()
	{
		$str='<label';
		
		if ($this->forId != '') {
			$str.= ' for="'.$this->forId.'"';
		}
		
		$str.='>';
		$str.=$this->labelValue;
		$str.='</label>';
		return $str;
	}

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $labelValue Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setLabel($labelValue){
		$this->labelValue =$labelValue;		
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $forId Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setForId($forId){
		$this->forId=$forId;
	}
}
?>