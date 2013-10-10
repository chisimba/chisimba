<?php
/* ----------- data class to create templates during ajax calls ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class to generate page templates of wage conditions during ajax calls
* @author Nic Appleby
*/

class wagedefault extends dbTable {
	
	function init() {
		parent::init('tbl_award_default_wage');
	}

	function updateContinuity($continuity) {
		parent::init('tbl_award_default_continuity');
		$this->update('id',1,array('continuity'=>$continuity));
		parent::init('tbl_award_default_wage');
	}
	
	function getContinuity() {
		parent::init('tbl_award_default_continuity');
		$res = $this->getRow('id',1);
		parent::init('tbl_award_default_wage');	
		return $res;
	}
	
}
?>