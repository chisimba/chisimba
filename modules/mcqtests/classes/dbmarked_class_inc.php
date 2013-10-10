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
 * Class for providing access to the table tbl_test_marked in the database.
 * The table stores the students answers to a test
 * @author Megan Watson
 *
 * @copyright (c) 2004 UWC
 * @package mcqtests
 * @version 1.2
 */
class dbmarked extends dbtable
{
    /**
     * Method to construct the class and initialise the table
     *
     * @access public
     * @return
     */
    public function init()
    {
        parent::init('tbl_test_marked');
        $this->table = 'tbl_test_marked';
        $this->answerTable = 'tbl_test_answers';
        $this->questionTable = 'tbl_test_questions';
        $this->resultsTable = 'tbl_test_results';
        $this->usersTable = 'tbl_users';
    }

    /**
     * Method to add a students answers to a test to the database.
     *
     * @access public
     * @param array $fields The fields to be inserted.
     * @param array $id The id of a previously selected answer to be updated.
     * @return
     */
    public function addMarked($fields, $id = NULL)
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE testid='".$fields['testid']."'";
        $sql.= " AND questionid='".$fields['questionid']."'";
        $sql.= " AND studentid='".$fields['studentid']."'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $id = $data[0]['id'];
            $this->update('id', $id, $fields);
        } else {
            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
     * Method to get a students answer to a specified question.
     *
     * @access public
     * @param string $studentId The id of the student.
     * @param string $questionId The id of the specified question.
     * @param string $testId The id of the test.
     * @return array $data The students answer.
     */
    public function getMarked($studentId, $questionId, $testId)
    {
        $sql = "SELECT answers.* FROM ".$this->table." AS marked,";
        $sql.= " tbl_test_answers AS answers";
        $sql.= " WHERE marked.answerid = answers.id";
        $sql.= " AND marked.studentid='$studentId'";
        $sql.= " AND marked.questionid='$questionId'";
        $sql.= " AND marked.testid='$testId'";
        $sql.= " ORDER BY marked.updated DESC";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get a students answer to a specified free form question.
     *
     * @access public
     * @param string $studentId The id of the student.
     * @param string $questionId The id of the specified question.
     * @param string $testId The id of the test.
     * @return array $data The students answer.
     */
    public function getMarkedFreeForm($studentId, $questionId, $testId)
    {
/*
        $sql = "SELECT DISTINCT answers.* FROM ".$this->table." AS marked,";
        $sql.= " tbl_test_answers AS answers";
        $sql.= " WHERE marked.answered = answers.answer";
        $sql.= " AND marked.studentid='$studentId'";
        $sql.= " AND marked.questionid='$questionId'";
        $sql.= " AND marked.testid='$testId'";
        $sql.= " ORDER BY marked.updated DESC";
*/

        $sql = "SELECT DISTINCT answers.*
                FROM
                    {$this->table} AS marked,
                    tbl_test_answers AS answers
                WHERE
                    marked.studentid = '{$studentId}'
                    AND marked.testid = '{$testId}'
                    AND marked.questionid = '{$questionId}'
                    AND answers.testid = '{$testId}'
                    AND answers.questionid = '{$questionId}'
                    AND marked.answered = answers.answer";

        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    public function getMarkedFreeFormAnswer($studentId, $questionId, $testId)
    {
        $sql = "SELECT answered
                FROM
                    {$this->table}
                WHERE
                    studentid='{$studentId}'
                    AND testid='{$testId}'
                    AND questionid='{$questionId}'";
        //$sql.= " ORDER BY marked.updated DESC";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to get the answers selected by the user for a test.
     *
     * @access public
     * @param string $studentId The id of the student.
     * @param string $testId The id of the test.
     * @return array $data The students answers.
     */
    public function getSelectedAnswers($studentId, $testId)
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql.= " WHERE studentid = '$studentId' AND testid = '$testId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get the answers selected by the user for a test with a check if correct and the mark.
     *
     * @access public
     * @param string $studentId The id of the student.
     * @param string $testId The id of the test.
     * @return array $data The students answers.
     */
    public function getCorrectAnswers($studentId, $testId)
    {
        $sql = "SELECT ans.correct, quest.mark FROM";
        $sql.= " tbl_test_marked AS marked, tbl_test_answers AS ans, tbl_test_questions AS quest";
        $sql.= " WHERE marked.answerid = ans.id";
        $sql.= " AND marked.questionid = quest.id";
        $sql.= " AND marked.studentid = '$studentId'";
        $sql.= " AND marked.testid = '$testId'";

        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }
/**
*Methof to get the correct answers entered by the student on a specified test.
*
* @access public
* @param string $studentId The id of the student.
* @param string $testId The id of the test.
* @return array data the question id,students correct answer and marks

*/
   public function getfreeformAnswers($studentId, $testId)
   {

/*
        $sql = "SELECT distinct marked.questionid,marked.answered,quest.mark FROM";
        $sql.= " tbl_test_marked AS marked, tbl_test_answers AS ans, tbl_test_questions AS quest";
        $sql.= " WHERE marked.answered = ans.answer";
        $sql.= " AND marked.questionid = quest.id";
        $sql.= " AND marked.studentid = '$studentId'";
        $sql.= " AND marked.testid = '$testId'";
*/
        $sql = "SELECT DISTINCT
                    questions.id AS questionid,
                    questions.mark,
                    marked.answered
                FROM
                    tbl_test_questions AS questions,
                    {$this->table} AS marked,
                    tbl_test_answers AS answers
                WHERE
                    questions.testid = '{$testId}'
                    AND questions.questiontype = 'freeform'
                    AND answers.testid = '{$testId}'
                    AND answers.questionid = questions.id
                    AND marked.studentid = '{$studentId}'
                    AND marked.testid = '{$testId}'
                    AND marked.questionid = questions.id
                    AND marked.answered = answers.answer";

        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
   }
    /**
     * Method to delete a set of answers for a student on a specified test.
     *
     * @access public
     * @param string $studentId The id of the student.
     * @param string $testId The id of the test.
     * @return bool
     */
    public function deleteMarked($studentId, $testId)
    {
        $sql = 'SELECT id FROM '.$this->table." WHERE studentid='$studentId' AND testid='$testId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            foreach($data as $line) {
                $this->delete('id', $line['id']);
            }
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Method to get the answers selected by the user
     * for a test for the export functionality
     *
     *
     * @access public
     * @param string $testId The id of the test
     * @param string $studentId The id of the student
     * @return array $data The students answers
     */
    public function getAnswersForExport($testId, $studentId)
    {
        $sql = "SELECT marked.studentid, marked.answered, questions.questionorder, questions.questiontype, answers.answerorder ";
        $sql.= "FROM ".$this->table." AS marked ";
        $sql.= "LEFT JOIN ".$this->questionTable." AS questions ON marked.questionid=questions.id ";
        $sql.= "LEFT JOIN ".$this->answerTable." AS answers ON marked.answerid=answers.id ";
        $sql.= "WHERE marked.testid='".$testId."' AND marked.studentid='".$studentId."' ";
        $sql.= "ORDER BY questions.questionorder";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to delete a single marked record
     *
     * @access public
     * @param string $id The id to delete
     * @return
     */
    public function deleteSingleMarked($id)
    {
        $this->delete('id', $id);
    }

    /**
     * Method to get a students answer to a specified question.
     *
     * @access public
     * @param string $studentId The id of the student.
     * @param string $questionId The id of the specified question.
     * @param string $testId The id of the test.
     * @return array $data The students answer.
     */
    public function getAllMarked($studentId, $questionId, $testId)
    {
        $sql = 'SELECT * FROM '.$this->table.' AS marked ';
        $sql.= "WHERE marked.studentid='$studentId' AND marked.questionid='$questionId'";
        $sql.= "AND marked.testid='$testId' ";
        $sql.= "ORDER BY marked.updated DESC";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }
} // end of class
?>