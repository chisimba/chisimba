<?php
/* ----------- data class extends dbTable for tbl_survey_question_rows ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_survey_question_rows
* @author Kevin Cyster
*/

class dbquestionrow extends dbTable
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
        parent::init('tbl_survey_question_rows');
        $this->table='tbl_survey_question_rows';

        $this->objUser=&$this->newObject('user','security');
        $this->userId=$this->objUser->userId();
    }

    /**
    * Method for adding a row to the database.
    *
    * @access public
    * @param string $surveyId  The id of the survey
    * @param string $questionId  The id of the question the rows are being added to
    * @return
    */
    public function addQuestionRows($surveyId, $questionId)
    {
        $arrRowData=$this->getSession('row');
        $arrRowList=$this->listQuestionRows($questionId);
        $i=empty($arrRowList)?'1':count($arrRowList)+1;
        foreach($arrRowData as $row){
            if($row['id']==''){
                $fields=array();
                $fields['survey_id']=$surveyId;
                $fields['question_id']=$questionId;
                $fields['row_order']=$i;
                $fields['row_text']=$row['row_text'];
                $fields['date_created']=date("Y-m-d H:i:s");
                $fields['creator_id']=$this->userId;
                $fields['updated']=date("Y-m-d H:i:s");
                $this->insert($fields);
                $i++;
            }
        }
    }

    /**
    * Method for editing a row on the database.
    *
    * @access public
    * @return
    */
    public function editQuestionRows()
    {
        $arrRowData=$this->getSession('row');
        foreach($arrRowData as $row){
            $rowId=$row['id'];
            if($rowId!=''){
                $fields=array();
                $fields['row_text']=$row['row_text'];
                $fields['date_modified']=date("Y-m-d H:i:s");
                $fields['modifier_id']=$this->userId;
                $fields['updated']=date("Y-m-d H:i:s");
                $this->update('id',$rowId,$fields);
            }
        }
    }

    /**
    * Method for deleting rows
    *
    * @access public
    * @return
    */
    public function deleteQuestionRows()
    {
        $arrRowData=$this->getSession('deletedRows');
        if($arrRowData != NULL){
            foreach($arrRowData as $rowId){
                $this->delete('id',$rowId);
            }
        }
    }

    /**
    * Method for retrieving a row
    *
    * @access public
    * @param string $rowId  The row to retrieve.
    * @return array $data The row information.
    */
    public function getQuestionRow($rowId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE id='$rowId'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for listing all rows
    *
    * @access public
    * @return array $data  All row information.
    */
    public function listQuestionRows($questionId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE question_id='$questionId'";
        $sql.=" ORDER BY 'row_order' ";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method to copy question rows for a survey
    *
    * @access public
    * @param string $questionId The id fo the question to copy rows from
    * @param string $newSurveyId The id of the new survey
    * @param string $newQuestionId The id of the question to copy rows to
    * @return
    */
    public function copyRows($questionId,$newSurveyId, $newQuestionId)
    {
        $arrRowList=$this->listQuestionRows($questionId);
        if(!empty($arrRowList)){
            foreach($arrRowList as $row){
                unset($row['id']);
                $row['survey_id']=$newSurveyId;
                $row['question_id']=$newQuestionId;
                $row['creator_id']=$this->userId;
                $row['date_created']=date('Y-m-d H:i:s');
                $row['updated']=date('Y-m-d H:i:s');
                unset($row['modifier_id']);
                unset($row['date_modified']);
                unset($row['puid']);
                $this->insert($row);
            }
        }
    }
}
?>
