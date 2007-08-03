<?php

/**
 * Class to parse a string (e.g. page content) that contains a link
 * to a Freemind mind map and render the map in the page
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
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
/**
*
* Class to parse a string (e.g. page content) that contains a link
* to a Freemind mind map and render the map in the page
*
* @author Derek Keats
*         
*/

class parse4mindmap extends object
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
        $this->objFlashFreemind = $this->newObject('flashfreemind', 'files');
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
        $str = stripslashes($str);
        $str = stripslashes($str);
        
        preg_match_all('/\\[MAP]<a.*?href="(?P<maplink>.*?)".*?>.*?<\/a>\\[\/MAP]/', $str, $results, PREG_PATTERN_ORDER);
        
        $counter = 0;
        
        foreach ($results[0] as $item)
        {
            $this->objFlashFreemind->setMindMap($results['maplink'][$counter]);
            $replacement = $this->objFlashFreemind->show();
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        
        return $str;
    }

}
?>