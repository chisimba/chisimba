<?php
/* ----------- data class extends dbTable for tbl_messages------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_liftclub_messages
 * @author Paul Mungai
 * @copyright 2009 University of the Western Cape
 */
class dbLiftclub_messages extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_liftclub_messages');
        $this->objUser = &$this->getObject('user', 'security');
    }
    /**
     * Return all records
     * @param string $id The message ID
     * @return array The entries
     */
    function listAll($id) 
    {
        return $this->getAll("WHERE id='" . $id . "'");
    }
    /**
     * Return a single record
     * @param string $id ID
     * @return array The values
     */
    function listSingle($id) 
    {
        return $this->getAll("WHERE id='" . $id . "'");
    }
    /**
     * Return records
     * @param string $userid User ID
     * @return array The values
     */
    function getMessagesforUser($userid) 
    {
        $sql = "SELECT * FROM tbl_liftclub_messages WHERE recipentuserid = '" . $userid . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    /**
     * Return records
     * @param string $userid User ID
     * @return array The values
     */
    function getMessagesbyUser($userid) 
    {
        $sql = "SELECT * FROM tbl_liftclub_messages WHERE userid = '" . $userid . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    /**
     * Insert a record userid recipentuserid timesent markasread markasdeleted messagetitle messagebody
     * @param string $userid The senders userid
     * @param string $recipentuserid receivers userid
     * @param string $favoureduserid favoured userId
     * @param string $messagetitle message title
     * @param string $messagebody message body
     */
    function insertSingle($userid, $recipentuserid, $messagetitle, $messagebody) 
    {
        $id = $this->insert(array(
            'userid' => $userid,
            'recipentuserid' => $recipentuserid,
            'timesent' => date('Y-m-d, h:i:s') ,
            'markasdeleted' => 0,
            '	markasread ' => 0,
            '	messagetitle' => $messagetitle,
            'messagebody' => $messagebody
        ));
        return $id;
    }
    /**
     * mark email as read or unread
     * @param string $id ID
     * @param string $userid The senders userid
     * @param string $markasread mark as read or unread
     */
    function markRead($id, $userid, $markasread) 
    {
        $this->update("id", $id, array(
            'userid' => $userid,
            'markasread' => $markasread
        ));
    }
    /**
     * mark email as trashed
     * @param string $id ID
     * @param string $bol mark as trashed
     */
    function markTrashed($id, $bol = 1) 
    {
        $this->update("id", $id, array(
            'markasdeleted' => $bol
        ));
    }
    /**
     * Delete a record
     * @param string $id ID
     */
    function deleteSingle($id) 
    {
        $this->delete("id", $id);
    }
}
?>
