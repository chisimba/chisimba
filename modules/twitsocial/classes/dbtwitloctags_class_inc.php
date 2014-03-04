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
class dbtwitloctags extends dbtable {

    public $objLanguage;

    public function init() {
        parent::init('tbl_twitsocial_locs');
    }

    public function checkLoc($loc) {
        $exists = $this->valueExists('location', $loc, 'tbl_twitsocial_locs');
        return $exists;
    }

    public function checkRow($loc) {
        return $this->getAll("WHERE location = '$loc'");
    }

    public function insertLoc($loctag) {
        if($loctag['location'] == NULL || $loctag['location'] == '') {
            return;
        }
        $this->insert($loctag);
    }

    public function getRecs() {
        return $this->getAll();
    }
}