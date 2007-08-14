<?php

/**
 * Input class for Chisimba
 * 
 * Input class acts as an base class for some commom objects
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
