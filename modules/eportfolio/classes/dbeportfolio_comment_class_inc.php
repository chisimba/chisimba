<?php
/* ----------- data class extends dbTable for tbl_eportfolio_comment------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for the table tbl_eportfolio_comment
 * @author Paul Mungai
 * @copyright 2009 University of the Western Cape
 */
class dbEportfolio_Comment extends dbTable
{
    /**
     * Constructor method to define the table
     */
    function init() 
    {
        parent::init('tbl_eportfolio_comment');
        $this->objUser = &$this->getObject('user', 'security');
    }
    /**
     * Return all records
     * @param string $eportfoliopartid The ePortfolio item
     * @return array The entries
     */
    function listAll($eportfoliopartid) 
    {
        return $this->getAll("WHERE eportfoliopartid='" . $eportfoliopartid . "'");
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
     * Return a many records
     * @param string $id ID
     * @return array The values
     */
    function getByItem($eportfoliopartid) 
    {
        $sql = "SELECT * FROM tbl_eportfolio_comment WHERE eportfoliopartid = '" . $eportfoliopartid . "'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        } else {
            return FALSE;
        }
    }
    /**
     * Insert a record
     * @param string $eportfoliopartid The eportfolio partID
     * @param string $comment The comment
     * @param string $isapproved The isapproved status
     */
    function insertSingle($eportfoliopartid, $comment, $isapproved = '0') 
    {
        $userid = $this->objUser->userId();
        $id = $this->insert(array(
            'eportfoliopartid' => $eportfoliopartid,
            'commentoruserid' => $userid,
            'comment' => $comment,
            'isapproved' => $isapproved,
            'isdeleted' => '0',
            'postdate' => date("Y-m-d H:i:s")
        ));
        return $id;
    }
    /**
     * Update a record
     * @param string $id The ID
     * @param string $isapproved The isapproved status
     * @param string $isdeleted The isdeleted status
     */
    function updateSingle($id, $isapproved = '0', $isdeleted = '0') 
    {
        $userid = $this->objUser->userId();
        $this->update("id", $id, array(
            'commentoruserid' => $userid,
            'isapproved' => $isapproved,
            'isdeleted' => $isdeleted
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
