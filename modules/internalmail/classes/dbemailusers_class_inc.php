<?php
/* ----------- data class extends dbTable for tbl_users ----------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_email_folders
 * @author Kevin Cyster
 */
class dbemailusers extends dbTable
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
        parent::init('tbl_users');
        $this->table = 'tbl_users';
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method for geting users from the database.
     *
     * @access public
     * @param string $field The field to search
     * @param string $search The search criteria
     * @return array $data The array of user data
     */
    public function getUsers($field, $search = NULL)
    {
        $sql = " SELECT * FROM ".$this->table;
        if ($search != NULL) {
            $sql.= " WHERE ".$field." LIKE '".$search."%'";
        }
        $sql.= " ORDER BY ".$field;
        $sql.= " LIMIT 0, 10";
        
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }
}
?>