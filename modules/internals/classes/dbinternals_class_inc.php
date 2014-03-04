<?php
// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dbinternals_class_inc
 *
 * @author monwabisi
 */
class dbinternals extends dbTable {

    var $altConfig;
    var $domDoc;

    //put your code here
    public function init() {
        parent::init('tbl_internals');
        $this->altConfig = $this->getObject('altconfig', 'config');
        $this->domDoc = new DOMDocument('utf-8');
    }

    /**
     * @access public
     * @return array List of internal rembers
     */
    public function getinternals() {
        return $this->getAll();
    }

    /**
     * Method to get the list of pennding requests
     * 
     * @access public
     * @param string $userId
     * @return array List of pending requests
     */
    public function getLeaveRequests($userId = NULL) {
        parent::init('tbl_internalrequests');
        if (!empty($userId)) {
            $requests = $this->getAll(NULL, "WHERE id='{$userId}' AND status='pending'");
        } else {
            $requests = $this->getAll();
        }
        return $requests;
    }

    /**
     * get the list of available leave type
     * 
     * @access public
     * @return array list of the leave types
     */
    public function getLeaveList() {
        parent::init('tbl_leaves');
        $list = $this->getAll();
        return $list;
    }

    /**
     * get leave name using the leave id as primary key
     * 
     * @access public
     * @return string Name of the leave
     */
    public function getLeaveName($id) {
        $leaveRow = $this->getRow('id', $id, 'tbl_leaves');
        return $leaveRow['name'];
    }

    /**
     * Method to return the leave ID using it's ID
     * 
     * @param string $leavename
     * @return The ID of the leave
     */
    public function getLeaveId($leavename) {
        $leaveRow = $this->getRow('name', $leavename, 'tbl_leaves');
        return $leaveRow['id'];
    }

    /**
     * Method to send the request to the database and to the module administrator
     * 
     * @access public
     * @param string $userID The database primary key for the user
     * @param string $leaveID The ID of the leave type the user applied for
     * @param date $startDate The date the user wishes ;to start leave
     * @param date $endDate The date the leave will expire
     * @param string $days The total number ofd days requested by the user
     * @param date $dateOfRequest Day when the request was made
     * @return boolean TRUE if the values were successfuly inserted to the database
     */
    public function postRequest($userID, $leaveID, $startDate, $endDate, $days,$dateOfRequest) {
        //create the holidays array

        $data = array(
            'id' => '',
            'userid' => $userID,
            'leaveid' => $leaveID,
            'days' => $days,
            'status' => 'pending',
            'startdate' => $startDate,
            'enddate' => $endDate,
            'requestdate'=>$dateOfRequest
        );
        return $this->insert($data, 'tbl_requests');
    }

    /**
     * Method to return the number of available days for the user
     * 
     * @acess public
     * @param string $leaveId The ID of the leave type the user applied for
     * @param string $userId The database primary key for the user
     * @return array The values retrieved from the database
     */
    public function getDaysLeft($leaveId, $userId) {
        $this->_tableName = 'tbl_leaverecords';
        $stateMent = "WHERE id = '{$leaveId}' AND userid = '{$userId}'";
        return $this->getAll($stateMent);
    }

    /**
     * Method to add the user to the leave mnagement database
     * 
     * @access public
     * @param string $userId The users primary key from the user's table in the system
     */
    public function addUser($userId) {
        if ($this->valueExists('id', $userId)) {
            //prevent duplication
            header('location:index.php?module=internals');
        } else {
            $values = array(
                'id' => $userId,
                'isinternalsadmin' => 'false'
            );
            //change the table
            $this->_tableName = 'tbl_internals';
            $this->insert($values);
            //change table
            $this->_tableName = "tbl_leaverecords";
        }
    }

    /**
     * The function to update the request after approval or rejection
     * 
     * @acces public
     * @param $requestId The unique record Id
     * @return boolean True or false depending on the query result
     */
    public function updateRequest($requestId, $userId) {
        $this->update('status', 'pending');
    }

    /**
     * Insert leave type to the database
     * 
     * @access public
     * @param string $leaveName The name of the leave |ie: Annual or sick.....
     * @param interg $numberOfDays the maximum number of days available for the leave type
     * @return boolean TRUE|FALSE returns true if the values were successfully inserted to the database
     */
    public function addLeaveType($leaveName, $numberOfDays) {
        $fields = array(
            'id' => NULL,
            'name' => $leaveName,
            'numberofdays' => $numberOfDays
        );
        $valueExists = FALSE;
        $leaveRecord = $this->getAll();
        if (count($leaveRecord) > 0) {
            $leaveName = trim($leaveName);
            $leaveName = strtolower($leaveName);
            foreach ($leaveRecord as $record) {
                $recordName = strtolower($record['name']);
                $recordName = trim($record['name']);
                if ($leaveName == $recordName) {
                    return TRUE;
                }
            }
        }
        if (!$valueExists) {
            return $this->insert($fields, 'tbl_leaves');
        }else{
            return TRUE;
        }
    }

}

?>
