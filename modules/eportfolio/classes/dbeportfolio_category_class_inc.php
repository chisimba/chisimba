<?php
/* ----------- data class extends dbTable for tbl_category------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_eportfolio_category
 * @author Paul Mungai
 * @copyright 2008 University of the Western Cape
 */
class dbEportfolio_Category extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_eportfolio_category');
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
     * Return all records
     * @param string $category The Category
     * @return array The entries
     */
    function listCategory($category) 
    {
        return $this->getAll("WHERE category LIKE '" . $category . "'");
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
    function getByItem() 
    {
        $sql = "SELECT * FROM tbl_eportfolio_category";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    /**
     * Insert a record
     * @param string $category Category
     * -- @param string $userId The user ID
     */
    function insertSingle($category) 
    {
        $userid = $this->objUser->userId();
        //$contextid = $objDbContext->getContextCode();
        //$datetime = date('Y-m-d H:m:s');
        $id = $this->insert(array(
            'userid' => $userid,
            'category' => $category
        ));
        return $id;
    }
    /**
     * Update a record
     * @param string $id ID
     * @param string $category Category
     * -- @param string $userId The user ID
     */
    function updateSingle($id, $category) 
    {
        $userid = $this->objUser->userId();
        $this->update("id", $id, array(
            'category' => $category
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
