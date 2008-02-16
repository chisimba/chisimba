<?php
/**
 *
 * A class to wrap the Tinyurl api from tinyurl.com
 *
 * This class provides an interface to the tinyurl api for Chisimba
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
 * @package   tinyurlapi
 * @author    Derek Keats dkeats@uwc.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* A class to wrap the Tinyurl api from tinyurl.com
*
* This class provides an interface to the tinyurl api for Chisimba
* 
* @useage 
*   $objTu = $this->getObject("tinyurlapi", "utilities");
*   echo $objTu->createTinyUrl("http://some.big.long.com/url/with/lots/of/parameters.php?name=funnylongname&height=short&girth=thin")
* 
* @author Derke Kets
* @package utilities
*
*/
class tinyurlapi extends object
{
	 /**
     * URL location for the TinyURL API
     *
     * @var string $tinyApi URL of TinyURL API location
     * @static
     */
    static $tinyApi;
    
    private $tinyUrlError;

    /**
    *
    * Intialiser for the _MODULECODE controller
    * @access public
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->tinyApi = 'http://tinyurl.com/api-create.php';
    }
    
    /**
    * 
    * Set the api call, the full URL passed to CURL
    * to do retrieve the tiny url
    * 
    * $param string $url The url of the site to be converted to a tiny url.
    * 
    */
    public function createTinyUrl($url)
    {
    	$url = $this->tinyApi . '?url=' . $url;
    	try {
    		$oCh = $this->getObject("curl", "utilities");
    		$result = $oCh->exec($url);
    		if ($this->isValidResult($result)) {
    			return $result;
    		} else {
    			return $this->tinyUrlError;
    		}
    		
    	} catch(customException $e) {
            customException::cleanUp();
            exit();
        }
    }
    
    public function simulateCreate($url)
    {
    	//Simulate a curl string being returned
    	$result = "http://tinyurl.com/dfsdf";
    	if ($this->isValidResult($result)) {
    		return $result;
    	} else {
    		return $this->tinyUrlError;
    	}
    }
    
    
    /**
    * 
    * Evaluate whether there is a valid result
    * 
    * @param string $result The resurlt returned by createTinyUrl calling curl
    * @access private
    * @return TRUE | FALSE
    * 
    */
    private function isValidResult(&$result)
    {
    	//Check that we are getting back a valid tiny url
    	if (!preg_match('/^http:\/\/tinyurl.com\/[a-z0-9]+/i', $result)) {
            $this->tinyUrlError = $this->objLanguage->languageText("mod_utilities_invaldtinyurl", "utilities") 
              . ": " . $result;
            return FALSE;
        }
        //Make sure that tiny URL has not sent something weird
        if (!preg_match('/^https?:\/\//i', $result)) {
            $this->tinyUrlError = $this->objLanguage->languageText("mod_utilities_weirdtinyurlresp", "utilities") 
              . ": " . $result;
            
        }
    	return TRUE;
    }

}
?>
