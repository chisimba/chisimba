<?php
/**
* dbCitations class extends dbTable
* @package etd
* @filesource
*/

/**
* Class for accessing the table containing the list of citations for a resource
* @author Megan Watson
* @copyright (c) 2007 University of the Western Cape
* @version 0.1
*/

class dbCitations extends dbTable
{
    /**
    * Constructor for the class
    *
    * @access public
    * @return void
    */
    public function init()
    {
        parent::init('tbl_etd_citations');
        $this->table = 'tbl_etd_citations';
                        
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }
    
    /**
    * Method to add / update a citation list
    *
    * @access private
    * @return void
    */
    public function addList($list, $submitId = NULL, $id = NULL)
    {
        $fields = array();
        $fields['citation_list'] = $list;
        
        if(isset($id) && !empty($id)){
            $fields['modifier_id'] = $this->userId;
            $fields['updated'] = date('Y-m-d H:i:s');
            $this->update('id', $id, $fields);
        }else{
            $fields['submission_id'] = $submitId;
            $fields['creator_id'] = $this->userId;
            $fields['date_created'] = date('Y-m-d H:i:s');
            $fields['updated'] = date('Y-m-d H:i:s');
            $id = $this->insert($fields);
        }
        return $id;
    }
    
    /**
    * Method to get a citation list.
    *
    * @access private
    * @param string $submitId The associated resource
    * @return array The citation list
    */
    public function getList($submitId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE submission_id = '{$submitId}'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
    
    /**
    * Method to remove a citation list
    *
    * @access public
    * @return void
    */
    public function deleteList($id)
    {
        $this->delete('id', $id);
    }
}
?>