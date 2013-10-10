<?php
/* ----------- data class extends dbTable for tbl_blog------------*/// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_faq
* @author Jeremy O'Connor,Juliet Mulindwa
* @copyright 2004 University of the Western Cape
*/
class dbWorkgroupUsers extends dbTable
{
    var $objUser;
    /**
    * Constructor method to define the table
    */
    function init()
    {
        parent::init('tbl_workgroup_users');
        //$this->USE_PREPARED_STATEMENTS=True;
        $this->objUser =& $this->getObject('user', 'security');
    }

    /**
    * Return all users in a context
	* @param string The context code
	* @return array The users in the workgroups
    */
	function getAllInContext($contextCode)
	{
		$sql = "SELECT {$this->_tableName}.userid FROM {$this->_tableName}, tbl_workgroup
		WHERE {$this->_tableName}.workgroupid=tbl_workgroup.id";
        if ($contextCode == NULL) {
            $sql .= " AND tbl_workgroup.contextcode IS NULL";
        }
        else {
            $sql .= " AND tbl_workgroup.contextcode='$contextCode'";
        }
        $sql .= " ORDER BY {$this->_tableName}.userid";
		return $this->getArray($sql);
		//return $this->getAll("WHERE workgroupId='".$workgroupId."'");
	}

    /**
    * Method to check if a user is a member of a workgroup
    * @param string $userId Record Id of the User
    * @param string $workgroup Record Id of the Workgroup
    * @return boolean True if member else False
    */
    function memberOfWorkGroup($userId, $workgroup)
    {
        $sql = 'WHERE workgroupid="'.$workgroup.'" AND  userid="'.$userId.'"';
        $list = $this->getAll($sql);

        if (count($list) == 0) {
			// If the user is not a student in the workgroup,
			// check if the user is a context lecturer.
            return $this->objUser->isContextLecturer();
        } else {
            return TRUE;
        }
    }

    /**
    * Return all records
	* @param string The workgroup id
	* @return array The users in the workgroup
    */
	function listAll($workgroupId)
	{
		$sql = "SELECT {$this->_tableName}.userid, tbl_users.username, tbl_users.firstname, tbl_users.surname FROM $this->_tableName, tbl_users
		WHERE {$this->_tableName}.userid=tbl_users.userid
        AND {$this->_tableName}.workgroupid='" . $workgroupId . "'
		ORDER BY tbl_users.surname, tbl_users.firstname ASC";
		$rows = $this->getArray($sql);
		$count = count($rows);
		for ($i = 0; $i < $count; $i++) {
			$rows[$i]['fullname'] = stripslashes($rows[$i]['surname']).', '.stripslashes($rows[$i]['firstname']);
		}
		return $rows;
		//return $this->getAll("WHERE workgroupId='".$workgroupId."'");
	}

    /**
    * Return one record
	* @param string The workgroup id
    * @param string The user id
	* @return array The users in the workgroup
    */
	function listSingle($workgroupId, $userId)
	{
		$sql = "SELECT {$this->_tableName}.userid,'tbl_users.firstName' || ' ' || 'tbl_users.surname' AS fullname FROM $this->_tableName, tbl_users
		WHERE {$this->_tableName}.userid=tbl_users.userid
		AND {$this->_tableName}.workgroupid='" . $workgroupId . "'
		AND {$this->_tableName}.userid='" . $userId . "'
		ORDER BY fullname";
		$rows = $this->getArray($sql);
		$count = count($rows);
		for ($i = 0; $i < $count; $i++) {
			$rows[$i]['fullname'] = stripslashes($rows[$i]['fullname']);
		}
		return $rows;
		//return $this->getAll("WHERE workgroupId='".$workgroupId."'");
	}

	/**
	* Insert a record
	* @param string The workgroup id
	* @param string The user id
	*/
	function insertSingle($workgroupId, $userId)
	{
		$this->insert(array(
			'workgroupid'=>$workgroupId,
			'userid'=>$userId
		));
		return;
	}

	/**
	* Delete a record
	* @param string The workgroup id
	* @param string The user id
	*/
	function deleteSingle($workgroupId, $userId)
	{

        $sql = "SELECT id FROM $this->_tableName
		WHERE workgroupid='".$workgroupId."'
		AND userid='".$userId."'";
        $list = $this->getArray($sql);
        if (!empty($list)) {
            $this->delete("id", $list[0]['id']);
        }
	}

	/**
	* Delete multiple records
	* @param string The workgroup id
	*/
	function deleteAll($workgroupId)
	{
        $sql = "SELECT id FROM $this->_tableName
		WHERE workgroupid='".$workgroupId."'";
        $list = $this->getArray($sql);
        foreach ($list as $element) {
		    $this->delete("id", $element['id']);
        }
	}
}
?>