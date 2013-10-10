<?php
/**
* dbpoll class extends dbtable
* @package poll
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbpoll class
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class dbpoll extends dbtable
{
    /**
    * @var string $context The current context - set it to root/lobby
    * @access private
    */
    private $context = 'root';
    
    /**
    * Constructor method
    */
    public function init()
    {
        try{
            parent::init('tbl_poll');
            $this->table = 'tbl_poll';
            $this->tblQuestions = 'tbl_poll_questions';
            
            $this->objUser = $this->getObject('user', 'security');
            $this->objLanguage = $this->getObject('language', 'language');
            
            $this->userId = $this->objUser->userId();
        } catch (Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }
    
    /**
    * Method to get the current poll - if there are no configuration settings for the current context, create a default set.
    *
    * @access public
    * @param string $contextCode
    * @return string $id The current poll id
    */
    public function getPoll($contextCode)
    {
        $sql = "SELECT id FROM {$this->table} WHERE context_code = '{$contextCode}' ";
            
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0]['id'];
        }else{
        $id = $this->createDefaultPoll($contextCode);
        return $id;
	}
    }
    
    /**
    * Method to get the current poll configuration settings
    *
    * @access public
    * @param string $id
    * @return array $data
    */
    public function getPollData($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = '{$id}' ";
            
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return array();
    }

    /**
    * Method to get the current poll configuration settings
    *
    * @access public
    * @param string $contextCode
    * @return array $data
    */
    public function getPollByContext($contextCode)
    {
        $sql = "SELECT * FROM {$this->table} WHERE context_code = '{$contextCode}' ";
            
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return array();
    }

    /**
    * Method to get the questions for the current context
    *
    * @access public
    * @param string $pollId
    * @return void
    */
    public function saveConfig($pollId)
    {
        $fields = array();
        $fields['cycle_rate'] = $this->getParam('rate');
        $fields['active_date'] = $this->getParam('date');
        $fields['modifier_id'] = $this->userId;
        $fields['updated'] = $this->now();
        
        $this->update('id', $pollId, $fields);
        return $pollId;
    }

    /**
    * Method to get the questions for the current context
    *
    * @access private
    * @param string $contextCode
    * @return string $id The new poll id
    */
    private function createDefaultPoll($contextCode)
    {
        $fields = array();
        $fields['context_code'] = $contextCode;
        $fields['cycle_rate'] = 'weekly';
        $fields['is_repeated'] = '0';
        $fields['randomise'] = '0';
        $fields['creator_id'] = $this->userId;
        $fields['date_created'] = $this->now();
        $fields['updated'] = $this->now();
        
        $id = $this->insert($fields);
        return $id;
    }
    
    /**
    * Method to get the questions for the current context
    *
    * @access public
    * @param string $contextCode
    * @return array $data
    */
    public function getPolls($contextCode)
    {
        $sql = "SELECT * FROM {$this->tblQuestions} AS quest
            WHERE poll_id = (SELECT id FROM {$this->table} WHERE context_code = '{$contextCode}') ";
            
        $data = $this->getArray($sql);
        return $data;
    } 
}
?>
