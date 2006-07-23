<?
/* ----------- data class extends dbTable for tbl_systext_text ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_systext_text
* @author Kevin Cyster
*/

class dbtext extends dbTable
{
    public public function init()
    {
        parent::init('tbl_systext_text');
        $this -> table = 'tbl_systext_text';
    }

    /**
    * Method for adding a text to the database.
    *
    * @param string $text  The word to be abstracted eg. contex
    * @param string $creatorId  The id of the user who created the text entry
    */
    public function addRecord($text, $creatorId)
    {
        $fields = array();
        $fields['text'] = $text;
        $fields['creatorid'] = $creatorId;
        $fields['datecreated'] = date('Y-m-d H:i:s');
        return $this -> insert($fields);
    }

    /**
    * Method for retrieving a text abstract
    *
    * @param string $id The id of the text
    * @return array $data  The text data
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
    * Method for deleting text
    *
    * @param string $id  The text to be deleted
    */
    public function deleteRecord($id)
    {
        $this -> delete('id', $id);
    }

    /**
    * Method for listing all text within a module
    *
    * @return array $data  All text data
    */
    public function listAllRecords()
    {
        $sql = "SELECT * FROM " . $this -> table;
        $sql .= " ORDER BY 'text' ";
        $data = $this -> getArray($sql);
        if(!empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
    * Method for editing a text item
    *
    * @param string $textId  The id of the text item being edited
    * @param string $text  The text
    */
    public function editRecord($textId, $text)
    {
        $fields = array();
        $fields['text'] = $text;
        $this -> update('id', $textId, $fields);
    }
}
?>