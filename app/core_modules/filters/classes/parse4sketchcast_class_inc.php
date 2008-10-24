<?php

/**
 * Class to parse a string (e.g. page content) that contains a Chisimba filter
 * of the form [SKETCHCAST: id=55016, width=WIDTH, height=HEIGHT]
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
/**
*
* Class to parse a string (e.g. page content) that contains a Chisimba filter
* of the form [SKETCHCAST id=55016, width=WIDTH, height=HEIGHT]
*
* @author Derek Keats
*         
*/

class parse4sketchcast extends object
{
    /**
    *
    * String to hold an error message
    * @accesss private
    */
    private $errorMessage;

    /**
     * Description for private
     * @var    string 
     * @access private
     */
    private $width;

    /**
     * Description for private
     * @var    string 
     * @access private
     */
    private $height;
    
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
        //Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
        //Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *                
    */
    public function parse($txt)
    {
        //Match straight URLs
        $this->width = "425";
        $this->height = "348";
        $regEx = "/\\[SKETCHCAST:(.*?)\\]/";
        preg_match_all($regEx, $txt, $results, PREG_PATTERN_ORDER);
        //Extract all the matches
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $str = $results[1][$counter];
            $ar= $this->objExpar->getArrayParams($str, ",");
            if (isset($this->objExpar->id)) {
                $id = $this->objExpar->id;
                if (isset($this->objExpar->width)) {
                    $this->width = $this->objExpar->width;
                }
                if (isset($this->objExpar->height)) {
                    $this->height = $this->objExpar->heigth;
                }
                $replacement = $this->getSlideObject($id);
            } else {
                $replacement = "<span class=\"error\">" 
                  . $this->objLanguage->languageText("mod_filters_error_notskcast", "filters")
                  . ":<br />$item</span>";
            }
            $txt = str_replace($item, $replacement, $txt);
            $counter++;
        }

        return $txt;
    }

    
    /**
     * 
     * Method to build the youtube video object code
     * @param  string  $videoId The id of the Youtube video
     * @return String  The object code
     * @access private
     *                 
     */
    private function getSlideObject($id)
    {
          
          return "<object width=\"" . $this->width . "\" "
            . "height=\"" . $this->height . "\">"
            . "<param name=\"movie\" "
            . "value=\"http://sketchcast.com/swf/player.swf?id="
            . $id ."\"></param><param name=\"wmode\" value=\"transparent\">"
            . "</param><embed src=\"http://sketchcast.com/swf/player.swf?id="
            . $id . "\" type=\"application/x-shockwave-flash\" wmode=\"transparent\""
            . " width=\"" . $this->width . "\" height=\"" . $this->height . "\">"
            . "</embed></object>";
    }
   
}
?>