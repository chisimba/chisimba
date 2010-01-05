<?php

/**
 * Class for parsing smilies in a text string
 * 
 * :-) will be replaces with an smiling image
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
 * @authors   Derek Keats <dkeats@uwc.ac.za>, Dean Wookey <dean@embrace.co.za>
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
 *
 * :-) will be replaces with an smiling image
 *
 *
 */
class parse4smileys extends object {
    /**
     *
     * The array is taken from the Moodle smiley parser. The
     * idea is to use preg_replace with a callback to increase efficiency.
     * The regular expression to detect the smileys is created from the 
     * smiley array.
     *
     * @access public
     * @return VOID
     *
     */
    public function init() {
        $this->objIcon = NULL;
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        $this->smileyIcons = array(
            htmlentities('>-)') => 'alien',
            htmlentities('O:-)') => 'angel',
            htmlentities('o:-)') => 'angel',
            htmlentities('X-(') => 'angry',
            htmlentities('x-(') => 'angry',
            htmlentities('=D>') => 'applause',
            htmlentities('b-(') => 'black_eye',
            htmlentities('P-(') => 'black_eye',
            htmlentities('p-(') => 'black_eye',
            htmlentities(':-[') => 'bye',
            htmlentities(':-p') => 'cheeky',
            htmlentities(':-P') => 'cheeky',
            htmlentities('~:>') => 'chicken',
            htmlentities(':o)') => 'clown',
            htmlentities(':O)') => 'clown',
            htmlentities(':-/') => 'confused',
            htmlentities('B-)') => 'cool',
            htmlentities('<):-)>') => 'cowboy',
            htmlentities('8-}') => 'crazy',
            htmlentities(':-((') => 'cry',
            htmlentities('/:-D/') => 'dance_of_joy',
            htmlentities('#-o') => 'doh',
            htmlentities('=P~') => 'drool',
            htmlentities('=p~') => 'drool',
            htmlentities('=b~') => 'drool',
            htmlentities(':"->') => 'embarrassed',
            htmlentities('>:-)') => 'evil',
            htmlentities(':-L') => 'frustrated',
            htmlentities('>:-D<') => 'hug',
            htmlentities(':-D') => 'grin',
            htmlentities('@-)') => 'hypnotised',
            htmlentities('*-:-)') => 'idea',
            htmlentities(':-*') => 'kiss',
            htmlentities(':-))') => 'laugh',
            htmlentities(':-x') => 'love',
            htmlentities(':-X') => 'love',
            htmlentities(':-B') => 'nerd',
            htmlentities('[-(') => 'not_talking',
            htmlentities('[-o<') => 'praying',
            htmlentities('[-O<') => 'praying',
            htmlentities('/:-)') => 'raise_eyebrow',
            htmlentities('8-|') => 'roll_eyes',
            htmlentities('@};-') => 'rose',
            htmlentities(':-(') => 'sad',
            htmlentities('[-X') => 'shame_on_you',
            htmlentities('[-x') => 'shame_on_you',
            htmlentities(':-O') => 'shocked',
            htmlentities(':-o') => 'shocked',
            htmlentities(';;-)') => 'shy',
            htmlentities(':-&') => 'sick',
            htmlentities('8-X') => 'skull',
            htmlentities('xx-P') => 'skull',
            htmlentities('8-x') => 'skull',
            htmlentities('XX-P') => 'skull',
            htmlentities('I-)') => 'sleeping',
            htmlentities(':-)>-') => 'victory',
            htmlentities(':-)') => 'smile',
            htmlentities('(:-|') => 'tired',
            htmlentities(':-|') => 'straight_face',
            htmlentities(':-?') => 'thinking',
            htmlentities(':-"') => 'whistle',
            htmlentities(';-)') => 'wink',
            htmlentities(':-s') => 'worried',
            htmlentities(':-S') => 'worried',
        );
    }
    
    
    
    /**
    *
    * Each time a new smiley is added to the list, you
    * should uncomment 
    * $regex = $this->createSmileyRegex(); and 
    * echo str_replace('\\', '\\\\', $regex);
    * then view a story of page that uses filters, and the regular expression
    * should print out at the top
    *
    * @param string $str The content to parse
    * @return string The parsed content
    * @access public
    *
    */
    public function parseSmiley($str)
    {   
        $regex = '/(\\&(g(t(\\;(\\-(\\)(|))|\\:(\\-(\\)(|)|D(\\&(l(t(\\;(|))))|))))))|l(t(\\;(\\)(\\:(\\-(\\)(\\&(g(t(\\;(|))))))))))))|O(\\:(\\-(\\)(|))))|o(\\:(\\-(\\)(|))))|X(\\-(\\((|))|X(\\-(P(|))))|x(\\-(\\((|))|x(\\-(P(|))))|\\=(D(\\&(g(t(\\;(|)))))|P(\\~(|))|p(\\~(|))|b(\\~(|)))|b(\\-(\\((|)))|P(\\-(\\((|)))|p(\\-(\\((|)))|\\:(\\-(\\[(|)|p(|)|P(|)|\\/(|)|\\((\\((|)|)|L(|)|D(|)|\\*(|)|\\)(\\)(|)|\\&(g(t(\\;(\\-(|))))|)|)|x(|)|X(|)|B(|)|O(|)|o(|)|\\&(a(m(p(\\;(|))))|q(u(o(t(\\;(|)))))|)|\\|(|)|\\?(|)|s(|)|S(|))|o(\\)(|))|O(\\)(|))|\\&(q(u(o(t(\\;(\\-(\\&(g(t(\\;(|))))))))))))|\\~(\\:(\\&(g(t(\\;(|))))))|B(\\-(\\)(|)))|8(\\-(\\}(|)|\\|(|)|X(|)|x(|)))|\\/(\\:(\\-(D(\\/(|))|\\)(|))))|\\#(\\-(o(|)))|\\@(\\-(\\)(|))|\\}(\\;(\\-(|))))|\\*(\\-(\\:(\\-(\\)(|)))))|\\[(\\-(\\((|)|o(\\&(l(t(\\;(|))))|)|O(\\&(l(t(\\;(|))))|)|X(|)|x(|)))|\\;(\\;(\\-(\\)(|)))|\\-(\\)(|)))|I(\\-(\\)(|)))|\\((\\:(\\-(\\|(|)))))/e';
        //Uncomment the two following lines to generate a new regular expression
        //$regex = $this->createSmileyRegex();
        //echo str_replace('\\', '\\\\', $regex);
        return preg_replace($regex, "\$this->getSmiley('\\1')", $str);
    }
    

