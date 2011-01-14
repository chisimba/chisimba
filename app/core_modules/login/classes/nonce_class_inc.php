<?php
/**
 *
 * A login nonce generation and use class
 *
 * Assist with various login security operations by generating a nonce
 * to ensure time and humber limited logins.
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
class nonce extends object
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
        $this->objDb = $this->getObject('dbnonce', 'login');
    }

    /**
     *
     * Generate a nonce and use the database class to store it
     *
     * @return string The generated nonce
     * @access public
     *
     */
    public function storeNonce()
    {
        try
        {
            $nonce = $this->generateNonce();
            $this->objDb->storeNonce($nonce);
            return $nonce;
        } catch (customException $e){
            echo customException::cleanUp($e);
            die();
        }

        
    }

    /**
     *
     * Get the number of tries (failed login attempts)
     *
     * @param string $nonce The nonce to lookup
     * @return integer The number of tries
     * @access public
     * 
     */
    public function getTries($nonce)
    {
        $tries = $this->objDb->getTries($nonce);
        return $tries;
    }

    // REMOVE--------------------------------------------------------__????????
    public function checkNonce($nonce)
    {
        $sql = 'SELECT * FROM tbl_login_nonce WHERE nonce=\'' . $nonce . '\'';
        $ar = $this->getArrayWithLimit($sql,0,1);
        return $ar['tries'];
    }

    /**
     *
     * See if the session has logins disabled due to failed attempts
     *
     * @return boolean TRUE|FALSE
     * @access public
     *
     */
    public function checkEnabledBySession()
    {
        return $this->objDb->checkEnabledBySession();
    }

    /**
     *
     * See if the nonce has logins disabled due to failed attempts
     *
     * @return boolean TRUE|FALSE
     * @access public
     * 
     */
    public function disableNonce($id)
    {
        return $this->objDb->disableNonce($id);
    }


    /**
     *
     * Generate a new nonce
     *
     * @return string The generated nonce
     * @access public
     * 
     */
    public function generateNonce()
    {
        $nonce = md5($_SERVER['REQUEST_URI'] . microtime() .  mt_rand());
        return $nonce;
    }

    /**
     *
     * Delete a nonce
     *
     * @param string $nonce  The nonce to delete
     * @access public
     * @retrun VOID
     * 
     */
    public function deleteNonce($nonce)
    {
        $this->objDb->deleteNonce($nonce);
    }

}
?>