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
 * Class for providing access to the table tbl_test_numerical in the database
 * @author Nguni Phakela
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbquestion_numerical extends dbtable {
    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public $table;

    public function init() {
        parent::init('tbl_test_question_numerical');
        $this->table = 'tbl_test_question_numerical';
    }

    public function addNumericalQuestions($id, $data) {
        //insert into this table
        $answers = $data['answer'];
        $answerData = array();
        $count = 1;
        foreach($answers as $row) {
            $mark = 'mark'.$count;
            $answerData['answer'] = $row;
            $answerData['questionid'] = $id;
            $answerData['mark'] = $data['mark'][$mark];
            $count++;
            $this->insert($answerData);
        }
    }

    public function updateNumericalQuestions($id, $data) {
        $this->delete("questionid", $id);
        $this->addNumericalQuestions($id, $data);
    }

    public function deleteNumericalQuestion($id) {
        $this->delete('questionid', $id);
    }

    public function getAnswers($id) {
        $answers = $this->getAll("WHERE questionid = '$id'");
        return $answers;
    }
}

?>
