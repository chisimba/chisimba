<?php
/* ----------- data class extends dbTable for tbl_blog------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_eportfolio_reflection
 * @author Paul Mungai
 * @copyright 2008 University of the Western Cape
 */
class dbEportfolio_Reflection extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_eportfolio_reflection');
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
        $sql = "SELECT * FROM tbl_eportfolio_reflection WHERE userid = '" . $userId . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    /**
     * Insert a record
     * @param string $language The language
     * @param string $rationale The rationale
     * @param string $creation_date The creation date
     * @param string $shortdescription The short description
     * @param string $longdescription The long description
     * -- @param string $userId The user ID
     */
    function insertSingle($language, $rationale, $creation_date, $shortdescription, $longdescription) 
    {
        $userid = $this->objUser->userId();
        $id = $this->insert(array(
            'userid' => $userid,
            'language' => $language,
            'rationale' => $rationale,
            'creation_date' => $creation_date,
            'shortdescription' => $shortdescription,
            'longdescription' => $longdescription
        ));
        return $id;
    }
    /**
     * Update a record
     * @param string $id ID
     * @param string $language The language
     * @param string $rationale The rationale
     * @param string $creation_date The creation date
     * @param string $shortdescription The short description
     * @param string $longdescription The long description
     * -- @param string $userId The user ID
     */
    function updateSingle($id, $language, $rationale, $creation_date, $shortdescription, $longdescription) 
    {
        $userid = $this->objUser->userId();
        $this->update("id", $id, array(
            'userid' => $userid,
            'language' => $language,
            'rationale' => $rationale,
            'creation_date' => $creation_date,
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
