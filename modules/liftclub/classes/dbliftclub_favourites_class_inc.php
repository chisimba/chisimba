<?php
/* ----------- data class extends dbTable for tbl_favourites------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_liftclub_favourites
 * @author Paul Mungai
 * @copyright 2009 University of the Western Cape
 */
class dbLiftclub_favourites extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_liftclub_favourites');
        $this->objUser = &$this->getObject('user', 'security');
    }
    /**
     * Return all records
     * @param string $id The City ID
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
        return $this->getAll("WHERE userid='" . $id . "'");
    }
    /**
     * Return records
     * @param string $userid User ID
     * @return array The values
     */
    function getFavoured($userid) 
    {
        $sql = "SELECT * FROM tbl_liftclub_favourites WHERE userid = '" . $userid . "'";
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
     * @param string $favoureduserid Favoured User ID
     * @return boolean
     */
    function checkIfExists($userid, $favoureduserid) 
    {
        $sql = "SELECT * FROM tbl_liftclub_favourites WHERE userid = '" . $userid . "' AND favoureduserid = '" . $favoureduserid . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * Return records
     * @param string $favoureduserid Favoured User ID
     * @return array The values
     */
    function getWhereFavoured($favoureduserid) 
    {
        $sql = "SELECT * FROM tbl_liftclub_favourites WHERE favoureduserid = '" . $favoureduserid . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    /**
     * Insert a record
     * @param string $city The city
     * @param string $favoureduserid favoured userId
     */
    function insertSingle($userid, $favoureduserid) 
    {
        $id = $this->insert(array(
            'userid' => $userid,
            'favoureduserid' => $favoureduserid,
            'datefavoured' => date('Y-m-d, h:i:s')
        ));
        return $id;
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
