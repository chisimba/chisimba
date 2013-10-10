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


class vacinventory extends dbtable {
	
  
	public function init() {
		try {
			parent::init('tbl_ahis_vacinventory');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	public function getData($month,$year,$val,$filter,$district){
	
	
	$sql = $this->getAll("WHERE month <='$month' AND year='$year' AND partname='$district'");
	$vdata = array();
	$value =$val;
		if($filter == "convac"){
   foreach($sql as $data){

   $value = $value+$data['condconvac'];
   
   }}else{
   
   
   foreach($sql as $data){

   $value = $value+$data['condprovac'];
   
   }
   
   }

	return $value;
	}
	
	public function getCon($month,$year,$district){
	
		$sql = $this->getAll("WHERE month <='$month' AND year='$year' AND partname='$district'");
		return $sql;
	
	}	
	
}