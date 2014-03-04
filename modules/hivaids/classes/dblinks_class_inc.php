<?php
/**
* dblinks class extends object
* @package hivaids
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* dblinks class
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class dblinks extends dbtable
{
    /**
    * Constructor method
    */
    public function init()
    {
        parent::init('tbl_hivaids_links');
        $this->table = 'tbl_hivaids_links';
        
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }
    
    /**
    * Method to get a page of links
    *
    * @access public
    * @param string $reference
    * @return array $data
    */
    public function getPage($reference = 'main')
    {
        $sql = "SELECT * FROM {$this->table}
            WHERE reference = '{$reference}'";
        
        $data = $this->getArray($sql);
        return $data;
    }
    
    /**
    * Method to add / update a new links page
    *
    * @access public
    * @param string $id The row id of the links page
    * @return void
    */
    public function addPage($id = NULL)
    {
        $fields = array();
        $fields['reference'] = 'main';
        $fields['linkspage'] = $this->getParam('linkspage');
        $fields['updated'] = $this->now();
        
        if(!empty($id)){
            $fields['modifierid'] = $this->userId;
            $this->update('id', $id, $fields);
        }else{
            $fields['creatorid'] = $this->userId;
            $fields['datecreated'] = $this->now();
            $id = $this->insert($fields);
        }
    }
}
?>