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
 * @version   CVS: $Id: parse4feeds_class_inc.php 10361 2008-09-01 12:27:12Z nic $
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
    }

    /**
    *
    * Method to parse the string
    * @param  string $str The string to parse
    * @return string The parsed string
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
            preg_match_all('/\\[RSS](.*?)\\[\/RSS]/', $str, $results, PREG_PATTERN_ORDER);
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
    * @param  string $url The text that is being searched for
    * @return string The rendered Feed.
    *                
    */
    public function getFeed($url)
    {
        $url=str_replace("&amp;", "&", $url);
        $feed = $this->getObject('spie', 'feed');
        $ret = $feed->getFeed($url);
        unset($feed);
        return $ret;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *                
    */
    public function ____parse($str)
    {
        // Check that the feed module is present and registered, else dont parse the tag
        $str = stripslashes($str);
        if (!$this->objModules->checkIfRegistered('feed')) {
            return $str;
        }
        // Get all the tags that are in links into an array - REMOVED: not useful
        /*preg_match_all('/\\[FEED]<a.*?href="(?P<feedlink>.*?)".*?>.*?<\/a>\\[\/FEED]/', $str, $results, PREG_PATTERN_ORDER);*/
        /*$counter = 0;
        foreach ($results[0] as $item) {
        	$link = $results['feedlink'][$counter];
        	$replacement = "<div class=\"feedhopper\" id=\"feedhopper" . $counter . "\">" . $this->fetchFeed($link) . "</div>";
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }*/
        // Get the ones that are straight URL links
        preg_match_all('/\\[FEED\s*(limit=\d*)?\s*(display=[a-zA-Z]*)?\s*(limit=\d*)?\s*](.*?)\\[\/FEED]/', $str, $results2, PREG_PATTERN_ORDER);
        $counter = 0;

        foreach ($results2[0] as $item)
        {
            // check for a limit=x parameter
            if ($results2[1][$counter] != "") {
                $maxCount = intval(substr($results2[1][$counter],strpos($results2[1][$counter],"=")+1));
            } else {
                $maxCount = 0;
            }
            // check for a display=xx parameter
            if (strtolower(substr($results2[2][$counter],strpos($results2[2][$counter],"=")+1)) == "titlesonly") {
                $showDescription = "FALSE";
            } else {
                $showDescription = "TRUE";
            }
            // check for a limit=x parameter after a display param
            if ($results2[2][$counter] != "" && $maxCount == 0) {
                $maxCount = intval(substr($results2[3][$counter],strpos($results2[3][$counter],"=")+1));
            }

            $link = $results2[4][$counter];
        	$replacement = "<div class=\"feedhopper\" id=\"feedhopper" . $counter . "\">" . $this->fetchFeed($link,$showDescription,$maxCount) . "</div>";
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }
    
    /**
     * 
     * Method to use the feed module to get the feed data
     * @param string $url The URL for the feed to process
     * @param string $showDescription true|false whether or not
     *  to display the description of the feed item
     * @param int $maxCount number of entried from the feed to display
     * @return string The full feed with title, link, and description
     *                
     */
    public function fetchFeed($url, $showDescription = "TRUE", $maxCount = 0)
    {
    	$url =  $this->cleanUrl($url);
        $objRss = $this->newObject('rssreader', 'feed');
        $objRss->parseRss($url);
        //$ar = $objRss->getRssItems(); REMOVED - get items doesnt return all feed data
        $ar = $objRss->getRssStruct();
        $total = count($ar);
        // Do some layout of flickr images
        $url = strtolower($url); // Make sure its lower case
        $pos = strpos($url, "flickr.com");
        if (!$pos === FALSE) {
            $isFlickr = TRUE;
            $ret = "<table>\n";
            if ($this->isOdd($total)) {
                $closingCell = "<td>&nbsp;</td></tr>";
            } else {
                $closingCell = "";
            }
        } else {
            $isFlickr = FALSE;
            $ret = "<ul>\n";
        }
        // Loop and build the output string
        $counter=0;
		foreach ($ar as $item) {
            if ($maxCount != 0 && $counter >= $maxCount) {
                break;
            }
			if(!isset($item['link'])) {
        		$item['link'] = NULL;
        	}
            // ignore the channel data
        	if ($item['type'] != "channel") {
                $counter++;
                if ($isFlickr == TRUE) {
                    if ($this->isOdd($counter)==TRUE) {
                        @$ret .= "<tr><td><a href=\"" . htmlentities($item['link']) 
                    	  . "\">" . htmlentities($item['title']) . "</a><br />\n"
                    	  . $item['description'] . "</td>";  
                    } else  {
                        @$ret .= "<td><a href=\"" . htmlentities($item['link']) 
                    	  . "\">" . htmlentities($item['title']) . "</a><br />\n"
                    	  . $item['description'] . "</td></tr>";  
                    }
                } else {
                	@$ret .= "<li><a href=\"" . htmlentities($item['link']) 
                	  . "\">" . htmlentities($item['title']) . "</a></li>\n";
                      if (array_key_exists('description',$item) && $showDescription == "TRUE") {
                        $ret .= "{$item['description']}<br />";
                      }
                }
            }

		}
		// End the table or UL depending on if we are parsing a Flickr image feed or not
		if ($isFlickr) {
		    $ret .= $closingCell . "</table>\n";
		} else {
		    $ret .=  "</ul>\n";
		}
		return $ret;
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
    
    /**
    * 
    * Method to determine if a number is odd
    * 
    * @access public 
    * @param  int     $num The number to test
    * @return boolean TRUE|FALSE depending on odd or even status of $num
    *                 
    */
    function isOdd( $num )
	{
		if( $num%2 == 1 ) {
    		// $odd == 1; the remainder of 25/2
    		return TRUE;
		} else {
		    // $odd == 0; nothing remains 
    		return FALSE;
		}
	}
	
}
?>