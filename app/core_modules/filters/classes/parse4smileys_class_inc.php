<?php

/**
 * Class for parsing smilies in a text string
 * 
 * tags such as 
 *   <code>
 *     $a="Hello";
 *     $b="World";
 *     echo $a . " " . $b .".";
 *   </code>
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
 * Class for parsing smilies in a text string
 * tags such as 
 *   <code>
 *     $a="Hello";
 *     $b="World";
 *     echo $a . " " . $b .".";
 *   </code>
 * 
 * @author    Derek Keats
 * @version   $Id$
 * @copyright 2003 GPL
 */

class parse4smileys extends object {
    /**
     * Method to take a string and return it with smiley
     * codes replaced by smiley icons
     * embeded in a layer that is designed for formatting code
     * 
     * The array is taken from the Moodle smiley parser, but
     * I chose to implement it in a different way using preg_repalce
     * with a regex to avoid doing it inside script blocks. Therefore
     * I also escaped the smiley part of the array for the test. Note
     * that this takes places of the preg_replace ability to deal with
     * arrays. I first make the parseSmiley array just to make it easy
     * to ensure that the items are in matching pairs, and then use this
     * array to build the two arrays to be parsed.
     * 
     * @param  string $str The string to be parsed
     * @return the    parsed string
     *                
     * @todo   -cparse4smileys Implement do not parse if inside a HTML tag.
     *         
     */
    function parseSmiley($str)
    {
        static $smileyIcons = array(
        '>-)' => 'alien',
        'O:-)' => 'angel', 'o:-)' => 'angel',
        'X-(' => 'angry', 'x-(' => 'angry',
        '=D>' => 'applause',
        'b-(' => 'black_eye', 'P-(' => 'black_eye', 'p-(' => 'black_eye',
        ':-[' => 'bye',
        ':-p' => 'cheeky', ':-P' => 'cheeky',
        '~:>' => 'chicken',
        ':o)' => 'clown', ':O)' => 'clown',
        ':-/' => 'confused',
        'B-)' => 'cool',
        '<):-)>' => 'cowboy',
        '8-}' => 'crazy',
        ':-((' => 'cry',
        '/:-D/' => 'dance_of_joy',
        '#-o' => 'doh',
        '=P~' => 'drool', '=p~' => 'drool', '=b~' => 'drool',
        ':"->' => 'embarrassed',
        '>:-)' => 'evil',
        ':-L' => 'frustrated',
        '>:-D<' => 'hug',
        ':-D' => 'grin',
        '@-)' => 'hypnotised',
        '*-:-)' => 'idea',
        ':-*' => 'kiss',
        ':-))' => 'laugh',
        ':-x' => 'love', ':-X' => 'love',
        ':-B' => 'nerd',    
        '[-(' => 'not_talking',
        '[-o<' => 'praying', '[-O<' => 'praying',
        '/:-)' => 'raise_eyebrow',
        '8-|' => 'roll_eyes',
        '@};-' => 'rose',
        ':-(' => 'sad',
        '[-X' => 'shame_on_you', '[-x' => 'shame_on_you',
        ':-O' => 'shocked', ':-o' => 'shocked',
        ';;-)' => 'shy',
        ':-&' => 'sick',
        '8-X' => 'skull', 'xx-P' => 'skull', '8-x' => 'skull', 'XX-P' => 'skull', 
        'I-)' => 'sleeping',
        ':-)>-' => 'victory',
        ':-)' => 'smile',
        '(:-|' => 'tired',
        ':-|' => 'straight_face',
        ':-?' => 'thinking',
        ':-"' => 'whistle',
        ';-)' => 'wink',
        ':-s' => 'worried', ':-S' => 'worried',

        );
        $objIcon = NULL;
        $objIcon = $this->getObject('geticon', 'htmlelements');
        
        //$objIcon = &$this->getObject('geticon', 'htmlelements');
        /* 
        *  Loop through the array and make the arrays for 
        *  $test and $replace for the regex. This is done because
        *  it is otherwise hard to keep track of the smileys 
        */ 
        foreach ($smileyIcons as $smiley => $image){
            $test[] = $smiley;
            $objIcon->setIcon($image, 'gif', 'icons/smileys/');
            $icon = $objIcon->show();
            $replace[] = $icon;
        }
        
        return str_replace($test, $replace, $str);

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