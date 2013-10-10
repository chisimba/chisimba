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
 * Class for providing access to the table tbl_test_questions in the database
 * @author James Scoble
 * @author Megan Watson
 *
 * @copyright (c) 2004 UWC
 * @package mcqtests
 * @version 1.2
 */
class dbquestions extends dbtable {

    /**
     *
     * @var object to hold the user class
     */
    public $objUser;
    /**
     *
     * @var object to hold tbl blocks class
     */
    public $dbBlocks;
    /**
     *
     * @var object to hold question_answers class
     */
    public $dbAnswers;
    /**
     *
     * @var object to hold dbdataset_definitions class
     */
    public $objDSDefinitions;
    /**
     *
     * @var object to hold dbdataset_items class
     */
    public $objDSItems;
    /**
     *
     * @var object to hold dbdataset class
     */
    public $objDBDataset;
    /**
     *
     * @var object to hold dbtag class
     */
    public $dbTag;
    /**
     *
     * @var object to hold dbtag_instance class
     */
    public $dbTagInstance;

    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    public function init() {
        parent::init('tbl_test_questions');
        $this->table = 'tbl_test_questions';
        $this->objWashout = $this->getObject('washout', 'utilities');
        // get the DB objects
        $this->objUser = $this->getObject('user', 'security');
        $this->dbBlocks = $this->newObject('dbblocks');
        $this->dbAnswers = &$this->newObject('dbanswers');
        $this->objDSDefinitions = $this->newObject("dbdataset_definitions");
        $this->objDSItems = $this->newObject("dbdataset_items");
        $this->objDBDataset = $this->newObject("dbdatasets");
        $this->dbTag = $this->newObject('dbtag');
        $this->dbTagInstance = $this->newObject('dbtag_instance');

        //$this->objUser = $this->newObject('user', 'security');
    }

    /**
     * Method to insert or update a question in the database.
     *
     * @access public
     * @param array $fields The table fields to be updated.
     * @param string $id The id of the tag to be edited. Default=NULL.
     * @param string $saveAsNew Flag indicating whether question should be saved as new qn
     * @return string $id The id of the inserted or updated question.
     */
    public function addQuestion($fields, $id = NULL, $saveAsNew = Null) {
        $fields['updated'] = date('Y-m-d H:i:s');
        $inserting = 0;
        if ($saveAsNew == 1) {
            $inserting = 1;
            $id = $this->insert($fields);
        } else if (!empty($id) && $saveAsNew == 0) {
            $upid = $this->update('id', $id, $fields);
        } else {
            $inserting = 1;
            $id = $this->insert($fields);
        }

        if (empty($qnid))
            $qnid = $id;

        //Add record to blocks
        if ($inserting == 1) {
            $arrData = array();
            $arrData['categoryid'] = $fields['testid'];
            $arrData['title'] = $fields['questiontype']; //qtype
            $arrData['side'] = "main";
            $arrData['isblock'] = "1";
            $arrData['content'] = $id;
            $arrData['blockname'] = $fields['questiontype']; //qtype
            $arrData['blockmodule'] = "mcqtests";
            $this->dbBlocks->insertBlock($arrData);
        }
        return $qnid;
    }

    /**
     * Method to insert or update a question in the database.
     *
     * @access public
     * @param array $fields The table fields to be added.
     * @return string $id The id of the new question.
     */
    public function addNewQuestion($fields) {
        $fields['updated'] = date('Y-m-d H:i:s');
        $id = $this->insert($fields);
        //Add record to blocks
        $arrData = array();
        $arrData['categoryid'] = $fields['testid'];
        $arrData['title'] = $fields['qtype'];
        $arrData['side'] = "main";
        $arrData['isblock'] = "1";
        $arrData['content'] = $id;
        $arrData['blockname'] = $fields['qtype'];
        $arrData['blockmodule'] = "mcqtests";
        $this->dbBlocks->insertBlock($arrData);

        return $id;
    }

