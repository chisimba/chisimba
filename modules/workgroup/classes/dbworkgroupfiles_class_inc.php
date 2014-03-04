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
// end security check


/**
 * Class to handle im elements
 *
 * @author    Wesley Nitsckie
 * @copyright GNU/GPL, AVOIR
 * @package   workgroup
 * @access    public
 */
class dbworkgroupfiles extends dbTable {


	/**
	*Constructor
	*/
	public function init()
	{
		parent::init('tbl_workgroupfiles');
		$this->objUser = $this->getObject('user', 'security');
	}
	
	/**
	* Method to insert a file into a workgroup
	*/
	public function insertFile($fields)
	{
		
		$fields['modifierid'] = $this->objUser->userId();
		$fields['datecreated'] = $this->now();
		$fields['datemodified'] = $this->now();
		
		$this->insert($fields, 'tbl_workgroupfiles');
	}
	
	
	/**
	* Method to get the workgroup files
	*/
	public function getWorkgroupFiles($workgroupId)
	{
		return $this->getAll("WHERE workgroupid='$workgroupId'	ORDER BY datemodified");
			
	}

	/**
	* Method to get the workgroup files
	*/
	public function getFileDetails($fileId)
	{
		return $this->getRow('id', $fileId);
			
	}
	
	/**
	* Method to get the workgroup files
	*/
	public function editWorkgroupFiles($workgroupId, $fields)
	{
			
		$fields['modifierid'] = $this->objUser->userId();	
		$fields['datemodified'] = $this->now();
		
		$this->update('id', $workgroupId, $fields, 'tbl_workgroupfiles');
	}

	
	/**
	* Method to get the workgroup files
	*/
	public function deleteWorkgroupFiles($workgroupId)
	{
			
			$this->delete('id', $workgroupId);
	}

	
}