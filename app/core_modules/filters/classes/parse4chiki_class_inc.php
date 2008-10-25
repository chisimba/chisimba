<?php

/**
 * Class to parse a string (e.g. page content) that contains a wiki
 * like tag such as {{Tag}}
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
 * @version   $Id: parse4pdf_class_inc.php 2813 2007-08-03 09:29:14Z paulscott $
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
 *
 * Class to parse a string (e.g. page content) that contains a wiki
 * like tag such as {{Tag}}
 *
 * @author Derek Keats
 *
 */

class parse4chiki extends object
{
    /**
     *
     * @var String chicki string
     */
    private $chikiStr;

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
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *
    */
    public function parse($str)
    {
    	$str = stripslashes($str);
        preg_match_all('/\\{\\{(.*?)\\}\\}/', $str, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item)
        {
            $extracted = $results[1][$counter];
        	/*if (strstr($extracted, "|")) {
        		$arParams = explode("|", $results[1][$counter]);
        		$repl = $arParams['0'];
        		$width = isset($arParams['1']) ? $arParams['1'] : '100%';
        	    $height = isset($arParams['2']) ? $arParams['2'] : '500';
        	} else {
        		$height = "500";
        		$width = "100%";
        		$repl = $results[1][$counter];
        	}*/
            $chikiStr =strtolower($results[1][$counter]);
            $chikiStr = $this->executeChikiCmd($chikiStr);
    		$replacement = $chikiStr;
            $str = str_replace($item, $replacement, $str);
       		$counter++;
        }
        return $str;
    }

    /**
    *
    * Execute the command identified by the chiki (single word string
    * enclosed in {{curly braces}})
    *
    * @access private
    * @param string $chikiStr The chiki string
    * @return string The parsed chiki
    *
    */
    private function executeChikiCmd(& $chikiStr)
    {
        $method = $this->__getMethod($chikiStr);

        return $this->$method();
    }

    /**
    *
    * Developer method for testing the chiki parser
    * @access private
    * @return string The parsed chiki for this method
    */
    private function __chikitest()
    {
        return "<span class='warning'>Chiki test was successful!</span>";
    }
    /**
    *
    * Method to return a link to blog
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __blog()
    {
        return $this->getModuleLink("blog");
    }
    /**
    *
    * Method to return a link to wiki
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __wiki()
    {
        return $this->getModuleLink("wiki");
    }
    /**
    *
    * Method to return a link to cms
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __cms()
    {
        return $this->getModuleLink("cms");
    }
    /**
    *
    * Method to return a link to forum
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __forum()
    {
        return $this->getModuleLink("forum");
    }
    /**
    *
    * Method to return a link to podcast
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __podcast()
    {
        return $this->getModuleLink("podcast");
    }
    /**
    *
    * Method to return a link to filemanager
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __filemanager()
    {
        return $this->getModuleLink("filemanager");
    }
    /**
    *
    * Method to return a link to worksheet
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __worksheet()
    {
        return $this->getModuleLink("worksheet");
    }
    /**
    *
    * Method to return a link to rubric
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __rubric()
    {
        return $this->getModuleLink("rubric");
    }
    /**
    *
    * Method to return a link to mcqtests
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __mcqtests()
    {
        return $this->getModuleLink("mcqtests");
    }
    
    /**
    *
    * Method to return insertlink statement
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __insertlink()
    {
        return $this->__makeChikiText($this->objLanguage->languageText("mod_filters_chiki_insertlink", "filters"));
    }
    
    /**
    *
    * Method to return updaterequired statement
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __updaterequired()
    {
        return $this->__makeChikiText($this->objLanguage->languageText("mod_filters_chiki_updaterequired", "filters"));
    }

    /**
    *
    * Method to return linkbroken statement
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __linkbroken()
    {
        return $this->__makeChikiText($this->objLanguage->languageText("mod_filters_chiki_linkbroken", "filters"));
    }
    /**
    *
    * Method to return needsedit statement
    * @access private
    * @return string The parsed chiki for this method
    *
    */
    private function __needsedit()
    {
        return $this->__makeChikiText($this->objLanguage->languageText("mod_filters_chiki_needsedit", "filters"));
    }

    /**
    *
    * Method to return a link to a module as used by the above methods
    * @access private
    * @return string The link to the module with the module code as the link text
    *
    */
    private function getModuleLink($modCode)
    {
        $uri = $this->uri(array(), $modCode);
        $objLink = $this->getObject("link", "htmlelements");
        $objLink->href = $uri;
        $objLink->title = $modCode;
        $objLink->link = $modCode;

        return $objLink->show();
    }

//-------------------------- Class methods for validation -------------//
    /**
    *
    * Method to convert a chiki string into the name of
    * a private method of this class.
    *
    * @access private
    * @param string $chikiStr The chiki string passed byref
    * @return string the name of the method
    *
    */
    function __getMethod(& $chikiStr)
    {
        $this->chikiStr = $chikiStr;
        if ($this->__validChiki($chikiStr)) {

            return "__" . $chikiStr;
        } else {

            return "__chikiError";
        }
    }

    /**
    *
    * Method to check if a given chiki is a valid method
    * of this class preceded by double underscore (__). If __chikiStr
    * is not a valid method it returns FALSE, if it is a valid method
    * of this class it returns TRUE.
    *
    * @access private
    * @param string $action The action parameter passed byref
    * @return boolean TRUE|FALSE
    *
    */
    function __validChiki(& $chikiStr)
    {
        if (method_exists($this, "__".$chikiStr)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
    *
    * Method to return an error when the chiki string is not a valid
    * method of this class
    *
    * @access private
    * @return string The dump template populated with the error message
    *
    */
    private function __chikiError()
    {
        return " <span class='error'>"
          . $this->objLanguage->languageText("mod_filters_unrecognizedchiki", "filters")
          .": <b>" . $this->chikiStr . "</b></span> ";
    }
    /**
     *
     * @param String $txt create a chiki text
     * @return String
     */
    private function __makeChikiText($txt)
    {
        return "<span class='error'>&lt;&lt;" . $txt . "</span>";
    }
}
?>