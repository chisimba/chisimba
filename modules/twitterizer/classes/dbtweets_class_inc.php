<?php
/**
 * twitterizer dbtable derived class
 *
 * Class to interact with the database for the twitterizer
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
 * @package   twitterizer
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2009 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (! /**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check

class dbtweets extends dbTable {
    /**
     * Constructor
     *
     */
    public function init() {
        parent::init ( 'tbl_twitterizer' );
        $this->objSysConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
    }

    public function addRec($recarr) {
        if($recarr['tweet'] == NULL || $recarr['screen_name'] == NULL || empty($recarr['tweet']) || empty($recarr['screen_name'])) {
            return FALSE;
        }
        else {
            return $this->insert($recarr);
        }
    }

    public function getRange($start, $num) {
        return $range = $this->getAll ( "ORDER BY tstamp DESC LIMIT {$start}, {$num}" );
        return array_reverse($range);
    }

    public function getMsgRecordCount () {
        return $this->getRecordCount();
    }
    
    public function getUserCount () {
        $users = $this->getArray("SELECT DISTINCT screen_name FROM tbl_twitterizer ORDER BY id");
        return count($users);
    }

    public function getAllPosts() {
        return $this->getAll("ORDER BY createdat ASC");
    }

    public function getSingle($id) {
        return $this->getAll("WHERE id = '$id'");
    }

    public function searcTable($keyword) {
        return $this->getAll("WHERE tweet LIKE '%%$keyword%%' OR screen_name LIKE '%%$keyword%%' OR name LIKE '%%$keyword%%' ");
    }
    
    public function getTweetCount() {
    	return $this->getArray("SELECT count(id) as cnt FROM tbl_twitterizer");
    }
    
    public function getDateRange($lastTimeCheck){    	
    	
    	return $this->getArray("SELECT * FROM tbl_twitterizer WHERE tstamp > $lastTimeCheck ORDER BY tstamp DESC");
    }

}
?>