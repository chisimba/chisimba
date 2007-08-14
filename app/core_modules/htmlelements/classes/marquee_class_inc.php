<?php
 /**
 * Marquee class for Chisimba
 * 
 * Marquee class acts as base class for the scrolling objects
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
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Elijah Omwenga and Otim Samuel
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */

/* -------------------- marquee class extends object ----------------*/
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
* Marquee class acts as base class
* for the scrolling objects
* extends controller class since it requires the getObject method
*
* @version   marquee_class_inc.php,v 1.0 2005/01/27 15:30hrs, Elijah Omwenga and Otim Samuel
* @copyright 2005
*            */


class marquee extends object implements ifhtml
{
	/**
	* variable declaration
	* these are the attributes to be used in the marquee class
	*/
	
	public $behavior;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $align;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $direction;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $height;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $scrollAmount;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $scrollDelay;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $onMouseOver;

    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $onMouseOut;
	
	// array variable to hold the elements of the marquee


    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $elements;
	
	//dbMarquee object class to use when adding content to the marquee


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $objDbMarquee;
	
	//user/administrator authentication


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $isAdmin;
	
	//link management


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $objHref;
	
	//icon object


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $objGetIcon;
	
	//language object


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $objLanguage;
	
	//marquee elements


    /**
     * Description for public
     * @var    string
     * @access public
     */
	public $elements;
	
	//number of marquee elements


    /**
     * Description for public
     * @var    integer
     * @access public 
     */
	public $numElements;
	
	//this class also requires the name of your module


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
	public $moduleName;
	
	/**
	* constructor to set default values
	*/
	
	public function init()
	{
		/**
		* establish the necessary routines for identifying administrators
		*/
        $this->objUser = $this->getObject('user', 'security');
        $this->isAdmin=$this->objUser->isAdmin();
		$this->objHref= $this->getObject('href','htmlelements');
		$this->objGetIcon = $this->getObject('geticon', 'htmlelements');	
		$this->objLanguage =  $this->getObject('language', 'language'); 
		/**
		* defaulted to only 1
		* for the firefox browser esp, this shall ensure
		* no scrolling
		*/
		$this->numElements=1;
	}	
	
	/**
	* SET METHODS
	*/

	public function setNumElements($numElements)
	{
		$this->numElements=$numElements;
	}
	
    /**
     * Method to set class property moduleName
     * 
     * @param  unknown $moduleName
     * @return void   
     * @access public 
     */
	public function setModuleName($moduleName)
	{
		$this->moduleName=$moduleName;
	}
	
    /**
     * Method to set class property elements
     * 
     * 
     * @param  unknown $elements 
     * @return void   
     * @access public 
     */
	public function setElements($elements)
	{
		$this->elements=$elements;
	}
	
    /**
     * Method to set class property behaviour
     * 
     * @param  unknown $behavior 
     * @return void   
     * @access public 
     */
	public function setBehavior($behavior)
	{
		$this->behavior=$behavior;
	}

    /**
     * Method to set class property align
     * 
     * @param  unknown $align 
     * @return void   
     * @access public 
     */
	public function setAlign($align)
	{
		$this->align=$align;
	}

    /**
     * Method to set class property direction
     * 
     * 
     * @param  unknown $direction
     * @return void   
     * @access public 
     */
	public function setDirection($direction)
	{
		$this->direction=$direction;
	}
	
    /**
     * Method to set class property height 
     * 
     * @param  unknown $height 
     * @access public 
     */
	public function setHeight($height)
	{
		$this->height=$height;
	}
	
    /**
     * Method to set class property scrollAmount
     * 
     * 
     * @param  unknown $scrollAmount 
     * @return void   
     * @access public 
     */
	public function setScrollAmount($scrollAmount)
	{
		$this->scrollAmount=$scrollAmount;
	}
	
    /**
     * Method to set class property scrollDelay
     * 
     * 
     * @param  unknown $scrollDelay 
     * @return void   
     * @access public 
     */
	public function setScrollDelay($scrollDelay)
	{
		$this->scrollDelay=$scrollDelay;
	}
	
    /**
     * Method to set class property onMouseOver
     * 
     * 
     * @param  unknown $onMouseOver 
     * @return void   
     * @access public 
     */
	public function setOnMouseOver($onMouseOver)
	{
		$this->onMouseOver=$onMouseOver;
	}

    /**
     * Method to set class property onMouseOut
     * 
     * 
     * @param  unknown $onMouseOut 
     * @return void   
     * @access public 
     */
	public function setOnMouseOut($onMouseOut)
	{
		$this->onMouseOut=$onMouseOut;
	}

	/**
	* GET METHODS
	*/

	public function getNumElements()
	{
		return $this->numElements;
	}
	
    /**
     * Method to return class property moduleName 
     * 
     * 
     * @return unknown Return 
     * @access public 
     */
	public function getModuleName()
	{
		return $this->moduleName;
	}
	
    /**
     * Method to return class property elements
     * 
     * 
     * @return string Return 
     * @access public
     */
	public function getElements()
	{
		return $this->elements;
	}
	
    /**
     * Method to return class property behaviour
     * 
     * 
     * @return string Return 
     * @access public
     */
	public function getBehavior()
	{
		return $this->behavior;
	}

    /**
     * Method to return class property align
     * 
     * 
     * @return string Return 
     * @access public
     */
	public function getAlign()
	{
		return $this->align;
	}

    /**
     * Method to return class property direction
     * 
     * 
     * @return string Return 
     * @access public
     */
	public function getDirection()
	{
		return $this->direction;
	}

    /**
     * Method to return class property height 
     * 
     * 
     * @return string Return 
     * @access public
     */
	public function getHeight()
	{
		return $this->height;
	}

    /**
     * Method to return class property ScrollAmount
     * 
     * 
     * @return string Return 
     * @access public
     */
	public function getScrollAMount()
	{
		return $this->scrollAmount;
	}

    /**
     * Method to return class property scrollDelay 
     * 
     * 
     * @return string Return 
     * @access public
     */
	public function getScrollDelay()
	{
		return $this->scrollDelay;
	}

    /**
     * Method to return class property onMouseOver
     * 
     * 
     * @return string Return 
     * @access public
     */
	public function getOnMouseOver()
	{
		return $this->onMouseOver;
	}

    /**
     * Method to return class property onMouseOut
     * 
     * 
     * @return string Return 
     * @access public
     */
	public function getOnMouseOut()
	{
		return $this->onMouseOut;
	}

	/**
	* show function to display the marquee elements
	*/

	public function show()
	{
		if($this->getNumElements()>1) {
			$str = "<marquee 
				behavior='".$this->behavior."' 
				align='".$this->align."' 
				direction='".$this->direction."' 
				height='".$this->height."' 
				scrollAmount='".$this->scrollAmount."' 
				scrollDelay='".$this->scrollDelay."' 
				onMouseOver='".$this->onMouseOver."' 
				onMouseOut='".$this->onMouseOut."'
				>";
		
			//get the content to be displayec within the marquee
			$str.=$this->elements;
			$str.="</marquee><br />";
		} else {
			$str="";
			$str.=$this->elements;
			$str.="<br />";
		}
	
		/**
		* in accordance with framework standards, the add
		* link should only be displayed within the corresponding
		* administrative module
		*
        if ($this->isAdmin) {
            $paramArray = array('action' => 'add');
            $str .= $this->objGetIcon->getAddIcon($this->uri($paramArray, $this->moduleName));
        }
		*/
		return $str;
	}
}
?>
