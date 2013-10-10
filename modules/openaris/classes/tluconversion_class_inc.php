<?php

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


class tluconversion extends dbtable {
	
  
	public function init() {
		try {
			parent::init('tbl_ahis_tropical_livestock_unit');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	public function getTlu($speciesId,$totspecies){
	
	$sql= $this->getRow('species_name_id',$speciesId);
	 $product=$sql['tlu_factor']*$totspecies;  
  

	return $product;
	}
}