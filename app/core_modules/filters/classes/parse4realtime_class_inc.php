<?php
/**
* Class to parse a string (e.g. page content) that contains a presentation
* item from the a webpresent module, whether local, URL or remote API
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
* @author    David Wafula
* @copyright 2008 David Wafula
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @link      http://avoir.uwc.ac.za
*/


class parse4realtime extends object
{
	/**
	*
	* String to hold an error message
	* @accesss private
	*/
	private $errorMessage;
    public $objConfig;
    public $objLanguage;
    public $objExpar;
    public $id;
    public $url;

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
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
        // Load the XML_RPC PEAR Class
        require_once($this->getPearResource('XML/RPC/Server.php'));
       // $this->objConfig = $this->getObject('altconfig', 'config');
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
        // Match all [FILEPREVIEW /] tags
        preg_match_all('%\[REALTIME.*?/\]%six', $txt, $result, PREG_PATTERN_ORDER);

        $result = $result[0];
        
        // Combine duplicates
        $result = array_unique($result);
        
        // If there are any matches
        if (count($result) > 0) {
            
            // Load Preview Class
            $realtime = $this->getObject('realtimestarter', 'realtime');
            
            // Go through each result
            foreach ($result as $str)
            {
                // Fix required - Replace &quot; with "
                $strReplace = str_replace ('&quot;', '"', $str);
                
                // Match ids
                preg_match_all('/id\ *?=\ *?"(?P<id>.*?)"/six', $strReplace, $resultId, PREG_PATTERN_ORDER);
                $resultId = $resultId['id'];
                
                // If ID is specified
                if (isset($resultId[0])) {
                    // get preview
                    $preview = $realtime->generateURL($resultId[0],'testagenda');
                } else {
                    // else set preview as nothing
                    $preview = '';
                }
                
                // Replace filter code with preview
                $txt = str_replace($str, $preview, $txt);

            } // End foreach
        } // End if count
       
        // Return rendered text
        return $txt;
    }
}
?>