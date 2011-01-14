<?php
/**
 *
 * A login nonce database class
 *
 * Assist with various login security operations by storing and retrieving a
 * nonce to ensure time and number limited logins.
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
* Database accesss class for Chisimba for the module login
*
* @author Derek Keats
* @package login
*
*/
class dbnonce extends dbtable
{

    /**
    *
    * Intialiser for the login database connector
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        //Set the parent table
        parent::init('tbl_login_nonce');
        $this->clearNonces();
    }

    /**
     *
     * Remove nonces older than 30 minutes. The 30 minute limit means you can only
     * have four login attempts in 30 minutes without verifying that you are
     * human using a captcha
     *
     * @access public
     * @return boolean TRUE|FALSE - The results of the query
     * 
     */
    public function clearNonces()
    {
        $curTime = $this->now()- 360;
        $sql =  "DELETE FROM tbl_login_nonce WHERE datecreated+60 < (NOW() - INTERVAL 30 MINUTE)";
        $this->query($sql);
    }

    /**
     *
     * Check if the nonce has been marked as disabled by looking for the session,
     * normally used when building the login form to make sure that the session
     * is entitled to it.
     *
     * @access public
     * @return int 1 or 0 representing true or false
     *
     */
    public function checkEnabledBySession()
    {
        // We are only interested in the last one.
        $sessionId = session_id();
        $sql = 'SELECT id,enabled FROM tbl_login_nonce WHERE sessionid=\''
           . $sessionId . '\' ORDER BY datecreated DESC LIMIT 1';
        $ar = $this->query($sql);
        if (count($ar) >= 1) {
            $enabled = $ar['0']['enabled'];
        } else {
            // The nonce has timed out
            $enabled = 1;
        }
        
        //die($sessionId . ' = ' . $enabled);
        return $enabled;
        
    }

    /**
     *
     * Save the nonce in the database together with session id and ip address
     *
     * @param string $nonce The generated nonce
     * @return boolean TRUE
     * 
     */
    public function storeNonce($nonce)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $sessionId = session_id();
        try
        {
            $this->insert(array(
                'ipaddress' => $ip,
                'nonce' => $nonce,
                'sessionid' => $sessionId,
                'datecreated' => $this->now(),
                'enabled' => TRUE,
                'tries' => 0));
            return TRUE;
        } catch (customException $e){
            echo customException::cleanUp($e);
            die();
        }
    }

    /**
     *
     * Retrieve and increment the number of tries they have had (failed
     * login attempts)
     *
     * @param string $nonce The nonce to lookup
     * @return integer / boolean The number of tries or FALSE
     *
     */
    public function getTries($nonce)
    {
        $sql = 'SELECT id,tries,nonce FROM tbl_login_nonce WHERE nonce=\'' . $nonce . '\'';
        $ar = $this->getArrayWithLimit($sql,0,1);
        if (count($ar) > 0) {
            $tries = $ar['0']['tries'];
            $id = $ar[0]['id'];
            $tries++;
            $this->updateTries($id, $tries);
            if ($tries > 3) {
                $this->disableNonce($id);
            }
            return $tries;
        } else {
            return FALSE;
        }
    }

    /**
     *
     * Set the enabled flag to 0, thus indicating that the nonce (and session)
     * are disabled.
     *
     * @param string $nonce The nonce to lookup
     * @return boolean FALSE
     *
     */
    public function disableNonce($nonce)
    {
        $ar = array('enabled' => FALSE);
        $this->update("nonce", $nonce, $ar);
        return FALSE;
    }

    /**
     *
     * Update the number of tries (failed logins)
     *
     * @param string $id The database id of the nonce
     * @param integer $tries The number of tries to set
     * @return boolean TRUE
     */
    public function updateTries($id, $tries)
    {
        $ar = array('tries' => $tries);
        $this->update("id", $id, $ar);
        return TRUE;
    }

    /**
     *
     * Remove a nonce
     *
     * @param string $nonce The nonce to remove
     * @return boolean TRUE on success, FALSE if not
     * 
     */
    public function deleteNonce($nonce)
    {
        return $this->delete('nonce', $nonce);
    }
    
}
?>