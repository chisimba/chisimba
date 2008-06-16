<?php
/**
* Class to parse a string (e.g. page content) that contains a filter
* code for including the enclosed text in a coloured box (pastel colours)
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
* Class to parse a string (e.g. page content) that contains a filter
* code for including the enclosed text in a coloured box (pastel colours))
*
* @author Derek Keats
*
*/
class parse4colorbox extends object
{

    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;

    /**
    *
    * String object $objExpar is a string to hold the parameter extractor object
    * @access public
    *
    */
    public $objExpar;

    /**
     *
     * String $type is the type of display item
     * Valid: text, textime
     * @access public
     *
     */
    public $type;

    /**
     *
     * Constructor for the TWITTER filter parser
     *
     * @return void
     * @access public
     *
     */
    public function init()
    {
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");

    }

    /**
    *
    * Method to parse the string
    * @param  string $str The string to parse
    * @return string The parsed string
    *
    */
    public function parse($txt)
    {
       	//Match filters based on a wordpress style
        //Note the ? in the regex is important to enable the multiline
        //   feature, else it greedy
        preg_match_all('/(\\[COLORBOX:)(.*?)\\](.*?)(\\[\\/COLORBOX\\])/ism', $txt, $results);
       	$counter = 0;
       	foreach ($results[3] as $item) {
            //Parse for the parameters
            $str = trim($results[2][$counter]);
            //The whole match must be replaced
            $replaceable = $results[0][$counter];
            //echo "!!!$str!!!<br/>";
        	$ar= $this->objExpar->getArrayParams($str, ",");
            $this->setupPage();
          	$replacement = $this->getBox($this->boxtype, $item);
        	$txt = str_replace($replaceable, $replacement, $txt);
        	$counter++;
        }
        return $txt;
    }

    /**
    *
    * Method to set up the parameter / value pairs for th efilter
    * @access public
    * @return VOID
    *
    */
    private function setUpPage()
    {
        //Get username
        if (isset($this->objExpar->boxtype)) {
            $this->boxtype = $this->objExpar->boxtype;
        } else {
            $this->boxtype=NULL;
        }

    }

    private function getBox($boxtype, &$item)
    {
        $oT = $this->getObject("colorbox", "utilities");
        return $oT->show($boxtype, $item);
    }

}
?>