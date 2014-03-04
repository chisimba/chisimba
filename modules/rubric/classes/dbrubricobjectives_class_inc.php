<?php
/* ----------- data class extends dbTable for tbl_blog------------*/// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_rubric_objectives
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
*/
class dbRubricObjectives extends dbTable
{
    /**
    * Constructor method to define the table
    */
    function init() 
    {
        parent::init('tbl_rubric_objectives');
        //$this->USE_PREPARED_STATEMENTS=True;
    }

	/**
	* Return a single record
	* @param string $tableId The table ID
	* @param integer $row The row
	* @return array The objective
	*/	
	function listSingle($tableId, $row)
	{
		$sql = "SELECT objective FROM tbl_rubric_objectives 
		WHERE (tableId = '" . $tableId . "')
		AND (row = '" . $row . "')";
		return $this->getArray($sql);
		//return $this->getRow("id", $id);
	}

	/**
	* Insert a record
	* @param string $tableId The table ID
	* @param integer $row The row
	* @param string $objective The objective
	*/
	function insertSingle($tableId, $row, $objective)
	{
		$this->insert(array(
        	'tableId' => $tableId,
        	'row' => $row,
        	'objective' => $objective
		));
		return;
	}

	/**
	* Delete a record
	* @param string $tableId The table ID
	* @param integer $row The row
	*/
	function deleteSingle($tableId, $row)
	{
		$sql = "SELECT id FROM {$this->_tableName}
		WHERE (tableId = '$tableId')
		AND (row = '$row')";
		$list = $this->getArray($sql);
        $id = $list[0]['id'];
		$this->delete("id", $id);
	}

	/**
	* Delete a record
	* @param string $tableId The table ID
	*/
	function deleteAll($tableId)
	{
		$this->delete("tableId", $tableId);
	}
}
?>
