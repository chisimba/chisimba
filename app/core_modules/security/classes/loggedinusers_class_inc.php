<?php
 /**
 * Loggedinusers class
 * 
 * This class keeps track of which users are logged into the system at any given time.
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
 * 
 * @category  Chisimba
 * @package   security
 * @author AVOIR
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Logged in users table.
*/
class loggedInUsers extends dbTable
{
    public $objConfig;
    private $systemTimeOut;

    public function init()
    {
        parent::init('tbl_loggedinusers');
        $this->objConfig=$this->getObject('altconfig','config');
        $this->systemTimeOut=$this->objConfig->getsystemTimeout();
    }

    /**
    * Insert a record.
    * @param string $userId The userId of the user logging in
    */
    public function insertLogin($userId) {
        // Delete old logins
        $sql="DELETE FROM tbl_loggedinusers
            WHERE
                (userid = '$userId')
                AND ((CURRENT_TIMESTAMP-whenlastactive)>'{$this->systemTimeOut}')";
        $this->query($sql);
        // Update the tbl_loggedinusers table
        $ipAddress=$_SERVER['REMOTE_ADDR'];
        $sessionId=session_id();
        $contextCode="lobby";
        $theme="default";
        $myDate=date('Y-m-d H:m:s');
        $isInvisible='0';
        $sql="
            INSERT INTO tbl_loggedinusers (
                                id,
                userid,
                ipaddress,
                sessionid,
                whenloggedin,
                 whenlastactive,
                isinvisible,
                coursecode,
                themeused
            )
            VALUES (
                                '".date('YmdHis')."',
                '$userId', 
                '$ipAddress', 
                '$sessionId', 
                CURRENT_TIMESTAMP, 
                    CURRENT_TIMESTAMP, 
                '$isInvisible', 
                '$contextCode', 
                '$theme'
            )";
        $this->query($sql);
    }

   /**
   * Logout user from the site. The method deletes
   * the user from the database table tbl_loggedinusers, destroys
   * the session, and redirects the user to the index page,
   * index.php.
   * @param string $userId The userId of the logged in user
   */
   public function doLogout($userId) {
        $sql="DELETE FROM tbl_loggedinusers
        WHERE
            userid='$userId'
            AND sessionid ='".session_id()."'
        ";
        $this->query($sql);
   }

    /**
    * Update the current user's active timestamp.
    */
    public function doUpdateLogin($userId,$contextCode='lobby')
    {
           $sql="UPDATE tbl_loggedinusers
        SET 
              whenlastactive = CURRENT_TIMESTAMP, 
            coursecode='$contextCode'  
        WHERE
              userid='$userId'
            AND sessionid ='".session_id()."'
        ";
        $this->query($sql);
   }

    /**
     * Return the time logged in.
     */
    public function getMyTimeOn($userId)
    {
        $sql="SELECT (whenlastactive - whenloggedin)/100 AS activetime FROM tbl_loggedinusers
        WHERE
            userid='$userId'
            AND sessionid='".session_id()."'
        ";
        $results = $this->getArray($sql);
        if (!empty($results)) {
            $timeActive=intval($results[0]['activetime']);
        }
        else {
            $timeActive=0;
        }
           return $timeActive;
   }

    /**
    * Count active users.
    */
    public function getActiveUserCount()
    {
        $sql="SELECT COUNT(id) AS usercount FROM tbl_loggedinusers";
        $results = $this->getArray($sql);
        if (!empty($results)) {
            $activeUserCount=intval($results[0]['usercount']);
        }
        else {
            $activeUserCount=0;
        }
        return $activeUserCount;
    }

    /**
    * Return how long since the user was last active.
    * @param string $userId
    */
    public function getInactiveTime($userId)
    {
        $sql="SELECT
            ((CURRENT_TIMESTAMP-whenlastactive)/100) AS inactivetime
        FROM
            tbl_loggedinusers
        WHERE
            userid='$userId'
            AND sessionid='".session_id()."'
        ";
        $results = $this->getArray($sql);
        if (!empty($results)) {
            $inactiveTime=intval($results[0]['inactivetime']);
        }
        else {
            $inactiveTime=10+$this->systemTimeOut;
        }
        return $inactiveTime;
    }

    /**
    * Method to clear inactive users
    */
    public function clearInactive()
    {        
        $sql="DELETE FROM tbl_loggedinusers 
        WHERE 
            ((CURRENT_TIMESTAMP-whenlastactive)/100) > '{$this->systemTimeOut}'
        ";
        $this->query($sql);
    }

    /**
    * Method to check if a specified userId is online
    * @param string $userId
    * returns TRUE|FALSE
    */
    public function isUserOnline($userId)
    {
        $sql="SELECT COUNT(userid) AS count FROM tbl_loggedinusers WHERE userid='$userid'";
          $results = $this->getArray($sql);
        if (!empty($results)) {
            if ($results[0]['count']>0) {
                return true;
            }
        }
        else {
            return false;
        }
    }

}
?>
