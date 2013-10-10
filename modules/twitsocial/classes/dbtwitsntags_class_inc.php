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
class dbtwitsntags extends dbtable {

    public $objLanguage;

    public function init() {
        parent::init('tbl_twitsocial_sn');
    }

    public function checkSn($sn) {
        $exists = $this->valueExists('screen_name', $sn, 'tbl_twitsocial_sn');
        return $exists;
    }

    public function checkRow($name) {
        return $this->getAll("WHERE screen_name = '$name'");
    }

    public function insertSn($sntag) {
        if($sntag['screen_name'] == NULL || $sntag['screen_name'] == '') {
            return;
        }
        $this->insert($sntag);
    }
}