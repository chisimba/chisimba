<?php

/**
 * Class to parse a string (e.g. page content) that contains a URL
 * to a yout would like to have turned into a link
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
 * @version   CVS: $Id: parse4youtube_class_inc.php 3526 2008-02-11 11:52:10Z dkeats $
 * @link      http://avoir.uwc.ac.za
 * @see
 */
/**
*
 * Class to parse a string (e.g. page content) that contains a URL
 * to a yout would like to have turned into a link
*
* @author Derek Keats
*
*/
class parse4link extends object
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
        //Match straight URL links
        preg_match_all('/\\[LINK](.*?)\\[\/LINK]/', $str, $results2, PREG_PATTERN_ORDER);
        //Get the ones that are straight URL links
        $counter = 0;
        foreach ($results2[0] as $item)
        {
            $link = trim($results2[1][$counter]);
            //Check if it is a valid link, if not return an error message
            if ($this->_isUrl($link)) {
            	$replacement = $this->_makeActive($link);
            } else {
            	$replacement = $this->errorMessage;
            }
            $str = str_replace($item, $replacement, $str);
            $counter++;
        }
        $counter = 0;
        return $str;
    }
    
    // DISABLED WORKING HERE....CANNOT FIND A SIMPLE WAY TO ALLOW VARIOUS REQUIRED URLS
    private function _isUrl($link)
    {
        return TRUE;
        /*
        $chLink = urlencode($link);
        $strictCh = array('strict' => FALSE);
        if ($this->objVal->uri($chLink, $strictCh)) {
            return TRUE;
        } else {
            $this->errorMessage = '<span class="error">' 
              . $this->objLanguage->languageText("mod_filters_error_invalidurl", "filters")
              .  ':</span> >>' . $link . '<<';
            return FALSE;  
        }
        */
    }
    
    /**
    * 
    * Make the link active
    * 
    * @param string $link The URL to activate
    * @return string The link in an anchor tag
    * @access private
    * 
    *  
    */
    private function _makeActive($link)
    {
        return '<a href="' . $link . '">' . $link . '</a>';
    }

}
?>