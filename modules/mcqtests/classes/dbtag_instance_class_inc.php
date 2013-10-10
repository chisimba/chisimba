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
 * Class for providing access to the table tbl_test_tag_instance in the database
 * @author Paul Mungai
 *
 * @copyright (c) 2010 University of the Witwatersrand
 * @package mcqtests
 * @version 1.2
 */
class dbtag_instance extends dbtable {

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
        parent::init('tbl_test_tag_instance');
        $this->table = 'tbl_test_tag_instance';
        $this->objUser = &$this->getObject('user', 'security');
        $this->userId = $this->objUser->userId();
    }

    /**
     * Method to insert or update a tag instance in the database.
     *
     * @access public
     * @param array $fields The table fields to be added/updated.
     * @param string $id The id of the description to be edited. Default=NULL.
     * @return string $id The id of the inserted or updated description.
     */
    public function addInstance($fields, $id = NULL) {
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
     * Method to get a set of instances for a particular item.
     *
     * @access public
     * @param string $itemId The Id of the item being used.
     * @param string $filter An additional filter on the select statement.
     * @return array $data The list of instances.
     */
    public function getInstances($itemId = NULL, $filter = NULL) {

        $sql = 'SELECT * FROM ' . $this->table;
        if ($filter && $itemId) {
            $sql .= " WHERE itemid='" . $itemId . "' AND " . $filter;
        } else if ($filter != NULL) {
            $sql.= " WHERE " . $filter;
        } else if ($itemId != NULL) {
            $sql.= " WHERE itemid='".$itemId."'";
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
     * Method to get a specific instance.
     *
     * @access public
     * @param string $id The id of the instance.
     * @return array $data The details of the instance.
     */
    public function getInstance($id) {
        $sql = 'SELECT * FROM ' . $this->table;
        $sql.= " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            return $data;
        }
        return FALSE;
    }

    /**
     * Method to delete an instance.
     * The sort order of the following instances is decreased by one.
     *
     * @access public
     * @param string $id The id of the instance to be deleted.
     * @return
     */
    public function deleteTagInstance($id) {
        $desc = $this->getInstance($id);
        if (!empty($desc)) {
            $filter = 'sortorder > ' . $desc[0]['sortorder'] . ' ORDER BY sortorder';
            $data = $this->getInstances($desc[0]['itemid'], $filter);
            if (!empty($data)) {
                foreach ($data as $line) {
                    $fields = array();
                    $fields['sortorder'] = $line['sortorder'] - 1;
                    $this->addInstance($fields, $line['id']);
                }
            }
        }
        $this->delete('id', $id);
    }

    /**
     * Method to delete a question instance.
     *
     * @access public
     * @param string $id The id of the item to be deleted.
     * @return
     */
    public function deleteInstance($itemid) {
        $this->delete('itemid', $itemid);
    }

    /**
     * Method to count the number of instances in an item.
     *
     * @access public
     * @param string $itemId The id of the specified item.
     * @return int $instnum The number of instances for a particular item.
     */
    public function countInstances($itemId) {
        $sql = "SELECT count(id) AS qnum FROM " . $this->table . " WHERE itemid='$itemId'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $qnum = $data[0]['qnum'];
            return $qnum;
        }
        return FALSE;
    }

    /**
     * Change the order of instances in an item
     *
     * @access public
     * @param string $id The id of the instance to be moved
     * @param bool $order If order is true move instance up else move instance down 1
     * @return bool TRUE if the order has been changed, FALSE if it hasn't.
     */
    public function changeOrder($id, $order) {
        $sql = 'SELECT itemId, sortorder FROM ' . $this->table . " WHERE id='$id'";
        $data = $this->getArray($sql);
        if (!empty($data)) {
            $pos = $data[0]['sortorder'];
            $itemId = $data[0]['itemid'];
            // if move desc up, check its not the first desc
            if ($order && $pos > 1) {
                $newpos = $pos - 1;
                // if move desc down, check its not the last desc
            } else if (!$order) {
                $num = $this->countInstances($itemId);
                if ($pos < $num) {
                    $newpos = $pos + 1;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
            // swap order of instance
            $sql = 'SELECT id FROM ' . $this->table . " WHERE itemid='$itemId' and sortorder='$newpos'";
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