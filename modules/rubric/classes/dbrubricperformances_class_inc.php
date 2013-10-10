<?php
/* ----------- data class extends dbTable for tbl_blog------------*/// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_rubric_performances
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
*/
class dbRubricPerformances extends dbTable
{
    /**
    * Constructor method to define the table
    */
    function init() 
    {
        parent::init('tbl_rubric_performances');
        //$this->USE_PREPARED_STATEMENTS=True;
    }

	/**
	* Return a single record
	* @param string $tableId The table ID
	* @param integer $col The column
	* @return array The performance
	*/	
	function listSingle($tableId, $col)
	{
		$sql = "SELECT performance FROM tbl_rubric_performances
		WHERE (tableId = '" . $tableId . "')
		AND (col = '" . $col . "')";
		return $this->getArray($sql);
		//return $this->getRow("id", $id);
	}

	/**
	* Insert a record
	* @param string $tableId The table ID
	* @param integer $col The column
	* @param string $performance The performance
	*/
	function insertSingle($tableId, $col, $performance)
	{
		$this->insert(array(
        	'tableId' => $tableId,
        	'col' => $col,
        	'performance' => $performance
		));
		return;
	}

	/**
	* Delete a record
	* @param string $tableId The table ID
	* @param integer $col The column
	*/
	function deleteSingle($tableId, $col)
	{
		$sql = "SELECT id FROM {$this->_tableName}
		WHERE (tableId = '$tableId')
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
