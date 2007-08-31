<?php
/**
* Class to parse a string (e.g. page content) that contains a FLICKR
* API item, and return the content inside the Chisimba page
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
*/
 
 
 
/**
*
* Class to parse a string (e.g. page content) that contains a link
* to a yout tube video and render the video in the page
*
* @author Derek Keats
*         
*/

class parse4flickr extends object
{
	/**
	* 
	* String to hold an error message
	* @accesss private 
	*/
	private $errorMessage;
    
    /**
     * 
     * Constructor for the wikipedia parser
     * 
     * @return void  
     * @access public
     * 
     */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        //Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The parsed string
    *                
    */
    public function parse($txt)
    {
        //Match filters based on a wordpress style
        preg_match_all('/\\[FLICKR:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
        //Get all the ones in links
        $counter = 0;

        foreach ($results[0] as $item)
        {
            $str = $results[1][$counter];
            $ar= $this->objExpar->getArrayParams($str, ",");
            if (isset($this->objExpar->type)) {
                $type = $this->objExpar->type;
            } else {
                $type="error";
            }
            
            switch ($type)
            {
                case "slideshow":
                    $replacement = $this->getSlideshow();
                    break; 
            
                default:
                    $replacement = $item . "<br .<span class=\"error\">"
                      . $this->objLanguage->languageText("mod_filters_error_flickr_invalid" , "filters") . "</span>";
                    break;
            }
            $txt = str_replace($item, $replacement, $txt);
            $counter++;
        }
        return $txt;
    }

    /**
     * 
     * Method to return a flickr slideshow
     * 
     * @return string The formatted slideshow object
     * 
     */
    private function getSlideshow()
    {
        //Initialize extras 
        $extras = "";
        //Get and set the width
        if (isset($this->objExpar->width)) {
            $width = $this->objExpar->width;
        } else {
            $width = 500;
        }
        //Get and set the height
        if (isset($this->objExpar->height)) {
            $height = $this->objExpar->height;
        } else {
            $height = 500;
        }
        //Get and set the tag
        if (isset($this->objExpar->tag)) {
            $tag = $this->objExpar->tag;
        } else {
            $tag = "NOTFOUND";
        }
        //Get and set the userid
        if (isset($this->objExpar->userid)) {
            $userid = $this->objExpar->userid;
        } else {
            $userid = "NOTFOUND";
        }
        
        return "<object type=\"text/html\" "
          . "data=\"http://www.flickr.com/slideShow/index.gne?user_id="
          . "$userid&tags=$tag\" "
          . "width=\"$width\" height=\"$height\" $extras> "
          . "</object>";
    }
}
?>