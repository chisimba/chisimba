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
 * Class for providing access to the table tbl_test_multianswers in the database
 * @author Nguni Phakela
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbquestion_multianswers extends dbtable {
    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public $table;


    public function init() {
        parent::init('tbl_test_question_multianswers');
        $this->table = 'tbl_test_question_multianswers';

    }

    public function addAnswers($data) {
        $this->insert($data);
    }

    public function getAnswers($id) {
        $filter = "WHERE questionid='$id'";
        return $this->getAll($filter);
    }

    public function insertAnswers($data) {
        $this->delete('questionid', $data['id']);
        $this->addAnswers($data);
    }

    public function deleteAnswers($id) {
        $this->delete('questionid', $id);
    }
}
?>
