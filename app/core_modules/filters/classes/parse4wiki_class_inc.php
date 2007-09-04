<?php
/**
* Class to parse a string (e.g. page content) that contains a Wiki
* item from the local wiki, and return the content inside the Chisimba 
* page
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
*/
 
 
 
/**
*
* Class to parse a string (e.g. page content) that contains a link
* to a local Wiki page and render the desired content.
*
* @author Derek Keats
*         
*/

class parse4wiki extends object
{
	/**
	* 
	* String to hold an error message
	* @accesss private 
	*/
	private $errorMessage;
    
    /**
     * 
     * Constructor for the wikipedia parser
     * 
     * @return void  
     * @access public
     * 
     */
    function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        //Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
    }
    
    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The parsed string
    *                
    */
    public function parse($txt)
    {
        //Match filters based on a wordpress style
        preg_match_all('/\\[WIKI:(.*?)\\]/', $txt, $results, PREG_PATTERN_ORDER);
        //Get all the ones in links
        $counter = 0;
        //Get the wiki display object
        $objWiki = $this->newObject('wikidisplay', 'wiki');
        foreach ($results[0] as $item)
        {
            $str = $results[1][$counter];
            $ar= $this->objExpar->getArrayParams($str, ",");
            if (isset($this->objExpar->wiki)) {
                $wiki = $this->objExpar->wiki;
            } else {
                $wiki="Default wiki";
            }
            if (isset($this->objExpar->page)) {
                $page = $this->objExpar->page;
            } else {
                $page="MainPage";
            }
            $page = $objWiki->showPage($wiki, $page);
            $replacement = $page;
            $txt = str_replace($item, $replacement, $txt);
            $counter++;
        }
        return $txt;
    }
}
?>