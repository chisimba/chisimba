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
 * Class for providing access to the table tbl_test_random_matching in the database
 * @author Paul Mungai
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbrandom_matching extends dbtable {

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
        parent::init('tbl_test_randomshortansmatch');
        $this->table = 'tbl_test_randomshortansmatch';
        $this->objUser = &$this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method to insert or update a Record in the database.
     *
     * @access public
     * @param array $fields The table fields to be added/updated.
     * @param string $id The id of the Record to be edited. Default=NULL.
     * @return string $id The id of the inserted or updated Record.
     */
    public function addRecord($fields, $id = NULL) {
        $fields['timemodified'] = date('Y-m-d H:i:s');
        //Check if the question has already been added
        $exists = $this->getRecords( 'questionid = "'.$fields["questionid"].'"');
        if(!empty($exists)){
            $id = $exists[0]["id"];
        }
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
     * Method to get a set of Records.
     *
     * @access public
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of Records.
     */
    public function getRecords($filter = NULL) {
        $sql = 'SELECT * FROM ' . $this->table;
        if ($filter != NULL) {
            $sql.= " WHERE ".$filter;
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
     * Method to get a specific Record.
     *
     * @access public
     * @param string $id The id of the instance.
     * @return array $data The details of the instance.
     */
    public function getRecord($id) {
        $sql = 'SELECT * FROM ' . $this->table;
        $sql.= " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to delete a Record.
     *
     * @access public
     * @param string $id The id of the Record to be deleted.
     * @return
     */
    public function deleteRecord($id) {
        $this->delete('id', $id);
    }

}

// end of class
?>