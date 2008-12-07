<?php

/**
 * Label class
 * 
 * Used to create labels for form elements
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
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
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
     * Constructor function
     * 
     * @param  string $labelValue .
     * @param  string $forId      
     * @return void   
     * @access public 
     */
    public function label($labelValue, $forId)
    {
        $this->labelValue=$labelValue;
        $this->forId=$forId;
    }
    
    /**
     * Standard display function - all htmlelements classes have one.
     * 
     * @return string Return 
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
     * Method to set labelValue property
     * 
     * @param  string $labelValue 
     * @return void   
     * @access public 
     */
    public function setLabel($labelValue){
        $this->labelValue =$labelValue;        
    }
    
    /**
     * Method to set forId property
     * 
     * 
     * @param  string $forId
     * @return void   
     * @access public 
     */
    public function setForId($forId){
        $this->forId=$forId;
    }
}
?>
