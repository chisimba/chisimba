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


class animal_population extends dbtable {
	
  
	public function init() {
		try {
			parent::init('tbl_ahis_animal_population_census');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
}