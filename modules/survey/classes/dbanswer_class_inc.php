<?php
/* ----------- data class extends dbTable for tbl_survey_answer ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_survey_answer
* @author Kevin Cyster
*/

class dbanswer extends dbTable
{
    /**
     * @var string $table The name of the database table to be affected
     * @access private
     */
    private $table;

    /**
     * @var object $dbItems The dbitem class in the survey module
     * @access private
     */
    private $dbItems;

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
        parent::init('tbl_survey_answer');
        $this->table='tbl_survey_answer';

        $this->dbItems=&$this->newObject('dbitem');
        $this->objUser=&$this->newObject('user','security');
        $this->userId=$this->objUser->userId();
    }

    /**
     * Method for adding a row to the database.
     *
     * @access public
     * @param string $responseId  The id of the response the answer is being added to
     * @param string $surveyId The id of the survey
     */
    public function addAnswer($responseId,$surveyId)
    {
        $arrAnswerData=$this->getSession('answer');
        foreach($arrAnswerData as $answer){
            $questionId=$answer['question_id'];
            $fields=array();
            $fields['response_id']=$responseId;
            $fields['survey_id']=$surveyId;
            $fields['question_id']=$questionId;
            if($answer['answered']==TRUE){
                $fields['answer_given']='1';
            }
            $fields['creator_id']=$this->userId;
            $fields['date_created']=date('Y-m-d H:i:s');
            $fields['updated']=date('Y-m-d H:i:s');
            $answerId=$this->insert($fields);
            foreach($answer as $key=>$item){
                if($key!='question_id' && $key!='question_comment' && $key!='answered'){
                    $this->dbItems->addItems($responseId,$answerId,$surveyId,$questionId,$key,$item);
                }
            }
        }
    }

    /**
    * Method for listing all rows
    *
    * @access public
    * @return array $data  All row information.
    */
    public function listRows($questionId)
    {
        $sql="SELECT * FROM ".$this->table;
        $sql.=" WHERE question_id='$questionId'";
        $data=$this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }
}
?>