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
 * Class for providing access to the table tbl_test_dataset_definitions in the database
 * @author Paul Mungai
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbdataset_definitions extends dbtable {

    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public $table;
    public $objUser;
    public $userId;
    private $datasetId;
    private $categoryId;
    /**
     *
     * @var object to hold dbdataset_items class
     */
    public $objDSItems;

    public function init() {
        parent::init('tbl_test_dataset_definitions');
        $this->table = 'tbl_test_dataset_definitions';
        $this->objUser = &$this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
        $this->objDSItems = $this->newObject("dbdataset_items");
    }

    /**
     * Method to insert or update a record in the database.
     *
     * @access public
     * @param array $fields The table fields to be added/updated.
     * @param string $id The id of the definition to be edited. Default=NULL.
     * @return string $id The id of the inserted or updated definitions.
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
     * Method to get a set of definitions for a particular category
     *
     * @access public
     * @param string $datasetId The Id of the datasetid being used.
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of definitions for the category.
     */
    public function getRecords($datasetId = NULL, $filter = NULL) {
        $sql = 'SELECT * FROM ' . $this->table;
        if ($filter && $datasetId) {
            $sql.= " WHERE datasetid='$datasetId' AND $filter";
        } else if ($filter != NULL) {
            $sql.= " WHERE datasetid='$datasetId'";
        } else if ($datasetId != NULL) {
            $sql.= " WHERE datasetid='$datasetId'";
        } else {
            $sql .= "";
        }
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $count = $this->countRecords($datasetId);
            $data[0]['count'] = $count;
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get a specific record.
     *
     * @access public
     * @param string $id The id of the definition.
     * @return array $data The details of the definition.
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
     * Method to delete a dataset-definition.
     *
     * @access public
     * @param string $id The id of the definition.
     * @return
     */
    public function deleteRecord($id) {
        $this->delete('id', $id);
    }

    /**
     * Method to delete a dataset-definition.
     *
     * @access public
     * @param string $id The id of the definition.
     * @return
     */
    public function deleteDataSetDef($dsetid) {
        //Get records related to the dset
        $defs = $this->getRecords($dsetid);
        foreach ($defs as $thisdef) {
            $this->objDSItems->deleteDSetDefRecs($thisdef['id']);
            $this->deleteRecord($thisdef['id']);
        }
    }

    /**
     * Method to count the number of dataset-definitions for the specified category.
     *
     * @access public
     * @param string $categoryId The id of the specified question.
     * @return int $catnum The number of dataset-definitions for the category.
     */
    public function countRecords($categoryId) {
        $sql = "SELECT count(id) AS qnum FROM " . $this->table . " WHERE categoryid='$categoryId'";
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