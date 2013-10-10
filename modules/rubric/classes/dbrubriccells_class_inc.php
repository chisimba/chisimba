<?php
/* ----------- data class extends dbTable for tbl_blog------------*/// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_rubric_cells
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
*/
class dbRubricCells extends dbTable
{
    /**
    * Constructor method to define the table
    */
    function init() 
    {
        parent::init('tbl_rubric_cells');
        //$this->USE_PREPARED_STATEMENTS=True;
    }

	/**
	* Return a single record
	* @param string $tableId The table ID
	* @param integer $row The row
	* @param integer $col The column
	* @return array The cell
	*/	
	function listSingle($tableId, $row, $col)
	{
		$sql = "SELECT contents FROM tbl_rubric_cells 
		WHERE (tableId = '" . $tableId . "')
		AND (row = '" . $row . "')
		AND (col = '" . $col . "')";
		return $this->getArray($sql);
		//return $this->getRow("id", $id);
	}

	/**
	* Insert a record
	* @param string $tableId The table ID
	* @param integer $row The row
	* @param integer $col The column
	* @param string $contents The contents of the cell
	*/
	function insertSingle($tableId, $row, $col, $contents)
	{
		$this->insert(array(
        	'tableId' => $tableId,
        	'row' => $row,
        	'col' => $col,
        	'contents' => $contents
		));
		return;
	}

	/**
	* Delete a record
	* @param string $tableId The table ID
	* @param integer $row The row
	* @param integer $col The column
	*/
	function deleteSingle($tableId, $row, $col)
	{
		$sql = "SELECT id FROM {$this->_tableName}
		WHERE (tableId = '$tableId')
		AND (row = '$row')
		AND (col = '$col')";
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