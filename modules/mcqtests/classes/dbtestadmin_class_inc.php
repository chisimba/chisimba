<?php

/**
 * @package mcqtests
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class provides access to the database table tbl_tests.
 * @author Megan Watson
 *
 * @copyright (c) 2004 UWC
 * @package mcqtests
 * @version 1.2
 */
class dbtestadmin extends dbtable {

    /**
     * Method to construct the class and initialise the database table.
     *
     * @access public
     * @return
     */
    public function init() {
        parent::init('tbl_tests');
        $this->table = 'tbl_tests';
        $this->dbQuestions = &$this->newObject('dbquestions');
        $this->dbAnswers = &$this->newObject('dbanswers');
        $this->dbResults = &$this->newObject('dbresults');
        $this->dbMarked = &$this->newObject('dbmarked');

    }

    /**
     * Method to add or update a test.
     *
     * @access public
     * @param array $fields The data to be inserted into the table
     * @param string $id The id of the test to be updated. Default=NULL.
     * @return
     */
    public function addTest($fields, $id = NULL) {
        if ($id) {
            $this->update('id', $id, $fields);
        } else {
            $id = $this->insert($fields);
        }
        return $id;
    }

    /**
     * Method to get a test or list of tests.
     *
     * @access public
     * @param string $fields A list of fields to be returned. Default=*.
     * @param string $id The id of the required test. Default=NULL.
     * @return array $data The details of the test or list of tests.
     */
    public function getTests($context, $fields = '*', $id = NULL) {
        $sql = "SELECT $fields FROM " . $this->table;
        if ($id) {
            $sql.= " WHERE id='$id'";
        } else {
            $sql.= " WHERE context='$context'";
        }
        $sql.= " and testtype not like 'Advanc%' ORDER BY closingdate DESC, name";
        
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get a test or list of tests for experimental question types.
     *
     * @access public
     * @param string $fields A list of fields to be returned. Default=*.
     * @param string $id The id of the required test. Default=NULL.
     * @return array $data The details of the test or list of tests.
     */
    public function getTests2($context, $fields = '*', $id = NULL) {
        $sql = "SELECT $fields FROM " . $this->table;
        if ($id) {
            $sql.= " WHERE id='$id'";
        } else {
            $sql.= " WHERE context='$context'";
        }
        $sql.= " and testtype like 'Advanc%' ORDER BY closingdate DESC, name";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get the newest test
     * Required especially in the gradebook module in uploading offline assessments
     *
     * @access public
     * @author otim samuel
     * @param string $fields A list of fields to be returned. Default=*.
     * @param string $filters The filters of the required test. Default=NULL.
     * @return array $data The details of the test or list of tests.
     */
    public function getNewestTest($fields = '*', $filter = NULL) {
        $sql = "SELECT $fields FROM " . $this->table;
        if ($filter) {
            $sql.= " WHERE $filter";
        }
        $sql.= " LIMIT 1";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get the total percentage of all the tests for the given context.
     * If editing a test, exclude its percentage.
     *
     * @access public
     */
    public function getPercentage($context, $testId = NULL) {
        $sql = "SELECT percentage FROM " . $this->table;
        $sql.= " WHERE context='$context'";
        if (isset($testId) && !empty($testId)) {
            $sql.= " AND id != '$testId'";
        }
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $total = 0;
            foreach ($data as $item) {
                $total = $total + $item['percentage'];
            }
            return $total;
        }
        return 0;
    }

    /**
     * Method to get a test or list of tests.
     *
     * @access public
     * @param string $fields A list of fields to be returned. Default=*.
     * @param string $id The id of the required test. Default=NULL.
     * @return array $data The details of the test or list of tests.
     */
    public function getStudentTests($context, $studentId) {
        $sql = "SELECT test.*, result.* FROM " . $this->table . ' AS test, ';
        $sql.= "tbl_test_results AS result ";
        $sql.= "WHERE result.testid = test.id ";
        $sql.= "AND test.context = '$context' ";
        $sql.= "AND result.studentid = '$studentId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to search tests in a context.
     *
     * @access public
     * @param string $field The table field in which to search.
     * @param string $value The value to search for.
     * @param string $context The current context.
     * @return array $data The results of the search.
     */
    public function search($field, $value, $context) {
        $sql = "SELECT * FROM " . $this->table;
        $sql.= " WHERE $field LIKE '$value%'";
        $sql.= " AND context='$context'";
        $sql.= ' ORDER BY closingdate, name';
        $data = $this->getArray($sql);
        if ($data) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to delete a test
     *
     * @access public
     * @param string $id The id of the test
     * @return
     */
    public function deleteTest($id) {
        $this->delete('id', $id);
        $this->dbQuestions->delete('testid', $id);
        $this->dbAnswers->delete('testid', $id);
        $this->dbResults->delete('testid', $id);
        $this->dbMarked->delete('testid', $id);
    }

    /**
     * Method to set the total mark for a test
     *
     * @access public
     * @param string $id The id of the test to update
     * @param string $total The mark to be added to the total
     * @return
     */
    public function setTotal($id, $total) {
        return $this->update('id', $id, array('totalmark' => $total));
    }

    /**
     * Method to get all the tests to fix results
     *
     * @access public
     * @return array $data The test data array
     */
    public function getAllTests() {
        $sql = "SELECT * FROM " . $this->table;
        $data = $this->getArray($sql);
        if ($data) {
            return $data;
        }
        return FALSE;
    }

    /*
     * Method to get all questions for this test (context)
     *
     * @access public
     *
     * @return array $data the multiple choice questions array
     */

    public function getContextQuestions($contextCode, $testid, $type=null, $courses=null, $start=null, $limit=null) {
        $sql = "SELECT A.name, B.id, INSERT(MID(B.question, 1, 100),100,3,'...') as question, B.hint,B.mark, B.questiontype FROM " . $this->table;
        $sql .= " as A join tbl_test_questions as B on A.id = B.testid";
        $sql .= " WHERE A.context = '" . $contextCode . "' AND B.testid != '" . $testid . "'";

        $start = ($start == null) ? 0 : $start;
        $count = ($limit == null) ? 20 : $limit;
        $type = str_replace("-", "", $type);
        if (strlen(trim($type)) > 0) {
            $courses = str_replace("-", "", $courses);
            $type = ($type == 'mcq') ? "'mcq' OR B.questiontype ='tf'" : $type;
            if (strlen(trim($courses)) > 0) {
                //get the data based on page in grid
                $type = ($type == 'mcq') ? "'mcq' OR B.questiontype ='tf'" : $type;
                $sql = "SELECT A.name, B.id,  INSERT(MID(B.question, 1, 100),100,3,'...') as question, B.hint,B.mark, B.questiontype FROM " . $this->table;
                $sql .= " as A join tbl_test_questions as B on A.id = B.testid";
                $sql .= " WHERE (B.questiontype=" . $type . ")";
                $sql .= " LIMIT " . $start . "," . $count;
                $data = $this->getArray($sql);
                $index = 0;
                foreach ($data as $row) {
                    $row['question'] = '<a href="#" onClick=\'showQuestion("'.$row['id'].'");\'>' . $row['question'] . '</a>';
                    $data[$index] = $row;
                    $index++;
                }

                // get the total number of records
                $myCountSql = "SELECT A.name FROM " . $this->table;
                $myCountSql .= " as A join tbl_test_questions as B on A.id = B.testid";
                $myCountSql .= " WHERE (B.questiontype=" . $type . ")";
                $countData = $this->getArray($myCountSql);
                $totalcount = count($countData);
                return json_encode(array('totalcount' => $totalcount, 'results' => $data));
            } else {
                $sql .= " and (B.questiontype=".$type.")";
                $sql .= " LIMIT " . $start . "," . $count;
                $data = $this->getArray($sql);
                $index = 0;
                foreach ($data as $row) {
                    $row['question'] = '<a href="#" onClick=\'showQuestion("'.$row['id'].'");\'>' . $row['question'] . '</a>';
                    $data[$index] = $row;
                    $index++;
                }

                // get the total number of records
                $myCountSql = "SELECT A.name FROM " . $this->table;
                $myCountSql .= " as A join tbl_test_questions as B on A.id = B.testid";
                $myCountSql .= " WHERE A.context = '" . $contextCode . "' AND B.testid != '" . $testid . "'";
                $myCountSql .= " and (B.questiontype=" . $type . ")";
                $countData = $this->getArray($myCountSql);
                $totalcount = count($countData);


                return json_encode(array('totalcount' => $totalcount, 'results' => $data));
            }
        } else {
            $sql .= " LIMIT " . $start . "," . $count;
            $data = $this->getArray($sql);
            $index = 0;
            foreach ($data as $row) {
                $row['question'] = '<a href="#" onClick=\'showQuestion("'.$row['id'].'");\'>' . $row['question'] . '</a>';
                $data[$index] = $row;
                $index++;
            }

            // get the total number of records
            $myCountSql = "SELECT A.name FROM " . $this->table;
            $myCountSql .= " as A join tbl_test_questions as B on A.id = B.testid";

            $countData = $this->getArray($myCountSql);
            $totalcount = count($countData);

            if ($data) {
                return json_encode(array('totalcount' => $totalcount, 'results' => $data));
            }
            return FALSE;
        }
    }

    /*
     * Method to get the name of the test
     *
     * @access public
     * @param string $id the id of the test
     *
     * @return string $name the name of the test
     */

    public function getTestName($testid) {
        $data = $this->getRow('id', $testid);

        return $data['name'];
    }

}

// end of class
?>