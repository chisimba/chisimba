<?php
/**
 * Presence mxitpress dbtable derived class
 *
 * Class to interact with the database for the mxitpress module
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
 * @package   mxitpress
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2010 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       api
 */
class dbmppresence extends dbTable {
    /**
     * Constructor
     *
     */
    public function init() {
        parent::init ( 'tbl_mxitpress_presence' );
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
    }

    public function updatePresence($userarr) {
        //split user and user agent
        $userSplit = explode ( "/", $userarr ['from'] );
        // check if user exists in msg table
        $status = $this->userExists ( $userSplit [0] );
        $times = $this->now ();
        $insarr ['datesent'] = $times;
        $insarr ['person'] = $userSplit [0];
        $person = $insarr ['person'];
        if (isset ( $userarr ['type'] )) {
            $insarr ['status'] = $userarr ['type'];
        } else {
            $insarr ['status'] = 'available';
        }
        $insarr ['presshow'] = $userarr ['show'];
        if(isset($userSplit [1])) {
            $insarr ['useragent'] = $userSplit [1];
        }
        else {
            $insarr ['useragent'] = 'Unknown';
        }
        if ($status === FALSE) {
            $this->insert ( $insarr, 'tbl_mxitpress_presence' );
        } else {
            // update the presence info for this user
            $this->update ( 'id', $status [0] ['id'], $insarr, 'tbl_mxitpress_presence' );
        }
    }

    /**
     * Private method to insert a record to the mxitpress table as a log.
     *
     * This method takes the IP and module_name and inserts the record with a timestamp for temporal analysis.
     *
     * @param array $recarr
     * @return string $id
     */
    private function addRecord($insarr) {

        return $this->insert ( $insarr, 'tbl_mxitpress_presence' );
    }

    public function userExists($user) {
        $count = $this->getRecordCount ( "WHERE person = '$user'" );
        if ($count > 0) {
            return $this->getAll ( "WHERE person = '$user'" );
        } else {
            return FALSE;
        }
    }

    public function getPresence($jid) {
        $userSplit = explode ( "/", $jid );
        $res = $this->getAll ( "WHERE person = '$userSplit[0]'" );
        if (! empty ( $res )) {
            return $res [0] ['presshow'];
        } else {
            return NULL;
        }
    }

}
?>
