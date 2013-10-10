<?php
/**
* dbDegrees class extends dbTable
* @package etd
* @filesource
*/

/**
* Class for accessing the table listing the degrees, departments and faculties in a university
* @author Megan Watson
* @copyright (c) 2006 University of the Western Cape
* @version 0.1
*/

class dbDegrees extends dbTable
{
    /**
    * Constructor for the class
    *
    * @access public
    * @return void
    */
    public function init()
    {
        parent::init('tbl_etd_degrees');
        $this->table = 'tbl_etd_degrees';
                        
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        
        $this->loadClass('dropdown', 'htmlelements');
    }
    
    /**
    * Method to add a new degree or faculty.
    *
    * @access public
    * @return void
    */
    public function addItem($name, $type, $id = NULL)
    {    
        $fields = array();
        $fields['name'] = $name;
        $fields['type'] = $type;
        
        if(isset($id) && !empty($id)){
            $fields['modifierid'] = $this->objUser->userId();
            $fields['updated'] = date('Y-m-d H:i:s');
            $this->update('id', $id, $fields);
        }else{
            $fields['creatorid'] = $this->objUser->userId();
            $fields['datecreated'] = date('Y-m-d H:i:s');
            $fields['updated'] = date('Y-m-d H:i:s');
            $id = $this->insert($fields);
        }
        return $id;
    }
    
    /**
    * Method to get all a degree / faculty
    *
    * @access public
    * @param string $id The row id
    * @param string $name The new degree / faculty name
    * @param string $type The type - degree / faculty / etc
    * @return string The degree / faculty
    */
    public function updateDB($id, $name, $type)
    {
        $dbThesis = $this->getObject('dbthesis', 'etd');
        
        if(isset($id) && !empty($id)){
            $oldName = $this->getItem($id);
            $fields = array();
            
            switch($type){
                case 'degree':
                    $fields['thesis_degree_level'] = $name;
                    $search = 'thesis_degree_name';
                    break;
                    
                case 'department':
                    $search = 'thesis_degree_discipline';
                    break;
                    
                case 'faculty':
                    $search = 'thesis_degree_faculty';
                    break;
            }
            $fields[$search] = $name;
            
            $dbThesis->replaceElement($search, $oldName, $fields);
            
        }else{
            return FALSE;
        }
    }
    
    /**
    * Method to get all a degree / faculty
    *
    * @access private
    * @param string $id The row id
    * @return string The degree / faculty
    */
    private function getItem($id)
    {
        $sql = "SELECT name FROM {$this->table} WHERE id = '{$id}'";
        $data = $this->getArray($sql);
        
        if(!empty($data)){
            return $data[0]['name'];
        }
        return FALSE;
    }
    
    /**
    * Method to get all degrees / faculties
    *
    * @access public
    * @return array The list of degrees / faculties
    */
    public function getList($type = 'faculty')
    {
        $sql = "SELECT * FROM {$this->table} WHERE type = '{$type}' ORDER BY name";
        
        return $this->getArray($sql);
    }
    
    /**
    * Method to remove a degree / faculty from the list
    *
    * @access public
    * @return void
    */
    public function deleteItem($id)
    {
        $this->delete('id', $id);
    }
    
    /**
    * Method to return a dropdown list of faculties / departments
    *
    * @access public
    * @return string html
    */
    public function getDropList($type = 'faculty', $select = '')
    {
        $data = $this->getList($type);
        
        $objDrop = new dropdown($type);
        
        if(!empty($data)){
            foreach($data as $item){
                $value = htmlentities($item['name']);
                $objDrop->addOption($value, $value);
            }
        }
        if($type == 'department'){
            $objDrop->addOption('', ' --- ');
        }
        $objDrop->setSelected($select);
        
        return $objDrop->show();
    }
}
?>