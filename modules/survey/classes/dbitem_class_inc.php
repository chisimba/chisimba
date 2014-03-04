<?php
/* ----------- data class extends dbTable for tbl_survey_item ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_survey_item
* @author Kevin Cyster
*/

class dbitem extends dbTable
{
    /**
     * @var string $table The name of the database table to be affected
     * @access private
     */
    private $table;

    /**
     * @var string $tblResponse An additional associated table
     * @access private
     */
    private $tblResponse;

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
        parent::init('tbl_survey_item');
        $this->table='tbl_survey_item';
        $this->tblResponse='tbl_survey_response';

        $this->objUser=&$this->newObject('user','security');
        $this->userId=$this->objUser->userId();
    }

    /**
     * Method for adding a row to the database.
     *
     * @access public
     * @param string $responseId  The id of the response
     * @param string $answerId  The id of the answer
     * @param string $surveyId The id of the survey
     * @param string $questionId The id of the question
     * @param string $itemName The name/type of the item
     * @param string $itemValue The item value
     * @return string $itemId The id of the item that was added
     */
    public function addItems($responseId,$answerId,$surveyId,$questionId,$itemName,$itemValue)
    {
        $fields=array();
        $fields['response_id']=$responseId;
        $fields['answer_id']=$answerId;
        $fields['survey_id']=$surveyId;
        $fields['question_id']=$questionId;
        $fields['item_name']=$itemName;
        $fields['item_value']=$itemValue;
        $fields['creator_id']=$this->userId;
        $fields['date_created']=date('Y-m-d H:i:s');
        $fields['updated']=date('Y-m-d H:i:s');
        $itemId = $this->insert($fields);
        return $itemId;
    }

    /**
    * Method for listing all rows for responses
    *
    * @access public
    * @param string $responseId The response id
    * @param string $questionId The response id
    * @return array $data  All row information.
    */
    public function getResponses($responseId,$questionId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE response_id='$responseId' AND question_id='$questionId'";
        $sql.=" ORDER BY 'question_id','item_name' ";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for listing all rows for results
    *
    * @access public
    * @param string $questionId The question id
    * @return array $data  All row information.
    */
    public function getResults($questionId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE question_id='$questionId'";
        $sql.=" ORDER BY 'item_name'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for listing all rows for results
    *
    * @access public
    * @param string $questionId The question id
    * @return array $data  All row information.
    */
    public function getOpenResults($questionId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" RIGHT JOIN ".$this->tblResponse." AS response";
        $sql.=" ON ".$this->table.".response_id=response.id";
        $sql.=" WHERE question_id='$questionId'";
        $sql.=" ORDER BY 'respondent_number','item_name'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
}
?>