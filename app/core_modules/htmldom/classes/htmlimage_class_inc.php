<?php
/**
 * Image class for Chisimba
 *
 * This file contains the image class which is used to generate
 * HTML image elements for forms. It was modified after the original
 * HTMLelements image class by Nguni Phakela as part of the Chisimba
 * hackathon 2010 12 02. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('htmlimage', 'htmldom').The
 * other attributes that were in image in htmlelements have since been
 * deprecated and these can be set using css. These are namely:
 * align
 * border
 * hspace
 * vspace
 *
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
 * @package   htmldom
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @author    Nguni Phakela <nonkululeko.phakela@wits.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: htmlimage_class_inc.php 11055 2008-10-25 16:25:24Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @example   $str = $this->newObject("htmlimage", "htmldom");
              $str->setValue("alt", "hello");
              $str->setValue("src", "images/hello");
              $str->setValue("onclick", "alert('clicked me');");
              $myCssId = $str->getValue("cssId");

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

class htmlimage extends object {
    /**
    * Specifies the width of an image
    */
    private $width;

    /**
     * Specifies the height of an image
     * @var    string
     * @access public
     */
    private $height;

    /*
     * Specifies the source of the image
     *
     */
    private $src;

    /*
     * Specifies an alternate text for an image
     *
     */
    private $alt;

    /**
      * Holds the CSS id for the image, and is set using $this->setValue().
      * Value is returned using $this->getValue();
      *
      * @var string $name
      * @access private
      *
      */
    private $cssId;

    /**
      *
      * Holds the CSS class for the image, and is set using $this->setValue().
      * @var string $cssClass:
      * @access private
      */
    private $cssClass;

    // Javascript tags
    /**
     * Description for private
     * @var    unknown
     * @access private
     */
    private $mouseover;

    /**
     * Description for private
     * @var    unknown
     * @access private
     */
    private $mouseout;

    /**
     * Description for private
     * @var    unknown
     * @access private
     */
    private $onclick;/**
     *
     * Object to hold the dom document
     *
     * @var string Object $objDom
     * @access private
     */
    private $objDom;


    /**
     *
     * Intialiser for the htmldom object
     *
     * @access public
     * @return void
     *
     */
    public function init() {
        $this->objDom = new DOMDocument;
    }

    
   
  
 


















    /**
     * Method to show the image
     *
     * @return The image complete as a string
     */
    public function show() {
        $image = $this->objDom->createElement("img");

        if($this->cssId) {
            $image->setAttribute('id', $this->cssId);
        }
        if ($this->cssClass) {
            $image->setAttribute('class', $this->cssClass);
        }
        if ($this->width) {
            $image->setAttribute('align', $this->width);
        }
        if ($this->height) {
            $image->setAttribute('align', $this->height);
        }
        if ($this->src) {
            $image->setAttribute('align', $this->src);
        }
        if ($this->alt) {
            $image->setAttribute('align', $this->alt);
        }
        if ($this->mouseout) {
            $image->setAttribute('onmouseout', $this->mouseout);
        }
        if ($this->mouseover) {
            $image->setAttribute('onmouseover', $this->mouseover);
        }
        if ($this->onclick) {
            $image->setAttribute('onclick', $this->onclick);
        }
        
        $this->objDom->appendChild($image);
        $ret = $this->objDom->saveHTML();

        return $ret;
    }


    /**
     *
     * A standard setter. The following params may be set here
     * $width - Specifies the width of the image
     * $height- Specifies the height of the image
     * $src - Specifies the source of the image
     *
     * @param string $param The name of the parameter to set
     * @param string $value The value to set the parameter to
     * @access public
     */
    public function setValue($param, $value) {
        $this->$param = $value;
    }

    /**
     * A standard getter. The following params may be retrieved here
     * A standard setter. The following params may be set here
     * $width - Specifies the width of the image
     * $height- Specifies the height of the image
     * $src - Specifies the source of the image
     *
     * @param string $param The name of the parameter to set
     * @access public
     */
    public function getValue($param) {
        return $this->$param;
    }
}

?>
