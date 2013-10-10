<?php
/* ----------- data class extends dbTable for tbl_sudoku ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_sudoku
* @author Kevin Cyster
*/

class dbsudoku extends dbTable
{
    public function init()
    {
        parent::init('tbl_sudoku');
        $this -> table = 'tbl_sudoku';
    }

    /**
    * Method for adding a puzzle to the database.
    *
    * @param string $difficulty  The difficulty level
    * @param string $solution  The solution to the puzzle
    * @param string $puzzle  The unsolved puzzle
    * @param string $creatorId  The id of the user who created the entry
    */
    public function addRecord($difficulty, $solution, $puzzle, $creatorId)
    {
        $fields = array();
        $fields['difficulty'] = $difficulty;
        $fields['solution'] = $solution;
        $fields['puzzle'] = $puzzle;
        $fields['creator_id'] = $creatorId;
        $fields['date_created'] = date('Y-m-d H:i:s');
        return $this -> insert($fields);
    }

    /**
    * Method for retrieving a puzzle
    *
    * @param string $id The id of the puzzle
    * @return array $data  Puzzle data
    */
    public function getRecord($id)
    {
        $sql = "SELECT * FROM " . $this -> table;
        $sql .= " WHERE id = '$id'";
        $data = $this -> getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for saving a puzzle
    *
    * @param string $id The id of the puzzle
    * @param string $saved  The saved puzzle
    * @param string $solved  The solved indicator
    */
    public function editRecord($id, $saved = NULL, $solved = NULL, $timer)
    {
        $fields = array();
        if($saved != NULL){
            $fields['saved'] = $saved;
            $fields['date_saved'] = date('Y-m-d H:i:s');
            $fields['solved'] = '';
            $fields['date_solved'] = '';
            $fields['time_taken'] = $timer;
        }
        if($solved != NULL){
            $fields['solved'] = $solved;
            $fields['date_solved'] = date('Y-m-d H:i:s');
            $fields['time_taken'] = $timer;
        }
        $this -> update('id', $id, $fields);
    }

    /**
    * Method for deleting a puzzle
    *
    * @param string $id  The puzzle to be deleted
    */
    public function deleteRecord($id)
    {
        $this -> delete('id', $id);
    }

    /**
    * Method for listing puzzles
    *
    * @return array $data  All puzzle data
    */
    public function listAllRecords($userId)
    {
        $sql = "SELECT * FROM " . $this -> table;
        $sql .= " WHERE creator_id = '$userId'";
        $sql .= " ORDER BY 'date_created' ";
        $data = $this -> getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
}
?>