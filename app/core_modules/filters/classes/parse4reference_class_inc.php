<?php

/**
 * Class to Parse for References and convert them into footnotes
 *
 * This class searches for references enclosed in [REF][/REF] tags.
 * Each of these references are then replaced by a superscript number
 * that links to an item in the footnote.
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
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */

/**
* Class to Parse for References and convert them into footnotes
*
* This class searches for references enclosed in [REF][/REF] tags.
* Each of these references are then replaced by a superscript number
* that links to an item in the footnote.
*
* @author Tohir Solomons
*/
class parse4reference extends object
{

    /**
    * Constructor
    */
    public function init()
    { }
    
    /**
    * Method to parse text for [REF]s and replace them with footnotes
    * @param  string $string String to be parse
    * @return string
    */
    public function parse($string)
    {
        // Get all instances of the [REF] tags
        preg_match_all('/\\[REF](?P<reference>.*?)\\[\/REF]/', $string, $result, PREG_PATTERN_ORDER);
        // Only use 'reference' named capturing group
        $result = $result['reference'];
        
        // If at least one result, start replacing
        if (count($result) > 0) {
            $count = 1; // Counter for Refences
            $referencefooter = '';
            
            // Loop through the results
            foreach ($result as $reference)
            {
                // Add backslash to every forwardslash
                $pattern = str_replace('/', '\/', $reference);
                
                // Create Pattern to be replaced based on reference
                $pattern = '/\\[REF]'.$pattern.'\\[\/REF]/';
                
                // Create Replacement based on reference
                $replacement = '<sup><a href="#ref'.$count.'" name="referencepoint_'.$count.'" title="'.$reference.'">['.$count.']</a></sup>';
                
                // Replace references with footnote link
                $string = preg_replace($pattern, $replacement, $string);
                
                // Add Reference to Footnote
                $referencefooter .= '<li><sup><a href="#referencepoint_'.$count.'" name="ref'.$count.'">?</a></sup> '.$reference.'</li>';
                
                // Increase Counter
                $count++;
            }
            
            //Add Reference Footnote below the text
            $string .= '<h3>References</h3><ol>'.$referencefooter.'</ol>';
            
            
            /*
            // This is added as an option that could be used
            // Instead of adding it to the bottom, the references could be added
            // anywhere the user has the placed the code [REFERENCES /]
            preg_match_all('/(?P<referencestag>\\[REFERENCES *?\/])/', $string, $referencesResult, PREG_PATTERN_ORDER);
            $referencesResult = $referencesResult['referencestag'];
            
            if (count($referencesResult) == 0) {
                $string .= '<h3>References</h3><ol>'.$referencefooter.'</ol>';
            } else {
                $string = preg_replace('/\\[REFERENCES *?\/]/', $referencefooter, $string);
            }
            */
        }

        return $string;
    }

}

?>