<?php
/* ----------- data class extends dbTable for tbl_survey_page_questions ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_survey_page_questions
* @author Kevin Cyster
*/

class dbpagequestions extends dbTable
{
    /**
     * @var string $table The name of the atabase table to be affected
     * @access private
     */
    private $table;

    /**
     * @var object $dbQuestion The dbQuestion class in the survey module
     * @access private
     */
    private $dbQuestion;

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
        parent::init('tbl_survey_page_questions');
        $this->table='tbl_survey_page_questions';

        $this->dbQuestion=&$this->newObject('dbquestion');
        $this->objUser=&$this->newObject('user','security');
        $this->userId=$this->objUser->userId();
    }

    /**
     * Method for adding a row to the database.
     *
     * @access public
     * @param string $pageId  The id of the page questions are being added to
     * @param string $surveyId  The id of the survey
     * @param array $arrQuestionId An array containing the question ids
     * @param array $arrQuestionId An array containing the question ids
     * @param array $arrQuestionId An array containing the question ids
     * @return
     */
    public function addPageQuestions($pageId,$surveyId,$arrQuestionId)
    {
        $arrPageQuestionList=$this->listRows($pageId);
        $i=!empty($arrPageQuestionList)?count($arrPageQuestionList)+1:'1';
        foreach($arrQuestionId as $questionId){
            $fields=array();
            $fields['page_id']=$pageId;
            $fields['survey_id']=$surveyId;
            $fields['question_id']=$questionId;
            $fields['question_order']=$i;
            $fields['creator_id']=$this->userId;
            $fields['date_created']=date('Y-m-d H:i:s');
            $fields['updated']=date('Y-m-d H:i:s');
            $this->insert($fields);
            $i++;
        }
    }

    /**
     * Method for editing a row on the database.
     *
     * @access public
     * @param string $id  The id of the record to be edited
     * @param string $pageId  The id of the page
     * @param string $pageQuestionOrder The order of the questions on the page
     * @return
     */
    public function editRecord($id,$pageId,$pageQuestionOrder)
    {
        $fields=array();
        $fields['page_id']=$pageId;
        $fields['question_order']=$pageQuestionOrder;
        $fields['modifier_id']=$this->userId;
        $fields['date_modified']=date('Y-m-d H:i:s');
        $fields['updated']=date('Y-m-d H:i:s');
        $this->update('id',$id,$fields);
    }

    /**
    * Method for deleting a row
    *
    * @access public
    * @param string $pageQuestionId  The row to be deleted
    * @return
    */
    public function deleteRecordById($pageQuestionId)
    {
        $this->delete('id',$pageQuestionId);
    }

    /**
    * Method for deleting a row
    *
    * @access public
    * @param string $questionId  The row to be deleted
    * @return
    */
    public function deleteRecordByQuestionId($questionId)
    {
        $this->delete('question_id',$questionId);
    }

    /**
    * Method for listing all rows
    *
    * @access public
    * @param string $pageId The page to select
    * @return array $data  All row information.
    */
    public function listRows($pageId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE page_id='$pageId'";
        $sql.=" ORDER BY 'question_order' ";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for listing all pages per survey
    *
    * @access public
    * @param string $surveyId The survey to select
    * @return array $data  All row information.
    */
    public function listSurveyPages($surveyId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE survey_id='$surveyId'";
        $sql.=" ORDER BY 'page_id','question_order' ";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to return a specific question
    *
    * @access public
    * @param string $questionId The question to select
    * @return array $data  All row information.
    */
    public function getQuestionRecord($questionId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE question_id='$questionId'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for retrieving a row
    *
    * @access public
    * @param string $pageId The id of the page
    * @param string $orderNo  The row to retrieve.
    * @return array $data The row information.
    */
    public function getRecordByOrderNo($pageId,$orderNo)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE page_id='$pageId'";
        $sql.=" AND question_order='$orderNo'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for editing fields on a page question in the database.
    *
    * @access public
    * @param string $pageQuestionId The id of the page question being edited
    * @param string $field The field being edited
    * @param string $value The new value of the field
    * @return
    */
    public function editPageQuestionField($pageQuestionId,$field,$value)
    {
        $fields=array();
        $fields[$field]=$value;
        $this->update('id',$pageQuestionId,$fields);
    }

    /**
    * Method to copy page questions for a survey
    *
    * @access public
    * @param string $pageId The id of the page to copy from
    * @param string $newPageId The id of the page to copy to
    * @param string $newSurveyId The id of the survey to copy the page questions to
    * @return
    */
    function copyPageQuestions($pageId,$newPageId,$newSurveyId)
    {
        $arrPageQuestionList=$this->listRows($pageId);
        if(!empty($arrPageQuestionList)){
            foreach($arrPageQuestionList as $pageQuestion){
                $questionId=$pageQuestion['question_id'];
                $newQuestionId=$this->dbQuestion->copySingleQuestion($questionId,$newSurveyId);
                unset($pageQuestion['id']);
                $pageQuestion['page_id']=$newPageId;
                $pageQuestion['survey_id']=$newSurveyId;
                $pageQuestion['question_id']=$newQuestionId;
                $pageQuestion['creator_id']=$this->userId;
                $pageQuestion['date_created']=date('Y-m-d H:i:s');
                $pageQuestion['updated']=date('Y-m-d H:i:s');
                unset($pageQuestion['modifier_id']);
                unset($pageQuestion['date_modified']);
                unset($pageQuestion['puid']);
                $this->insert($pageQuestion);
            }
        }
    }
}
?>