<?php
/**
 *
 * Sanatize data to prevent common hacks
 *
 * Sanatize data to prevent common hacks such as SQL injection, 
 * JavaScript injection, etc
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
* Sanatize data to prevent common hacks
*
* Sanatize data to prevent common hacks such as SQL injection, 
* JavaScript injection, etc
*
* @author Derek Keats
* @package canvas
*
*/
class loginsecurity extends object
{
    /**
    *
    * @var string object Holds the language object
    * @access public
    * 
    */
    public $objLanguage;
    
    /**
    *
    * @var string object Holds the language object
    * @access public
    * 
    */
    public $arrayBanned=array("SELECT", "SHOW TABLES", "SHOW DATABASES",
          "INSERT", "DELETE", "JAVASCRIPT", "DROP");

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
        $this->objLanguage = $this->getObject('languagetext', 'language');
    }
    
    /**
     *
     * Run a full sanity scan of the querystring and submitted form elements.
     * 
     * @param string array $arrayOfFormKeyValues An array of forme value=>key pairs
     * @param type $arrayBanned 
     * 
     */
    public function fullSanityScreen($arrayOfFormKeyValues, $arrayBanned=FALSE)
    {
        if (!$arrayBanned) {
            $arrayBanned = $this->arrayBanned;
        }
        $this->disallowQueryStringLogins();
        $this->disallowQuerystringFormElements($arrayBanned);
        $bannedCount = 0;
        foreach ($arrayOfFormKeyValues as $key => $stringToCheck) {
            $bannedCount = $bannedCount + 
              $this->checkElements($stringToCheck, $arrayBanned);
        }
        if ($bannedCount >= 1) {
            $er = $this->objLanguage->languageText(
              "mod_security_illegaltermsfm", "security",
              "Illegal terms in a form submission|Illegal terms in a form "
              . "submission. Some fields contain Javascript, SQL or other "
              . "insecure items."
            );
            throw new customException($er);
            exit();
        }
        
    }
    
    /**
     * 
     * Disallow logins via the querystring as this is insecure.
     * 
     * @return void
     * @access public
     * 
     */
    public function disallowQueryStringLogins()
    {
        $qs = $_SERVER['QUERY_STRING'];
        // Disallow loging in via the querystring.
        if (strpos($qs,"username=") || strpos($qs,"password=")) {
            $this->objLanguage = $this->getObject('language', 'language');
            $er = $this->objLanguage->languageText(
              "mod_security_noqslogin", "login",
              "Logging into this site with the username and password in the URL is prohibited."
            );
            throw new  customException($er);
            exit();
        }
    }
    
    /**
     * 
     * Disallow querystring submission of form elements by sending an
     * array of banned words/terms
     * 
     * @param string array An array of things to ban
     * @return boolean|void FALSE|Throw an error
     * @access public
     * 
     */
    public function disallowQuerystringFormElements($arrayBanned)
    {
        $bannedCount = 0;
        $stringToCheck= $_SERVER['QUERY_STRING'];
        $bannedCount = $this->checkElements($stringToCheck, $arrayBanned);
        if ($bannedCount >=1) {
            $er = $this->objLanguage->languageText(
              "mod_security_illegaltermsfm", "security",
              "Illegal terms in a form submission|Illegal terms in a form "
              . "submission. Some fields contain Javascript, SQL or other "
              . "insecure items."
            );
            throw new customException($er);
            exit();
        }
    }
    
    /**
     *
     * Check a string for banned elements and return a count of those found
     * 
     * @param string array $arrayBanned An array of banned words
     * @param string $stringToCheck The string to check
     * @return int A count of the found items
     * 
     */
    public function checkElements($stringToCheck, $arrayBanned=FALSE)
    {
        if (!$arrayBanned) {
            $arrayBanned = $this->arrayBanned;
        }
        $bannedCount = 0;
        $stringToCheck = strtoupper($stringToCheck);
        foreach($arrayBanned as $bannedWord) {
            $bannedWord = strtoupper($bannedWord);
            if (strpos($stringToCheck,$bannedWord)) {
                $bannedCount++;
            }
        }
        return $bannedCount;
    }
    
    /**
     *
     * Sanitize a string, cleaning up things that might be attacks,
     * manual or automated
     *
     * @param string $value The value to be checked
     * @param string array $arrayBanned An optional array of banned terms
     * @param string 
     * @return string The sanitized username
     * @access public
     *
     */
    public function sanitize($value, $arrayBanned=FALSE, $stripQuotes=TRUE)
    {
        if (!$arrayBanned) {
            $arrayBanned = $this->arrayBanned;
        }
        $value = stripslashes($value);
        $value = strip_tags($value);
        foreach($arrayBanned as $term) {
            $valueTest = strtoupper($value);
            if (strstr($valueTest, $term)) {
                str_ireplace($term, 'XXXXXX', $value);
            }
        }
        if ($stripQuotes == TRUE) {
            $value = str_replace("'", NULL, $value);
            $value = str_replace('"', NULL, $value);
        }
        return $value;
    }
}
?>