<?php
/**
* dbquestions class extends dbtable
* @package poll
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbquestions class
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class dbquestions extends dbtable
{   
    /**
    * @var string $contextCode The current context .. to do.
    * @access private
    */
    private $contextCode = 'root';
    
    /**
    * Constructor method
    */
    public function init()
    {
        try{
            parent::init('tbl_poll_questions');
            $this->table = 'tbl_poll_questions';
            
            $this->objUser = $this->getObject('user', 'security');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->dbAnswers = $this->getObject('dbanswers', 'poll');
            $this->dbPoll = $this->getObject('dbpoll', 'poll');
            
            $this->userId = $this->objUser->userId();
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }
    
    /**
    * Method to get the current poll
    *
    * @access public
    * @param string $pollId
    * @return array $data
    */
    public function getCurrentPoll($pollId, $order)
    {
        $sql = "SELECT * FROM {$this->table} AS quest
            WHERE poll_id = '{$pollId}' AND order_num = '{$order}' ";
            
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return array();
    }
    
    /**
    * Method to get the questions for the current poll
    *
    * @access public
    * @param string $pollId
    * @return array $data
    */
    public function getQuestions($pollId)
    {
        $sql = "SELECT * FROM {$this->table}
            WHERE poll_id = '{$pollId}' ";
            
        $data = $this->getArray($sql);
        return $data;
    }
    /**
    * Method to get the questions for the current poll with a given visiblility state
    *
    * @access public
    * @param string $pollId
    * @param string $access
    * @return array $data
    */
    public function getQuestionsAccess($pollId, $visible)
    {
        $sql = "SELECT * FROM {$this->table}
            WHERE poll_id = '{$pollId}' and is_visible = '{$visible}'";
            
        $data = $this->getArray($sql);
        return $data;
    }
    
    /**
    * Method to get a question for the current poll / context
    *
    * @access public
    * @param string $id
    * @return array $data
    */
    public function getQuestion($id)
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
    * Method to save a question
    *
    * @access public
    * @param string $pollId
    * @return array $data
    */
    public function saveQuestion($pollId, $id = NULL)
    {
        $fields = array();
        $fields['poll_id'] = $pollId;
        $fields['question'] = $this->getParam('question');
        $fields['question_type'] = $this->getParam('type');
        $fields['is_visible'] = $this->getParam('visible');
        $fields['updated'] = $this->now();
        
        if(isset($id) && !empty($id)){
            $fields['modifier_id'] = $this->userId;
            
            $this->update('id', $id, $fields);
        }else{
            $order = $this->getLastOrder($pollId) + 1;
        
            $fields['order_num'] = $order;
            $fields['creator_id'] = $this->userId;
            $fields['date_created'] = $this->now();
            
            $id = $this->insert($fields);
        }
        return $id;
    }
    /**
    * Method to save a question where answer type is open
    *
    * @access public
    * @param string $pollId
    * @return array $data
    */
    public function saveQnTypeOpen($pollId, $id = NULL)
    {
        $fields = array();
        $fields['poll_id'] = $pollId;
        $fields['question'] = $this->getParam('question');
        $fields['question_type'] = $this->getParam('type');
        $fields['is_visible'] = $this->getParam('visible');
        $fields['updated'] = $this->now();
        
        if(isset($id) && !empty($id)){
            $fields['modifier_id'] = $this->userId;
            
            $this->update('id', $id, $fields);
        }else{
            $order = $this->getLastOrder($pollId) + 1;
        
            $fields['order_num'] = $order;
            $fields['creator_id'] = $this->userId;
            $fields['date_created'] = $this->now();
            
            $id = $this->insert($fields);
        }
	//Save the answers now
        $qnType = $this->getParam('type');
	//Remove any previous (Boolean or True False Only) answers
	$del = $this->dbAnswers->delAnswersBoolTf($id);
        return $id;
    }
    /**
    * Method to save a question and answers
    *
    * @access public
    * @param string $pollId
    * @return array $data
    */
    public function saveQnandAnswers($pollId, $id = NULL)
    {
        $fields = array();
        $fields['poll_id'] = $pollId;
        $fields['question'] = $this->getParam('question');
        $fields['question_type'] = $this->getParam('type');
        $fields['is_visible'] = $this->getParam('visible');
        $fields['updated'] = $this->now();
        
        if(isset($id) && !empty($id)){
            $fields['modifier_id'] = $this->userId;
            
            $this->update('id', $id, $fields);
        }else{
            $order = $this->getLastOrder($pollId) + 1;
        
            $fields['order_num'] = $order;
            $fields['creator_id'] = $this->userId;
            $fields['date_created'] = $this->now();
            
            $id = $this->insert($fields);
        }
	//Save the answers now
        $qnType = $this->getParam('type');
	//Remove any previous answers
	$del = $this->dbAnswers->getAnswersDelete($id);
	//Add the answers
	$yes = 'Yes';
	$no = 'No';
	$true = 'True';
	$false = 'False';
	if($qnType=='yes'){
	     $first = $this->dbAnswers->saveAnswer($id,$yes, null);
	     $second = $this->dbAnswers->saveAnswer($id,$no, null);
	}else{
	     $first = $this->dbAnswers->saveAnswer($id,$true, null);
	     $second = $this->dbAnswers->saveAnswer($id,$false, null);
	}
        return $id;
    }
    
    /**
    * Method to get the last order num
    *
    * @access private
    * @param string $pollId
    * @return integer $orderNum
    */
    private function getLastOrder($pollId)
    {
        $sql = "SELECT order_num FROM {$this->table}
            WHERE poll_id = '{$pollId}'
            ORDER BY order_num LIMIT 1 ";
            
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0]['order_num'];
        }
        return 0;
    }
    
    /**
    * Method to delete a question
    *
    * @access public
    * @param string $id
    * @return void
    */
    public function deleteQuestion($id)
    {
        $this->delete('id', $id);
    }
}
?>
