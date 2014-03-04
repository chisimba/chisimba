<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
die( "You cannot view this page directly" );
}
/**
* This class wraps dbtable to act on the table tbl_lrs_users
*
* @copyright (c) 2007, AVOIR (http://avoir.uwc.ac.za)
* @license GNU/GPL
* @package lrs
* @version $Id: dbuserreg_class_inc.php,v 1.1 2007/09/27 13:01:56 nic Exp $
* @author Nic Appleby
*/

class dbuserreg extends dbTable
{
	function init() {
		parent::init('tbl_award_users');
	}
	
	function insertOrUpdate($userId,$tuId) {
		if ($this->valueExists('userid',$userId)) {
			$this->update('userid',$userId,array('tuid'=>$tuId));
		} else {
			$this->insert(array('userid'=>$userId,'tuid'=>$tuId));
		}
	}
	
}

?>