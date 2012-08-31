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
 * @version   $Id$
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
* @category  Chisimba
* @package   htmlelements
* @author    Paul Scott <pscott@uwc.ac.za>
* @version   $Id$
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
* @link      http://avoir.uwc.ac.za
*/

class marquee extends object implements ifhtml
{
     /**
     * @var string $behaviour: hold the behaviour
     *
     */
        public $behavior;

    /**
     * 
     * @var    string $align: Holds the alignment
     * @access public
     */
    public $align;

    /**
     * 
     * @var    string $direction: Hold the direction
     * @access public
     */
    public $direction;

    /**
     * 
     * @var    string $height: Holds the height
     * @access public
     */
    public $height;

    /**
     * 
     * @var    string $scrollAmount: Holds the scroll amount
     * @access public
     */
    public $scrollAmount;

    /**
     * 
     * @var    string $scrollDelay: Holds the scroll delay
     * @access public
     */
    public $scrollDelay;

    /**
     * 
     * @var    string $onMouseOver: Holds where mouse is over
     * @access public
     */
    public $onMouseOver;

    /**
     * 
     * @var    string $onMouseOut: Holds where mouse is out
     * @access public
     */
    public $onMouseOut;

    /**
     * array variable to hold the elements of the marquee
     * @var    string $elements
     * @access public
     */
    public $elements;

    /**
     * dbMarquee object class to use when adding content to the marquee
     * @var  object $objDbMarquee
     * @access public 
     */
    public $objDbMarquee;

    /**
     * user/administrator authentication
     * @var    boolean $isAdmin
     * @access public 
     */
    public $isAdmin;

    /**
     * link management
     * @var    object $objHref
     * @access public 
     */
    public $objHref;

    /**
     * icon object
     * @var object $objGetIcon
     * @access public 
     */
    public $objGetIcon;

    /**
     * language object
     * @var object $objLanguage
     *
     * @access public 
     */
    public $objLanguage;


    /**
     * number of marquee elements
     * @var    integer $numElements
     * @access public 
     */
    public $numElements;

    /**
     * this class also requires the name of your module
     * @var    unknown $moduleName
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
<<<<<<< marquee_class_inc.php
     * Set the module name
=======
     * Method to set class property moduleName
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @param  string $moduleName string: The name of the module
=======
     * @param  unknown $moduleName
     * @return void   
>>>>>>> 1.7
     * @access public 
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName=$moduleName;
    }
    
    /**
<<<<<<< marquee_class_inc.php
     * Set the elements
=======
     * Method to set class property elements
     * 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @param  string $elements string: The elements for the array 
=======
     * @param  unknown $elements 
     * @return void   
>>>>>>> 1.7
     * @access public 
     */
    public function setElements($elements)
    {
        $this->elements=$elements;
    }
    
    /**
<<<<<<< marquee_class_inc.php
     * Set the behaviour
=======
     * Method to set class property behaviour
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @param  string $behavior string: Holds the behaviour
=======
     * @param  unknown $behavior 
     * @return void   
>>>>>>> 1.7
     * @access public 
     */
    public function setBehavior($behavior)
    {
        $this->behavior=$behavior;
    }

    /**
<<<<<<< marquee_class_inc.php
     * Set the alignment
=======
     * Method to set class property align
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @param  integer $align integer: alignment
=======
     * @param  unknown $align 
     * @return void   
>>>>>>> 1.7
     * @access public 
     */
    public function setAlign($align)
    {
        $this->align=$align;
    }

    /**
<<<<<<< marquee_class_inc.php
     * Set the direction
=======
     * Method to set class property direction
     * 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @param  string $direction string: The direction
=======
     * @param  unknown $direction
     * @return void   
>>>>>>> 1.7
     * @access public 
     */
    public function setDirection($direction)
    {
        $this->direction=$direction;
    }
    
    /**
<<<<<<< marquee_class_inc.php
     * Set the height
=======
     * Method to set class property height 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @param  integer $height integer: The hieght
=======
     * @param  unknown $height 
>>>>>>> 1.7
     * @access public 
     */
    public function setHeight($height)
    {
        $this->height=$height;
    }
    
