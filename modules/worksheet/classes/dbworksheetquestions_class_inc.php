<?php
/**
* Data class extends dbTable for tbl_worksheet_questions.
* @package worksheet
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
        die("You cannot view this page directly");
}


/**
* Model class for the table tbl_worksheet_questions.
* @author Tohir Solomons
* @author Megan Watson
* @copyright (c) 2004 UWC
* @package worksheet
* @version 0.2
*/
class dbworksheetquestions extends dbTable
{

    /**
    * Constructor method to define the table
    */
    public function init()
    {
        parent::init('tbl_worksheet_questions');
        $this->table='tbl_worksheet_questions';

        $this->objUser = $this->getObject('user', 'security');
        $this->objWashout = $this->getObject('washout','utilities');
    }


    /**
    * Method to insert a single question into the database.
    * @param string $worksheet_id The id of the worksheet being editted.
    * @param string $question The question being submitted.
    * @param string $answer The model answer to the question.
    * @param string $question_worth The marks allocated to the question.
    * @param string $question_order The position of the question in the worksheet.
    * @param string $userId The id of the creator
    * @param string $dateLastUpdated The time of editing
    * @return
    */
    public function insertSingle($worksheet_id, $question, $answer, $question_worth)
    {
        $result = $this->insert(array(
                'worksheet_id' => $worksheet_id,
                'question' => $question,
                'model_answer' => $answer,
                'question_worth' => $question_worth,
                'question_order' => $this->getLastOrder($worksheet_id)+1,
                'userid' => $this->objUser->userId(),
                'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                'updated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
            ));

        if ($result != FALSE) {
            $objWorksheet = $this->getObject('dbworksheet');
            $objWorksheet->updateTotalMark($worksheet_id);
        }

        return $result;
    }

    public function updateQuestion($question_id, $question, $answer, $question_worth)
    {
        $result = $this->update('id', $question_id, array(
                'question' => $question,
                'model_answer' => $answer,
                'question_worth' => $question_worth,
                'userid' => $this->objUser->userId(),
                'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                'updated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
            ));

        if ($result != FALSE) {
            $objWorksheet = $this->getObject('dbworksheet');
            $question = $this->getQuestion($question_id);
            $objWorksheet->updateTotalMark($question['worksheet_id']);
        }

        return $result;
    }

    /**
    * Method to get the number of the last question in the worksheet.
    * @param string $worksheet_id The id of the current worksheet.
    * @return string $lastOrder The number of the last question in the worksheet.
    */
    public function getLastOrder($worksheet_id)
    {
        $sql = "SELECT question_order FROM tbl_worksheet_questions WHERE worksheet_id='{$worksheet_id}' ORDER BY question_order DESC LIMIT 1";

        $result = $this->getArray($sql);

        if (count($result) == 0) {
            return 0;
        } else {
            return $result[0]['question_order'];
        }
    }

    /**
    * Method to delete a question.
    * The order of each of the questions following the deleted question is decreased by 1.
    * Delete image if set.
    * @param string $id The id of the question.
    * @return
    */
    public function deleteQuestion($id)
    {
        $sql = 'SELECT question_order, worksheet_id, imageId FROM '.$this->table;
        $sql .= " WHERE id='$id'";
        $result = $this->getArray($sql);

        if(!empty($result)){
            // Reorder remaining questions
            $row = $this->getQuestions($result[0]['worksheet_id'], $result[0]['question_order'], FALSE);
            if(!empty($row)){
                foreach($row as $line){
                    $pos = $line['question_order']-1;
                    $this->update('id',$line['id'],array('question_order'=>$pos));
                }
            }
        }

        return $this->delete('id',$id);
    }

    /**
    * Delete questions in a worksheet.
    * @param string $worksheetId The ID of the worksheet
    * @return void
    */
    public function deleteQuestions($worksheetId)
    {
        $sql = "SELECT id FROM {$this->_tableName} WHERE worksheet_id = '{$worksheetId}'";
        $rs = $this->getArray($sql);
        if (!empty($rs)) {
            $dbWorksheetAnswers = $this->getObject('dbworksheetanswers');
            foreach ($rs as $row){
                $questionId = $row['id'];
                $dbWorksheetAnswers->deleteAnswers($questionId);
                $this->delete('id', $questionId);
            }
        }
        return;
    }

