<?php

/**
 * Class to parse a string (e.g. page content) that contains a request
 * to load a RSS feed in the form [FEED]username[/FEED]
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
 * @version   $Id: parse4feeds_class_inc.php 10361 2008-09-01 12:27:12Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
/**
*
* Class to parse a string (e.g. page content) that contains a request
* to load a RSS feed in the form [FEED]username[/FEED]
*
* @author Derek Keats
*         
*/

class parse4rss extends object
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
        // Get the config object.
        $this->objConfig = $this->getObject('altconfig', 'config');
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
    }

    /**
    *
    * Method to parse the string
    * @param  string $str The string to parse
    * @return string The parsed string
    *                
    */
    public function parse($txt)
    {
        // Check that the feed module is present and registered, else dont parse the tag
        if (!$this->objModules->checkIfRegistered('feed')) {
            return $txt;
        } else {
            $txt = stripslashes($txt);
            // Match filters based on a Chisimba style
            preg_match_all('/(\\[RSS:?)(.*?)\\](.*?)(\\[\\/RSS\\])/ism', $txt, $results);
            // Match filters that use the FEED tag
            preg_match_all('/(\\[FEED:?)(.*?)\\](.*?)(\\[\\/FEED\\])/ism', $txt, $resultsFeed);
            // Match filters based on NIC style
            preg_match_all('/\\[RSS\s*(limit=\d*)?\s*(display=[a-zA-Z]*)?\s*(limit=\d*)?\s*](.*?)\\[\/RSS]/', $txt, $results2, PREG_PATTERN_ORDER);
            // Match filters based on NIC style for the FEED tag
            preg_match_all('/\\[FEED\s*(limit=\d*)?\s*(display=[a-zA-Z]*)?\s*(limit=\d*)?\s*](.*?)\\[\/FEED]/', $txt, $results2Feed, PREG_PATTERN_ORDER);

            // Parse the first pattern (RSS)
            $counter = 0;
            foreach ($results[3] as $item) {
                //Parse for the parameters
                $str = trim($results[2][$counter]);
                $this->objExpar->getArrayParams($str, ",");
                //$this->objExpar->getParamsQuoted($str, ",", "'"); --- NOT WORKING YET
                //The whole match must be replaced
                $replaceable = $results[0][$counter];
                $this->setupPage();
                $replacement = $this->getFeed($item, $this->limit);
                $txt = str_replace($replaceable, $replacement, $txt);
                $counter++;
            }
            $item=NULL;
            
            // Parse the second pattern (FEED)
            $counter = 0;
            foreach ($resultsFeed[3] as $item) {
                //Parse for the parameters
                $str = trim($resultsFeed[2][$counter]);
                $this->objExpar->getArrayParams($str, ",");
                //$this->objExpar->getParamsQuoted($str, ",", "'"); --- NOT WORKING YET
                //The whole match must be replaced
                $replaceable = $resultsFeed[0][$counter];
                $this->setupPage();
                $replacement = $this->getFeed($item, $this->limit);
                $txt = str_replace($replaceable, $replacement, $txt);
                $counter++;
            }
            $item=NULL;
            
            // Parse the third pattern pattern
            $counter = 0;
            foreach ($results2[0] as $item)
            {
                // check for a limit=x parameter
                if ($results2[1][$counter] != "") {
                    $limit = intval(substr($results2[1][$counter],strpos($results2[1][$counter],"=")+1));
                } else {
                    $limit = NULL;
                }
                $replacement = $this->getFeed($results2[4][$counter], $limit);
                $txt = str_replace($item, $replacement, $txt);
                $counter++;
            }
            $item=NULL;
            
            // Parse the fourth pattern pattern with the FEED tag
            $counter = 0;
            foreach ($results2Feed[0] as $item)
            {
                // check for a limit=x parameter
                if ($results2Feed[1][$counter] != "") {
                    $limit = intval(substr($results2Feed[1][$counter],strpos($results2Feed[1][$counter],"=")+1));
                } else {
                    $limit = NULL;
                }
                $replacement = $this->getFeed($results2Feed[4][$counter], $limit);
                $txt = str_replace($item, $replacement, $txt);
                $counter++;
            }

            return $txt;
        }
    }
    
    /**
    * 
    * Method to get the feed and render it for output
    * 
    * @param  string $url The text that is being searched for
    * @return string The rendered Feed.
    *                
    */
    public function getFeed($url, $limit=NULL)
    {
        $url=str_replace("&amp;", "&", $url);
        $feed = $this->getObject('spie', 'feed');
        $retStr = "";
        if ($limit) {
            $feed->setLimit($limit);
        }
        // Get the feed using the smart display method
        if (isset($this->fields) && is_array($this->fields)) {
            if ( count($this->fields) == 0 ) {
                $retStr = $feed->getFeed($url, "displaySmart");
            } else {
                // Extract the fields into an array
                $fields=array();
                // We have to call a method that will display only certain fields
                $retStr = $feed->getFields($url, $this->fields);
            }
        }
        unset($this->limit, $this->fields);
        $feed->setLimit(NULL);
        unset($feed);
        return $retStr;
    }
    
    /**
     *
     * Method to set up the parameter / value pairs for th efilter
     * @access public
     * @return VOID
     *
     */
    public function setUpPage()
    {
        // Get data from fields='title, description, date'
        if (isset($this->objExpar->fields)) {
            $fields = $this->objExpar->fields;
            $this->fields = explode("|", $fields);
        } else {
            $this->fields = array();
        }
        if (isset($this->objExpar->limit)) {
            $this->limit = $this->objExpar->limit;
        } else {
            $this->limit=NULL;
        }
    }

    
    
    
    /**
     * 
     * The stupid WYSWYG editor in Chisimba replaces & with &amp; in URLs
     * so this needs to be reversed for the feed to work
     * 
     * @param  string $url The Url to be cleaned
     * @return string The Url with &amp; replaced by &
     *                
     *                
     */
    public function cleanUrl($url) 
    {
       return str_replace("&amp;", "&", $url);
    }
}
?>
