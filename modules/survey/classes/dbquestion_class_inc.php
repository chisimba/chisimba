<?php
/* ----------- data class extends dbTable for tbl_survey_question ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_survey_question
* @author Kevin Cyster
*/

class dbquestion extends dbTable
{
    /**
     * @var string $table The name of the atabase table to be affected
     * @access private
     */
    private $table;

    /**
     * @var object $dbRows The dbquestionrow class in the survey module
     */
    private $dbRows;

    /**
     * @var object $dbColumns The dbquestioncol class in the survey module
     */
    private $dbColumns;

    /**
     * @var object $objUser The user class in the security module
     * @access private
     */
    private $objUser;

    /**
     * @var string $userId The userid of the current user
     * @access private
     */
    private $userId;

    /**
    * Method to construct the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        parent::init('tbl_survey_question');
        $this->table='tbl_survey_question';

        $this->dbRows=&$this->newObject('dbquestionrow');
        $this->dbColumns=&$this->newObject('dbquestioncol');

        $this->objUser=&$this->newObject('user','security');
        $this->userId=$this->objUser->userId();
    }

    /**
    * Method for adding a question to the database.
    *
    * @access public
    * @param string $questionOrder The order of the question
    * @return string $questionId The id of the question added
    */

    public function addQuestion($questionOrder)
    {
        $arrQuestionData=$this->getSession('question');
        $fields=array();
        $fields['survey_id']=$arrQuestionData['survey_id'];
        $fields['type_id']=$arrQuestionData['type_id'];
        $fields['question_order']=$questionOrder;
        $fields['question_text']=$arrQuestionData['question_text'];
        $fields['question_subtext']=$arrQuestionData['question_subtext'];
        $fields['compulsory_question']=$arrQuestionData['compulsory_question'];
        $fields['vertical_alignment']=$arrQuestionData['vertical_alignment'];
        $fields['comment_requested']=$arrQuestionData['comment_requested'];
        $fields['comment_request_text']=$arrQuestionData['comment_request_text'];
        $fields['radio_element']=$arrQuestionData['radio_element'];
        $fields['preset_options']=$arrQuestionData['preset_options'];
        $fields['true_or_false']=$arrQuestionData['true_or_false'];
        $fields['rating_scale']=$arrQuestionData['rating_scale'];
        $fields['constant_sum']=$arrQuestionData['constant_sum'];
        $fields['minimum_number']=$arrQuestionData['minimum_number'];
        $fields['maximum_number']=$arrQuestionData['maximum_number'];
        $fields['date_created']=date("Y-m-d H:i:s");
        $fields['creator_id']=$this->userId;
        $fields['updated']=date("Y-m-d H:i:s");
        $questionId=$this->insert($fields);
        return $questionId;
    }

    /**
    * Method for editing a question on the database.
    *
    * @access public
    * @return
    */

    public function editQuestion()
    {
        $arrQuestionData=$this->getSession('question');
        $questionId=$arrQuestionData['question_id'];
        $fields=array();
        $fields['question_text']=$arrQuestionData['question_text'];
        $fields['question_subtext']=$arrQuestionData['question_subtext'];
        $fields['compulsory_question']=$arrQuestionData['compulsory_question'];
        $fields['vertical_alignment']=$arrQuestionData['vertical_alignment'];
        $fields['comment_requested']=$arrQuestionData['comment_requested'];
        $fields['comment_request_text']=$arrQuestionData['comment_request_text'];
        $fields['radio_element']=$arrQuestionData['radio_element'];
        $fields['preset_options']=$arrQuestionData['preset_options'];
        $fields['true_or_false']=$arrQuestionData['true_or_false'];
        $fields['rating_scale']=$arrQuestionData['rating_scale'];
        $fields['constant_sum']=$arrQuestionData['constant_sum'];
        $fields['minimum_number']=$arrQuestionData['minimum_number'];
        $fields['maximum_number']=$arrQuestionData['maximum_number'];
        $fields['date_modified']=date("Y-m-d H:i:s");
        $fields['modifier_id']=$this->userId;
        $fields['updated']=date("Y-m-d H:i:s");
        $this->update('id',$questionId,$fields);
    }

