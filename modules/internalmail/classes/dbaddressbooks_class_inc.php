<?php
/* ----------- data class extends dbTable for tbl_internalmail_addressbooks ----------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_internalmail_folders
 * @author Kevin Cyster
 */
class dbaddressbooks extends dbTable
{
    /**
     * @var string $userId The userId of the current user
     * @access private
     */
    private $userId;

    /**
     * Method to construct the class.
     *
     * @access public
     * @return
     */
    public function init()
    {
        parent::init('tbl_internalmail_addressbooks');
        $this->table = 'tbl_internalmail_addressbooks';
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method for adding a row to the database.
     *
     * @access public
     * @param string $bookName The name of the addressbook
     * @return string $bookId The id of the addressbook
     */
    public function addBook($bookName)
    {
        $fields = array();
        $fields['user_id'] = $this->userId;
        $fields['book_name'] = $bookName;
        $fields['updated'] = date("Y-m-d H:i:s");
        $bookId = $this->insert($fields);
    }

    /**
     * Method for editing a row on the database.
     *
     * @access public
     * @param string $bookId The id of the addressbook to edit
     * @param string $bookName The name of the $bookId
     * @return
     */
    public function editBook($bookId, $bookName)
    {
        $fields = array();
        $fields['book_name'] = $bookName;
        $fields['updated'] = date("Y-m-d H:i:s");
        $this->update('id', $bookId, $fields);
    }

    /**
     * Method for deleting a row from the database.
     *
     * @access public
     * @param string $bookId The id of the addressbook to delete
     * @return
     */
    public function deleteBook($bookId)
    {
        $this->delete('id', $bookId);
    }

    /**
     * Method for listing all rows for the current user
     *
     * @access public
     * @return array $data  All row information.
     */
    public function listBooks()
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE user_id='".$this->userId."'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method for retieving a row for the current user
     *
     * @access public
     * @param string $bookId The id of the addressbook to retrieve
     * @return array $data  All row information.
     */
    public function getBook($bookId)
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE id='".$bookId."'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $data = $data[0];
            return $data;
        }
        return FALSE;
    }
}
?>