<?php
/* ----------- data class extends dbTable for tbl_internalmail_folders ----------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_internalmail_folders
 * @author Kevin Cyster
 */
class dbfolders extends dbTable
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
        parent::init('tbl_internalmail_folders');
        $this->table = 'tbl_internalmail_folders';
        $this->dbRules = $this->getObject('dbrules');
        $this->dbRouting = $this->getObject('dbrouting');
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method for adding a row to the database.
     *
     * @access public
     * @param string $folderName The name of the folder
     * @return string $folderId The id of the folder
     */
    public function addFolder($folderName)
    {
        $fields = array();
        $fields['user_id'] = $this->userId;
        $fields['folder_name'] = $folderName;
        $fields['updated'] = date("Y-m-d H:i:s");
        $folderId = $this->insert($fields);
    }

    /**
     * Method for editing a row on the database.
     *
     * @access public
     * @param string $folderId The id of the folder to edit
     * @param string $folderName The name of the folder
     * @return
     */
    public function editFolder($folderId, $folderName)
    {
        $fields = array();
        $fields['folder_name'] = $folderName;
        $fields['updated'] = date("Y-m-d H:i:s");
        $this->update('id', $folderId, $fields);
    }

    /**
     * Method for deleting a row from the database.
     *
     * @access public
     * @param string $folderId The id of the folder to delete
     * @return
     */
    public function deleteFolder($folderId)
    {
        $this->delete('id', $folderId);
        $this->dbRouting->delete('folder_id', $folderId);
    }

    /**
     * Method for listing all rows for the current user
     *
     * @access public
     * @return array $data  All row information.
     */
    public function listFolders()
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE user_id='".$this->userId."' OR user_id='system'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            foreach($data as $key => $line) {
                $this->dbRules->applyRules($line['id']);
                $emails = $this->dbRouting->getAllMail($line['id'], array(
                    1 => 3,
                    2 => 'DESC'
                ) , NULL);
                $data[$key]['allmail'] = !empty($emails) ? count($emails) : 0;
                $unreadMail = $this->dbRouting->getUnreadMail($line['id']);
                $data[$key]['unreadmail'] = !empty($unreadMail) ? count($unreadMail) : 0;
            }
            return $data;
        }
        return FALSE;
    }

    /**
     * Method for getting a folder for the current user
     *
     * @access public
     * @param string $folderId The id of the folder to retrieve
     * @return array $data  All row information.
     */
    public function getFolder($folderId)
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE id='".$folderId."'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            foreach($data as $key => $line) {
                $emails = $this->dbRouting->getAllMail($line['id'], array(
                    1 => 3,
                    2 => 'DESC'
                ) , NULL);
                $data[$key]['allmail'] = !empty($emails) ? count($emails) : 0;
                $unreadMail = $this->dbRouting->getUnreadMail($line['id']);
                $data[$key]['unreadmail'] = !empty($unreadMail) ? count($unreadMail) : 0;
            }
            $data = $data[0];
            return $data;
        }
        return FALSE;
    }
    
	/**
     * Method for listing all rows for the current user
     * This method is used for the API and will pass the userId as a parameter
     *
     * @access public
     * @return array $data  All row information.
     */
    public function listFoldersForUser($userId)
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE user_id='".$userId."' OR user_id='system'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            foreach($data as $key => $line) {
                $this->dbRules->applyRules($line['id']);
                $emails = $this->dbRouting->getAllMail($line['id'], array(
                    1 => 3,
                    2 => 'DESC'
                ) , NULL);
                $data[$key]['allmail'] = !empty($emails) ? count($emails) : 0;
                $unreadMail = $this->dbRouting->getUnreadMail($line['id']);
                $data[$key]['unreadmail'] = !empty($unreadMail) ? count($unreadMail) : 0;
            }
            return $data;
        }
        return FALSE;
    }
}
?>