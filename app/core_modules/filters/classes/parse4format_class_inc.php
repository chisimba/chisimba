<?php

/**
 * Class for parsing smilies in a text string
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
 * @author    Kevin Cyster <kcyster@uwc.ac.za>
 * @copyright 2007 Kevin Cyster
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
 * Class for parsing smilies in a text string
 * @author    Kevin Cyster
 * @version   $Id: parse4format_class_inc.php,v 1.0 2007/03/01
 * @copyright 2003 GPL
 */
class parse4format extends object {
    /**
     * Method to take a string and return it with font formating
     * @param  string $str The string to be parsed
     * @return the    parsed string
     *                
     * @todo   -cparse4smileys Implement do not parse if inside a HTML tag.
     *         
     */
    function parseSmiley($str)
    {
        static $formats = array(
        '[B]' => '<b>',
        '[/B]' => '</b>',
        '[I]' => '<i>',
        '[/I]' => '</i>',
        '[U]' => '<u>',
        '[/U]' => '</u>',
        '[RED]' => '<font style="color: red">',
        '[/RED]' => '</font>',
        '[BLUE]' => '<font style="color: blue">',
        '[/BLUE]' => '</font>',
        '[YELLOW]' => '<font style="color: yellow">',
        '[/YELLOW]' => '</font>',
        '[GREEN]' => '<font style="color: green">',
        '[/GREEN]' => '</font>',
        '[S1]' => '<font size="2">',
        '[S2]' => '<font size="3">',
        '[S3]' => '<font size="4">',
        '[S4]' => '<font size="5">',
        '[S5]' => '<font size="6">',
        '[S6]' => '<font size="7">',
        '[/S1]' => '</font>',
        '[/S2]' => '</font>',
        '[/S3]' => '</font>',
        '[/S4]' => '</font>',
        '[/S5]' => '</font>',
        '[/S6]' => '</font>',
        );
 
        $objIcon = $this->getObject('geticon', 'htmlelements');
        /* 
        *  Loop through the array and make the arrays for 
        *  $test and $replace for the regex. This is done because
        *  it is otherwise hard to keep track of the smileys 
        */ 
        foreach ($formats as $code => $tag){
            $test[] = $code;
            $replace[] = $tag;
        }

        return str_ireplace($test, $replace, $str);

    } # end of function
    

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $str Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public 
     */
    public function parse($str)
    {
    	return $this->parseSmiley($str);
    }
       
} # end of class
?>