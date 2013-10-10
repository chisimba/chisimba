<?php

/**
 * Class the records the pages a user has visited.
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
 * @package   contextcontent
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright @2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class the records the user sessions.
 *
 * Records the start and end time of a context session. Simply the time a user 
 * joins or leaves a context.
 *
 * @category  Chisimba
 * @package   contextcontent
 * @author    Paul Mungai <paulwando@gmail.com>
 * @copyright @2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 */

class db_learningcontent_activitystreamer extends dbtable
{

    /**
     * Constructor
     */
    public function init()
    {
        parent::init('tbl_learningcontent_sessions');
        $this->objUser =& $this->getObject('user', 'security');
    }
    
    /**
     * Method to add a record.
     *
     * @access public
     * @param string $userId User ID
     * @param string $sessionid session ID
     * @param string $contextCode context Code
     * @param string $sessionstarttime session start time
     * @param string $sessionendtime session end time
     */
   public function addRecord($userId, $sessionid, $contextCode, $sessionstarttime=NULL, $sessionendtime=NULL)
    {
        $row = array();
        $row['userid'] = $userId;
        $row['contextcode'] = $contextCode;
        $row['sessionid'] = $sessionid;
        $row['starttime'] = $sessionstarttime;
        $row['endtime'] = $sessionendtime;

        return $this->insert($row);
    }
    /**
     * Method to Record the Session end time
     *
     * @param string id Record Id of the Page
     * @param string $sessionendtime session end time
     * @return boolean
     */
    public function updatePage($id, $sessionendtime)
    {
        $row = array();
        $row['endtime'] = $sessionendtime;

        return $this->update('id', $id, $row);
    }

   /**
     * Checks if record exists.
     *
     * @access public
     * @param string $id The activitystreamer id.
     * @return boolean
     */
    public function idExists($id)
    {
        return $this->valueExists('id', $id);
    }
    /**
     * Method to check if record exists according to userId, contextItemId and contextCode.
     *
     * @access public
     * @param string $userId User ID
     * @param string $sessionId session Id
     * @param string $contextCode Context Code
     * @return TRUE
     */
    public function getRecord($userId, $sessionId, $contextCode)
    {
        $where = "WHERE userid = '$userId' AND sessionid = '$sessionId' AND contextcode = '$contextCode'";
        $results = $this->getAll($where);
        if (isset($results[0]['id'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }    
    /**
     * Method to retrieve a record id according to userId, contextItemId and contextCode.
     *
     * @access public
     * @param string $userId User ID
     * @param string $contextItemId Context Item Id
     * @param string $contextCode Context Code
     * @return string Record ID
     */
    public function getRecordId($userId, $sessionId, $contextCode)
    {
        $where = "WHERE userid = '$userId' AND sessionid = '$sessionId' AND contextcode = '$contextCode'";
        $results = $this->getAll($where);
        if (isset($results[0]['id'])) {
            return $results[0]['id'];
        } else {
            return FALSE;
        }
    }
    /**
     * Method to delete a record
     * @param string $contextItemId Context Item Id
     */
    function deleteRecord($sessionId)
    {
        // Delete the Record
        $this->delete('sessionid', $sessionId);
    }
}
?>
