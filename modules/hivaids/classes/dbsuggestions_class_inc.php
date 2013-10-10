<?php
/**
* dbsuggestions class extends object
* @package hivaids
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dbsuggestions class
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class dbsuggestions extends dbtable
{
    /**
    * Constructor method
    */
    public function init()
    {
        parent::init('tbl_hivaids_suggestions');
        $this->table = 'tbl_hivaids_suggestions';
        
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }
    
    /**
    * Method to get all the suggestions.
    *
    * @access public
    * @return array $data
    */
    public function getSuggestions()
    {
        $sql = "SELECT * FROM {$this->table} 
            ORDER BY updated DESC";
        
        $data = $this->getArray($sql);
        return $data;
    }
    
    /**
    * Method to add a suggestion
    *
    * @access public
    * @return void
    */
    public function addSuggestion()
    {
        $fields = array();
        $fields['suggestion'] = $this->getParam('suggestion');
        $fields['updated'] = $this->now();
        $fields['user_id'] = $this->userId;
        $id = $this->insert($fields);
        
        return $id;
    }
}
?>