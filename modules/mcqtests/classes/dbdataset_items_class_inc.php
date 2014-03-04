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
 * Class for providing access to the table tbl_test_dataset_items in the database
 * @author Paul Mungai
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbdataset_items extends dbtable {

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
        parent::init('tbl_test_dataset_items');
        $this->table = 'tbl_test_dataset_items';
        $this->objUser = &$this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
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
     * Method to get a set of items for a particular dataset
     *
     * @access public
     * @param string $datasetId The Id of the dataset being used.
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of items for the dataset.
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
            return $data;
        return FALSE;
    }

    /**
     * Method to get a specific record.
     *
     * @access public
     * @param string $id The id of the item.
     * @return array $data The details of the item.
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
     * Method to delete a dataset-item.
     *
     * @access public
     * @param string $id The id of the item.
     * @return
     */
    public function deleteRecord($id) {
        $this->delete('id', $id);
    }
    /**
     * Method to delete all dataset-definition-instances.
     *
     * @access public
     * @param string $dsetid The id of the dataset definition
     * @return
     */
    public function deleteDSetDefRecs($dsetid) {
        $this->delete('datasetid', $dsetid);
    }
}
// end of class
?>