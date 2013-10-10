<?php
/**
* Data class extends dbTable for tbl_worksheet_answers.
* @package worksheet
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
        die("You cannot view this page directly");
}


/**
* Model class for the table tbl_worksheet_answers
* @author Tohir Solomons
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package worksheet
* @version 0.2
*/
class dbworksheetanswers extends dbTable
{

    /**
    * Constructor method to define the table
    */
    public function init() {
        parent::init('tbl_worksheet_answers');
        $this->table='tbl_worksheet_answers';
    }


    public function saveAnswers($worksheetId, $userId)
    {
        $objWorksheetQuestion = $this->getObject('dbworksheetquestions');

        $questions = $objWorksheetQuestion->getQuestions($worksheetId, 0, FALSE);

        foreach ($questions as $question)
        {
            $answer = $this->getParam($question['id']);

            $id = $this->answerExists($question['id'], $userId);

            if ($id == FALSE) {
                $this->insert(array(
                        'question_id' => $question['id'],
                        'student_id' => $userId,
                        'dateanswered' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                        'answer' => $answer,
                        'updated' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                    ));
            } else {
                $this->update('id', $id, array(
                        'question_id' => $question['id'],
                        'student_id' => $userId,
                        'dateanswered' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                        'answer' => $answer,
                        'updated' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                    ));
            }
        }
    }

    public function saveMarks($student, $worksheet, $lecturer)
    {
        $studentAnswers = $this->getStudentAnswers($worksheet, $student);

        $finalMark = 0;

        foreach ($studentAnswers as $answer)
        {
            $result = $this->update('id', $answer['id'], array(
                'mark' => $this->getParam($answer['id'], 0),
                'comments' => $this->getParam('comment_'.$answer['id']),
                'lecturer_id' => $lecturer,
                'datemarked' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            ));
            if ($result) {
                $finalMark += $this->getParam($answer['id'], 0);
            }
        }

        $objWorksheetResults = $this->getObject('dbworksheetresults', 'worksheet');
        $resultId = $objWorksheetResults->getWorksheetResult($student, $worksheet);

        return $objWorksheetResults->addResult(array('mark'=>$finalMark), $resultId['id']);

    }

    /**
    * Method to insert a lecturers mark and comment on a students answer.
    * @param array $fields The fields and values to be updated in the database.
    * @param string $id The id of the answer being marked.
    * @return
    */
    public function insertMark($fields, $id)
    {
        $this->update('id',$id,$fields);
    }

    /**
    * Method to add up the total marks for a students worksheet
    * @param string $worksheet_id The id of the current worksheet.
    * @param string $student_id The id of the student answering the worksheet.
    * @return string $total The total mark achieved by the student.
    */
    public function addMarks($worksheet_id,$student_id)
    {
        $sql = 'SELECT mark FROM '.$this->table."
        INNER JOIN tbl_worksheet_questions ON (tbl_worksheet_questions.worksheet_id='$worksheet_id'
        AND ".$this->table.".question_id = tbl_worksheet_questions.id)
        WHERE ".$this->table.".student_id='$student_id'";

        $data=$this->getArray($sql);

        $total=0;
        if($data){
            foreach($data as $val){
                $total=$total+$val['mark'];
            }
        }
        return $total;
    }

    /**
    * Method to retrieve the last marked answer for a student.
    * @param string $worksheet_id The id of the current worksheet.
    * @param string $student_id The id of the student answering the worksheet.
    * @return array $data The details of the last marked answer.
    */
    public function getStudentAnswers($worksheet_id, $student_id)
    {
        $sql = 'SELECT '.$this->table.'.* FROM '.$this->table."
        INNER JOIN tbl_worksheet_questions ON (tbl_worksheet_questions.worksheet_id='$worksheet_id'
        AND ".$this->table.".question_id = tbl_worksheet_questions.id)
        WHERE ".$this->table.".student_id='$student_id'
        ORDER BY tbl_worksheet_questions.question_order";

        return $this->getArray($sql);
    }

    /**
    * Method to retrieve a set number of answers and questions in a worksheet.
    * @param string $worksheet_id The id of the current worksheet.
    * @param string $student_id The id of the student answering the worksheet.
    * @param string $start The first question to retrieve. Default=0.
    * @param string $limit The number of questions to retrieve. Default=1.
    * @return array $data The details of the answers.
    */
    public function getAnswers($worksheet_id, $student_id, $start=0, $limit=1)
    {
        $sql = 'SELECT * FROM '.$this->table."
        INNER JOIN tbl_worksheet_questions ON (tbl_worksheet_questions.worksheet_id='$worksheet_id'
        AND ".$this->table.".question_id = tbl_worksheet_questions.id)
        WHERE ".$this->table.".student_id='$student_id'
        AND tbl_worksheet_questions.question_order > $start
        ORDER BY tbl_worksheet_questions.question_order ASC
        LIMIT $limit";

        $data=$this->getArray($sql);

        if($data){
            $data[0]['totalmark'] = $this->addMarks($worksheet_id,$student_id);
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to retrieve a students answer for a question.
    * @param string $question_id The id of the question.
    * @param string $student_id The id of the student answering the question.
    * @return array $data The details of the answer.
    */
    public function getAnswer($question_id, $student_id)
    {
        $sql = 'SELECT * FROM '.$this->table."
        WHERE student_id='$student_id' AND question_id='$question_id' LIMIT 1";

        $data=$this->getArray($sql);

        if(count($data) > 0){
            return $data[0];
        } else {
            return FALSE;
        }
    }

    /**
    * Method to check if an answer exists for the specified question.
    * @param string $question_id The id of the question.
    * @param string $student_id The id of the student answering the question.
    * @return string $result The id of the answer.
    */
    public function answerExists($question_id, $student_id)
    {
        $sql = 'SELECT id  FROM tbl_worksheet_answers
        WHERE question_id = "'.$question_id.'" AND student_id = "'.$student_id.'" ';

        $result = $this->getArray($sql);

        if(count($result) == 0){
            return FALSE;
        } else {
            return $result[0]['id'];
        }

    }

    /**
    * Method to count the number of questions answered.
    * @param string $worksheet_id The id of the worksheet being answered.
    * @param string $userId The id of the student answering the worksheet.
    * @return int $row The number of answers.
    */
    public function countAnswers($worksheet_id,$userId)
    {
        $sql = 'SELECT COUNT('.$this->table.'.question_id) AS count FROM '.$this->table."
        INNER JOIN tbl_worksheet_questions ON (tbl_worksheet_questions.worksheet_id='$worksheet_id'
        AND ".$this->table.".question_id = tbl_worksheet_questions.id)
        WHERE ".$this->table.".student_id='$userId'";

        $rows=$this->getArray($sql);
        if($rows){
            return $rows[0]['count'];
        }
        return FALSE;
    }

    /**
    * Delete answers in a worksheet.
    * @param string $questionId The ID of the question
    * @return void
    */
    public function deleteAnswers($questionId)
    {
        $sql = "SELECT id FROM {$this->_tableName} WHERE question_id = '{$questionId}'";
        $rs = $this->getArray($sql);
        if (!empty($rs)) {
            foreach ($rs as $row){
                $this->delete('id',$row['id']);
            }
        }
    }

} //end of class
?>