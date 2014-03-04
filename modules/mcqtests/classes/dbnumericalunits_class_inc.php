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
 * Class for providing access to the table tbl_test_numericalunits in the database
 * @author Nguni Phakela
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbnumericalunits extends dbtable {
    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public $table;

    public function init() {
        parent::init('tbl_test_question_numericalunits');
        $this->table = 'tbl_test_question_numericalunits';
    }

    public function addNumericalUnits($data) {
        $id = $this->insert($data);
        return $id;
    }
    /**
     * Method to add a unit to the database.
     * If the $id field is not null then the answer is updated.
     *
     * @access public
     * @param array $fields The fields to be inserted.
     * @param string $id The id of the answer to be updated.
     * @return string $id The id of the inserted or updated answer.
     */
    public function addNUnit($fields, $id = NULL)
    {
        if ($id) {
            $this->update('id', $id, $fields);
        } else {
            $id = $this->insert($fields);
        }
        return $id;
    }
    
    public function updateNumericalUnits($id, $data) {
        $this->update('questionid', $id, $data);
        return $id;
    }

    public function deleteNumericalUnit($id) {
        $this->delete('questionid', $id);
    }
    public function updateNU($id, $data) {
        $this->update('id', $id, $data);
        return $id;
    }

    public function deleteNU($id) {
        $this->delete('id', $id);
    }

    public function getNumericalUnits($qnid) {
        $units = $this->getAll("WHERE questionid = '$qnid'");
        return $units;
    }
}

?>