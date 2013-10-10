<?php

/**
 * @package mcqtests
 * @filesource
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class for providing access to the table tbl_test_tag_correlation in the database
 * @author Paul Mungai
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.3
 */
class dbtag_correlation extends dbtable {
    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public $table;
    public $objUser;
    public $userId;

    public function init() {
        parent::init('tbl_test_tag_correlation');
        $this->table = 'tbl_test_tag_correlation';
        $this->objUser = &$this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method to insert or update a correlated tag in the database.
     *
     * @access public
     * @param array $fields The table fields to be added/updated.
     * @param string $id The id of the tag to be edited. Default=NULL.
     * @return string $id The id of the inserted or updated description.
     */
    public function addCorrelation($fields, $id = NULL) {
        $fields['timemodified'] = date('Y-m-d H:i:s');
        if ($id) {
            $fields['timemodified'] = date('Y-m-d H:i:s');
            $fields['modifiedby'] = $this->userId;
            $this->update('id', $id, $fields);
        } else {
            $fields['timecreated'] = date('Y-m-d H:i:s');
            $fields['createdby'] = $this->userId;
            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
     * Method to get all correlated tags.
     *
     * @access public
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of tags.
     */
    public function getCorrelations($filter = NULL) {
        $sql = 'SELECT * FROM ' . $this->table;
        if ($filter != NULL) {
            $sql.= " WHERE '$filter'";
        } else {
            $sql .= "";
        }
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get a specific tag.
     *
     * @access public
     * @param string $id The id of the tag.
     * @return array $data The details of the tag.
     */
    public function getCorrelation($id) {
        $sql = 'SELECT * FROM ' . $this->table;
        $sql.= " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to delete a tag.
     * The sort order of the following tags is decreased by one.
     *
     * @access public
     * @param string $id The id of the tag.
     * @return
     */
    public function deleteCorrelation($id) {
        $this->delete('id', $id);
    }
}
// end of class
?>