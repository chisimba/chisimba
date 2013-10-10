<?php
/* ----------- data class extends dbTable for tbl_survey_question_columns ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_survey_question_columns
* @author Kevin Cyster
*/

class dbquestioncol extends dbTable
{
    /**
     * @var string $table The name of the database table to be affected
     * @access private
     */
    private $table;

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
        parent::init('tbl_survey_question_cols');
        $this->table='tbl_survey_question_cols';

        $this->objUser=&$this->newObject('user','security');
        $this->userId=$this->objUser->userId();
    }

    /**
    * Method for adding a column to the database.
    *
    * @access public
    * @param string $surveyId  The id of the survey
    * @param string $questionId  The id of the question the columns are being added to
    * @return
    */
    public function addQuestionColumns($surveyId, $questionId)
    {
        $arrColumnData=$this->getSession('column');
        $arrColumnList=$this->listQuestionColumns($questionId);
        $i=empty($arrColumnList)?'1':count($arrColumnList)+1;
        foreach($arrColumnData as $column){
            if($column['id']==''){
                $fields=array();
                $fields['survey_id']=$surveyId;
                $fields['question_id']=$questionId;
                $fields['column_order']=$i;
                $fields['column_text']=$column['column_text'];
                $fields['date_created']=date("Y-m-d H:i:s");
                $fields['creator_id']=$this->userId;
                $fields['updated']=date("Y-m-d H:i:s");
                $this->insert($fields);
                $i++;
            }
        }
    }

    /**
    * Method for editing a column on the database.
    *
    * @access public
    * @return
    */
    public function editQuestionColumns()
    {
        $arrColumnData=$this->getSession('column');
        foreach($arrColumnData as $column){
            $columnId=$column['id'];
            if($columnId!=''){
                $fields=array();
                $fields['column_text']=$column['column_text'];
                $fields['date_modified']=date("Y-m-d H:i:s");
                $fields['modifier_id']=$this->userId;
                $fields['updated']=date("Y-m-d H:i:s");
                $this->update('id',$columnId,$fields);
            }
        }
    }

    /**
    * Method for deleting columns
    *
    * @access public
    * @return
    */
    public function deleteQuestionColumns()
    {
        $arrColumnData=$this->getSession('deletedColumns');
        if($arrColumnData != NULL){
            foreach($arrColumnData as $columnId){
                $this->delete('id',$columnId);
            }
        }
    }

    /**
    * Method for retrieving a column
    *
    * @access public
    * @param string $columnId  The column to retrieve.
    * @return array $data The column information.
    */
    public function getQuestionColumn($columnId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE id='$columnId'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for listing all columns
    *
    * @access public
    * @return array $data  All column information.
    */
    public function listQuestionColumns($questionId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE question_id='$questionId'";
        $sql.=" ORDER BY 'column_order' ";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to copy question columns for a survey
    *
    * @access public
    * @param string $questionId The id fo the question to copy columns from
    * @param string $newSurveyId The id of the survey
    * @param string $newQuestionId The id of the question to copy columns to
    * @return
    */
    public function copyColumns($questionId,$newSurveyId, $newQuestionId)
    {
        $arrColumnsList=$this->listQuestionColumns($questionId);
        if(!empty($arrColumnsList)){
            foreach($arrColumnsList as $column){
                unset($column['id']);
                $column['survey_id']=$newSurveyId;
                $column['question_id']=$newQuestionId;
                $column['creator_id']=$this->userId;
                $column['date_created']=date('Y-m-d H:i:s');
                $column['updated']=date('Y-m-d H:i:s');
                unset($column['modifier_id']);
                unset($column['date_modified']);
                unset($column['puid']);
                $this->insert($column);
            }
        }
    }
}
?>