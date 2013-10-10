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
 * Class for providing access to the table tbl_test_results in the database.
 * The table stores the student mark for a test
 * @author Megan Watson
 *
 * @copyright (c) 2004 UWC
 * @package mcqtests
 * @version 1.2
 */
class dbresults extends dbtable {
    /**
     * Method to construct the class and initialise the table
     *
     * @access public
     * @return
     */
    public function init() {
        parent::init('tbl_test_results');
        $this->table = 'tbl_test_results';
    }

    /**
     * Method to add a students result in a test to the database.
     *
     * @access public
     * @param array $fields The fields to be inserted.
     * @return string $id The new result id.
     */
    public function addResult($fields) {
        $fields['starttime'] = date("Y-m-d H:i:s");
        $fields['updated'] = date("Y-m-d H:i:s");
        $id = $this->insert($fields);
        return $id;
    }

    /**
     * Method to clean up results for export
     * fixes a UWC-specific problem
     * @access public
     * @param string $data The data
     * @param string $flag Alter userId or not
     * @return array $data2 The result.
     */
    public function cleanUp($data,$flag){
        $this->objUser=$this->getObject('user','security');
        $this->objSysCfg=$this->getObject('dbsysconfig','sysconfig');
        $doCleanUp=$this->objSysCfg->getValue('DO_CLEANUP','mcqtests');
        // Do no changes to the data unless the config param is set.
        if (($doCleanUp!=1)||($flag==0)){
            return $data;
        }
        foreach ($data as $line){
            if ($line['studentid']>19000000){
                $username=$this->objUser->userName($line['studentid']);
                $fullname=$this->objUser->fullname($line['studentid']);
                if (is_numeric($username)){
                    $line['surname']=$this->objUser->getSurname($line['studentid']).$line['studentid'];
                    $line['firstname']=$this->objUser->getFirstname($line['studentid']).$line['studentid'];
                    $line['studentid']=$username;
                    $line['fullname']=$fullname;
                }
            }
            //added for sorting
            $key=$this->objUser->getSurname($line['studentid']).$line['studentid'];
            $data2[$key]=$line;
        }
        ksort($data2);
        return $data2;
    }

    /**
     * Method to get a students result for a completed test.
     *
     * @access public
     * @param string $userId The id of the student.
     * @param string $testId The id of the test.
     * @return array $data The result.
     */
    public function getResult($userId, $testId) {
        $sql = 'SELECT * FROM '.$this->table;
        $sql.= " WHERE studentid='$userId' AND testid='$testId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get all results for a test.
     *
     * @access public
     * @param string $testId The id of the test.
     * @return array $data The results.
     */
    public function getResults($testId,$flag=0) {
        $sql = 'SELECT * FROM '.$this->table;
        $sql.= " WHERE testid='$testId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $this->cleanUp($data,$flag);
        }
        return FALSE;
    }

    /**
     * Method to get all results for a test for
     * the export functionality.
     *
     * @access public
     * @param string $testId The id of the test.
     * @return array $data The results.
     */
    public function getResultsForExport($testId,$flag=0) {
        $sql = "SELECT
            {$this->table}.*,
            tbl_users.username,
            tbl_users.firstname,
            tbl_users.surname
        FROM {$this->table}, tbl_users
        WHERE {$this->table}.testid='$testId'
            AND {$this->table}.studentid = tbl_users.userId";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $this->cleanUp($data,$flag);
        } else {
            return FALSE;
        }
    }

    /**
     * added by otim samuel, sotim@dicts.mak.ac.ug: 13th Jan 2006
     * for specific use within the gradebook module
     * Method to get all test results
     * as a percentage of the total year's mark
     *
     * @access public
     * @param string $filter
     * @param string $fields The required fields. Default = * (all);
     * @param string $tables The tables to be queried
     * @return array $data The result.
     */
    public function getAnnualResults($filter, $fields = '*', $tables = 'tbl_test_results') {
        $sql = "SELECT $fields FROM ".$tables;
        $sql.= " WHERE $filter";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to delete a result for a student.
     *
     * @access public
     * @param string $testId The id of the test.
     * @param string $studentId The id of the student.
     * @return bool
     */
    public function deleteResult($testId, $studentId) {
        $data = $this->getResult($studentId, $testId);
        if (!empty($data)) {
            $this->delete('id', $data[0]['id']);
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Method to update the mark in a result.
     *
     * @access public
     * @param string $id The id of the result to be updated.
     * @param string $mark The mark to be added.
     */
    public function addMark($id, $mark) {
        $this->update('id', $id, array(
                'mark' => $mark,
                'endtime' => date("Y-m-d H:i:s") ,
                'updated' => date("Y-m-d H:i:s")
        ));
    }
} // end of class
?>
