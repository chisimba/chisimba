<?php
/**
* dbanswers class extends dbtable
* @package poll
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbanswers class
* @author Paul Mungai
* @copyright (c) 2008 UWC & UoN
* @version 0.1
*/

class dbanswers extends dbtable
{   
    /**
    * Constructor method
    */
    public function init()
    {
        try{
            parent::init('tbl_poll_answers');
            $this->table = 'tbl_poll_answers';
            
            $this->objUser = $this->getObject('user', 'security');
            $this->objLanguage = $this->getObject('language', 'language');
            
            $this->userId = $this->objUser->userId();
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }
    
    /**
    * Method to get the current answer
    *
    * @access public
    * @param string $questionId
    * @return array $answer
    */
    public function getCurrentAnswer($questionId, $answer)
    {
        $sql = "SELECT * FROM {$this->table} AS answer
            WHERE question_id = '{$questionId}' AND answer = '{$answer}' ";
            
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return array();
    }
    
    /**
    * Method to get the answers for the current question
    *
    * @access public
    * @param string $questionId
    * @return array $data
    */
    public function getAnswers($questionId)
    {
        $sql = "SELECT * FROM {$this->table}
            WHERE question_id = '{$questionId}' ";
            
        $data = $this->getArray($sql);
        return $data;
    }
    /**
    * Method to get the answers for the current question and delete for update
    *
    * @access public
    * @param string $questionId
    * @return array $data
    */
    public function getAnswersDelete($questionId)
    {
        $sql = "SELECT * FROM {$this->table}
            WHERE question_id = '{$questionId}' ";
            
        $data = $this->getArray($sql);
	if (!empty($data)) {	
	    foreach($data as $dataItem) {
		$this->deleteAnswer($dataItem['id']);
	    }
	}
        return $data;
    }
    /**
    * Method to get the answers for the current question and delete for update
    * where the answer is either boolean or true false
    * @access public
    * @param string $questionId
    * @return array $data
    */
    public function delAnswersBoolTf($questionId)
    {
        $sql = "SELECT * FROM {$this->table}
            WHERE question_id = '{$questionId}' ";
            
        $data = $this->getArray($sql);
	if (!empty($data)) {	
	    foreach($data as $dataItem) {
		if($dataItem['answer']=='True' || $dataItem['answer']=='False' || $dataItem['answer']=='Yes' || $dataItem['answer']=='No'){
			$this->deleteAnswer($dataItem['id']);
		}
	    }
	}
        return $data;
    }
    
    /**
    * Method to get an answer for the question
    *
    * @access public
    * @param string $id
    * @return array $data
    */
    public function getAnswer($id)
    {
        $sql = "SELECT * FROM {$this->table}
            WHERE id = '{$id}' ";
            
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return array();
    }
    
    /**
    * Method to save an answer
    *
    * @access public
    * @param string $qnId
    * @param string $answer
    * @param string $id
    * @return array $id
    */
    public function saveAnswer($qnId, $answer, $id = NULL)
    {
        $fields = array();
        $fields['question_id'] = $qnId;
        $fields['answer'] = $answer;
        $fields['updated'] = $this->now();
        
        if(isset($id) && !empty($id)){
            $fields['modifier_id'] = $this->userId;            
            $this->update('id', $id, $fields);
        }else{
            $fields['creator_id'] = $this->userId;
            $fields['date_created'] = $this->now();            
            $id = $this->insert($fields);
        }
        return $id;
    }
      
    /**
    * Method to delete an answer
    *
    * @access public
    * @param string $id
    * @return void
    */
    public function deleteAnswer($id)
    {
        $this->delete('id', $id);
    }
}
?>
