<?php
/* ----------- data class extends dbTable for tbl_systext_system ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_systext_system
* @author Kevin Cyster
*/

class dbsystem extends dbTable
{
    public function init()
    {
        parent::init('tbl_systext_system');
        $this -> table = 'tbl_systext_system';
    }

    /**
    * Method for adding a system type to the database.
    *
    * @param string $systemType  The name of the system type
    * @param string $creatorId  The id of the user who created the system type
    */
    public function addRecord($systemType, $creatorId)
    {
        $fields = array();
        $fields['systemtype'] = $systemType;
        $fields['creatorid'] = $creatorId;
        $fields['datecreated'] = date('Y-m-d H:i:s');
        return $this -> insert($fields);
    }

    /**
    * Method for retrieving a system type
    *
    * @param string $id The id of the system type
    * @return array $data  The system type data
    */
    public function getRecord($id)
    {
        $sql = "SELECT * FROM " . $this -> table;
        $sql .= " WHERE id = '$id'";
        $data = $this -> getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for deleting a system type
    *
    * @param string $id  The system type to be deleted
    */
    public function deleteRecord($id)
    {
        $this -> delete('id', $id);
    }

    /**
    * Method for listing all system types
    *
    * @return array $data  All system type data
    */
    public function listAllRecords()
    {
        $sql = 'SELECT * FROM ' . $this -> table;
        $sql .= ' ORDER BY "systemtype" ';

        //echo $sql;
        $data = $this -> getArray($sql);

        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for editing a system type
    *
    * @param string $systemId  The id of the system type being edited
    * @param string $systemType  The system type
    */
    public function editRecord($systemId, $systemType)
    {
        $fields = array();
        $fields['systemtype'] = $systemType;
        $this -> update('id', $systemId, $fields);
    }
}
?>