<?php
/**
 * Class to facilitate connection to the module catalogue table
 * 
 * @author Nic Appleby
 * @version 1.0
 * @copyright GPL UWC 2006
 *
 */

class dbmodcat extends dbtable
{
	/**
	 * Standard init function
	 *
	 */
	public function init() {
		try {
			parent::init('tbl_modcat');
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();	
		}
	}
	
	/**
	 * Function to return array of all known categories
	 *
	 * @return array known categories
	 */
	public function getCategories() {
		try {
			return $this->getArray('SELECT DISTINCT category FROM tbl_modcat ORDER BY category');
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();	
		}
	}
	
	public function getModules($category='all') {
		try {
			if (strtolower($category)=='all') {
				return $this->getAll('ORDER BY modName');
			} else {
				return $this->getAll("WHERE category = '$category' ORDER BY modName");
			} 
		} catch (Exception $e) {
			$this->errorCallback('Caught exception: '.$e->getMessage());
        	exit();	
		}
	}
}
?>