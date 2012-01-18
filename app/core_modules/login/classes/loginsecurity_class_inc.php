<?php
/**
 *
 * A login security class
 *
 * Assist with various login security operations, including sanatizing username
 * and password and other login data.
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
 * @package   login
 * @author    Multiple contributors
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://www.chisimba.com
 * 
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
 * A login security class
 *
 * Assist with various login security operations, including sanatizing username
 * and password and other login data.
*
* @author Derek Keats
* @package canvas
*
*/
class loginsecurity extends object
{

    /**
    *
    * Intialiser for the Loginops class. It also checks and prevents
    * attempts to login via the querystring to prevent easy dictionary
    * scans.
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $qs = $_SERVER['QUERY_STRING'];
        // Disallow loging in via the querystring.
        if (strpos($qs,"username=") || strpos($qs,"password=")) {
            $objLanguage = $this->getObject('language', 'language');
            $er = $objLanguage->languageText(
              "mod_security_noqslogin", "security",
              "Logging into this site with the username and password in the URL is prohibited."
            );
            throw new  customException($er);
        }
    }

    /**
     *
     * Get the sanitized username
     *
     * @return string The sanitized username
     * @access public
     *
     */
    public function getUserName()
    {
        $userName = $_REQUEST['username'];
        return $this->sanitize($userName);
    }

    /**
     *
     * Get the sanitized password
     *
     * @return string The sanitized password
     * @access public
     *
     */
    public function getPassword()
    {
        $password = $_REQUEST['password'];
        return $this->sanitize($password);
    }

    /**
     *
     * Get any sanitized variable
     *
     * @var string $variableName The name of the variable to get
     * @var string $defaultValue The default value to assign the variable
     * @return string The sanitized variable
     * @access public
     *
     */
    public function getVariable($variableName, $defaultValue=NULL)
    {
        $value = $_REQUEST[$variableName];
        if ($value == NULL || $value == "" || $value==FALSE) {
            return $defaultValue;
        } else {
            return $this->sanitize($value);
        }
    }

    /**
     *
     * Get the sanitized username
     *
     * @return string The sanitized username
     * @access public
     *
     */
    public function sanitize($value)
    {
        $value = stripslashes($value);
        $value = strip_tags($value);
        $value = str_replace("'", NULL, $value);
        $value = str_replace("SELECT", NULL, $value);
        $value = str_replace("INSERT", NULL, $value);
        return $value;
    }

}
?>