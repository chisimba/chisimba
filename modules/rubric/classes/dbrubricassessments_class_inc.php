<?php
/* ----------- data class extends dbTable for tbl_blog------------*/// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_rubric_assessments
* @author Jeremy O'Connor
* @copyright 2004 University of the Western Cape
*/
class dbRubricAssessments extends dbTable
{
    /**
    * Constructor method to define the table
    */
    public function init() 
    {
        parent::init('tbl_rubric_assessments');
        //$this->USE_PREPARED_STATEMENTS=True;
    }

    /**
    * Return all records
	* @param string $tableId The table ID
	* @return array The assessments for a table
    */
	public function listAll($tableId)
	{
		$sql = "SELECT id, teacher, studentNo, student, scores, timestamp FROM tbl_rubric_assessments
		WHERE tableId = '$tableId'
		ORDER BY student, teacher, timestamp";
		return $this->getArray($sql);
		//return $this->getAll();
	}

	/**
	* Return a single record
	* @param string $id The ID
	* @return array An assessment
	*/	
	public function listSingle($id)
	{
		$sql = "SELECT tableId, teacher, studentNo, student, scores, timestamp FROM tbl_rubric_assessments
		WHERE id = '$id'";
		return $this->getArray($sql);
		//return $this->getRow("id", $id);
	}

	/**
	* Insert a record
	* @param string $tableId The table ID
	* @param string $teacher The teacher
	* @param string $studentNo The student number
	* @param string $student The student
	* @param string $scores The list of scores, comma separated
	* @param string $timestamp The timestamp
	*/
	public function insertSingle($tableId, $teacher, $studentNo, $student, $scores, $timestamp)
	{
		$this->insert(array(
        	'tableId' => $tableId,
			'teacher' => $teacher,
			'studentNo' => $studentNo, 
        	'student' => $student,
        	'scores' => $scores,
			'timestamp' => $timestamp
		));
		return $this->getLastInsertId();		
	}

	/**
	* Update a record
	* @param string $id The ID
	* @param string $tableId The table ID
	* @param string $teacher The teacher
	* @param string $studentNo The student number
	* @param string $student The student
	* @param string $scores The list of scores, comma separated
	* @param string $timestamp The timestamp
	*/
	public function updateSingle($id, $tableId, $teacher, $studentNo, $student, $scores, $timestamp)
	{
		$this->update('id', $id, array(
        	'tableId' => $tableId,
			'teacher' => $teacher,
			'studentNo' => $studentNo, 
        	'student' => $student,
        	'scores' => $scores,
			'timestamp' => $timestamp
		));
		return;
	}

	/**
	* Delete all records
	* @param string $tableId The table ID
	*/
	public function deleteAll($tableId)
	{
		$this->delete("tableId", $tableId);
	}

	/**
	* Delete a record
	* @param string $id The ID
	*/
	public function deleteSingle($id)
	{
		$this->delete("id", $id);
	}
}
?>
