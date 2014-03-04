<?php
/* ----------- data class extends dbTable for tbl_internalmail_addressbook ----------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_internalmail_addressbook
 * @author Kevin Cyster
 */
class dbbookentries extends dbTable
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
        parent::init('tbl_internalmail_addressbook_entries');
        $this->table = 'tbl_internalmail_addressbook_entries';
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method for adding a row to the database.
     *
     * @access public
     * @param string $bookId The id of the address book the entry is being added to
     * @param string $recipientId The id of the recipient to be added
     * @return string $entryId The id of the address book entry
     */
    public function addBookEntry($bookId, $recipientId)
    {
        $fields = array();
        $fields['addressbook_id'] = $bookId;
        $fields['recipient_id'] = $recipientId;
        $fields['updated'] = date("Y-m-d H:i:s");
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE addressbook_id='".$bookId."'";
        $sql.= " AND recipient_id='".$recipientId."'";
        $data = $this->getArray($sql);
        if (empty($data)) {
            $entryId = $this->insert($fields);
            return $entryId;
        }
    }

    /**
     * Method for deleting a row from the database.
     *
     * @access public
     * @param string $entryId The id of the entry to delete
     * @return
     */
    public function deleteBookEntry($entryId)
    {
        $this->delete('id', $entryId);
    }

    /**
     * Method for deleting a rows from the database.
     *
     * @access public
     * @param string $bookId The id of the address book entries to delete
     * @return
     */
    public function deleteBook($bookId)
    {
        $this->delete('addressbook_id', $bookId);
    }

    /**
     * Method for listing all rows for the current address book
     *
     * @access public
     * @param string $bookId The id of the address book to list
     * @return array $data  All row information.
     */
    public function listBookEntries($bookId)
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE addressbook_id='".$bookId."'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }
}
?>