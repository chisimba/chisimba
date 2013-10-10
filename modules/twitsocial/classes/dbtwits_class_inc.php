<?php
// security check - must be included in all scripts
if (! /**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
/**
 *
 */
/**
 * @access     public
 */
class dbtwits extends dbtable {

    /**
     *
     * @var langauge an object reference.
     */
    public $objLanguage;

    public $objDbTwitSn;
    public $objDbTwitLoc;
    public $objTC;

    /**
     * Method that initializes the objects
     *
     * @access private
     * @return nothing
     */
    public function init() {
        parent::init('tbl_twitsocial');
        $this->objDbTwitLoc = $this->getObject('dbtwitloctags');
        $this->objDbTwitSn = $this->getObject('dbtwitsntags');
        $this->objTC = $this->getObject('tagcloud', 'utilities');
    }


    public function saveRecords($arr) {
        if(!is_array($arr)) {
            return $this->insert($arr);
        }
        foreach ($arr as $rec) {
            $this->insert($rec);
            // check that the location is not already in the location tags table
            $locExists = $this->objDbTwitLoc->checkLoc($rec['location']);
            if($locExists === TRUE) {
                $lo = $rec['location'];
                $lrec = $this->objDbTwitLoc->checkRow($lo);
                $lrec = $lrec[0];
                // update the table and add +1 to the weight
                $weight = intval($lrec['weight']) + 1;
                $this->update('id', $lrec['id'], array('location' => $lo, 'weight' => $weight), 'tbl_twitsocial_locs');
            }
            else {
                // add the location to the locs tag table
                $loctag = array('location' => $rec['location'], 'weight' => 1);
                $this->objDbTwitLoc->insertLoc($loctag);
            }
            // check that the screen name exists or not in the sn tags table
            $snExists = $this->objDbTwitSn->checkSn($rec['screen_name']);
            if($snExists === TRUE) {
                $sn = $rec['screen_name'];
                $srec = $this->objDbTwitSn->checkRow($sn);
                // update the table and add +1 to the weight
                $srec = $srec[0];
                $weight = intval($srec['weight']) + 1;
                $this->update('id', $srec['id'], array('screen_name' => $sn, 'weight' => $weight), 'tbl_twitsocial_sn');
            }
            else {
                // add the location to the locs tag table
                $sntag = array('screen_name' => $rec['screen_name'], 'weight' => 1);
                $this->objDbTwitSn->insertSn($sntag);
            }


        }
        return;
    }


    public function getUnchecked() {
        $ret = $this->getAll("WHERE checked = 0");
        return $ret;
    }

    public function setCheck($id) {
        $this->update('id', $id, array('checked' => 1), 'tbl_twitsocial');
    }

    public function searchLocations($location = NULL) {
        $locs = $this->getAll("WHERE location like '%$location%%'");
        return $locs;
    }

    public function getLocationTagWeight($location) {
        $location = urlencode($location);
        $count = $this->getArray("SELECT count(*) as RC from tbl_twitsocial WHERE location = '$location'"); //$this->getRecordCount("WHERE location = '$location'");
        // var_dump($count); die();
        return $count[0]['rc'];
    }

    public function locTagCloud() {
        $locs = $this->objDbTwitLoc->getRecs();
        if(empty($locs)) {
            return NULL;
        }
        foreach ($locs as $loc) {
            // create the url
            $url = $this->uri(array(
                'action' => 'viewlocusers',
                'tag' => $loc['location'],
            ));
            // get the count of the tag (weight)
            $weight = intval($loc['weight']);
            $weight = $weight*1000;
            $tag4cloud = array(
                'name' => $loc['location'],
                'url' => $url,
                'weight' => $weight,
                'time' => $this->now(),
            );
            $ret[] = $tag4cloud;
        }

        return $this->objTC->buildCloud($ret);
    }

    public function getUsersByLoc($loc) {
        $users = $this->getAll("WHERE location = '$loc'");
        /*var_dump($users);
        //return $users;
        $users = array_unique($users);
        var_dump($users);
        die();*/

        return $users;
    }
} //end of class
?>