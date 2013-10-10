<?php
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_eportfolio_affiliation
 * @author Paul Mungai
 * @copyright 2005 University of the Western Cape
 */
class dbEportfolio_Affiliation extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_eportfolio_affiliation');
        $this->objUser = &$this->getObject('user', 'security');
    }
    /**
     * Return all records
     * @param string $userid The User ID
     * @return array The entries
     */
    function listAll($userid) 
    {
        return $this->getAll("WHERE userid='" . $userid . "'");
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
    function getByItem($userId) 
    {
        $sql = "SELECT * FROM tbl_eportfolio_affiliation WHERE userid = '" . $userId . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    /**
     * Insert a record
     * @param string $id ID
     * @param string $type The type
     * @param string $classification The classification
     * @param string $role The role
     * @param string $organisation The organisation
     * @param string $start The start date
     * @param string $finish The finish date
     * -- @param string $userId The user ID
     */
    function insertSingle($type, $classification, $role, $organisation, $start, $finish, $shortdescription, $longdescription) 
    {
        $userid = $this->objUser->userId();
        $id = $this->insert(array(
            'userid' => $userid,
            'type' => $type,
            'classification' => $classification,
            'role' => $role,
            'organisation' => $organisation,
            'start' => $start,
            'finish' => $finish,
            'shortdescription' => $shortdescription,
            'longdescription' => $longdescription
        ));
        return $id;
    }
    /**
     * Update a record
     * @param string $id ID
     * @param string $type The type
     * @param string $classification The classification
     * @param string $role The role
     * @param string $organisation The organisation
     * @param string $start The start date
     * @param string $finish The finish date
     * -- @param string $userId The user ID
     */
    function updateSingle($id, $type, $classification, $role, $organisation, $start, $finish, $shortdescription, $longdescription) 
    {
        $userid = $this->objUser->userId();
        $id = $this->update("id", $id, array(
            'userid' => $userid,
            'type' => $type,
            'classification' => $classification,
            'role' => $role,
            'organisation' => $organisation,
            'start' => $start,
            'finish' => $finish,
            'shortdescription' => $shortdescription,
            'longdescription' => $longdescription
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
