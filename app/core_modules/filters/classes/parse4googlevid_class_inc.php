<?php

/**
 * Class to parse a string (e.g. page content) that contains a request
 * to load a google video in the form [GVID]username[/GVID]
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
 * @version   $Id$
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
 * Class to parse a string (e.g. page content) that contains a request
 * to load a google video in the form [GVID]username[/GVID]
 *
 * @author Derek Keats
 *
 */

class parse4googlevid extends object
{
    /**
    *
    * String to hold an error message
    * @accesss private
    */
    private $errorMessage;
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
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
    function parse($str)
    {
        $str = stripslashes($str);
        //Get all the tags into an array for ones that are in links
        preg_match_all('/\\[GVID]<a.*?href="(?P<gvlink>.*?)".*?>.*?<\/a>\\[\/GVID]/', $str, $results, PREG_PATTERN_ORDER);
        //Match straight URLs
        preg_match_all('/\\[GVID](.*?)\\[\/GVID]/', $str, $results2, PREG_PATTERN_ORDER);
        //Get all the ones in links
        $counter = 0;
        foreach ($results[0] as $item) {
            $link = $results['gvlink'][$counter];
            //Check if it is a valid link, if not return an error message
            if ($this->isGoogleVideo($link)) {
                $videoId = $this->getVideoCode($link);
                $replacement = $this->getVideoObject($videoId);
            } else {
                $replacement = $this->errorMessage;
            }
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        //Get the ones that are straight URL links
        $counter = 0;
        foreach ($results2[0] as $item)
        {
            $link = $results2[1][$counter];
            //Check if it is a valid link, if not return an error message
            if ($this->isGoogleVideo($link)) {
                $videoId = $this->getVideoCode($link);
                $replacement = $this->getVideoObject($videoId);
            } else {
                $replacement = $this->errorMessage;
            }
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        
        return $str;
    }

    /**
     * 
     * Method to extract the video code from a Google video link
     * The video link is after ?docId=CODE, so we can extract the params
     * by splitting on ? and then the link by splitting on =
     * @param  string  $link The Google video link
     * @return string  The video code on Google video
     * @access private
     *                 
     */
    private function getVideoCode($link)
    {
        $vCode = explode("?", $link);
        $vTxt = $vCode[1];
        $vCode = explode("=", $vTxt);
        $vTxt = $vCode[1];

        return $vTxt;
    }
    
    /**
     * 
     * Method to build the youtube video object code
     * @param  string  $videoId The id of the Google video
     * @return String  The object code
     * @access private
     *                 
     */
    private function getVideoObject($videoId) {
        $ret = "<embed style=\"width:400px; height:326px;\" 
          id=\"VideoPlayback\" type=\"application/x-shockwave-flash\" 
          src=\"http://video.google.com/googleplayer.swf?docId=$videoId&#38;hl=en\" 
          flashvars=\"playerMode=embedded\"> </embed>";

    return $ret;   
    }

    /**
    *
    *  A method to validate a link as a valid Google video link. It should start with http, 
    *  and have v= in it. It sets the value of the errorMessage property to be the appropriate
    *  error.
    * 
    * @param  string  $link The link to check
    * @return boolean TRUE|FALSE True if it is a valid link, false otherwise
    *                 
    */
    private function isGoogleVideo($link)
    {
        $link=strtolower($link);
        if (strstr($link,"http://") && strstr($link, "docid=")) {
            return TRUE;
        } else {
               $objLanguage = $this->getObject('language', 'language');
            $this->errorMessage = "[GVID] <span class=\"error\">" 
                . $objLanguage->languageText("mod_filters_error_notgvid", "filters")
                . "</span> [/GVID]";
            return FALSE;
        }
 
    }
}    
?>