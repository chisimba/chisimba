<?php
/**
 *
 * JSON utils helper class
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
 * @package   utilities
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 AVOIR
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
 * Events utils helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott <pscott@uwc.ac.za>
 * @package utilities
 *
 */
class jsonutils extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
     * Convert any array to a php object
     *
     * @param  array $array the input array
     * @return object $obj An object of the input array
     * @access public
     */
    public function array2object($array) {
        if (is_array($array)) {
            $obj = new StdClass();
            foreach ($array as $key => $val){
                $obj->$key = $val;
            }
        }
        else { 
            $obj = $array; 
        }
       return $obj;
    }
    
    /**
     * Convert any php object to a php array
     *
     * @param  object $object the input object
     * @return array  $array  An array of the input object
     * @access public
     */
    public function object2array($object) {
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[strtolower($key)] = $value;
            }
        }
        else {
            $array = $object;
        }
        if(!isset($array)) {
            $array = array();
        }
        return $array;
    }
    
    /**
     * JSON encode object
     *
     * Take an object and return a JSON object with headers 
     *
     * @param  object $phpobj the input object
     * @return object $obj An object of the input object as JSON
     * @access public
     */
    public function jsonObject($object, $headers = TRUE) {
        if(is_object($object)) {
            if($headers == TRUE) {
                header("Content-Type: application/json");
            }
            return json_encode($object);
        }
        elseif(is_array($object)) {
            $object = $this->array2object($object);
            if($headers == TRUE) {
                header("Content-Type: application/json");
            }
            return json_encode($object);
        }
        else {
            if($headers == TRUE) {
                header("Content-Type: application/json");
            }
            return NULL;
        }    
    }
     
    /**
     * JSON encode something
     *
     * Take an anything and return a JSON object with headers This is a just more than a simple wrapper for json_encode
     *
     * @param  object|array|string $data the input
     * @return object $object An object of the input object as JSON
     * @access public
     */
    public function jsonEncodeHeader($data, $headers = TRUE) {
        if($headers == TRUE) {
            header("Content-Type: application/json");
        }
        return json_encode($data);
    }
    
    /**
     * JSON decode something
     *
     * Take an anything and return a JSON decoded bit
     *
     * @param  object|array|string $data the input
     * @return data
     * @access public
     */
    public function jsonDecode($data) {
        return json_decode($data);
    }
}
?>
