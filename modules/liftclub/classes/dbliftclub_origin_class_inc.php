<?php
/* ----------- data class extends dbTable for tbl_email------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_liftclub_origin
 * @author Paul Mungai
 * @copyright 2009 University of the Western Cape
 */
class dbLiftclub_origin extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_liftclub_origin');
        $this->objUser = &$this->getObject('user', 'security');
    }
    /**
     * Return all records
     * @param string $id The Origin ID
     * @return array The entries
     */
    function listAll($id) 
    {
        return $this->getAll("WHERE id='" . $id . "'");
    }
    /**
     * Return Id
     * @param string $userId The User ID
     * @return the Origin Id or False
     */
    function getId($userId) 
    {
        $sql = "SELECT id FROM tbl_liftclub_origin WHERE userid = '" . $userId . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data[0]['id'];
        } else {
            return FALSE;
        }
    }
    /**
     * Return a single record
     * @param string $userId user ID
     * @return array The values
     */
    function userOrigin($userId) 
    {
        return $this->getAll("WHERE userid='" . $userId . "'");
    }
    function getByItem($Id) 
    {
        $sql = "SELECT * FROM tbl_liftclub_origin WHERE id = '" . $Id . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    /**
     * Insert a record
     * @param string $userid The user Id
     * @param string $street street
     * @param string $suburb suburb
     * @param string $city city
     * @param string $province province
     * @param string $neighbour neighbour
     */
    function insertSingle($userid, $street, $suburb, $city, $province, $neighbour) 
    {
        $id = $this->insert(array(
            'userid' => $userid,
            'street' => $street,
            'suburb' => $suburb,
            'city' => $city,
            'province' => $province,
            'neighbour' => $neighbour
         ));
        return $id;
    }
    /**
     * Update a record
     * @param string $city The city
     * -- @param string $id The record ID
     * @param string $street street
     * @param string $suburb suburb
     * @param string $city city
     * @param string $province province
     * @param string $neighbour neighbour
     */
    function updateSingle($id, $street, $suburb, $city, $province, $neighbour) 
    {
        $this->update("id", $id, array(
            'street' => $street,
            'suburb' => $suburb,
            'city' => $city,
            'province' => $province,
            'neighbour' => $neighbour
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
