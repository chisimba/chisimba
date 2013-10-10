<?php
/**
* dbProfiles class extends dbTable
* @package profiles
* @filesource
*/

/**
* Class for accessing the table containing the list of user profiles
*
* @author Megan Watson
* @copyright (c) 2007 University of the Western Cape
* @version 0.1
*/

class dbProfiles extends dbTable
{
    /**
    * Constructor for the class
    *
    * @access public
    * @return void
    */
    public function init()
    {
        parent::init('tbl_profiles');
        $this->table = 'tbl_profiles';
        $this->usrTable = 'tbl_users';
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }
    
    /**
    * Method to add / update a profile
    *
    * @access public
    * @return void
    */
    public function saveProfile($id = NULL)
    {
        $fields = array();
        $fields['profile'] = $this->getParam('profile');
        $fields['userid'] = $this->userId;
        $fields['updated'] = $this->now();
        
        if(isset($id) && !empty($id)){
            $fields['modifierid'] = $this->userId;
            $this->update('id', $id, $fields);
        }else{
            $fields['creatorid'] = $this->userId;
            $fields['datecreated'] = $this->now();
            $id = $this->insert($fields);
        }
        return $id;
    }
    
    /**
    * Method to get a profile.
    *
    * @access public
    * @param string $submitId The associated resource
    * @return array The citation list
    */
    public function getProfile($userId)
    {
        $sql = "SELECT *, pr.id AS prid FROM {$this->table} AS pr, {$this->usrTable} AS u 
            WHERE u.userid = pr.userid AND pr.userid = '{$userId}'";

        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0];
        }
        return FALSE;
    }
    
    /**
    * Method to get a profile.
    *
    * @access public
    * @param string $submitId The associated resource
    * @return array The citation list
    */
    public function getProfiles($start = 0, $limit = NULL)
    {
        $sql = "SELECT * FROM {$this->table} AS pr, {$this->usrTable} AS u
            WHERE u.userid = pr.userid 
            ORDER BY firstname ";
            
        $sql .= (isset($limit) && !empty($limit)) ? "LIMIT {$limit} OFFSET {$start}" : '';
        
        $data = $this->getArray($sql);
        return $data;
    }

    /**
    * Method to search the profiles
    *
    * @access public
    * @return array $data the search results
    */
    public function searchProfiles($col, $val)
    {
        $sql = "SELECT * FROM {$this->table} AS pr, {$this->usrTable} AS u
            WHERE u.userid = pr.userid AND {$col} LIKE '%{$val}%' 
            ORDER BY firstname ";
            
        $data = $this->getArray($sql);
        return $data;
    }

    /**
    * Method to remove a profile
    *
    * @access public
    * @return void
    */
    public function deleteProfile($id)
    {
        $this->delete('id', $id);
    }
}
?>