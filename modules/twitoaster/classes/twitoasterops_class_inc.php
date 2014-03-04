<?php
/**
 *
 * twitoaster helper class
 *
 * PHP version 5.1.0+
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
 * @package   twitoaster
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 *
 * twitoaster helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott
 * @package twitoaster
 *
 */
class twitoasterops extends object {

    /**
     * @var string $objLanguage String object property for holding the language object
     *
     * @access public
     */
    public $objLanguage;

    /**
     * @var string $objConfig String object property for holding the config object
     *
     * @access public
     */
    public $objConfig;

    /**
     * @var string $objSysConfig String object property for holding the sysconfig object
     *
     * @access public
     */
    public $objSysConfig;

    /**
     * @var string $objWashout String object property for holding the washout object
     *
     * @access public
     */
    public $objWashout;

    /**
     * @var string $objUser String object property for holding the user object
     *
     * @access public
     */
    public $objUser;

    /**
     * @var string $objCurl String object property for holding the curl object
     *
     * @access public
     */
    public $objCurl;

    
    public $objTags;


    /**
     * Constructor
     *
     * @access public
     */
    public function init() {
        $this->objLanguage  = $this->getObject('language', 'language');
        $this->objConfig    = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objWashout   = $this->getObject('washout', 'utilities');
        $this->objUser      = $this->getObject('user', 'security');
        $this->objCurl      = $this->getObject('curlwrapper', 'utilities');
        $this->objTags      = $this->getObject('dbtags', 'tagging');
    }

    /**
     * Returns the extended information of the requested user. The user has to be registered on Twitoaster. Otherwise, a 404 error is returned. 
     *
     * HTTP method is GET and parameter(s) must be UTF-8 & URL encoded. 
     *
     * @param $screen_name string screen name of the twitoaster user to query (optional)
     * @param $userid userid of the twitoaster user to query (optional)
     * @param $format string php_serial, json, xml
     */
    public function showUser($screen_name = NULL, $userid = NULL, $format = 'json') {
        if(isset($screen_name)) {
            $url = "http://api.twitoaster.com/user/show.$format?screen_name=$screen_name";
        }
        elseif(isset($userid)) {
            $url = "http://api.twitoaster.com/user/show.$format?user_id=$userid";
        }
        $ret = $this->objCurl->exec($url);
        return $ret;
    }

    /**
     * Tests if supplied API Key is valid
     *
     * HTTP method is GET and parameter(s) must be UTF-8 & URL encoded.
     *
     * @param $format string (Optional) php_serial, json, xml
     */
    public function verifyKey($format = 'json') {
        $apikey = $this->objSysConfig->getValue('apikey', 'twitoaster');
        $url = "http://api.twitoaster.com/user/verify_api_key.$format?api_key=$apikey";
        $ret = $this->objCurl->exec($url);

        return $ret;
    }

    /**
     * Updates the Twitter user's status through Twitoaster (creates a new thread). Requires authentication. 
     *
     * HTTP method is POST and parameter(s) must be UTF-8 & URL encoded.
     *
     * @param $status string (Required) status string
     * @param $extended boolean (Optional) whether or not to use the 420 char extended status.
     * @param $format string (Optional) format php_serial, json, xml
     */
    public function userUpdate($status, $extended = FALSE, $format = 'json') {
        $apikey = $this->objSysConfig->getValue('apikey', 'twitoaster');
        $url = "http://api.twitoaster.com/user/update.$format";
        $postargs = array('api_key' => $apikey, 'status' => $status);
        $ret = $this->objCurl->postCurl($url, $postargs);
        
        return $ret;
    }

   /**
    * Returns the conversation containing the requested tweet. If the tweet can't be found in any Twitoaster conversation, a 404 error is returned. 
    *
    * HTTP method is GET and parameter(s) must be UTF-8 & URL encoded. 
    *
    * @param $id
    * @param $format string (Optional) format php_serial, json, xml
    */
    public function showConvo($id, $format = 'json') {
        $url = "http://api.twitoaster.com/conversation/show.$format?id=$id";
        $ret = $this->objCurl->exec($url);

        return $ret;
    }

   /**
    * Returns the 20 most recent conversations started by the requested user. The user has to be registered on Twitoaster. Otherwise, a 404 error is returned. 
    *
    * HTTP method is GET and parameter(s) must be UTF-8 & URL encoded. 
    * 
    * @param $userid integer (Optional) twitter user id
    * @param $screen_name string (Optional) twitter screen name
    * @param $page integer (Optioanal) Page nuber to show
    * @param $show_replies boolean (Optional) whether to show replies or not
    * @param $format string (Optional) format php_serial, json, xml
    */
    public function convoUser($userid = NULL, $screen_name = NULL, $page = NULL, $show_replies = TRUE, $format = 'json') {
        $url = "http://api.twitoaster.com/conversation/user.$format?";
        if(isset($userid)) {
            $url .= "user_id=$userid";
        }
        elseif(isset($screen_name)) {
            $url .= "screen_name=$screen_name";
        }
        if(isset($page)) {
            $url .= "&page=$page";
        }
        if($show_replies == FALSE) {
            $url .= "&show_replies=$show_replies";
        }

        $ret = $this->objCurl->exec($url);
        
        return $ret;
    }

   /**
    * Returns the 20 most recent (or revelant) conversations that match the specified query. If none, a 404 error is returned.
    *
    * HTTP method is GET and parameter(s) must be UTF-8 & URL encoded. 
    *
    * @param $query string (Required) Search query. The NOT (!), OR (|) and PHRASE (") operators are supported.  
    * @param $format string (Optional) format php_serial, json, xml
    */
    public function convoSearch($query, $format = 'json') {
        $url = "http://api.twitoaster.com/conversation/search.$format?query=$query";
        $ret = $this->objCurl->exec($url);
        
        return $ret;
    }
}
?>