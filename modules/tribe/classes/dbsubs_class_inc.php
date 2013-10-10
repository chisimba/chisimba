<?php
/**
 * Subscriptions dbtable derived class
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
        parent::init ( 'tbl_tribe_subs' );
    }

    public function followUser($followarr) {
        $followarr['datesent'] = $this->now();

        return $this->insert($followarr, 'tbl_tribe_subs');
    }

    public function unfollow($followarr) {
        $followarr['datesent'] = $this->now();
        $followid = $followarr['followid'];
        $userid = $followarr['userid'];
        $rec = $this->getAll("WHERE followid = '$followid' and userid = '$userid'");

        return $this->delete('id', $rec[0]['id'], 'tbl_tribe_subs');
    }

    public function checkIfFollow($userid, $followid) {
        $count = $this->getRecordCount("WHERE followid = '$followid' and userid = '$userid'");
        if($count > 0) {
            return TRUE;
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

    public function getFollowers($userid) {
        // my followers are defined by users setting the followid to me...
        $recs = $this->getAll("WHERE followid = '$userid'");
        return $recs;
    }

}
?>
