<?php
/**
* dbresponse class extends dbtable
* @package poll
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbresponse class
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class dbresponse extends dbtable
{    
    /**
    * Constructor method
    */
    public function init()
    {
        try{
            parent::init('tbl_poll_responses');
            $this->table = 'tbl_poll_responses';
            
            $this->objUser = $this->getObject('user', 'security');
            $this->objLanguage = $this->getObject('language', 'language');
            
            $this->userId = $this->objUser->userId();
            $this->dbAnswers = $this->getObject('dbanswers', 'poll');
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }
    
    /**
    * Method to save a response to a poll question
    *
    * @access public
    * @return void
    */
    public function addResponse()
    {
        $fields = array();
        $fields['question_id'] = $this->getParam('questionId');
        $fields['user_id'] = $this->userId;
	$qnId = $this->getParam('questionId');
	$answer = $this->getParam('answer');
        $myResult = $this->dbAnswers->getCurrentAnswer($qnId, $answer);
	$answerId = $myResult[0]['id'];

        $type = $this->getParam('questionType');        
        if($type == 'bool' || $type == 'yes' || $type == 'open'){
            $fields['answer_id'] = $answerId;
        }

	$qnId = $this->getParam('questionId');
        $sql = "SELECT * FROM {$this->table}
            WHERE user_id = '{$this->userId}' and question_id = '{$qnId}'";
            
        $data = $this->getArray($sql);
        if(!empty($data)){
            $id = $data[0]['id'];
	    $fields['date_modified'] = $this->now();
            $this->update('id', $id, $fields);
        }else{
	    $fields['date_created'] = $this->now();
	    $id = $this->insert($fields);
	}
        return $id;
    }
    /**
    * Method to get poll responses
    *
    * @access public
    * @param string $pollId
    * @return array $data
    */
    public function getPollResponses($qnId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE question_id = '{$qnId}'";            
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return array();
    }
    /**
    * Method to get poll answer responses
    *
    * @access public
    * @param string $pollId
    * @return array $data
    */
    public function getAnswerResponses($qnId, $answer)
    {
        $sql = "SELECT * FROM {$this->table} WHERE question_id = '{$qnId}' AND answer_id = '{$answer}'";            
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return array();
    }

}
?>
