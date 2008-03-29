<?php
/**
*
* Class to parse a string (e.g. page content) that contains a flag
* for the enclosed text.
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
* Class to parse a string (e.g. page content) that contains a flag
* for the enclosed text. For example,
* [FLAG type=needrefs]Some text to flag.[/FLAG]
*
* This causes the text to be placed in a table with an image that
* flags the surrounded text for attention, and generally for action
* or feedback.
*
* @author Derek Keats
*
*/
class parse4flag extends object
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
        preg_match_all('/(\\[FlAG:)(.*?)\\](.*?)(\\[\\/FLAG\\])/ism', $txt, $results);
       	$counter = 0;
       	foreach ($results[3] as $item) {
            //Parse for the parameters
            $str = trim($results[2][$counter]);
            //The whole match must be replaced
            $replaceable = $results[0][$counter];
            //echo "!!!$str!!!<br/>";
        	$ar= $this->objExpar->getArrayParams($str, ",");
            $this->setupPage();
          	//$replacement = $this->getBox($this->boxtype, $item);
            $replacement = $this->getTable($this->type, $item);
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
        //Get type
        if (isset($this->objExpar->type)) {
            $this->type = $this->objExpar->type;
        } else {
            $this->type=NULL;
        }
        //Get title
        if (isset($this->objExpar->title)) {
            $this->title = $this->objExpar->title;
        } else {
            $this->title=NULL;
        }
        //Get comment
        if (isset($this->objExpar->comment)) {
            $this->comment = $this->objExpar->comment;
        } else {
            $this->comment=NULL;
        }
    }

    private function getTable($flagType, &$item)
    {
        if (isset($flagType) && $flagType !== NULL) {
            $iconImg = $this->insertIcon($flagType);
        }
        if (isset($this->title) && $this->title !== NULL) {
            $instrLangTitle = $this->title;
        } else {
            $instrLangTitle = "mod_filters_flag_title_" . $flagType;
            $instrLangTitle = $this->objLanguage->languageText($instrLangTitle, "filters");
        }
        if (isset($this->comment) && $this->comment !== NULL) {
            $instrLangTxt = $this->comment;
        } else {
            $instrLangTxt = "mod_filters_flag_txt_" . $flagType;
            $instrLangTxt = $this->objLanguage->languageText($instrLangTxt, "filters");
        }
        $flagTable = "<table cellspacing=0 class=\"flagbox\">";
        $flagTable .= "<tr><td style=\"background:red;\">&nbsp;</td><td colspan=2 style=\"padding-left: 8px; padding-top:8px;\"><h4>$instrLangTitle</h4></td></tr>";
        $flagTable .= "<tr><td style=\"background:red;\">&nbsp;</td><td width=64 style=\"padding-left: 8px\">$iconImg</td><td style=\"padding-left: 8px;\"><span class=\"error\">$instrLangTxt</span></td></tr>";
        $flagTable .= "<tr><td style=\"background:red;\">&nbsp;</td><td colspan=2 style=\"padding-left: 8px; padding-bottom:8px;\">" .  $item .  "</td></tr>";
        $flagTable .= "</table>";
        return $flagTable;
    }

    private function insertIcon($flagType)
    {
        $urlPath = $this->getResourceUri("flagimg", "filters");
        return "<img src=\"" . $urlPath . "/" . $flagType . ".png\" align=\"left\" style= style=\"margin-right: 12px\">";
    }

}