<?php
/**
 *
 * IPTC helper class
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
 * IPTC helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package metadata
 *
 */
class iptcmeta extends object {

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
        1 => "JPEG",
        2 => "PSD",
        3 => "TIFF",
        4 => "TIFF"
    );
    
    /**
    * @var string
    * The name of the image file that contains the IPTC fields to extract and
    * modify.
    *
    * @access private
    */
    private $filename = null;

    /**
    * @var array
    * The IPTC fields that were extracted from the image or updated by this
    * class.
    * @see getAllTags(), getTag(), setTag()
    * @access private
    */
    private $IPTC = array();

    /**
    * @var boolean
    * The state of the getimagesize() function. If the parsing was successful,
    * this value will be set to true if the APP header data could be obtained.
    * @see isValid()
    * @access private
    */
    private $IPTCParse = false;


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
        if(!function_exists("iptcparse")) {
            die("You need to install the PHP GD extension to use these functions");
        }
    }
    
    public function setImage($filename) {
        $this->filename = $filename;
        if (is_file($this->filename)) {
           if (@getimagesize($this->filename, $aAPP) && !empty($aAPP)) {
               $this->IPTC = @iptcparse($aAPP['APP13']);
               $this->IPTCParse = true;
           }
        }
    }
    
   /**
    * Returns the status of IPTC parsing during instantiation
    *
    * You'll normally want to call this method before trying to change or
    * get IPTC fields.
    *
    * @return boolean
    * Returns true if the getimagesize() function successfully extracted APP
    * information from the supplied file
    *
    * @access public
    */
    public function isValid()
    {
        return $this->IPTCParse;
    }
    
   /**
    * Set IPTC fields to a specific value or values
    *
    * @param mixed
    * The field (by number or string) of the IPTC data you wish to update
    *
    * @param mixed
    * If the value supplied is scalar, then the block assigned will be set to
    * the given value. If the value supplied is an array, then the entire tag
    * will be given the value of the array.
    *
    * @param integer
    * The block to update. Most tags only use the 0th block, but certain tags,
    * like the "keywords" tag, use a list of values. If set to a negative
    * value, the entire tag block will be replaced by the value of the second
    * parameter.
    *
    * @access public
    */
    public function setTag( $xTag, $xValue, $nBlock = 0 )
    {
        $tagName = $this->_lookupTag($xTag);
        if (($nBlock < 0) || is_array($xValue)) {
            $this->IPTC[$tagName] = $xValue;
        } else {
            $this->IPTC[$tagName][$nBlock] = $xValue;
        }
    }
    
   /**
    * Get a specific tag/block from the IPTC fields
    *
    * @return mixed
    * If the requested tag exists, a scalar value will be returned. If the block
    * is negative, the entire
    *
    * @param mixed
    * The tag name (by number or string) to access. For a list of possible string
    * values look at the _lookupTag() method.
    *
    * @param integer
    * The block to reference. Most fields only have one block (the 0th block),
    * but others, like the "keywords" block, are an array. If you want
    * to get the whole array, set this to a negative number like -1.
    *
    * @see _lookupTag()
    * @access public
    */
    public function getTag( $xTag, $nBlock = 0 )
    {
        $tagName = $this->_lookupTag($xTag);
        if (isset($this->IPTC[$tagName]) && is_array($this->IPTC[$tagName])) {
            if ($nBlock < 0) {
                return $this->IPTC[$tagName];
            } else if (isset($this->IPTC[$tagName][$nBlock])) {
                return $this->IPTC[$tagName][$nBlock];
            }
        }
        return null;
    }
    
   /**
    * Get a copy of all IPTC tags extracted from the image
    *
    * @return array
    * An array of IPTC fields as it extracted by the iptcparse() function
    *
    * @access public
    */
    function getAllTags()
    {
        return $this->IPTC;
    }
    
   /**
    * Save the IPTC block to an image file
    *
    * @return boolean
    *
    * @param string
    * If supplied, the altered IPTC block and image data will be saved to another
    * file instead of the same file.
    *
    * @access public
    */
    public function save( $outputFile = null )
    {
        if (empty($outputFile)) {
           $outputFile = $this->filename;
        }
        $IPTCBlock = $this->_getIPTCBlock();
        $imageData = @iptcembed($IPTCBlock, $this->filename, 0);
        $hImageFile = @fopen($outputFile, 'wb');
        if (is_resource($hImageFile)) {
           flock($hImageFile, LOCK_EX);
           fwrite($hImageFile, $imageData);
           flock($hImageFile, LOCK_UN);
           return fclose($hImageFile);
        }
        return false;
    }
    
   /**
    * Embed IPTC data block and output to standard output
    *
    * @access public
    */
    public function output()
    {
        $IPTCBlock = $this->_getIPTCBlock();
        @iptcembed($IPTCBlock, $this->filename, 2);
    }
    
    /**
    * Return the numeric code of an IPTC field name
    *
    * @return integer
    * Returns a numeric code corresponding to the name of the IPTC field that
    * was supplied.
    *
    * @param string
    * A field name representing the type of tag to return
    *
    * @access private
    */
    private function _lookupTag( $tag )
    {
        $nTag = -1;
        $tag = strtolower(str_replace(' ','_',$tag));

        switch($tag) {

          case 'object_name':
             $nTag = 5;
             break;

          case 'edit_status':
             $nTag = 7;
             break;

          case 'priority':
             $nTag = 10;
             break;

          case 'category':
             $nTag = 15;
             break;

          case 'supplementary_category':
             $nTag = 20;
             break;

          case 'fixture_identifier':
             $nTag = 22;
             break;

          case 'keywords':
             $nTag = 25;
             break;

          case 'release_date':
             $nTag = 30;
             break;

          case 'release_time':
             $nTag = 35;
             break;

          case 'special_instructions':
             $nTag = 40;
             break;

          case 'reference_service':
             $nTag = 45;
             break;

          case 'reference_date':
             $nTag = 47;
             break;

          case 'reference_number':
             $nTag = 50;
             break;

          case 'created_date':
             $nTag = 55;
             break;

          case 'originating_program':
             $nTag = 64;
             break;

          case 'program_version':
             $nTag = 70;
             break;

          case 'object_cycle':
             $nTag = 75;
             break;

          case 'byline':
             $nTag = 80;
             break;

          case 'byline_title':
             $nTag = 85;
             break;

          case 'city':
             $nTag = 90;
             break;

          case 'province_state':
             $nTag = 95;
             break;

          case 'country_code':
             $nTag = 100;
             break;

          case 'country':
             $nTag = 101;
             break;

          case 'original_transmission_reference':
             $nTag = 103;
             break;

          case 'headline':
             $nTag = 105;
             break;

          case 'credit':
             $nTag = 110;
             break;

          case 'source':
             $nTag = 115;
             break;

          case 'copyright_string':
             $nTag = 116;
             break;

          case 'caption':
             $nTag = 120;
             break;

          case 'local_caption':
             $nTag = 121;
             break;

        }

        if ($nTag > 0) {

           return sprintf('2#%03d', $nTag);

        }

        return 0;
    }
    
   /**
    * Generate an IPTC block from the current tags
    *
    * @return string
    * Returns a binary string that contains the new IPTC block that can be used
    * in the iptcembed() function call
    *
    * @access private
    */
    private function &_getIPTCBlock()
    {
        $IPTCBlock = null;
        foreach($this->IPTC as $sTagID => $aTag) {
            $sTag = str_replace('2#', null, $sTagID);
            for($ci = 0; $ci < sizeof($aTag); $ci++) {
                $nLen = strlen($aTag[$ci]);
                // The below code is based on code contributed by Thies C. Arntzen
                // on the PHP website at the URL: http://www.php.net/iptcembed
                $IPTCBlock .= pack('C*', 0x1C, 2, $sTag);
                if ($nLen < 32768) {
                    $IPTCBlock .= pack('C*', $nLen >> 8, $nLen & 0xFF);
                } else {
                    $IPTCBlock .= pack('C*', 0x80, 0x04);
                    $IPTCBlock .= pack('C', $nLen >> 24 & 0xFF);
                    $IPTCBlock .= pack('C', $nLen >> 16 & 0xFF);
                    $IPTCBlock .= pack('C', $nLen >> 8 & 0xFF);
                    $IPTCBlock .= pack('C', $nLen & 0xFF);
                }
                $IPTCBlock .= $aTag[$ci];
            }
        }
        return $IPTCBlock;
    }
}
?>
