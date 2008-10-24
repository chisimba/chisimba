<?php

/**
 * Class to parse a string (e.g. page content) that contains a link
 * to a mySpace video and render the video in the page
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
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
    // security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Class to parse a string (e.g. page content) that contains a link
 * to a mySpace video and render the video in the page
 *
 * @author Derek Keats
 *
 */

class parse4myspacevideo extends object
{
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    function init()
    {
        
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
        preg_match_all('/\\[MYSPACEVID]<a.*?href="(?P<youtubelink>.*?)".*?>.*?<\/a>\\[\/MYSPACEVID]/', $str, $results, PREG_PATTERN_ORDER);
        //Match straight URLs
        preg_match_all('/\\[MYSPACEVID](.*?)\\[\/MYSPACEVID]/', $str, $results2, PREG_PATTERN_ORDER);
        
        //Get all the ones in links
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $link = $results['youtubelink'][$counter];
            $videoId = $this->getVideoCode($link);
            $replacement = $this->getVideoObject($videoId);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        
        //Get the ones that are straight URL links
        $counter = 0;
        foreach ($results2[0] as $item) {
        	$link = $results2[1][$counter];
            $videoId = $this->getVideoCode($link);
            $replacement = $this->getVideoObject($videoId);
            $str = str_replace($item, $replacement, $str);
            $counter++;
    	}
        
        return $str;
    }
    
    /**
     * 
     * Method to extract the video code from a myspace video link
     * @param  string  $link The myspace video link
     * @return string  The video code on mySpace
     * @access private
     *                 
     */
    private function getVideoCode($link)
    {
        $vCode = explode("?", $link);
        $vTxt = $vCode[1];
        $vCode = explode("=", $vTxt);
        $vTxt = $vCode[2];

        return $vTxt;
    }
    
    /**
     * 
     * Method to build the youtube video object code
     * @param  string  $videoId The id of the Youtube video
     * @return String  The object code
     * @access private
     *                 
     */
    private function getVideoObject($videoId)
    {
		$vid = "<embed src=\"http://lads.myspace.com/videos/vplayer.swf\" flashvars=\"m="
		  . $videoId . "&amp;type=video\" type=\"application/x-shockwave-flash\""
		  . " width=\"430\" height=\"346\"></embed>";

		return $vid;
    }
    
}
?>