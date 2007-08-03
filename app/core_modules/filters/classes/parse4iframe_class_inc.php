<?php

/**
 * Class to parse a string (e.g. page content) that contains a request
 * to load a page into an iframe in the form [IFRAME]URL|width|height[/IFRAME]
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
* Class to parse a string (e.g. page content) that contains a request
* to load a page into an iframe in the form [IFRAME]URL|width|height[/IFRAME]
*
* @author Derek Keats
*         
*/

class parse4iframe extends object
{
    
    /**
     * 
     * Standard Chisimba init method. 
     * to use
     * 
     */
    public function init()
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
        $str = stripslashes($str);
        //Get all the tags into an array
        preg_match_all('/\\[IFRAME](.*?)\\[\/IFRAME]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $extracted = $results[1][$counter];
        	if (strstr($extracted, "|")) {
	        	$arParams = explode("|", $extracted);
	        	$url = $arParams['0'];
	        	$width = $arParams['1'];
	        	if (count($arParams) >= 2) {
        			$height = $arParams['2'];
	        	} else {
	        		$height = "500";
	        	}
        	} else {
        		$url = $results[1][$counter];
        		$height = "450";
        		$width = "800";
        	}
        	$replacement = $this-> getIframe($url, $width, $height);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }
    
    /**
    * 
    * Method to get the javascript for displaying delicious tags
    * for $deliciousUser
    * 
    * @param  string $deliciousUser The username on del.icio.us
    * @return string The javascript
    *                
    */
    public function getIframe($url, $width, $height)
    {
    	return "<iframe src=\"$url\" width=\"$width\" height=\"$height\"></iframe>"; 
    }

}
?>