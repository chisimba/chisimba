<?php
/**
* Class dbContent extends dbTable.
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package pbl
* @version 1
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
 * Class for providing access to the content table in the database
 * The table contains the learning issues and hypothesis formulated by the students
 * in a classroom.
 *
 * @author Megan Watson
 * @copyright (c) 2004 UWC
 * @package pbl
 * @version 1
 */

class dbContent extends dbTable
{
    /**
     * var $table The name of the table
     */
    private $table = 'tbl_pbl_content';

    /**
     * Constructor method to define the table and initialise objects
     */
    public function init()
    {
        parent::init('tbl_pbl_content');
    }

    /**
     * Save student added content (learning issues or hypothesis).
     *
     * @param string $field Database field to update
     * @param string $notes Content to insert into database
     * @return
     */
    public function saveNotes($notes, $field)
    {
        $sesClass = $this->getSession('classroom');

        $newNote = $this->retrieveNotes("*");
        $fields = array();
        if (!$newNote) {
            $fields['classroomid'] = $sesClass;
            $fields[$field] = $notes;
            $this->insert($fields);
        } else {
            $fields[$field] = $notes;

            $sql = 'SELECT id FROM '.$this->table;
            $sql .= " WHERE classroomid='" . $sesClass . "'";
            $result = $this->getArray($sql);

            if($result){
                $this->update('id',$result[0]['id'],$fields);
            }
        }
    }

    /**
     * Method to retrieve the content for learning issues or hypothesis for display from the current classroom.
     *
     * @param string $field Database field to access
     * @return array $rows The content
     */
    public function retrieveNotes($field)
    {
        $sesClass = $this->getSession('classroom');
        $sql = "select " . $field . " from " . $this->table . " where classroomid='" .$sesClass. "'";
        $rows = $this->getArray($sql);
        if (!$rows){
            return FALSE;
        }
        return $rows;
    }

    /**
     * Method to erase the content from the learning issues or hypothesis.
     * Delete the row if both fields are NULL.
     *
     * @param string $field Database field to access
     * @return
     */
    public function eraseNotes($field)
    {
        $sesClass = $this->getSession('classroom');
        // Set field to NULL
        $fields = array();
        $fields[$field] = NULL;

        $sql = 'SELECT id FROM '.$this->table;
        $sql .= " WHERE classroomid='" . $sesClass. "'";
        $result = $this->getArray($sql);

        if($result){
            $this->update('id',$result[0]['id'],$fields);
        }

        // retrieve all fields, if no content saved delete record
        $row = $this->retrieveNotes("*");
        $delete = TRUE;
        if($row){
            foreach($row as $content) {
                foreach($content as $key => $val) {
                    if ($key == 'li' || $key == 'hypothesis') {
                        if ($val != NULL) {
                            $delete = FALSE;
                        }
                    }
                }
            }
        }
        if ($delete && $result){
            $this->delete('id',$result[0]['id']);
        }
    }
}

?>