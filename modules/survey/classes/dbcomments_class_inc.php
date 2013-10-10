<?php
/* ----------- data class extends dbTable for tbl_survey_comment ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_survey_comment
* @author Kevin Cyster
*/

class dbcomments extends dbTable
{
    /**
     * @var string $table The name of the database table to be affected
     * @access private
     */
    private $table;

    /**
     * @var string $tblResponse An additional associated database table
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
        parent::init('tbl_survey_comment');
        $this->table='tbl_survey_comment';
        $this->tblResponse='tbl_survey_response';

        $this->objUser=&$this->newObject('user','security');
        $this->userId=$this->objUser->userId();
    }

    /**
    * Method for adding a comment to the database.
    *
    * @access public
    * @param string $surveyId The surveyId of the questions comments are being added to
    * @return
    */

    public function addComments($responseId,$surveyId)
    {
        $arrAnswerData=$this->getSession('answer');
        foreach($arrAnswerData as $answer){
            if(isset($answer['question_comment']) && $answer['question_comment']!=''){
                $fields=array();
                $fields['response_id']=$responseId;
                $fields['survey_id']=$surveyId;
                $fields['question_id']=$answer['question_id'];
                $fields['question_comment']=$answer['question_comment'];
                $fields['date_created']=date("Y-m-d H:i:s");
                $fields['creator_id']=$this->userId;
                $fields['updated']=date("Y-m-d H:i:s");
                $commentId=$this->insert($fields);
            }
        }
    }

    /**
    * Method for retrieving comments on a question
    *
    * @access public
    * @param string $questionId The question id
    * @return array $data  All row information.
    */
    public function listComments($questionId)
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

    /**
    * Method for retrieving comments on a question
    *
    * @access public
    * @param string $responseId The response id
    * @param string $questionId The question id
    * @return array $data  All row information.
    */
    public function getComment($responseId,$questionId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE response_id='$responseId' AND question_id='$questionId'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
}
?>