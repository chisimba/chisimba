<?php
/* ----------- data class extends dbTable for tbl_product------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_eportfolio_product
 * @author Paul Mungai
 * @copyright 2008 University of the Western Cape
 */
class dbEportfolio_Product extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_eportfolio_product');
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
        $sql = "SELECT * FROM tbl_eportfolio_product WHERE userid = '" . $userId . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    /**
     * Insert a record
     * @param string $type The product type
     * @param string $comment A comment
     * @param string $referential_source The referential source
     * @param string $referential_id The referential id
     * @param string $assertion_id The assertion id
     * @param string $assignment_id The assignment id
     * @param string $essay_id The essay id
     * @param string $creation_date The creation date
     * @param string $shortdescription The short description
     * @param string $longdescription The long description
     * -- @param string $userId The user ID
     */
    function insertSingle($type, $comment, $referential_source, $referential_id, $assertion_id, $assignment_id, $essay_id, $creation_date, $shortdescription, $longdescription) 
    {
        $userid = $this->objUser->userId();
        $id = $this->insert(array(
            'userid' => $userid,
            'type' => $type,
            'comment' => $comment,
            'referential_source' => $referential_source,
            'referential_id' => $referential_id,
            'assertion_id' => $assertion_id,
            'assignment_id' => $assignment_id,
            'essay_id' => $essay_id,
            'creation_date' => $creation_date,
            'shortdescription' => $shortdescription,
            'longdescription' => $longdescription
        ));
        return $id;
    }
    /**
     * Update a record
     * @param string $type The product type
     * @param string $comment A comment
     * @param string $referential_source The referential source
     * @param string $referential_id The referential id
     * @param string $assertion_id The assertion id
     * @param string $assignment_id The assignment id
     * @param string $essay_id The essay id
     * @param string $creation_date The creation date
     * @param string $shortdescription The short description
     * @param string $longdescription The long description
     * -- @param string $userId The user ID
     */
    function updateSingle($id, $type, $comment, $referential_source, $referential_id, $assertion_id, $assignment_id, $essay_id, $creation_date, $shortdescription, $longdescription) 
    {
        $userid = $this->objUser->userId();
        $this->update("id", $id, array(
            'userid' => $userid,
            'type' => $type,
            'comment' => $comment,
            'referential_source' => $referential_source,
            'referential_id' => $referential_id,
            'assertion_id' => $assertion_id,
            'assignment_id' => $assignment_id,
            'essay_id' => $essay_id,
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
