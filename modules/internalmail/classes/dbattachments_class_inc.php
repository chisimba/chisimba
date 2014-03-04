<?php
/* ----------- data class extends dbTable for tbl_internalmail_attachments ----------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_internalmail_new
 * @author Kevin Cyster
 */
class dbattachments extends dbTable
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
        parent::init('tbl_internalmail_attachments');
        $this->table = 'tbl_internalmail_attachments';
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method for adding attachments to the database.
     *
     * @access public
     * @param string $emailId The id of the email the attachments are for
     * @param array $attachment The file data
     * @return string $attachmentId The id of the attachment
     */
    public function addAttachments($emailId, $attachment)
    {
        $fields = array();
        $fields['email_id'] = $emailId;
        $fields['file_name'] = $attachment['filename'];
        $fields['file_type'] = $attachment['mimetype'];
        $fields['file_size'] = $attachment['filesize'];
        $fields['updated'] = date('Y-m-d H:i:s');
        $attachmentId = $this->insert($fields);
        $updateArray['stored_name'] = $attachmentId;
        $this->update('id', $attachmentId, $updateArray);
        return $attachmentId;
    }

    /**
     * Method to retrieve an attachment from the data base
     *
     * @access public
     * @param string $attachmentId The id of the attachments
     * @return array $data The attachment data
     */
    public function getAttachment($attachmentId)
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE id='".$attachmentId."'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to retrieve attachments from the data base
     *
     * @access public
     * @param string $emailId The id of the email the attachments are for
     * @return array $data The attachment data
     */
    public function getAttachments($emailId)
    {
        $sql = "SELECT * FROM ".$this->table;
        $sql.= " WHERE email_id='".$emailId."'";
        $sql.= " ORDER BY file_name";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }
}
?>