    /**
     * Method to get a set of questions for a particular test.
     *
     * @access public
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of questions.
     */
    public function getQuestions($testId, $filter = NULL) {
        $sql = 'SELECT * FROM ' . $this->table;
        if ($filter) {
            $sql.= " WHERE testid='$testId' AND $filter";
        } else {
            $sql.=" WHERE testid='$testId' ORDER BY questionorder";
        }
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $count = $this->countQuestions($testId);
            $data[0]['count'] = $count;
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get a set of questions with their correct answers for a particular test.
     *
     * @access public
     * @param string $testId The id of the test being used.
     * @param string $num The question to start from.
     * @return array $data The list of questions and answers in the test.
     */
    public function getQuestionCorrectAnswer($testId, $num = 0) {
        $sql = "SELECT DISTINCT question.*, question.id AS questionId, answer.*";
        $sql.= " FROM " . $this->table . " AS question,";
        $sql.= " tbl_test_answers AS answer";
        $sql.= " WHERE question.id = answer.questionid";
        $sql.= " AND question.testid='$testId'";
        $sql.= " AND answer.correct = 1";
        $sql.= " AND question.questionorder > $num";
        $sql.= " ORDER BY question.questionorder LIMIT 10";
        $data = $this->getArray($sql);

        if (!empty($data)) {
            $count = $this->countQuestions($testId);
            $data[0]['count'] = $count;
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get a set of questions with answers for a particular test.
     *
     * @access public
     * @param string $testId The id of the test being used.
     * @return array $data The list of questions and answers in the test.
     */
    public function getQuestionAndAnswer($testId) {
        $sql = "SELECT question.*, answer.* FROM " . $this->table . " AS question,";
        $sql.= " tbl_test_answers AS answer";
        $sql.= " WHERE question.id = answer.questionid";
        $sql.= " AND testid='$testId'";
        $sql.= " ORDER BY question.questionorder, answer.answerorder";

        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get a question.
     *
     * @access public
     * @param string $id The id of the question.
     * @return array $data The details of the question.
     */
    public function getQuestion($id) {
        $sql = 'SELECT * FROM ' . $this->table;
        $sql.= " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to delete a question.
     * The question order of the following questions is decreased by one.
     *
     * @access public
     * @param string $id The id of the question.
     * @return
     */
    public function deleteQuestion($id) {
        //Get All answers and delete
        $this->dbAnswers->removeAnswers('questionid', $id);
        //Get all question tag instances and delete
        $this->dbTagInstance->deleteInstance($id);
        //Delete all related datasets
        $this->objDBDataset->deleteQnRecord($id);
        //Delete Question
        $this->delete('id', $id);
    }

    /**
     * Method to count the number of questions in a specified test.
     *
     * @access public
     * @param string $testId The id of the specified test.
     * @return int $qnum The number of questions in the test.
     */
    public function countQuestions($testId) {
        $sql = "SELECT count(id) AS qnum FROM " . $this->table . " WHERE testid='$testId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $qnum = $data[0]['qnum'];
            return $qnum;
        }
        return FALSE;
    }

    /**
     * Method to sum the total mark for a test.
     *
     * @access public
     * @param string $testId The id of the specified test.
     * @return int $tmark The total mark for the test.
     */
    public function sumTotalmark($testId) {
        $sql = "SELECT sum(mark) AS totalmark FROM " . $this->table . " WHERE testid='$testId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $tmark = $data[0]['totalmark'];
            return $tmark;
        }
        return FALSE;
    }

    /**
     * Change the order of questions in the test
     *
     * @access public
     * @param string $id The id of the question to be moved
     * @param bool $order If order is true move question up else move question down 1
     * @return bool TRUE if the order has been changed, FALSE if it hasn't.
     */
    public function changeOrder($id, $order) {
        $sql = 'SELECT testid, questionorder FROM ' . $this->table . " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $pos = $data[0]['questionorder'];
            $testId = $data[0]['testid'];
            // if move question up, check its not the first question
            if ($order && $pos > 1) {
                $newpos = $pos - 1;
                // if move question down, check its not the last question
            } else if (!$order) {
                $num = $this->countQuestions($testId);
                if ($pos < $num) {
                    $newpos = $pos + 1;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
            // swap order of questions
            $sql = 'SELECT id FROM ' . $this->table . " WHERE testid='$testId' and questionorder='$newpos'";
            $result = $this->getArray($sql);
            if (!empty($result)) {
                $this->update('id', $result[0]['id'], array(
                    'questionorder' => $pos
                ));
                $this->update('id', $id, array(
                    'questionorder' => $newpos
                ));
                return TRUE;
            }
        }
        return FALSE;
    }

    public function getTotalMarks($testId) {
        $mark = 0;
        $questions = $this->getQuestions($testId);

        //var_dump($questions);

        if (count($questions) > 0 && $questions != FALSE) {
            foreach ($questions as $question) {
                $mark += $question['mark'];
            }
        }

        return $mark;
    }

    /**
     * Method to get the preview of a question
     * @param array $question Array containing details of the question
     * @return string Preview of Question
     */
    public function previewQuestion($question) {
        //var_dump($question);
        return $this->objWashout->parseText($question['question']);
    }

    /*
     * Method to submit questions selected from grid view.
     *
     * @access public
     * @param string $contextID
     * @param array $data Array containing details of the question
     * @param string $id The id of the test
     * @return none
     */

    public function submitDBQuestions($contextID, $data, $id) {
        $dbAnswers = $this->newObject('dbanswers');
        $myIDs = explode(",", $data);
        // get the order of the questions
        $questionOrder = $this->countQuestions($id);
        foreach ($myIDs as $eachID) {
            $questionOrder++;
            $data = $this->getRow('id', $eachID);
            $lastQID = $data['id'];
            unset($data['id']);
            unset($data['puid']);
            unset($data['questionorder']);
            $data['testid'] = $id;
            $data['questionorder'] = $questionOrder;
            $qID = $this->addQuestion($data);

            // get answers for this id and also insert them
            $sql = "select * from tbl_test_answers where questionid = '" . $lastQID . "'";
            $ansData = $this->getArray($sql);

            foreach ($ansData as $row) {
                $row['questionid'] = $qID;
                unset($row['id']);
                unset($row['puid']);
                unset($row['updated']);
                $row['testid'] = $id;
                $aID = $dbAnswers->addAnswers($row);
            }
        }
        // update the total mark for this test
        $totalMark = $this->getTotalMarks($id);
        $this->updateTotalMarks($id, $totalMark);
    }

    public function updateTotalMarks($testid, $mark) {
        $dbTestadmin = $this->newObject('dbtestadmin');
        $dbTestadmin->setTotal($testid, $mark);
    }

    public function getMaxOrder($id) {
        $sql = "select max(questionorder) questionorder from $this->table where testid = '" . $id . "'";

        $data = $this->getArray($sql);
        return $data[0]['questionorder'];
    }

}

// end of class
?>