<?php

/**
 * Class for parsing kngtext, special text embeded in pseudo HTML
 * 
 * tags such as 
 *     [code]
 *       $a="Hello";
 *       $b="World";
 *       echo $a . " " . $b .".";
 *     [code]
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
* Class for parsing kngtext, special text embeded in pseudo HTML
* tags such as 
*     [code]
*       $a="Hello";
*       $b="World";
*       echo $a . " " . $b .".";
*     [code]
* 
* @author     Derek Keats
* @version    $Id$
* @copyright  2003 GPL
* @deprecated
*             
*             This class is deprecated and will be removed
*             
*/
class parse4kngtext extends object 
{
    /**
    * 
    * Method to take a string and return it with code blocks
    * embeded in a layer that is designed for formatting code
    * It uses the /e qualifier for the regex, which allows the
    * regex to run PHP code contained in the replacement
    * expression.
    * 
    * @param  string $str The string to be parsed
    * @return string $str The parsed string
    */
    function parseCode($str)
    { 
        // Match the code tag [code][/code]
        $search = "/(\[code\])(.+)(\[\/code\])/iseU"; 
        // Put a temporary replacepattern <putreturn></putreturn> for inserting BR tags
        return "DEPRECATED-------" . preg_replace($search, "'<div class=\"cdblk\">'.\$this->_nl2br('\\2').'</div>'", $str);
    } # end of function
    

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $txt Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public 
     */
    public function parse($txt)
    {
    	//class deprecated so no return, just here for the interface
    	return $txt;
    }


    
    /*-------------------------- PRIVATE METHODS BELOW LINE ---------------------*/

    /**
    * 
    * Method to highlight php code in a string
    * 
    * @param  string $str The string to be parsed
    * @return string $str The parsed string
    *                
    */   
    function _nl2br($str)
    {
        return str_replace(" ", "&nbsp;", str_replace("\n", "<br/>", $str));
    } # end of function
    
    /**
    * 
    * Method to syntax highlight php code in a string
    * 
    * @param  string $str The string to be parsed
    * @return string $str The parsed string
    *                
    */   
    function _highlight($str)
    {
        return highlight_string($str, True);
    } # end of function
    

} # end of class

?>