    /**
     * Method to take a string and return it with smiley
     * codes replaced by smiley icons
     * embeded in a layer that is designed for formatting code
     *
     * @param  string $str The string to be parsed
     * @return the    parsed string
     *                
     * @todo   -cparse4smileys Implement do not parse if inside a HTML tag.
     *         
     */
    public function parse($str)
    {
        return $this->parseSmiley($str);
    }
    
    
    
    /**
     * Method that takes a smiley (;-)) as a parameter and returns its
     * associated image in html
     *
     * @param string $smiley The smiley to be converted to an image
     * @return the html string corresponding to the given smiley
     * @access public
     * 
     */
    public function getSmiley($smiley) {
        $this->objIcon->setIcon($this->smileyIcons[$smiley], 'gif', 'icons/smileys/');
        return $this->objIcon->show();
    }
    
    
    
    /**
     * Method that creates a regular expression that matches all
     * smileys given in the smileyIcons array
     * @returns a regular expression that matches all given smileys
     */
    public function createSmileyRegex()
    {
        $regexGraph = $this->constructSmileyRegexGraph();
        $regex = '/(' . $this->depthFirstRegex($regexGraph) . ')/e';
        return $regex;
    }
    
    /**
     * Method that creates a tree from all smileys given
     * in the smileyIcons array. For example, :-) and :-(
     * both have the same root :-, but different characters
     * in the 3rd position. The aim of the tree is to optimise
     * the regular expression by doing the following:
     * /:-(\)|\()/
     * If a smiley such as :-) and another smiley :-)< are
     * both in the array, :-) wouldn't be recognised. The solution
     * was to add a value in the tree to let it know if it is ok to
     * stop at that point in the tree.
     * A sample graph looks like so:
     * Array (
     *   [:] = Array ( 
     *       [iscomplete] = false,
     *       [nextlevel]  = Array (
     *           [)] = Array(
     *               [iscomplete] = true,
     *               [nextlevel] = Array()
     *           )
     *           [(] = Array(
     *               [iscomplete] = true,
     *               [nextlevel] = Array()
     *           )
     *       )
     *   )
     * )
     * This will recognise both :) and :(, but not : by
     * itself.
     * @returns an array as described above
     * @access public
     *
     */           
    public function constructSmileyRegexGraph()
    {
        $nodes = array();
        foreach ($this->smileyIcons as $smiley=>$image) {
            $array = &$nodes;
            $counter = 0;
            $smileyChars = str_split($smiley);
            for ($counter = 0; $counter < strlen($smiley); $counter++) {
                if (!array_key_exists($smileyChars[$counter], $array)) {
                    $array[$smileyChars[$counter]]['nextlevel'] = array();
                    $array[$smileyChars[$counter]]['iscomplete'] = 0;
                }
                if ($counter == (strlen($smiley)-1)) {
                    $array[$smileyChars[$counter]]['iscomplete'] = 1;
                }
                $array = &$array[$smileyChars[$counter]]['nextlevel'];
            }
        }
        return $nodes;
    }
    
    
    
    /**
     * A recursive method that traverses a graph and builds a regular
     * expression as it goes.
     *
     * @param array $graph - a tree array as described in
     * constructSmileyRegexGraph() comments.
     * @returns a regular expression string
     */
    function depthFirstRegex($graph)
    {
        $regex = "";
        $counter = 0;
        $isend = '';
        if (count($graph) == 0) {
            return "";
        }
        foreach ($graph as $character=>$value) {
            if ($graph[$character]['iscomplete'] == 1) {
                $isend = '|';
            }
            if ($counter != 0) {
                $regex .= '|';
            }
            $charRegex = '/^[A-Z0-9a-z]/';
            $printchr = $character;
            if (preg_match($charRegex,$character) == 0) {
                $printchr = '\\' . $character;
            }
            $next = $this->depthFirstRegex($graph[$character]['nextlevel']);
            $regex .= $printchr . '(' . $next . $isend . ')';
            $counter++;
        }
        return $regex . '';
    }
       
} # end of class
?>