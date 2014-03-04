<?php
/**
 *
 * EXIF helper class
 *
 * PHP version 5.1.0+
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
 * @package   metadata
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * EXIF helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package metadata
 *
 */
class exifmeta extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;
    
    /**
     * @var array $types An array of the most common EXIF types. This can be expanded/overridden as is needed. Array keys correspond to the EXIF return types
     *
     * @access public
     */
    public $types = array(
        1 => "GIF",
        2 => "JPEG",
        3 => "PNG",
        4 => "SWF",
        5 => "PSD",
        6 => "BMP",
        7 => "TIFF",
        8 => "TIFF"
    );

    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage   = $this->getObject('language', 'language');
        $this->objConfig     = $this->getObject('altconfig', 'config');
        $this->objSysConfig  = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser       = $this->getObject('user', 'security');
        if(!function_exists("exif_imagetype")) {
            die("You need to install the PHP EXIF extension to use these functions");
        }
    }
    
    /**
     * Method to determine image type
     *
     * Uses built in ext EXIF function to simply return image type. FALSE on failure
     *
     * @access public
     * @param string $image
     * @return type or false on failure
     */
    public function getImageType($image) {
        $imagetype = exif_imagetype($image);
        if (array_key_exists($imagetype, $this->types)) {
            return $this->types[$imagetype];
        } else {
            return FALSE;
        }
    }
    
    /**
     * Function to read ALL EXIF headers for an image
     *
     * There are seven sections (arrays) of data types:
     * FILE:  Contains the file’s name, size, timestamp, and what other sections were found (as listed below)
     * COMPUTED: Contains the actual attributes of the image
     * ANY_TAG:  Any information that is tagged
     * IFD0:  Mostly contains information about the camera itself, the software used to edit the image, when it was last modified, etc
     * THUMBNAIL: Information about the embedded thumbnail for the image
     * COMMENT: Comment headers for JPEG images
     * EXIF: Contains more information supplementary to what is in IFD0, mostly related to the camera (includes focal length, zoom ratio, etc)
     *
     * @access public
     * @param string image
     * @return array of EXIF data
     */
    public function readHeaders($image, $formattedarray = TRUE) {
        $exif = @exif_read_data($image, 0, true);
        if($formattedarray == FALSE) {
            return $exif;
        }
        foreach ($exif as $key => $section) {
            foreach ($section as $name => $val) {
                $data[] = array($key.$name, $val);
            }
        }
        
        return $data;
    }
    
    /**
     * Method to read EXIF data of a Specific header key
     *
     * @access public
     * @param string $image
     * @param string $headerkey
     * @return array
     * @example headerkey could be: "IFD0" to get camera info only
     */
    public function readHeadersByKey($image, $headerkey) {
        $exif = exif_read_data($image, 0, true);
        foreach ($exif as $key => $section) {
            foreach ($section as $name => $val) {
                if($key == $headerkey){
                    $data[] = array($key.$name, $val);
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Method to return EXIF based thumbnail of the image
     * 
     * NOTE: not all images have an embedded thumbnail, please check headers before using this function!
     *
     * @param string $image
     * @param integer $width
     * @param integer $height
     * @access public
     */
    public function getExifThumb($image, $width, $height) {
        $thumbnail = @exif_thumbnail($image, $width, $height, $this->getImageType($image));
        return "<img  width='$width' height='$height' src='data:image;base64,".base64_encode($thumbnail)."'>";
    }
}
?>