    /**
<<<<<<< marquee_class_inc.php
     * Set the scoll amount
=======
     * Method to set class property scrollAmount
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @param  integer $scrollAmount integer: The scoll amount
=======
     * 
     * @param  unknown $scrollAmount 
     * @return void   
>>>>>>> 1.7
     * @access public 
     */
    public function setScrollAmount($scrollAmount)
    {
        $this->scrollAmount=$scrollAmount;
    }
    
    /**
<<<<<<< marquee_class_inc.php
     * Set the scroll delay
=======
     * Method to set class property scrollDelay
     * 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @param  integer $scrollDelay integer: scroll delay amount
=======
     * @param  unknown $scrollDelay 
     * @return void   
>>>>>>> 1.7
     * @access public 
     */
    public function setScrollDelay($scrollDelay)
    {
        $this->scrollDelay=$scrollDelay;
    }
    
    /**
<<<<<<< marquee_class_inc.php
     * Set the on mouse over
=======
     * Method to set class property onMouseOver
     * 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @param  integer $onMouseOver integer: The on mouse over
=======
     * @param  unknown $onMouseOver 
     * @return void   
>>>>>>> 1.7
     * @access public 
     */
    public function setOnMouseOver($onMouseOver)
    {
        $this->onMouseOver=$onMouseOver;
    }

    /**
<<<<<<< marquee_class_inc.php
     * Set the on mouse out
=======
     * Method to set class property onMouseOut
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @param  integer $onMouseOut integer: The on mouse out
=======
     * 
     * @param  unknown $onMouseOut 
     * @return void   
>>>>>>> 1.7
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
<<<<<<< marquee_class_inc.php
     * Get the module name
=======
     * Method to return class property moduleName 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @return modulename
=======
     * 
     * @return unknown Return 
>>>>>>> 1.7
     * @access public 
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }
    
    /**
<<<<<<< marquee_class_inc.php
     * Get the elements
=======
     * Method to return class property elements
     * 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @return elements
=======
     * @return string Return 
>>>>>>> 1.7
     * @access public
     */
    public function getElements()
    {
        return $this->elements;
    }
    
    /**
<<<<<<< marquee_class_inc.php
     * Get the behaviour
=======
     * Method to return class property behaviour
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @return behaviour
=======
     * 
     * @return string Return 
>>>>>>> 1.7
     * @access public
     */
    public function getBehavior()
    {
        return $this->behavior;
    }

    /**
<<<<<<< marquee_class_inc.php
     * Get the Alignment
=======
     * Method to return class property align
     * 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @return align
=======
     * @return string Return 
>>>>>>> 1.7
     * @access public
     */
    public function getAlign()
    {
        return $this->align;
    }

    /**
<<<<<<< marquee_class_inc.php
     * Get the direction
=======
     * Method to return class property direction
     * 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @return direction
=======
     * @return string Return 
>>>>>>> 1.7
     * @access public
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
<<<<<<< marquee_class_inc.php
     * Get the height
=======
     * Method to return class property height 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @return height
=======
     * 
     * @return string Return 
>>>>>>> 1.7
     * @access public
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
<<<<<<< marquee_class_inc.php
     * Get the scroll amount
=======
     * Method to return class property ScrollAmount
     * 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @return scrollAmount
=======
     * @return string Return 
>>>>>>> 1.7
     * @access public
     */
    public function getScrollAMount()
    {
        return $this->scrollAmount;
    }

    /**
<<<<<<< marquee_class_inc.php
     * Get the scroll delay
=======
     * Method to return class property scrollDelay 
     * 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @return scrollDelay
=======
     * @return string Return 
>>>>>>> 1.7
     * @access public
     */
    public function getScrollDelay()
    {
        return $this->scrollDelay;
    }

    /**
<<<<<<< marquee_class_inc.php
     * Get on mouse over
=======
     * Method to return class property onMouseOver
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @return onMouseEver
=======
     * 
     * @return string Return 
>>>>>>> 1.7
     * @access public
     */
    public function getOnMouseOver()
    {
        return $this->onMouseOver;
    }

    /**
<<<<<<< marquee_class_inc.php
     * get on mouse out
=======
     * Method to return class property onMouseOut
     * 
>>>>>>> 1.7
     * 
<<<<<<< marquee_class_inc.php
     * @return onMouseOut
=======
     * @return string Return 
>>>>>>> 1.7
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
