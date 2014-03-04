<?php
/**
 * @package mcqtests
 * @filesource
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class for providing access to the table tbl_test_question_numericaloptions in the database
 * @author Nguni Phakela
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.202
 */
class dbnumericalunitsoptions extends dbtable {
    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public $table;

    public function init() {
        parent::init('tbl_test_question_numericaloptions');
        $this->table = 'tbl_test_question_numericaloptions';
    }
    /**
     * Method to add/update a numerical-option to the database.
     * If the $id field is not null then the answer is updated.
     *
     * @access public
     * @param array $fields The fields to be inserted.
     * @param string $id The id of the answer to be updated.
     * @return string $id The id of the inserted or updated answer.
     */
    public function addNOption($fields, $id = NULL)
    {
        if ($id) {
            $this->update('id', $id, $fields);
        } else {
            $id = $this->insert($fields);
        }
        return $id;
    }
    public function addNumericalOptions($data) {
        //insert into this table
        $id = $this->insert($data);
        return $id;
    }

    public function updateNumericalOptions($id, $data) {
        $this->update('questionid', $id, $data);
        return $id;
    }
    public function updateNO($id, $data) {
        $this->update('id', $id, $data);
        return $id;
    }
    public function deleteNumericalOptions($id) {
        $this->delete('questionid', $id);
    }
    public function deleteNO($id) {
        $this->delete('id', $id);
    }
    public function getNumericalOptions($id) {
        $filter = "WHERE questionid='$id'";
        $data = $this->getAll($filter);
        return $data;
    }
}
?>