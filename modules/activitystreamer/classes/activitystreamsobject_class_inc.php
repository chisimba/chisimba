<?php

/**
 * Methods to track activities in the Chisimba framework
 * into the Chisimba framework
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
 * @package   activitystreamer
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check


/**
 * Methods to track activities in the Chisimba framework
 * into the Chisimba framework
 * 
 * @author Paul Scott <pscott@uwc.ac.za>
 */

class activitystreamsobject extends object
{
    /**
     * Property to hold activity stream object properties
     * @access public
     */
    public $properties = array();

    /**
     * Method to get a property
     *
     * @access public
     * @param string property_name $property_name
     */
    public function getProperty($property_name) {
        if (isset($this->properties[$property_name])) return $this->properties[$property_name];
    }
	
	/**
     * Method to set a property
     *
     * @access public
     * @param string property_name $property_name
     * @param string property_value $property_value
     */
    public function setProperty($property_name, $property_value) {
        $this->properties[$property_name] = $property_value;
    }
	
	/**
     * Constructor
     *
     */
    public function init() {
        $this->published = time();
    }

    /**
     * Method to add an object type
     *
     * @access public
     * @param object property_name $object_type
     */
    public function addObjectType($object_type) {
        if (!isset($this->properties['object-type'])) $this->properties['object-type'] = array();
        $this->properties['object-type'][] = (string) $object_type;
    }
	
	/**
     * Method to set the stream to a string
     *
     * @access public
     * @param void
     */	
    public function __toString() {
        $string = '';
        $string .= "\n<activity:object>";
        foreach($this->properties as $property => $value) {
            if (!is_array($value)) $value = array($value);
                switch($property) {
                    case 'title':
                        $attr = 'type="html"';
                    break;

                    case 'link':
                        $attr = 'rel="alternate" type="text/html" href="'.$value[0].'"';
                        $value = array(''); 
                    break;
					
                    default:	$attr = '';
                    break;
                }
                if (sizeof($value))
                foreach($value as $val) {
                    if (empty($val))
                        $string .=  "\n\t<{$property} {$attr} />";
                    else if ($property == 'content' || $property == 'title')
                        $string .= "\n\t<{$property} {$attr}><![CDATA[{$val}]]><{$property}>";
                    else
                        $string .=  "\n\t<{$property} {$attr}>{$val}<{$property}>";
                }
            }    
            $string .=  "\n</activity:object>";
			
            return $string;
        }
}
?>