    /**
    * Method for editing fields on a question in the database.
    *
    * @access public
    * @param string $questionId The id of the question being edited
    * @param string $field The field being edited
    * @param string $value The new value of the field
    * @return
    */
    public function editQuestionField($questionId,$field,$value)
    {
        $fields=array();
        $fields[$field]=$value;
        $this->update('id',$questionId,$fields);
    }

    /**
    * Method for deleting a question
    *
    * @access public
    * @param string $questionId  The id of the question to be deleted
    * @return
    */
    public function deleteQuestion($questionId)
    {
        $this->delete('id',$questionId);
        $this->dbRows->delete('question_id',$questionId);
        $this->dbColumns->delete('question_id',$questionId);
    }

    /**
    * Method for retrieving a question
    *
    * @access public
    * @param string $questionId  The id of the question to retrieve.
    * @return array $data The question information.
    */
    public function getQuestion($questionId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE id='$questionId'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for listing all questions
    *
    * @access public
    * @param string $surveyId The id of the survey
    * @param string $questionOrder The order number of the question
    * @return array $data  All question information.
    */
    public function listQuestions($surveyId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE survey_id='$surveyId'";
        $sql.=" ORDER BY question_order ";
        $data=$this->getArray($sql);
        return $data;
    }

    /**
    * Method for retrieving a question by question order
    *
    * @access public
    * @param string $surveyId The id of the survey
    * @param string $questionOrder The order number of the question
    * @return array $data  All question information.
    */
    public function getQuestionByQuestionOrder($surveyId, $questionOrder)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE survey_id='$surveyId'";
        $sql.=" AND question_order='$questionOrder'";
        $data=$this->getArray($sql);

        return $data;
    }

    /**
    * Method to copy questions for a survey
    *
    * @access public
    * @param string $surveyId The id fo the survey to copy questions from
    * @param string $newSurveyId The id of the survey to copy questions to
    * @return
    */
    public function copyQuestions($surveyId,$newSurveyId)
    {
        $arrQuestionList=$this->listQuestions($surveyId);
        if(!empty($arrQuestionList)){
            foreach($arrQuestionList as $question){
                $questionId=array_shift($question);
                $question['survey_id']=$newSurveyId;
                $question['creator_id']=$this->userId;
                $question['date_created']=date('Y-m-d H:i:s');
                $question['updated']=date('Y-m-d H:i:s');
                unset($question['modifier_id']);
                unset($question['date_modified']);
                unset($question['puid']);
                $newQuestionId=$this->insert($question);
                $this->dbRows->copyRows($questionId,$newSurveyId,$newQuestionId);
                $this->dbColumns->copyColumns($questionId,$newSurveyId, $newQuestionId);
            }
        }
    }

    /**
    * Method to copy questions for a survey
    *
    * @access public
    * @param string $questionId The id of the question to copy
    * @param string $newSurveyId The id of the new survey to copy questions to
    * @return string $newQuestionId The id of the new question
    */
    public function copySingleQuestion($questionId,$newSurveyId)
    {
        $arrQuestionData=$this->getQuestion($questionId);
        $arrQuestionData=$arrQuestionData['0'];
        unset($arrQuestionData['id']);
        $arrQuestionData['survey_id']=$newSurveyId;
        $arrQuestionData['creator_id']=$this->userId;
        $arrQuestionData['date_created']=date('Y-m-d H:i:s');
        $arrQuestionData['updated']=date('Y-m-d H:i:s');
        unset($arrQuestionData['modifier_id']);
        unset($arrQuestionData['date_modified']);
        unset($arrQuestionData['puid']);
        $newQuestionId=$this->insert($arrQuestionData);
        $this->dbRows->copyRows($questionId,$newSurveyId,$newQuestionId);
        $this->dbColumns->copyColumns($questionId,$newSurveyId,$newQuestionId);
        return $newQuestionId;
    }
}
?>
