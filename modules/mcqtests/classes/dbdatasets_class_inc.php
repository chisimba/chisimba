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
 * Class for providing access to the table tbl_test_datasets in the database
 * @author Paul Mungai
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbdatasets extends dbtable {

    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public $table;
    public $objUser;
    public $userId;
    /**
     *
     * @var object to hold dbdataset_definitions class
     */
    public $objDSDefinitions;

    public function init() {
        parent::init('tbl_test_datasets');
        $this->table = 'tbl_test_datasets';
        $this->objUser = &$this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->objDSDefinitions = $this->newObject("dbdataset_definitions");
    }

    /**
     * Method to insert or update a record in the database.
     *
     * @access public
     * @param array $fields The table fields to be added/updated.
     * @param string $id The id of the dataset to be edited. Default=NULL.
     * @return string $id The id of the inserted or updated dataset.
     */
    public function addRecord($fields, $id = NULL) {
        if ($id) {
            $this->update('id', $id, $fields);
        } else {
            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
     * Method to get a set of records for a particular question.
     *
     * @access public
     * @param string $questionId The Id of the category being used.
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of datasets for the question.
     */
    public function getRecords($questionId = NULL, $filter = NULL) {
        $sql = 'SELECT * FROM ' . $this->table;
        if ($filter && $questionId) {
            $sql.= " WHERE questionid='".$questionId."' AND ".$filter;
        } else if ($filter != NULL) {
            $sql.= " WHERE ".$filter;
        } else if ($questionId != NULL) {            
            $sql.= " WHERE questionid='".$questionId."'";
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
     * Method to get a specific record.
     *
     * @access public
     * @param string $id The id of the dataset.
     * @return array $data The details of the dataset.
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
     * Method to delete a dataset.
     *
     * @access public
     * @param string $id The id of the dataset.
     * @return boolean
     */
    public function deleteRecord($id) {
        $this->delete('id', $id);
    }
    /**
     * Method to delete a dataset.
     *
     * @access public
     * @param string $qnId The id of the deleted question.
     * @return boolean
     */
    public function deleteQnRecord($qnId) {
        //Get related dataset id's
        $dsets = $this->getRecords($qnId);
        foreach ($dsets as $dset) {
            //Delete related instance
            $this->objDSDefinitions->deleteDataSetDef($dset['id']);
            //Delete dataset
            $this->deleteRecord($dset['id']);
        }
        //Delete dataset
        //$this->delete('questionid', $qnId);
    }
    /**
     * Method to count the number of datasets for the specified question.
     *
     * @access public
     * @param string $questionId The id of the specified question.
     * @return int $catnum The number of datasets for the question.
     */
    public function countRecords($questionId) {
        $sql = "SELECT count(id) AS qnum FROM " . $this->table . " WHERE questionid='$questionId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $qnum = $data[0]['qnum'];
            return $qnum;
        }
        return FALSE;
    }
}
// end of class
?>