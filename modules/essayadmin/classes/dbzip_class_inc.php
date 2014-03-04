<?php
/* ----------- zip class extends dbTable for tbl_essay_zipfiles------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_essay_zipfiles
*/
class dbzip extends dbTable
{
	//variable holding the user object
	public $objUser;
	
	//variable holding the language object
	public $objLanguage;
	
	//administrative evaluation
	public $isAdmin;

	//lecturer evaluation
	public $isLecturer;
	
	//database table
	public $table;
	
	//the context objects
	public $objContext;
	public $objContextCode;
	
	/**
	* initialization function making declaration of the
	* whatsnew table to be used within the module
	*/
	
	public function init()
	{
		// Set the database table for this class
		parent::init('tbl_essay_zipfiles');
		$this->table = 'tbl_essay_zipfiles';
		// Get an instance of the user object
		$this->objUser = & $this->getObject('user', 'security');
		$this->objPerm =& $this->getObject('contextcondition', 'contextpermissions');
		$this->isAdmin=$this->objUser->isAdmin();
		$this->isLecturer=$this->objPerm->isContextMember('Lecturers');
		// Get an instance of the language object
		$this->objLanguage = & $this->getObject('language', 'language');
		//context
		$this->objContext =& $this->getObject('dbcontext', 'context');
		$this->objContextCode = $this->objContext->getContextCode();
	}

		
	/**
	* function to read all the files from the table
	*/

	public function getAllFiles()
	{
		$sql = 'SELECT * FROM '.$this->table;
		$ar=$this->getArray($sql);
		if($ar) {
			return $ar;
		} else {
			return FALSE;
		}
	}

	/**
	* function to insert data into the database
	* the parsed parameters are
	*/
    public function insertData($filename,$filepath,$fileurl)
    {
		$this->insert(array(
			'filename' => $filename,
			'filepath' => $filepath,
			'fileurl' => $fileurl,
			'creatorId' => $this->objUser->userId(),
			'dateCreated' => date("Y/m/d H:i:s")));
    }

	/**
	* function to remove information from the table
	* @param: id - information is removed based on it's id or
	*/
	public function deleteById($id)
	{
        if ($this->isAdmin || $this->isLecturer) {
            $this->delete('id', $id);
            return TRUE;
        } else {
            return FALSE;
        }		
	}

	/**
	* function to obtain the difference between two submitted dates
	*/

	public function getDateDifference($date1,$date2)
	{
		//this avails the difference between the two dates in seconds
		$difference=0;
		//initialization
		$xdate1=0;
		$xdate1=strtotime($date1);
		$xdate2=0;
		$xdate2=strtotime($date2);
		$difference=$xdate1-$xdate2;
		/**
		* 1 day = (1*24)hours
		* so anything in excess of 24hours which in essence is 86,400 seconds
		* should be deleted
		* hence all evaluations shall be based on whether $difference > 86,400
		*/
		return $difference;
	}

	/**
	* function to check and remove old files
	*/

	public function deleteOldFiles()
	{
		$filesGeneralArray=array();
		$filesGeneralArray=$this->getAllFiles();
		if(!empty($filesGeneralArray)) {
			foreach($filesGeneralArray as $farray) {
				/**
				* if the amount of time between today and the dateCreated
				* is greater than 1 days (86,400 seconds)
				* delete the information from the row
				*/
				if($this->getDateDifference(date("Y-m-d H:i:s"),$farray["dateCreated"])>86400) {
					//remove the db reference
					$this->deleteById($farray["id"],$this->objUser->isAdmin());
					//remove the actual file
					if(is_file($farray["filepath"])) {
						unlink($farray["filepath"]);
					}
				}
			}
		}
	}
}
?>