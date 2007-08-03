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
 * Input class acts as an base class
 * for some commom objects 
 * eg. buttons , text ,radio buttons ,check boxes
 *
 * @version   $Id$
 * @copyright 2003
 *            */
 class input implements ifhtml
 {

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
 	public $size;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $value;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $name;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $width;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $css;
	
	/**
     * Initialization method to set default values
     *
	function input ($name,$value,$size,$width){
		$this->name=$name;
		$this->value=$value;
		if(!$size){
			$this->size=10;
		}else{
			$this->size=$size;
		}
		if(!$width){
			$this->width=10;
		}else{
			$this->width=$width;
		}
	}
	*/
	
/**************************************************************
*         GET METHODS                                         *
* *************************************************************/
	public function getName(){
		return $this->$name;
	}
 	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return unknown Return description (if any) ...
     * @access public 
     */
	public function getSize(){
		return $this->$size;
	}
 	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return unknown Return description (if any) ...
     * @access public 
     */
	public function getValue(){
		return $this->$value;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return unknown Return description (if any) ...
     * @access public 
     */
	public function getCSS(){
		return $this->css;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return unknown Return description (if any) ...
     * @access public 
     */
	public function getvType(){
		return $this->vtype;
	}

/**************************************************************
*         SET METHODS                                         *
* *************************************************************/

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $name Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setName($name){
		$this->name=$name;
	} 

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $size Parameter description (if any) ...
     * @return void   
     * @access public 
     */
 	public function setSize($size){
		$this->size;
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
	public function setValue($value){
		$this->value=$value;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $css Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setCss($css){
		$this->class=$css;
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $vtype Parameter description (if any) ...
     * @return void   
     * @access public 
     */
	public function setvType($vtype){
		$this->vtype=$vtype;
	}
 }
?>