    /**
    * Method to get the number of questions in the worksheet.
    * @param string $worksheet_id The id of the current worksheet.
    * @return string $result The number of questions in the worksheet.
    */
    public function getNumQuestions($worksheet_id)
    {
        $sql = 'SELECT count( DISTINCT tbl_worksheet_questions.id ) AS questionCount
        FROM `tbl_worksheet_questions`
        INNER JOIN tbl_worksheet ON ( tbl_worksheet_questions.worksheet_id = tbl_worksheet.id )
        WHERE tbl_worksheet.id = "'.$worksheet_id.'" ';

        $result = $this->getArray($sql);
 	      return $result[0]['questioncount'];
    }

    /**
    * Method to retrieve a maximum of 4 questions in a worksheet starting from the given position.
    * @param string $worksheet_id The id of the current worksheet.
    * @param int $order The position of the last question viewed.
    * @param bool $limit Flag to determine whether to limit the number of questions retrieved.
    * @return array $result The worksheet questions.
    */
    public function getQuestions($worksheet_id, $order=0, $limit=TRUE)
    {
        $sql = 'SELECT * FROM '.$this->table." WHERE worksheet_id='$worksheet_id' AND
        question_order>$order ORDER BY question_order";

        /*if($limit){
            $sql .= " LIMIT 4";
        }*/
        $data = $this->getArray($sql);
        return $data;
    }

    /**
    * Get a question from database
    * @param string $id The id of the required question.
    * @return array $rows The question details and the total number of questions.
    */
    public function getQuestion($id)
    {
        $row = $this->getRow('id', $id);
        $row['question'] = $this->objWashout->parseText($row['question']);
        return $row;
    }

    /**
    * Change the order of questions in the worksheet
    * @param string $id The id of the question to be moved
    * @param bool $order If order is true move question up else move question down 1
    * @return bool TRUE if the order has been changed, FALSE if it hasn't.
    */
    public function changeOrder($id,$order)
    {
        $sql = 'SELECT worksheet_id, question_order FROM '.$this->table
        ." WHERE id='$id'";
        $rows=$this->getArray($sql);

        if($rows){
            $pos=$rows[0]['question_order'];
            $wid=$rows[0]['worksheet_id'];
            // if move question up, check its not the first question
            if($order && $pos>1){
                $newpos=$pos-1;
            // if move question down, check its not the last question
            }else if(!$order){
                    $num=$this->getNumQuestions($wid);
                    if($pos<$num){
                        $newpos=$pos+1;
                    }else{
                        return FALSE;
                    }
            }else{
                return FALSE;
            }
            // swap order of questions
            $sql = 'SELECT id FROM '.$this->table." WHERE worksheet_id='$wid' and question_order='$newpos'";
            $row=$this->getArray($sql);

            if($row){
                $this->update('id',$row[0]['id'],array('question_order'=>$pos));
                $this->update('id',$id,array('question_order'=>$newpos));
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
    * Method to get the first question in the worksheet for marking.
    * @param string $worksheet_id The id of the current worksheet.
    * @return array $data The details of the first question in the worksheet.
    */
    public function getFirstQuestion($worksheet_id)
    {
        $sql = 'SELECT * FROM '.$this->table."
        WHERE worksheet_id='$worksheet_id' ORDER BY question_order LIMIT 4";

        $data=$this->getArray($sql);
        $data[0]['count']=$this->getNumQuestions($worksheet_id);

        if($data){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to get the next question in the worksheet for marking.
    * @param string $worksheet_id The id of the current worksheet.
    * @return array $data The details of the last question in the worksheet.
    */
    public function getNextQuestion($worksheet_id,$order)
    {
        $sql = 'SELECT * FROM '.$this->table."
        WHERE worksheet_id='$worksheet_id' AND question_order='$order' LIMIT 1";

        $data=$this->getArray($sql);
        $data[0]['count']=$this->getNumQuestions($worksheet_id);

        if($data){
            return $data;
        }
        return FALSE;
    }
 /*
 public function saveRecord($question_id, $question, $answer, $question_worth)
    {
        $result = $this->update('id', $question_id, array(
                'question' => $question,
                'model_answer' => $answer,
                'question_worth' => $question_worth,
                'userid' => $this->objUser->userId(),
                'datelastupdated' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
                'updated' => strftime('%Y-%m-%d %H:%M:%S', mktime())
            ));

        if ($result != FALSE) {
            $objWorksheetquestions = $this->getObject('dbworksheetquestions');
            $question = $this->getQuestion($question_id);
            $objWorksheet->saveRecord($question['worksheet_id']);


       return $result;
    }
*/
function updateQn($pkfield, $pkvalue, $fields)
 {
   $tablename='tbl_worksheet_questions';
   $status=$this->update($pkfield,$pkvalue,$fields,$tablename);
   return $status;
 }
} //end of class
?>
