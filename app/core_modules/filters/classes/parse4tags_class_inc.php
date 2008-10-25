<?php

/**
 * Class to parse a string (e.g. page content) that contains a request
 * to load del.icio.us tags in the form [TAGS]username[TAGS]
 * 
 * PHP version 3
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
 * @see       References to other sections (if any)...
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
 * to load del.icio.us tags in the form [TAGS]username[TAGS]
 *
 * @author Derek Keats
 *
 */

class parse4tags extends object
{
    
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
        //Get all the tags into an array
        preg_match_all('/\\[TAGS](.*?)\\[\/TAGS]/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
        	$replacement = $this->getTagJs($results[1][$counter]);
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        return $str;
    }
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $deliciousUser Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public 
     */
    function getTagJs($deliciousUser)
    {
    	$ret = "<script type=\"text/javascript\"" 
		  . "src=\"http://del.icio.us/feeds/js/tags/$deliciousUser?icon;size=12-35;"
		  . "color=87ceeb-0000ff;title=my%20del.icio.us%20tags;name;showadd\"></script>";

        return $ret;
    }

}
?>