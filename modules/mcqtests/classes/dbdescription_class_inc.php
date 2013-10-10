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
 * Class for providing access to the table tbl_test_description in the database
 * @author Paul Mungai
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbdescription extends dbtable {

    /**
     * Method to construct the class and initialise the table.
     *
     * @access public
     * @return
     */
    /**
     *
     * @var object to hold tbl blocks class
     */
    public $dbBlocks;
    public $table;
    public $objUser;
    public $userId;

    public function init() {
        parent::init('tbl_test_description');
        $this->table = 'tbl_test_description';
        //Get Objects
        $this->dbBlocks = $this->newObject('dbblocks');
        $this->objUser = &$this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method to insert or update a description in the database.
     *
     * @access public
     * @param array $fields The table fields to be added/updated.
     * @param string $id The id of the description to be edited. Default=NULL.
     * @return string $id The id of the inserted or updated description.
     */
    public function addDescription($fields, $id = NULL) {
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
     * @param string $categoryId The Id of the category being used.
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of descriptions in the category.
     */
    public function getDescriptions($categoryId = NULL, $filter = NULL) {
        $sql = 'SELECT * FROM ' . $this->table;
        if ($filter && $categoryId) {
            $sql.= " WHERE categoryid='$categoryId' AND $filter";
        } else if ($filter != NULL) {
            $sql.= " WHERE categoryid='$categoryId' ORDER BY sortorder";
        } else if ($categoryId != NULL) {
            $sql.= " WHERE categoryid='$categoryId' ORDER BY sortorder";
        } else {
            $sql .= "";
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
     * Method to get a specific description.
     *
     * @access public
     * @param string $id The id of the description.
     * @return array $data The details of the description.
     */
    public function getDescription($id) {
        $sql = 'SELECT * FROM ' . $this->table;
        $sql.= " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to delete a description.
     * The sort order of the following descriptions is decreased by one.
     *
     * @access public
     * @param string $id The id of the description.
     * @return
     */
    public function deleteDescription($id) {
        $desc = $this->getDescription($id);
        if (!empty($desc)) {
            $filter = 'sortorder > ' . $desc[0]['sortorder'] . ' ORDER BY sortorder';
            $data = $this->getDescriptions($desc[0]['categoryid'], $filter);
            if (!empty($data)) {
                foreach ($data as $line) {
                    $fields = array();
                    $fields['sortorder'] = $line['sortorder'] - 1;
                    $this->addDescription($fields, $line['id']);
                }
            }
        }
        $this->delete('id', $id);
    }

    /**
     * Method to count the number of descriptions in the specified category.
     *
     * @access public
     * @param string $categoryId The id of the specified category.
     * @return int $catnum The number of descriptions in the category.
     */
    public function countDescriptions($categoryId) {
        $sql = "SELECT count(id) AS qnum FROM " . $this->table . " WHERE categoryid='$categoryId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $qnum = $data[0]['qnum'];
            return $qnum;
        }
        return FALSE;
    }

    /**
     * Change the order of descriptions in a category
     *
     * @access public
     * @param string $id The id of the description to be moved
     * @param bool $order If order is true move description up else move description down 1
     * @return bool TRUE if the order has been changed, FALSE if it hasn't.
     */
    public function changeOrder($id, $order) {
        $sql = 'SELECT categoryId, sortorder FROM ' . $this->table . " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $pos = $data[0]['sortorder'];
            $categoryId = $data[0]['categoryid'];
            // if move desc up, check its not the first desc
            if ($order && $pos > 1) {
                $newpos = $pos - 1;
                // if move desc down, check its not the last desc
            } else if (!$order) {
                $num = $this->countDescriptions($categoryId);
                if ($pos < $num) {
                    $newpos = $pos + 1;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
            // swap order of desc
            $sql = 'SELECT id FROM ' . $this->table . " WHERE categoryid='$categoryId' and sortorder='$newpos'";
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