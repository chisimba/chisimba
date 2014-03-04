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
 * Class for providing access to the table tbl_test_dbshortanswer in the database
 * @author Paul Mungai
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbshortanswer extends dbtable {

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
        parent::init('tbl_test_dbshortanswer');
        $this->table = 'tbl_test_dbshortanswer';
        $this->objUser = &$this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method to insert or update a short answer to the database.
     *
     * @access public
     * @param array $fields The table fields to be added/updated.
     * @param string $id The id of the answer to be edited. Default=NULL.
     * @return string $id The id of the inserted or updated answer.
     */
    public function addAnswer($fields, $id = NULL) {
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
     * Method to get a set of descriptions for a particular category.
     *
     * @access public
     * @param string $questionId The Id of the question.
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of answers for the question.
     */
    public function getAnswers($questionId, $filter = NULL) {
        $sql = 'SELECT * FROM ' . $this->table;
        if ($filter) {
            $sql.= " WHERE questionid='$questionId' AND $filter";
        } else {
            $sql.= " WHERE questionid='$questionId' ORDER BY sortorder";
        }
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $count = $this->countDescriptions($categoryId);
            $data[0]['count'] = $count;
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to get a specific answer.
     *
     * @access public
     * @param string $id The id of the answer.
     * @return array $data The details of the answer.
     */
    public function getAnswer($id) {
        $sql = 'SELECT * FROM ' . $this->table;
        $sql.= " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to delete an answer.
     * The sort order of the following answers is decreased by one.
     *
     * @access public
     * @param string $id The id of the answer to delete.
     * @return
     */
    public function deleteAnswer($id) {
        $desc = $this->getAnswer($id);
        if (!empty($desc)) {
            $filter = 'sortorder > ' . $desc[0]['sortorder'] . ' ORDER BY sortorder';
            $data = $this->getAnswers($desc[0]['questionid'], $filter);
            if (!empty($data)) {
                foreach ($data as $line) {
                    $fields = array();
                    $fields['sortorder'] = $line['sortorder'] - 1;
                    $this->addAnswer($fields, $line['id']);
                }
            }
        }
        $this->delete('id', $id);
    }

    /**
     * Method to count the number of descriptions in the specified category.
     *
     * @access public
     * @param string $questionId The id of the specified question.
     * @return int $catnum The number of answers for a particular question.
     */
    public function countAnswers($questionId) {
        $sql = "SELECT count(id) AS qnum FROM " . $this->table . " WHERE questionid='$questionId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $qnum = $data[0]['qnum'];
            return $qnum;
        }
        return FALSE;
    }

    /**
     * Change the order of answers in a given question
     *
     * @access public
     * @param string $id The id of the answer to be moved
     * @param bool $order If order is true move answer up else move answer down 1
     * @return bool TRUE if the order has been changed, FALSE if it hasn't.
     */
    public function changeOrder($id, $order) {
        $sql = 'SELECT questionid, sortorder FROM ' . $this->table . " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $pos = $data[0]['sortorder'];
            $questionId = $data[0]['questionid'];
            // if move desc up, check its not the first desc
            if ($order && $pos > 1) {
                $newpos = $pos - 1;
                // if move desc down, check its not the last desc
            } else if (!$order) {
                $num = $this->countAnswers($questionId);
                if ($pos < $num) {
                    $newpos = $pos + 1;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
            // swap order of answer
            $sql = 'SELECT id FROM ' . $this->table . " WHERE questionid='$questionId' and sortorder='$newpos'";
            $result = $this->getArray($sql);
            if (!empty($result)) {
                $this->update('id', $result[0]['id'], array(
                    'sortorder' => $pos
                ));
                $this->update('id', $id, array(
                    'sortorder' => $newpos
                ));
                return TRUE;
            }
        }
        return FALSE;
    }

}
// end of class
?>