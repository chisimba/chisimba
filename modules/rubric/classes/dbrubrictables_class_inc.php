<?php
/* ----------- data class extends dbTable for tbl_blog------------*/// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_rubric_tables
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
*/
class dbRubricTables extends dbTable
{
    /**
    * Constructor method to define the table
    */
    function init() 
    {
        parent::init('tbl_rubric_tables');
        //$this->USE_PREPARED_STATEMENTS=True;
    }

    /**
    * Return all records
	* @param string $contextCode The context code
	* @return array The tables
    */
	function listAll($contextCode, $userId=NULL)
	{
		$sql = "SELECT * FROM tbl_rubric_tables"
		." WHERE contextCode = '$contextCode'" . (is_null($userId) ? " AND userId IS NULL" : " AND userId='$userId'")
		." ORDER BY title";
		return $this->getArray($sql);
		//return $this->getAll();
	}   
	
	/**
	* Return a single record
	* @param string $id ID
	* @return array The table
	*/	
	function listSingle($id)
	{
		$sql = "SELECT * FROM tbl_rubric_tables 
		WHERE id = '" . $id . "'";
		return $this->getArray($sql);
		//return $this->getRow("id", $id);
	}

	/**
	* Insert a record
	* @param string $contextCode The context code
	* @param string $title The table title 
	* @param string $description The table description
	* @param integer $rows The number of rows
	* @param integer $cols The number of columns
	*/
	function insertSingle($contextCode, $title, $description, $rows, $cols, $userId=NULL)
	{
		$this->insert(array(
			'contextCode' => $contextCode,
        	'title' => $title,
        	'description' => $description,
        	'rows' => $rows,
        	'cols' => $cols,
			'userId' => $userId
		));
		return $this->getLastInsertId();		
	}

	/**
	* Update a record
	* @param string $id ID
	* @param string $title The table title 
	* @param string $description The table description
	*/
	function updateSingle($id, $title, $description)
	{
		$this->update("id", $id, 
			array(
        		'title' => $title,
	        	'description' => $description
			)
		);
	}
	
	/**
	* Update a record
	* @param string $id ID
    * @param int $rows Rows
	*/
	function updateRows($id, $rows)
	{
		$this->update("id", $id, 
			array(
        		'rows' => $rows
			)
		);
	}

	/**
	* Update a record
	* @param string $id ID
    * @param int $cols Columns
	*/
	function updateCols($id, $cols)
	{
		$this->update("id", $id, 
			array(
        		'cols' => $cols
			)
		);
	}

	/**
	* Delete a record
	* @param string $id ID
	*/
	function deleteSingle($id)
	{
		$this->delete("id", $id);
	}
}
?>
