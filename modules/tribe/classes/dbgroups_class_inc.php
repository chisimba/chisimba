<?php
/**
 * Tribe groups dbtable derived class
 *
 * Class to interact with the database
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
 * @category  chisimba
 * @package   tribe
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 */
class dbgroups extends dbTable {

    public $objUser;
    public $objMembers;

    /**
     * Constructor
     *
     */
    public function init() {
        parent::init ( 'tbl_tribe_groups' );
        $this->objUser = $this->getObject('user', 'security');
        $this->objMembers = $this->getObject('dbgroupmembers');
        $this->dbUsers = $this->getObject('dbusers');
    }

    /**
     * Private method to insert a record to the popularity contest table as a log.
     *
     * This method takes the IP and module_name and inserts the record with a timestamp for temporal analysis.
     *
     * @param array $recarr
     * @return string $id
     */
    public function addRecord($insarr, $jid) {
        parent::init ( 'tbl_tribe_groups' );
        if(!$this->objUser->userId()) {
            $userid = $this->dbUsers->getUserIdfromJid($jid);
        }
        else {
            $userid = $this->objUser->userId();
        }
        $insarr['createdat'] = $this->now();
        $insarr['userid'] = $userid;
        $insarr['status'] = 1;

        if($this->userExists($jid) === FALSE) {
            // only active users are allowed to create groups...
            return 1;
        }
        // check whether the group name exists
        if($this->groupExists($insarr['groupname'])) {
            return 2;
        }
        else {
            $grpid = $this->insert ( $insarr, 'tbl_tribe_groups' );
            // add the user to the group
            $this->objMembers->addRecord($userid, $grpid, $jid);
            return 3;
        }
    }

    public function inactiveRecord($groupid) {

    }

    public function groupExists($groupname) {
        parent::init ( 'tbl_tribe_groups' );
        $count = $this->getRecordCount ( "WHERE groupname = '$groupname'" );
        if ($count > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function userExists($jid) {
        $this->_changeTable('tbl_tribe_users');
        $count = $this->getRecordCount ( "WHERE jid = '$jid'" );
        if ($count > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getActive() {
        parent::init ( 'tbl_tribe_groups' );
        return $this->getAll("WHERE status = 1");
    }

    public function getNoActive() {
        parent::init ( 'tbl_tribe_groups' );
        return count($this->getAll("WHERE status = 1"));
    }

    private function _changeTable($table) {
        return parent::init($table);
    }

    public function getLastPublic($number) {
        return $this->getAll("WHERE privacy = 'public' AND status = '1' LIMIT {$number}");
    }

    public function getLastPrivate($number) {
        return $this->getAll("WHERE privacy = 'private' AND status = '1' LIMIT {$number}");
    }

    public function getLastAll($number) {
        return $this->getAll("WHERE status = '1' LIMIT {$number}");
    }

    public function getGroupInfo($groupname) {
        $res = $this->getAll("WHERE groupname = '$groupname'");
        return $res[0];
    }

}
?>