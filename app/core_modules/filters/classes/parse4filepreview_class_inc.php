<?php

/**
* 
* Parse string for filter for file preview
*  
* Class to parse a string (e.g. page content) that contains a filter
* code for a file preview. It takes the file id, and returns a preview
* as is in the file manager.
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
* @version   $Id: parse4files_class_inc.php 3630 2008-02-29 12:45:21Z dkeats $
* @link      http://avoir.uwc.ac.za
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
* Parse string for filter for file preview
*  
* Class to parse a string (e.g. page content) that contains a filter
* code for a file preview. It takes the file id, and returns a preview
* as is in the file manager.
*
* @author Tohir Solomons
*
*/
class parse4filepreview extends object
{


    /**
     *
     * Constructor
     *
     * @return void
     * @access public
     *
     */
    public function init()
    {}

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
        preg_match_all('%\[FILEPREVIEW.*?/\]%six', $txt, $result, PREG_PATTERN_ORDER);
        $result = $result[0];
        
        // Combine duplicates
        $result = array_unique($result);
        
        // If there are any matches
        if (count($result) > 0) {
            
            // Load Preview Class
            $objPreview = $this->getObject('filepreview', 'filemanager');
            
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
                    $preview = $objPreview->previewFile($resultId[0]);
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