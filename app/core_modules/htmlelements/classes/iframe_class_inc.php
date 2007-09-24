<?php

/**
 * Iframe class for Chisimba htmlelements
 *
 * HTML control class to create and IFRAME(<IFRAME>) tag
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
 * HTML control class to create and IFRAME(<IFRAME>) tag
*/
class iframe implements ifhtml
{
    /**
    * Define all vars, these are obvious so not individually labelled
    */
    public $width;

    /**
     * Description for public
     * @var    mixed
     * @access public
     */
    public $height;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $src;

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
    public $frameborder; //must be 0 or 1


    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $marginheight;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $marginwidth;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $name;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $id;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $scrolling;

    /**
     * Description for public
     * @var    unknown
     * @access public
     */
    public $theFrame;

    /**
     * Any additional extra paramaters
     * @var    string $extra
     * @access public
     */
    public $extra;


    /**
     * Initialization method to set default values
     */
    public function iframe()
    {
        $this->width="800";
        $this->height="600";
    }

    /**
    * Method to return an invisible IFRAME
    */
    public function invisibleIFrame($src)
    {
        $this->width=0;
        $this->height=0;
        $this->src="http://".$src;
        return $this->_buildIframe();
    }

    /**
    * Show method
    */
    public function show()
    {
        return $this->_buildIframe();
    }


    /*-------------- PRIVATE METHODS BELOW LINE ------------------*/

    /**
    * Method to build the Iframe from the parameters
    */
    private function _buildIframe()
    {
        $ret="<iframe width=\"".$this->width."\" height=\"".$this->height."\" ";
        $ret .= "src=\"".$this->src."\"";
        if ($this->align) {
            $ret .= " align=\"".$this->align."\" ";
        }
        if (isset($this->frameborder)) {
            $ret .= " frameborder=\"".$this->frameborder."\" ";
        }
        if ($this->align) {
            $ret .= " align=\"".$this->align."\" ";
        }
        if ($this->marginheight) {
            $ret .= " marginheight=\"".$this->marginheight."\" ";
        }
        if ($this->marginwidth) {
            $ret .= " marginwidth=\"".$this->marginwidth."\" ";
        }
        if ($this->name) {
            $ret .= " name=\"".$this->name."\" ";
        }
        if ($this->id) {
            $ret .= " id=\"".$this->id."\" ";
        }
        if ($this->scrolling) {
            $ret .= " scrolling=\"".$this->scrolling."\" ";
        }

        if ($this->extra) {
            $ret .= ' '.$this->extra.' ';
        }

        $ret .= ">Iframe support required</iframe>";
        return $ret;
    }
}
?>
