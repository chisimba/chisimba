<?php

/**
 * Class to parse a string (e.g. page content) that contains a a URL for a 
 * PDF file and embeds it in the page
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
 *
 * Class to parse a string (e.g. page content) that contains a a URL for a
 * PDF file and embeds it in the page
 *
 * @author Derek Keats
 *
 */

class parse4pdf extends object
{
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    public function init()
    {
        //Nothing to do here
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
        preg_match_all('/\\[PDF](.*?)\\[\/PDF]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $extracted = $results[1][$counter];
        	if (strstr($extracted, "|")) {
        		$arParams = explode("|", $results[1][$counter]);
        		$repl = $arParams['0'];
        		$width = isset($arParams['1']) ? $arParams['1'] : '100%';
        	    $height = isset($arParams['2']) ? $arParams['2'] : '500';
        	} else {
        		$height = "500";
        		$width = "100%";
        		$repl = $results[1][$counter];
        	}
    		$replacement = "<EMBED src=\"" . $repl . "\" href=\"" 
    		  . $repl ."\" width=\"$width\" height=\"$height\"></EMBED>";
   	   		$str = str_replace($item, $replacement, $str);
       		$counter++;
        }
        return $str;
    }
}
?>