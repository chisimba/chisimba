<?php

/**
 * Class to parse a string (e.g. page content) that contains a request
 * 
 * Class to parse a string (e.g. page content) that contains a request
 * to load del.icio.us tags in the form [TAGS]username[TAGS]
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
 * @package   filters
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id: parse4deltags_class_inc.php 2806 2007-08-03 08:43:49Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
/**
*
* Class to parse a string (e.g. page content) that contains a request
* to load del.icio.us tags in the form [TAGS]username[TAGS]
*
* @author Derek Keats
*         
*/

class parse4searchrss extends object
{
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
     */
    public function init()
    {
        // Use this object to check if the feed module is registered.
        $this->objModules = $this->getObject('modules','modulecatalogue');
        //Load simple pie
        //require_once($this->getResourcePath('simplepie.inc', "feed"));
        // Get the config object.
        $this->objConfig = $this->getObject('altconfig', 'config');
    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *                
    */
    public function parse($str)
    {
        // Check that the feed module is present and registered, else dont parse the tag
        $str = stripslashes($str);
        if (!$this->objModules->checkIfRegistered('feed')) {
            return $str;
        } else {
            $str = stripslashes($str);
            //Get all the tags into an array
            preg_match_all('/\\[SEARCHRSS](.*?)\\[\/SEARCHRSS]/', $str, $results, PREG_PATTERN_ORDER);
            $counter = 0;
            foreach ($results[0] as $item)
            {
            	$replacement = $this->getFeed($results[1][$counter]);
                $str = str_replace($item, $replacement, $str);
                $counter++;
            }
            return $str;
        }
    }
    
    /**
    * 
    * Method to get the feed and render it for output
    * 
    * @param  string $txt The text that is being searched for
    * @return string The rendered Feed.
    *                
    */
    public function getFeed($txt)
    {
        $url = $this->makeUrl($txt);
        $feed = $this->getObject('spie', 'feed');
        $ret = $feed->getFeed($url);
        unset($feed);
        return $ret;
    }
    
    /**
     * 
     * Make the requested search term into a URL
     * 
     * @param string $text The search string for Google
     * @return string The formatted URL
     * @access public
     * 
     */
    public function makeUrl($txt)
    {
        $txt=urlencode($txt);
        return "http://uselever.com/search/feed?name=$txt";
    }

}
?>