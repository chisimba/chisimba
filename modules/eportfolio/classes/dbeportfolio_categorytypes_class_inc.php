<?php
/* ----------- data class extends dbTable for tbl_categorytypes------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_eportfolio_categorytype
 * @author Paul Mungai
 * @copyright 2008 University of the Western Cape
 */
class dbEportfolio_Categorytypes extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_eportfolio_categorytypes');
        $this->objUser = &$this->getObject('user', 'security');
        $this->objDbCategoryList = &$this->getObject('dbeportfolio_category', 'eportfolio');
        //$this->objUserContext = &$this->newObject('utils', 'contextpostlogin');
    }
    /**
     * Return record
     * @param string $userid The User ID
     * @return array The entries
     */
    function listAll($userid) 
    {
        return $this->getAll("WHERE userid='" . $userid . "'");
    }
    /**
     * Return record
     * @param string $categoryid The category ID
     * @return array The entries
     */
    function listCategory($categoryid) 
    {
        return $this->getAll("WHERE categoryid='" . $categoryid . "'");
    }
    /**
     * Return record
     * @param string $catType The category Type
     * @return array The entries
     */
    function listByType($catType) 
    {
        return $this->getAll("WHERE type='" . $catType . "'");
    }
    /**
     * Return record
     * @param string $category The category
     * @return array The entries
     */
    function listCategorytype($category) 
    {
        $catId = $this->objDbCategoryList->listCategory($category);
        return $this->getAll("WHERE categoryid='" . $catId[0]['id'] . "'");
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
        $sql = "SELECT * FROM tbl_eportfolio_categorytypes";
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
     * @param string $categoryid Related Category
     * @param string $type Type
     * -- @param string $userId The user ID
     */
    function insertSingle($categoryid, $type) 
    {
        $userid = $this->objUser->userId();
        //$contextid = $objDbContext->getContextCode();
        //$datetime = date('Y-m-d H:m:s');
        $id = $this->insert(array(
            'categoryid' => $categoryid,
            'userid' => $userid,
            'type' => $type
        ));
        return $id;
    }
    /**
     * Update a record
     * @param string $id ID
     * @param string $categoryid Related Category
     * @param string $type Type
     * -- @param string $userId The user ID
     */
    function updateSingle($id, $categoryid, $type) 
    {
        $userid = $this->objUser->userId();
        $this->update("id", $id, array(
            'categoryid' => $categoryid,
            'userid' => $userid,
            'type' => $type
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
