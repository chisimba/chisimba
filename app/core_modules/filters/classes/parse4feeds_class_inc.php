<?php
/**
*
* Class to parse a string (e.g. page content) that contains a request
* to load a RSS feed in the form [FEED]username[/FEED]
*
* @author Derek Keats
*
*/

class parse4feeds extends object
{
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
     * @access public
     * 
     */
    public function init()
    {

    }
    
    /**
    *
    * Method to parse the string
    * @param String $str The string to parse
    * @return The parsed string
    *
    */
    public function parse($str)
    {
        $str = stripslashes($str);
        //Get all the tags into an array
        preg_match_all('/\\[FEED]<a.*?href="(?P<feedlink>.*?)".*?>.*?<\/a>\\[\/FEED]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item) {
        	$link = $results['feedlink'][$counter];
        	$replacement = $this->fetchFeed($link);
        	//die($link);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }
    
    /**
     * 
     * Method to use the feed module to get the feed
     * @param string $url The URL for the feed to process
     * @return string The full feed with title, link, and description
     * 
     */
    public function fetchFeed($url)
    {
    	$url =  $this->cleanUrl($url);
        $objRss = $this->newObject('rssreader', 'feed');
        $objRss->parseRss($url);
        $ret = "<ul>\n";
		foreach ($objRss->getRssItems() as $item) {
        	if(!isset($item['link'])) {
        		$item['link'] = NULL;
        	}
    		@$ret .= "<li><a href=\"" . htmlentities($item['link']) 
    		  . "\">" . htmlentities($item['title']) . "</a></li>\n"
    		  . $item['description'] . "<br /><br />";
		}
		$ret .=  "</ul>\n";
		return $ret;
    }
    
    /**
     * 
     * The stupid WYSWYG editor in Chisimba replaces & with &amp; in URLs
     * so this needs to be reversed for the feed to work
     * 
     * @param string $url The Url to be cleaned
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