<?php
/* ----------- data class extends dbTable for tbl_podcaster_category------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_podcaster_category
 * @author Paul Mungai
 * @copyright 2011 University of the Witwatersrand
 */
class dbPodcaster_Category extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_podcaster_category');
        $this->objDbEvents = &$this->getObject('dbpodcaster_events', 'podcaster');
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
    /**
     * Function to fetch all categories
     * @return array
     */
    function getAllCategories()
    {
        return $this->getAll();
    }
    /**
     * Insert a record
     * @param string $category
     * @param string $description
     */
    function insertSingle($category, $description)
    {
        $userid = $this->objUser->userId();
        $id = $this->insert(array(
            'userid' => $userid,
            'category' => $category,
            'description' => $description
        ));
        return $id;
    }
    
    /**
     * Update a record
     * @param string $id
     * @param string $category
     * @param string $description
     */
    function updateSingle($id, $category, $description)
    {
        $userid = $this->objUser->userId();
        $this->update("id", $id, array(
            'category' => $category,
            'description' => $description
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