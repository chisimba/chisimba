<?php
/**
 *
 * Database access for SAHRIS collections
 *
 * Database access for Image Vault. It allow access to the table
 *  which contains a list of posted images for the gallery.
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
 * @package   sahriscollections
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
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
 * Database access for SAHRIS collections
 *
 * Database access for Simple gallery. It allow access to the table
 *  which contains a list of posted images for the gallery.
 *
 * @package   sahriscollections
 * @author    Paul Scott <pscott@uwc.ac.za>
 *
 */
class dbsahriscollections extends dbtable
{

    /**
     *
     * Intialiser for the simpleblog database connector
     * @access public
     * @return VOID
     *
     */
    public function init()
    {
        //Set the parent table to our default table
        parent::init('tbl_sahriscollections_collections');
    }     
     
     /**
      * Method to insert a collection to the collections table
      *
      * @param  integer $userid
      * @param  array   $insarr
      * @return string id
      */
    public function insertCollection($insarr)
    {   
        $this->changeTable('tbl_sahriscollections_collections');
        $insarr['datecreated'] = $this->now();
        $collid = $this->insert($insarr);
        return $collid;
    }
    
    public function getCollectionNames() {
        $this->changeTable('tbl_sahriscollections_collections');
        return $this->getAll();
    }
    
    public function getCollById($id) {
        $this->changeTable('tbl_sahriscollections_collections');
        return $this->getAll("WHERE id = '$id'");
    }
    
    public function getCollByName($name) {
        $this->changeTable('tbl_sahriscollections_collections');
        $det = $this->getAll("WHERE collname = '$name'");
        if(empty($det)) {
            return NULL;
        }
        else {
            return $det[0]['id'];
        }
    }
    
    public function getCollectionsBySiteId($sid) {
        $this->changeTable('tbl_sahriscollections_collections');
        return $this->getAll("WHERE siteid = '$sid'");
    }
    
    public function getCollCountBySite($sid) {
        $this->changeTable('tbl_sahriscollections_collections');
        return $this->getRecordCount("WHERE siteid = '$sid'");
    }
    
    public function insertRecord($insarr) {
        $this->changeTable('tbl_sahriscollections_items');
        $insarr['datecreated'] = $this->now();
        $insarr['obj_ts'] = time();
        return $this->insert($insarr);
    }
    
    public function updateRecord($id, $arr) {
        $this->changeTable('tbl_sahriscollections_items');
        $this->update('id', $id, $arr);
    }
    
    public function deleterecord($recordid) {
        $this->changeTable('tbl_sahriscollections_items');
        return $this->delete('id', $recordid, 'tbl_sahriscollections_items');
    }
    
    public function getSingleRecord($acno, $coll) {
        $this->changeTable('tbl_sahriscollections_items');
        return $this->getAll("WHERE accno = '$acno' AND collectionname = '$coll'");
    }
    
    public function getSingleRecordById($id) {
        $this->changeTable('tbl_sahriscollections_items');
        return $this->getAll("WHERE id = '$id'");
    }
    
    public function getAbsAllPosts() {
        $this->changeTable('tbl_sahriscollections_items');
        return $this->getAll();
    }
    
    public function getCollRecords($collid) {
        $this->changeTable('tbl_sahriscollections_items');
        return $this->getAll("WHERE collectionid = '$collid'");
    }
    
    public function getCollRecordCount($collid) {
        $this->changeTable('tbl_sahriscollections_items');
        return $this->getRecordCount("WHERE collectionid = '$collid'");
    }
    
    public function getRange($collid, $start, $num) {
        $this->changeTable('tbl_sahriscollections_items');
        $range = $this->getAll ( "WHERE collectionid='$collid' ORDER BY puid ASC LIMIT {$start}, {$num}" );
        return  $range;
    }
    
    public function countItemsInSite($sid) {
        $this->changeTable('tbl_sahriscollections_items');
        return $this->getRecordCount("WHERE siteid = '$sid'");
    }
    
    
    public function searchItems($q) {
        $this->changeTable('tbl_sahriscollections_items');
        $res = $this->getAll("WHERE physdesc LIKE '%%$q%%' OR accno LIKE '%%$q%%' OR objname LIKE '%%$q%%' OR objtype LIKE '%%$q%%'");
        return $res;
    }
    
    public function addSiteData($siteins) {
        $this->changeTable('tbl_sahriscollections_sites');
        return $this->insert($siteins);
    }
    
    public function getSiteByName($sitename) {
        $this->changeTable('tbl_sahriscollections_sites');
        $det = $this->getAll("WHERE sitename = '$sitename'");
        // var_dump($det);
        if(empty($det)) {
            return NULL;
        }
        else {
            return $det[0]['id'];
        }
    }
    
    public function getSiteDetails($sid) {
        $this->changeTable('tbl_sahriscollections_sites');
        $det = $this->getAll("WHERE id = '$sid'");
        return $det;
    }
    
    public function getAllSites() {
        $this->changeTable('tbl_sahriscollections_sites');
        return $this->getAll();
    }
    
    public function updateSiteInfo($updatearr, $id) {
        $this->changeTable('tbl_sahriscollections_sites');
        $this->update('id', $id, $updatearr, 'tbl_sahriscollections_sites');
        $this->changeTable('tbl_sahriscollections_collections');
        $this->update('siteid', $id, array('sitename' => $updatearr['sitename']), 'tbl_sahriscollections_collections');
        return;
    }
    
    /**
     * Method to get all the posts of objects made within a month
     *
     * @param  mixed  $startdate
     * @param  string $userid
     * @return array
     */
    public function getPostsMonthly($startdate)
    {
        $this->changeTable('tbl_sahriscollections_items');
        $this->objCollOps = $this->getObject('sahriscollectionsops');
        $times = $this->objCollOps->retDates($startdate);
        print_r($times);
        $now = date('r', mktime(0, 0, 0, date("m", time()) , date("d", time()) , date("y", time())));
        echo date('Y,M,d', $startdate);
        $monthstart = $times['mbegin'];
        $prevmonth = $times['prevmonth'];
        $nextmonth = $times['nextmonth'];
        //get the entries from the db
        $filter = "WHERE obj_ts > '$prevmonth' AND obj_ts < '$nextmonth' ORDER BY obj_ts DESC";
        $ret = count($this->getAll($filter));
        return $ret;
    }
    
    public function getMonthCount($ts) {
        $start = $ts;
        $end = $ts+2629743;
        $filter = "WHERE obj_ts > '$start' AND obj_ts < '$end' ORDER BY obj_ts DESC";
        $ret = count($this->getAll($filter));
        return $ret;     
    }
    
    private function changeTable($table) {
        parent::init($table);
    }
    
}
?>
