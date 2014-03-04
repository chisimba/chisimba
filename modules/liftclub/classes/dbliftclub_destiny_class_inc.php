<?php
/* ----------- data class extends dbTable for tbl_email------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_liftclub_destiny
 * @author Paul Mungai
 * @copyright 2009 University of the Western Cape
 */
class dbLiftclub_destiny extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_liftclub_destiny');
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
     * @return the destiny Id or False
     */
    function getId($userId) 
    {
        $sql = "SELECT id FROM tbl_liftclub_destiny WHERE userid = '" . $userId . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data[0]['id'];
        } else {
            return FALSE;
        }
    }
    /**
     * Return a single record
     * @param string $id user ID
     * @return array The values
     */
    function userDestiny($userId) 
    {
        return $this->getAll("WHERE userid='" . $userId . "'");
    }
    function getByItem($Id) 
    {
        $sql = "SELECT * FROM tbl_liftclub_destiny WHERE id = '" . $Id . "'";
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
     * @param string $institution institution
     * @param string $street street
     * @param string $suburb suburb
     * @param string $city city
     * @param string $province province
     * @param string $neighbour neighbour
     */
    function insertSingle($userid, $institution, $street, $suburb, $city, $province, $neighbour) 
    {
        $id = $this->insert(array(
            'institution' => $institution,
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
    function updateSingle($id, $institution, $street, $suburb, $city, $province, $neighbour) 
    {
        $this->update("id", $id, array(
            'institution' => $institution,
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
