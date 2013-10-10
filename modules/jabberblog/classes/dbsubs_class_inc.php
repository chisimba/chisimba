<?php
/**
 * Subscriptions jabberblog dbtable derived class
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
 * @package   jabberblog
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       jabberblog
 */
class dbsubs extends dbTable {
    /**
     * Constructor
     *
     */
    public function init() {
        parent::init ( 'tbl_jabberblog_subs' );
    }

    /**
     * Private method to insert a record to the popularity contest table as a log.
     *
     * This method takes the IP and module_name and inserts the record with a timestamp for temporal analysis.
     *
     * @param array $recarr
     * @return string $id
     */
    public function addRecord($jid) {
        $insarr = array('jid' => $jid, 'status' => 1, 'datesent' => $this->now());
        if($this->userExists($jid) === FALSE) {
            return $this->insert ( $insarr, 'tbl_jabberblog_subs' );
        }
        else {
            if($this->userExists($jid) === TRUE) {
                $person = $this->getAll("WHERE jid = '$jid'");
                $id = $person[0]['id'];

                return $this->update ( 'id', $id, $insarr, 'tbl_jabberblog_subs' );
            }
            else {
                return FALSE;
            }
        }
    }

    public function inactiveRecord($jid) {
        $actarr = array('jid' => $jid, 'status' => 0, 'datesent' => $this->now());
        if($this->userExists($jid) === TRUE) {
            $person = $this->getAll("WHERE jid = '$jid'");
            $id = $person[0]['id'];

            return $this->update ( 'id', $id, $actarr, 'tbl_jabberblog_subs' );
        }
        else {
            return FALSE;
        }
    }

    public function userExists($jid) {
        $count = $this->getRecordCount ( "WHERE jid = '$jid'" );
        if ($count > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getActive() {
        return $this->getAll("WHERE status = '1'");
    }

    public function getNoSubs() {
        return count($this->getAll("WHERE status = '1'"));
    }
}
?>
