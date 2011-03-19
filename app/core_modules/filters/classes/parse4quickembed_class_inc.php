<?php

/**
 *
 * OEMBED filter
 *
 * Class to parse a string (e.g. page content) that contains a wordpress 
 * style OEMBED plugin in the format
 *   [oembed:http://oembedUrl] 
 * for example
 *   [oembed:http://polldaddy.com/oembed?url=http://answers.polldaddy.com/...
       poll/2431815/&format=xml]
 *
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
 * @version   $Id: parse4ss_class_inc.php 12758 2009-03-06 20:14:13Z dkeats $
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
 * OEMBED filter
 *
 * Class to parse a string (e.g. page content) that contains a wordpress
 * style OEMBED plugin in the format
 *   [oembed:http://oembedUrl]
 * for example
 *   [oembed:http://polldaddy.com/oembed?url=http://answers.polldaddy.com/...
       poll/2431815/&format=xml]
 *
 * @author Derek Keats
 *
 */

class parse4quickembed extends object
{
    /**
    *
    * String to hold an error message
    * @accesss private
    */
    private $errorMessage;
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
    function init()
    {
        
    }
    
    /**
    *
    * Method to parse the string
    *
    * @param  String $str The string to parse
    * @return string The parsed string
    * @access public
    *                
    */
    public function parse($str)
    {
        //Check if the oembed module is installed
        $objModule = $this->getObject('modules','modulecatalogue');
        if ($objModule->checkIfRegistered('oembed', 'oembed')) {
            $regEx = "/\\[quickembed:(.*?)\\]/i";
            preg_match_all($regEx, $str, $results, PREG_PATTERN_ORDER);
            //Extract all the matches
            $counter = 0;
            foreach ($results[0] as $item)
            {
                $oembedUrl = $results[1][$counter];
                $replacement = $this->getReplacement($oembedUrl);
                $str = str_replace($item, $replacement, $str);
                $counter++;
            }
        }
            
            
            
        // Return the string, parsed or not, depending on if oembed is isntalled
        return $str;
    }

    /**
    *
    * Method to get the replacement for the filter using the jqoembed object
    *
    * @access private
    * @param string $oembedUrl The URL for a compatible object
    * @return string The code and DIV tag for embedding
    *
    */
    private function getReplacement($oembedUrl)
    {
        //$this->setVar('JQUERY_VERSION', '1.3.1');
        $oEmb = $this->getObject('jqoembed', 'oembed');
        $oEmb->loadOembedPlugin();
        $ret = $oEmb->getEmbedAppend();
        $ret .= '<div><a href="' . $oembedUrl . '" class="oembed">Loading...</a></div>';
        return $ret;
    }
}
?>