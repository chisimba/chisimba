<?php
/**
 *
 * Ellerdale class
 *
 * Ellerdale returns information about a topic from the web
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
 * @package   utilities
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
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
* Ellerdale class
*
* @package utilities
* @author Paul Scott <pscott@uwc.ac.za>
*/
 
class ellerdale extends object
{
    /**
     * Additional curl headers
     * 
     * @var $curl_headers
     */
    public $curl_headers;
    
    /**
     * Additional curl options
     * 
     * @var $curl_options
     */
    public $curl_options;
    
    /**
     * Standard construct based function
     * 
     */
    public function init() {
        $username = 'chisimba';
        $password = 'jA2tvHys68uR';
        $this->username = ($username) ? $username : null;
        $this->password = ($password) ? $password : null;
        $this->base_url = 'http://api.ellerdale.com/v1/';
 
        //custom headers and options that are passed to the curl method
        $this->curl_headers = array();
        $this->curl_options = array();
        
        $this->objCurl = $this->getObject('curlwrapper', 'utilities');
    }
  
    /**
     * find function
     *
     * @param string $q query string
     * @param array $options additional options
     */ 
    public function find( $title ) {
        
    }
  
    
    
    /**
     * Curls the lot
     * 
     * @param string $url url
     * @access public
     */
    public function curl($url) {
        return $this->objCurl->exec($url);
    }
}
?>
