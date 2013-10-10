<?php
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_eportfolio_qcl
 * @author Paul Mungai
 * @copyright 2008 University of the Western Cape
 */
class dbEportfolio_Qcl extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_eportfolio_qcl');
        $this->objUser = &$this->getObject('user', 'security');
        //$this->objUserContext = &$this->newObject('utils', 'contextpostlogin');
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
        $sql = "SELECT * FROM tbl_eportfolio_qcl WHERE userid = '" . $userId . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    /**
     * Insert a record
     * @param string $qcl_type The qcl Type
     * @param string $qcl_title The qcl Title
     * @param string $organisation The qcl organisation
     * @param string $qcl_level The qcl level
     * @param string $start The qcl start date
     * @param string $shortdescription The qcl short description
     * @param string $longdescription The qcl long description
     * -- @param string $userId The user ID
     */
    function insertSingle($qcl_type, $qcl_title, $organisation, $qcl_level, $award_date, $shortdescription, $longdescription) 
    {
        $userid = $this->objUser->userId();
        $id = $this->insert(array(
            'userid' => $userid,
            'qcl_type' => $qcl_type,
            'qcl_title' => $qcl_title,
            'organisation' => $organisation,
            'qcl_level' => $qcl_level,
            'award_date' => $award_date,
            'shortdescription' => $shortdescription,
            'longdescription' => $longdescription
        ));
        return $id;
    }
    /**
     * Update a record
     * @param string $id ID
     * @param string $qcl_type The qcl Type
     * @param string $qcl_title The qcl Title
     * @param string $organisation The qcl organisation
     * @param string $qcl_level The qcl level
     * @param string $award_date The qcl award date
     * @param string $shortdescription The qcl short description
     * @param string $longdescription The qcl long description
     * -- @param string $userId The user ID
     */
    function updateSingle($id, $qcl_type, $qcl_title, $organisation, $qcl_level, $award_date, $shortdescription, $longdescription) 
    {
        $userid = $this->objUser->userId();
        $this->update("id", $id, array(
            'userid' => $userid,
            'qcl_type' => $qcl_type,
            'qcl_title' => $qcl_title,
            'organisation' => $organisation,
            'qcl_level' => $qcl_level,
            'award_date' => $award_date,
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
