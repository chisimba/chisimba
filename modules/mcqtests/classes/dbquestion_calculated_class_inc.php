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
 * Class for providing access to the table tbl_test_question_calculated in the database
 * @author Nguni Phakela
 *
 * @copyright (c) 2004 UWC
 * @package mcqtests
 * @version 1.2
 */
class dbquestion_calculated extends dbtable
{
    /**
     * Method to construct the class and initialise the table
     *
     * @access public
     * @return
     */
    public function init()
    {
        parent::init('tbl_test_question_calculated');
        $this->table = 'tbl_test_question_calculated';
    }
        /**
     * Method to add a set of calculated-qn-answers to the database.
     * If the $id field is not null then the answer is updated.
     *
     * @access public
     * @param array $fields The fields to be inserted.
     * @param string $id The id of the answer to be updated.
     * @return string $id The id of the inserted or updated answer.
     */
    public function addAnswers($fields, $id = NULL)
    {        
        if ($id) {
            $this->update('id', $id, $fields);
        } else {
            $id = $this->insert($fields);
        }
        return $id;
    }


    /**
     * Method to delete an calculated-qn-answer.
     *
     * @access public
     * @param string $answerId The id of the answer to be deleted.
     * @return
     */
    public function deleteAnswer($answerId)
    {
        $this->delete('id', $answerId);
    }
    /**
     * Method to get the calculated-qn-answers for a specific question
     *
     * @access public
     * @param string $questionId The id of the specified question.
     * @return int $array The answers associated with the question.
     */
    public function getAnswers($questionId)
    {
        $answers = $this->getAll("WHERE questionid = '$questionId'");
        return $answers;
    }
    /**
     * Method to get the calculated-qn-answers for a specific question
     *
     * @access public
     * @param string $answerId The id of the specified answer from tbl_test_question_answers.
     * @return int $array The answers associated with the question.
     */
    public function getAnswerRelated($answerId)
    {
        $answers = $this->getAll("WHERE answer = '$answerId'");
        return $answers;
    }
}
?>