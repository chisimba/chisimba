<?php
/**
 *
 * Events utils helper class
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
 * @package   events
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
 * @author Paul Scott
 * @package events
 *
 */
class eventsutils extends object {

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
     * @var string $objWashout String object property for holding the washout object
     *
     * @access public
     */
    public $objWashout;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;

    /**
     * @var string $objCurl String object property for holding the curl object
     *
     * @access public
     */
    public $objCurl;

    public $objTags;


    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage = $this->getObject('language', 'language');
    }

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

    public function createMediaTag($eventname) {
        $num = rand(0,99);
        $name = metaphone($eventname, 5);
        return $name;
    }
    
    public function restoreTags($input) { 
        $opened = array(); 
        // loop through opened and closed tags in order  
        if(preg_match_all("/<(\/?[a-z]+)>?/i", $input, $matches)) { 
            foreach($matches[1] as $tag) { 
                if(preg_match("/^[a-z]+$/i", $tag, $regs)) { 
                    // a tag has been opened  
                    if(strtolower($regs[0]) != 'br') {
                        $opened[] = $regs[0]; 
                    }
                } 
                elseif(preg_match("/^\/([a-z]+)$/i", $tag, $regs)) { 
                    // a tag has been closed  
                    unset($opened[array_pop(array_keys($opened, $regs[1]))]); 
                } 
            } 
         } 
         // close tags that are still open  
         if($opened) { 
             $tagstoclose = array_reverse($opened); 
             foreach($tagstoclose as $tag) $input .= "</$tag>"; 
         } 
         return $input; 
    }
    
    public function truncateDescription($eventid, $string, $limit, $break=".", $pad="...") { 
        $morelink = $this->newObject('link', 'htmlelements');
        $morelink->href = $this->uri(array('action' => 'viewsingle', 'eventid' => $eventid));
        $morelink->link = $this->objLanguage->languageText("mod_events_moreeventinfo", "events");
        $morelink = $morelink->show();
        // return with no change if string is shorter than $limit  
        if(strlen($string) <= $limit) {
            return $string." [$morelink]"; 
        }
        // is $break present between $limit and the end of the string?  
        if(false !== ($breakpoint = strpos($string, $break, $limit))) { 
            if($breakpoint < strlen($string) - 1) { 
                $string = substr($string, 0, $breakpoint) . $pad; 
            } 
        } 
        $string = $this->restoreTags($string); 
        return $string." [$morelink]";
    }
    
    public function truncateBigDescription($eventid, $string, $limit, $break=".", $pad="...") { 
        $morelink = $this->newObject('alertbox', 'htmlelements');
        $morelink = $morelink->show($this->objLanguage->languageText("mod_events_moreeventinfo", "events"), $this->uri(array('action' => 'eventdesconly', 'eventid' => $eventid)));
        // return with no change if string is shorter than $limit  
        if(strlen($string) <= $limit) {
            return $string; 
        }
        // is $break present between $limit and the end of the string?  
        if(false !== ($breakpoint = strpos($string, $break, $limit))) { 
            if($breakpoint < strlen($string) - 1) { 
                $string = substr($string, 0, $breakpoint) . $pad; 
            } 
        } 
        $string = $this->restoreTags($string); 
        return $string." [$morelink]";
    }
}
?>