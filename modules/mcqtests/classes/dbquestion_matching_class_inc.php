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
 * Class for providing access to the table tbl_test_question_matching in the database
 * @author Nguni Phakela
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbquestion_matching extends dbtable {
    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public $table;
    public $objUser;
    public $userId;
    public $objQuestions;
    public $objMultiAnswers;

    public function init() {
        parent::init('tbl_test_question_matching');
        $this->table = 'tbl_test_question_matching';
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();        
        $this->objMultiAnswers = $this->getObject('dbquestion_multianswers', 'mcqtests');
    }

    public function addMatchingQuestions($id, $matchingQuestionData) {
        //insert into this table
        $questionData = array();
        $questionData['questionid'] = $id;
        foreach($matchingQuestionData['subquestions'] as $row) {
            $questionData['subquestions'] = $row;
            if(trim(strlen($questionData['subquestions'])  > 0)) {
                $this->insert($questionData);
            }
        }
        $this->addAnswers($id, $matchingQuestionData);
    }

   public function addAnswers($id, $matchingQuestionData) {
        $answerData = array();
        $answerData['questionid'] = $id;
        foreach($matchingQuestionData['subanswers'] as $row) {
            if(strlen(trim($row['answer'])) > 0) {
                $row['questionid'] = $id;
                $this->objMultiAnswers->addAnswers($row);
            }
        }
    }

    public function updateMatchingQuestions($id, $matchingQuestionData) {
        $this->delete('questionid', $id);
        $this->deleteAnswers($id);
        $this->addMatchingQuestions($id, $matchingQuestionData);
    }

    public function getMatchingQuestions($id) {
        $filter = "WHERE questionid='$id'";
        return $this->getAll($filter);
    }

    public function deleteQuestions($id) {
        $this->delete('questionid', $id);
    }

    public function deleteAnswers($id) {
        $this->objMultiAnswers->deleteAnswers($id);
    }

    public function getAnswers($id) {
        $answers = array();
        // get subquestions
        $sql = "select * from $this->table where questionid='$id'";
        $data = $this->getArray($sql);
        $answers['questions'] = $data;
        
        // get answers
        $sql = "select * from tbl_test_question_multianswers where questionid='$id'";
        $data = $this->getArray($sql);
        $answers['answers'] = $data;
        
        return $answers;
    }
}
?>