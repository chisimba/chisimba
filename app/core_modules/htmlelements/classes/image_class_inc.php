<?php
/**
 * Image class for Chisimba
 * 
 * HTML control class to create image tags
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
 * @copyright 2007 Wesley Nitsckie
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
* HTML control class to create image tags
*
* @author Derek Keats
*         
* @todo   -c Implement .mouseover effects --> can someone take this over?
*         
*/
class image implements ifhtml
{
    /**
    * Define all vars
    */
    public $width;

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
    public $alt;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $imageTag;

    /**
     * Description for public
     * @var    mixed 
     * @access public
     */
    public $border;
    // Javascript tags


    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
    public $mouseover;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
    public $mouseout;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
    public $onclick;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
    public $over_image_src;

    /**
    * Initialization method to set default values
    */
    public function image()
    {
        $this->alt = null;
        $this->imageTag = null;
        $this->border = 0;
    }

    /**
    * Method to return an invisible IFRAME
    */
    public function show()
    {
        $this->_buildImage();
        return $this->imageTag;
    }

    /*-------------- PRIVATE METHODS BELOW LINE ------------------*/

    /**
    * Method to build the Iframe from the parameters
    */
    private function _buildImage() {
        $this->imageTag = "<img src=\"" . $this->src . "\"";
        if ($this->width && $this->height) {
            $this->imageTag .= " width=\"" . $this->width . "\"";
            $this->imageTag .= " height=\"" . $this->height . "\"";
        } /*else {
            $this->imageTag .= " ".$this->_getImageSize();
        } */
        if ($this->height) {

        }
        if ($this->align) {
            $this->imageTag .= " align=\"" . $this->align . "\"";
        }
        if ($this->alt) {
            $this->imageTag .= " alt=\"" . $this->alt . "\"";
        }
        if ($this->border) {
            $this->imageTag .= " border=\"" . $this->border . "\"";
        }
        $this->imageTag .= " />";

    }

    /**
    * Get the size of an image
    * Function produces a warning error if the path to the src is an http://... path.
    * This bug has been fixed in PHP5.
    * @return string the width and height tag for the image
    */
    private function _getImageSize()
    {
        $image_size = getimagesize ($this->src);
        return $image_size['3']; //the formatted witdth and height tag
    }
}
?>
