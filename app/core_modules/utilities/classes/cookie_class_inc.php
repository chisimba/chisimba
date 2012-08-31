<?php
/**
 *
 * Cookie helper class
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
 * @package   utilities
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
 * Cookie helper class
 *
 * PHP version 5.1.0+
 *
 * @author Paul Scott <pscott@uwc.ac.za>
 * @package utilties
 *
 */
class cookie extends object {
    /**
     * Reserved session keys
     * @access private
     */
    private static $_reserved = array();

    /**
     * Constructor
     * 
     * @access public
     */
    public function init() {
    }

    /**
     * Alias for delete() function
     *
     * @access public
     * @seealso delete
     */
    public function del($key)
    {
        $this->cookiedelete($key);
    }
   
    /**
     * Delete a cookie
     *
     * @access public
     * @param string key 
     */
    public function cookiedelete($key)
    {
        $key = self::_scrubKey($key);
        if($this->exists($key))
        {
            if(is_array($key))
            {
                list($k, $v) = each($key);
                $key = $k.'['.$v.']';
                // Set expiration time to -1hr (will cause browser deletion)
                setcookie($key, false, time() - 3600);
                unset($_COOKIE[$k][$v]);
            }
            elseif(is_array($_COOKIE[$key]))
            {
                foreach($_COOKIE[$key] as $k => $v)
                {
                    $cookie = $key . '[' . $k . ']';
                    setcookie($cookie, false, time() - 3600);
                    unset($_COOKIE[$key][$k]);
                }
            }
            else
            {
                setcookie($key, false, time() - 3600);
                unset($_COOKIE[$key]);
            }
        }
    }

    /**
     * See if a cookie key exists
     *
     * @access public
     * @param string key
     */
    public function exists($key)
    {
        $key = self::_scrubKey($key);
        if(is_array($key)) {
            list($k, $v) = each($key);
            if(isset($_COOKIE[$k][$v])) {
                return TRUE;
            }
        }
        elseif(isset($_COOKIE[$key])) {
            return TRUE;
        }
        return FALSE;
    }
   
    /**
     * Get cookie information
     * 
     * @access public
     * @param string key
     */
    public function get($key) {
        $key = self::_scrubKey($key);
        if (is_array($key)) {
            list($k, $v) = each($key);
            if (isset($_COOKIE[$k][$v])) {
                return $_COOKIE[$k][$v];
            }
        }
        elseif(isset($_COOKIE[$key])) {
           return $_COOKIE[$key];
        }
        else {
            return NULL; 
        }
    }
   
    /** 
     * Return the cookie array
     *
     * @access public
     */
    public function contents()
    {
        return $_COOKIE;
    }
   
    /**
     * Set cookie information
     *
     * @access public
     * @param string key
     * @param mixed value
     * @param integer expire
     * @param string path
     * @param string domain
     * @param boolean secure
     * @param boolean httponly
     */
    public function set( $key, $value, $expire = 0, $path = '', $domain = '', $secure = FALSE, $httponly = TRUE) {       
        // Make sure they aren't trying to set a reserved word
        if(!in_array($key, self::$_reserved)) {       
            // If $key is in array format, change it to string representation
            $key = self::_scrubKey($key, true);
            // Store the cookie
            setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);   
        }
        // Otherwise, throw an error
        else  {
            throw new customException(Error::warning('Could not set key -- it is reserved.', __CLASS__));
        }
    }
   
    /**
     * Converts strings to arrays (or vice versa if toString = true)
     *
     * @access private
     * @param string key
     * @param boolena toString
     */
    private static function _scrubKey($key, $toString = FALSE) {
        if($toString) {
            // If $key is in array format, change it to string representation
            if(is_array($key)) {
                // Grab key/value pair
                list ($k, $v) = each($key);
                // Set string representation
                $key = $k . '[' . $v . ']';
            }
        }
        // Converting from string to array
        elseif(!is_array($key)) {
            // is this a string representation of an array?
            if (preg_match('/([\w\d]+)\[([\w\d]+)\]$/i', $key, $matches)) {
                // Store as key/value pair
                $key = array($matches[1] => $matches[2]);
            }
        }
        return $key;
    }
}
?>
