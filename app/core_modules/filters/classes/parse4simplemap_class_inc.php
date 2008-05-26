<?php

/**
 * Class to parse a string (e.g. page content) that contains a link
 * to a simplemap and render the simplemap in the page
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
* Class to parse a string (e.g. page content) that contains a link
* to a simplemap and render the simplemap in the page
*
* @author Derek Keats
*         
*/

class parse4simplemap extends object
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
        //There is nothing to do here
    }  
   
    /**
    * 
    * A parse method as required by the washout filter parser that washes all the output for 
    * parseable filters
    * 
    * @access Public
    * @param  string $str The text to parse
    * @return string The parsed text 
    *                
    */
    public function parse($str)
    {
    	//Instantiate the modules class to check if simplemap is registered
    	$objModule = $this->getObject('modules','modulecatalogue');
    	//See if the simple map module is registered and set a param
    	$isRegistered = $objModule->checkIfRegistered('simplemap');
    	if ($isRegistered){
	    	//Instantiate the simplemap parser
	    	$objSMParser = $this->getObject('smapparser', 'simplemap');
    	}
	    //Extract the URL
    	preg_match_all('/\\[SIMPLEMAP]<a.*?href="(?P<simplemaplink>.*?)".*?>.*?<\/a>\\[\/SIMPLEMAP]/', $str, $results, PREG_PATTERN_ORDER);
	    $counter = 0;
	    foreach ($results[0] as $item) {
	    	if ($isRegistered) {
	            $replacement = $objSMParser->getRemote($results['simplemaplink'][$counter]);
	    	} else {
    			$objLanguage = $this->getObject('language', 'language');
    	    	$replacement = $results[1][$counter] . "<br /><div class=\"error\"><h3>" 
    	      	  . $objLanguage->languageText("mod_filters_error_smapnotinstalled", "filters")
    	      	  . "</h3></div>";
	    	}
            $str = str_replace($item, $replacement, $str);
            $counter++;
    	}
        return $str;
    }

}
?>