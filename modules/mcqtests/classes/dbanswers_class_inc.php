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
 * Class for providing access to the table tbl_test_answers in the database
 * @author Megan Watson
 *
 * @copyright (c) 2004 UWC
 * @package mcqtests
 * @version 1.2
 */
class dbanswers extends dbtable
{
    /**
     * Method to construct the class and initialise the table
     *
     * @access public
     * @return
     */
    public function init()
    {
        parent::init('tbl_test_answers');
        $this->table = 'tbl_test_answers';
    }

    /**
     * Method to add a set of answers to the database.
     * If the $id field is not null then the answer is updated.
     *
     * @access public
     * @param array $fields The fields to be inserted.
     * @param string $id The id of the answer to be updated.
     * @return string $id The id of the inserted or updated answer.
     */
    public function addAnswers($fields, $id = NULL)
    {
        
        $fields['updated'] = date('Y-m-d H:i:s');
        if ($id) {
            $this->update('id', $id, $fields);
        } else {
            $id = $this->insert($fields);
        }
        return $id;
    }


    /**
     * Method to delete an answer.
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
     * Method to set or unset the correct answer.
     *
     * @access public
     * @param string $id The id of the answer to set or unset.
     * @param string $set 1 to set, 0 to unset. DEFAULT=1.
     * @return
     */
    public function setCorrect($id, $set = 1)
    {
        $this->update('id', $id, array(
            'correct' => $set
        ));
    }

    /**
     * Method to count the number of answers in a specified question.
     *
     * @access public
     * @param string $answerId The id of the specified question.
     * @return int $num The number of answers in the question.
     */
    public function countAnswers($questionId)
    {
        $sql = "SELECT count(id) AS num FROM ".$this->table." WHERE questionid='$questionId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $num = $data[0]['num'];
            return $num;
        }
        return FALSE;
    }

    /**
     * Method to get the answers for a specific question
     *
     * @access public
     * @param string $questionId The id of the specified question.
     * @return int $array The answers associated with the question.
     */
    public function getAnswers($questionId)
    {
        $answers = $this->getAll("WHERE questionid = '$questionId' ORDER BY answerorder");
        return $answers;
    }

    /**
     * Method to remove existing answers
     *
     * @access public
     * @param string $questionId The id of the specified question.
     * @return int $array The answers associated with the question.
     */
    public function removeAnswers($questionId)
    {
        return $this->delete('questionid', $questionId);
    }

    public function getCorrectAnswer($questionId)
    {
        $answers = $this->getAll("WHERE questionid = '$questionId' AND correct='1' ORDER BY answerorder");

        if (count($answers) == 0) {
            return 0;
        } else {
            return $answers[0]['answerorder'];
        }
    }

    public function getAlternativeAnswers($testId, $questionId)
    {
        $sql = "SELECT answer
        FROM tbl_test_answers
        WHERE testid='{$testId}'
        AND questionid='{$questionId}'
        AND answerorder <> 1
        ORDER BY answerorder ASC";
        $data = $this->getArray($sql);
        return empty($data)?FALSE:$data;
    }

} // end of class
?>