<?php
/* ----------- data class extends dbTable for tbl_survey_response ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_survey_response
* @author Kevin Cyster
*/

class dbresponse extends dbTable
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
        parent::init('tbl_survey_response');
        $this->table='tbl_survey_response';

        $this->objUser=&$this->newObject('user','security');
        $this->userId=$this->objUser->userId();
    }

    /**
     * Method for adding a row to the database.
     *
     * @access public
     * @param string $surveyId  The id of the survey the response is being added to
     * @param integer $respondentNumber The number of times the survey has been answered
     * @return string $responseId The id of the response
     */
    public function addResponse($surveyId,$respondentNumber)
    {
        $fields=array();
        $fields['survey_id']=$surveyId;
        $fields['user_id']=$this->userId;
        $fields['respondent_number']=$respondentNumber;
        $fields['creator_id']=$this->userId;
        $fields['date_created']=date('Y-m-d H:i:s');
        $fields['updated']=date('Y-m-d H:i:s');
        $responseId = $this->insert($fields);
        return $responseId;
    }

    /**
    * Method for listing all rows
    *
    * @access public
    * @param string $surveyId The id of the survey
    * @return array $data  All row information.
    */
    public function listResponses($surveyId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE survey_id='$surveyId'";
        $sql.=" ORDER BY 'respondent_number' ";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
}
?>