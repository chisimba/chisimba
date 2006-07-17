<?
/* ----------- data class extends dbTable for tbl_systext_abstract ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_systext_abstract
* @author Kevin Cyster
*/

class dbabstract extends dbTable
{
    public function init()
    {
        parent::init('tbl_systext_abstract');
        $this -> table = 'tbl_systext_abstract';
    }

    /**
    * Method for adding a abstract element to the database.
    *
    * @param string $systemId The id of the system type the abstract is being added to
    * @param string $textId  The id of the text that is being abstracted
    * @param string $abstract  The abstracted word eg. course
    * @param string $creatorId  The id of the user who created the text entry
    * @param string $canDelete  Indicates whether item can be deleted
    */
    public function addRecord($systemId, $textId, $abstract, $creatorId, $canDelete = NULL)
    {
        $fields = array();
        $fields['systemId'] = $systemId;
        $fields['textId'] = $textId;
        $fields['abstract'] = $abstract;
        $fields['creatorId'] = $creatorId;
        $fields['dateCreated'] = date('Y-m-d H:i:s');
        if(!empty($canDelete)){
            $fields['canDelete'] = $canDelete;
        }
        return $this -> insert($fields);
    }

    /**
    * Method for retrieving a text abstract by cross reference
    *
    * @param string $syetemId The id of the system
    * @param string $textId The id of the text item
    * @return array $data  The text data
    */
    public function getRecord($systemId, $textId)
    {
        $sql = "SELECT * FROM " . $this -> table;
        $sql .= " WHERE systemId = '$systemId' AND textId = '$textId'";
        $data = $this -> getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for retrieving a text abstract
    *
    * @param string $id The id of the abstract
    * @return array $data  The text data
    */
    public function getRecordById($id)
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
    * Method for deleting text
    *
    * @param string $id  The text to be deleted
    */
    public function deleteRecord($id)
    {
        $this -> delete('id', $id);
    }

    /**
    * Method for listing all text abstracts for a system type
    *
    * @return array $data  All text abstract data
    */
    public function listRecords($systemId)
    {
        $sql = "SELECT * FROM " . $this -> table;
        $sql .= " WHERE systemId = '$systemId'";
        $data = $this -> getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for editing an abstract
    *
    * @param string $id  The id of the abstract being edited
    * @param string $abstract The abstract
    * @param string $canDelete  Indicates whether item can be deleted
    */
    public function editRecord($id, $abstract, $canDelete = NULL)
    {
        $fields = array();
        $fields['abstract'] = $abstract;
        if(!empty($canDelete)){
            $fields['canDelete'] = $canDelete;
        }
        $this -> update('id', $id, $fields);
    }
}
?>