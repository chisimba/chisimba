<?php
/**
 * 
 * Class to parse a string (e.g. page content) that contains a request
 * to create a Google-o-meter gage using a filter of the form
 * [GAGE:target=70,max=100,actual=55,colors=green-red]Caption[/GAGE]
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
* to create a Google-o-meter gage using a filter of the form
* [GAGE:target=70,max=100,actual=55,colors=green-red]Caption[/GAGE]
*
* @author Derek Keats
*         
*/

class parse4gage extends object
{
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
     */
    public function init()
    {
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
        // Get an instance of the washout class for parsing filters in RSS
        $this->objWashout = $this->getObject("washout", "utilities");
        
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
        $txt = stripslashes($txt);
        // Match filters that use the FEED tag
        preg_match_all('/(\\[GAGE:?)(.*?)\\](.*?)(\\[\\/GAGE\\])/ism', $txt, $results);
        // Parse the second pattern (FEED)
        $counter = 0;
        foreach ($results[3] as $item) {
            //Parse for the parameters
            $str = trim($results[2][$counter]);
            $caption = trim($results[3][$counter]);
            $this->objExpar->getArrayParams($str, ",");
            //The whole match must be replaced
            $replaceable = $results[0][$counter];
            $this->setupPage();
            $replacement = $this->makeOmeter($caption) . "<br />". $caption;
            $txt = str_replace($replaceable, $replacement, $txt);
            $counter++;
        }
        $item=NULL;
        return $txt;
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
        if (isset($this->objExpar->actual)) {
            $this->actual = $this->objExpar->actual;
        } else {
            $this->actual = "0";
        }
        if (isset($this->objExpar->label)) {
            $this->label = $this->objExpar->label;
        } else {
            $this->label=NULL;
        }
        if (isset($this->objExpar->colors)) {
            $this->colors = $this->objExpar->colors;
        } else {
            $this->colors='red-green';
        }
        if (isset($this->objExpar->size)) {
            $this->size = $this->objExpar->size;
        } else {
            $this->size='250x180';
        }
        
    }

    /**
    *
    * Method to create the image link for the google-o-meter
    * gage. 
    * 
    * @return string An image tag for the google-o-meter
    * @access private
    * @param string $caption The caption to appear under the image.
    */
    private function makeOmeter($caption=NULL) {
        $colors = $this->getColors();
        $pmsArray=array(
          'chs'=>$this->size,
          'chf'=>'bg,ls,0,ffffff,0.2,ffffff,0.2',
          'cht'=>'gom',
          'chd'=>'t:' . $this->actual,
          'chl'=>$this->label,
          'chco'=>$this->getColors());
        $pms = http_build_query($pmsArray);
        $url = $this->getBaseUri() .$pms;
        $ret =  "<img src='$url' alt='$caption'/>";
        //return htmlentities($url);
        return $ret;
    }
    
    /**
    *
    * Method to get the colours for the gage, currently
    * allows red-green and green-red, defaulting to the former.
    * 
    * @return string Colour sequence for the gage
    * @access private
    *
    */
    private function getColors()
    {
        switch($this->colors) {
            case 'red-green':
                $ret = 'ff0000,ff6600,ffff00,00ff00';
                break;
            case 'green-red':
                $ret = '00ff00,ffff00,ff6600,ff0000';
                break;
            default:
                $ret = 'ff0000,ff6600,ffff00,00ff00';
                break;
        }
        return $ret;
    }
    

    
    /**
    *
    * Method to get the base URL for calling google chart API.
    *
    * @return string The base URI of the google chart API
    * @access private
    *
    */
    private function getBaseUri()
    {
        return "http://chart.apis.google.com/chart?";
    }
    
    /**
    * 
    * The stupid WYSWYG editor in Chisimba replaces & with &amp; in URLs
    * so this needs to be reversed for the feed to work
    * 
    * @param  string $url The Url to be cleaned
    * @return string The Url with &amp; replaced by &
    *                               
    */
    public function cleanUrl($url) 
    {
       return str_replace("&amp;", "&", $url);
    }
}
?>
