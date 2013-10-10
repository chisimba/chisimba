<?php

class dbgift extends dbtable {

    /**
     * Assign the table name in dbtable to be the table specified below
     */
    public function init() {
        parent::init("tbl_gift");
        $this->objUser = $this->getObject("user", "security");
    }

    /**
     * Submitted information from the Add Gift form is saved as a new record
     * in the database.
     * @param string $donor
     * @param string $recipient
     * @param string $giftname
     * @param blob $description
     * @param int $value
     * @param boolean $listed
     * @return boolean
     */
    public function addInfo(
    $donor, $recipient, $giftname, $description, $value, $listed, $division, $type, $comments, $date_recieved) {
        $donor = str_replace("'", "\'", $donor);
        $comments = str_replace("'", "\'", $comments);
        $description = str_replace("'", "\'", $description);

        $data = array(
            "donor" => $donor,
            "recipient" => $recipient,
            "giftname" => $giftname,
            "description" => $description,
            "value" => $value,
            "listed" => $listed,
            "comments" => addslashes($comments),
            "gift_type" => $type,
            "division" => $division,
            "deleted" => 'N',
            "date_recieved" => $date_recieved,
            "tran_date" => strftime('%Y-%m-%d %H:%M:%S', mktime()));
        $result = $this->insert($data);
        return $result;
    }

    /**
     * Edited, submitted information from the Edit Gift form is updated
     * in the database under the correct row.
     * @param string $donor
     * @param string $recipient
     * @param string $giftname
     * @param blob $description
     * @param int $value
     * @param boolean $listed
     * @param string $id
     * @return boolean
     * $donor, $recipient, $name, $description, $value, $listed, $id,$comments
     */
    public function updateInfo($donor, $recipient, $giftname, $description, $value, $listed, $id, $comments, $date_recieved) {
        $data = array(
            "donor" => $donor,
            "recipient" => $recipient,
            "giftname" => $giftname,
            "description" => $description,
            "value" => $value,
            "listed" => $listed,
            "comments" => $comments,
            "date_recieved" => $date_recieved
        );



        $result = $this->update('id', $id, $data);
        return $result;
    }

    public function updateGift($id, $data) {
        $result = $this->update('id', $id, $data);
        return $result;
    }

    public function deleteGift($id) {
        $data = array("deleted" => "Y");
        $result = $this->update('id', $id, $data);
        return $result;
    }

    /**
     * Get the array associated with a specific query.
     * @param string $qry
     * @return array
     */
    public function getInfo($qry) {
        $data = $this->getArray($qry);
        return $data;
    }

    function exists($name,$departmentid) {
        $sql =
                "select * from tbl_gift where giftname ='$name' and division='$departmentid'";
        $rows = $this->getArray($sql);
        if (count($rows) > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Replaces the archived status value with the opposite value.
     * (i.e. if listed is 0, then listed becomes 1 and vice versa)
     * Used to archive non-archived gifts and unarchive archived gifts.
     * @param string $id
     * @return boolean
     */
    public function archive($id) {
        $listed = !$this->_getListedValue($id);
        $data['listed'] = $listed;
        $result = $this->update('id', $id, $data);
        return $result;
    }

    public function checkDuplicates($data) {
        $qry = "SELECT * FROM tbl_gift WHERE
                donor = '{$data['donor']}',
                recipient = '{$data['recipient']}',
                giftname = '{$data['giftname']}',
                description = '{$data['description']}'
                value = '{$data['value']}'";
        $info = $this->getArray(qry);
        return count($info);
    }

    /**
     * Used in conjunction with archive to get the value of the field "listed"
     * so that the value can be altered.
     * @param string $id
     * @return int;
     */
    private function _getListedValue($id) {
        $result = $this->getRow('id', $id);
        return $result['listed'];
    }

    /*
      public function getGifts() {
      $sql = "select * from tbl_gift"; //." where userid = '".$userid."'";
      $rows = $this->getArray($sql);
      return $rows;
      } */

    public function getNumberOfGifts() {
        return $this->getRecordCount();
    }

    public function getGiftCountByDepartment($department) {
        $qry = "SELECT count(id) as total FROM tbl_gift WHERE  division= '$department' and (deleted='N' or deleted is null)";
        $total = 0;
        $data = $this->getInfo($qry);
        if (count($data) > 0) {
            $row = $data[0];
            $total = $row['total'];
        }
        return $total;
    }

    public function getGifts($department) {
        $recipient = $this->objUser->userid();     // Recipient name

        $qry = "SELECT * FROM tbl_gift WHERE recipient = '$recipient'";// and (deleted='N' or deleted is null)";
        /* if (isset($query)) {
          $qry .= " AND (giftname LIKE '%" . addslashes($query) . "%' )";
          } */
        $qry.=" and division= '$department'";
        if ($this->objUser->isAdmin()) {
            $qry = "SELECT * FROM tbl_gift";
            $qry.=" where division= '$department'";// and (deleted='N' or deleted is null)";
        }

        $data = $this->getInfo($qry);

        return $data;
    }

    //returns boolean

    function userExists($userid) {
        return $this->valueExists('userid', $userid);
    }

    function getGift($id) {
        return $this->getRow("id", $id);
    }

    function searchGifts($query) {
        $sql =
                "select * from tbl_gift where
        donor like '%$query%' or giftname like '%$query%' or description like '%$query%'
         or date_recieved like '%$query%' or value like '%$query%'";// and (deleted='N' or deleted is null)";
        return $this->getArray($sql);
    }

    function searchGiftsByDate($dateFrom, $dateTo) {
        $sql =
                "select * from tbl_gift where date_recieved between '$dateFrom 00:00:00.0' and '$dateTo 00:00:00.0'";// and (deleted='N' or deleted is null)";
        $data = $this->getArray($sql);

        return $data;
    }

    function searchGiftsByDonor($donor) {
        $sql =
                "select * from tbl_gift where donor like '%$donor%'";
        $data = $this->getArray($sql);

        return $data;
    }

    function searchGiftsByType($type) {
        $sql =
                "select * from tbl_gift where gift_type='$type'";
        $data = $this->getArray($sql);

        return $data;
    }

    function searchGiftsByValue($min, $max) {
        $sql =
                "select * from tbl_gift where value >= '$min' and value <= '$max'";
        $data = $this->getArray($sql);

        return $data;
    }

    function getUserActivity($startdate, $enddate, $module) {
        $sql =
                "select * from tbl_useractivity
        where  (createdon between '$startdate' and '$enddate')

        and (module='$module' or module = 'security')order by createdon";
        return $this->getArray($sql);
    }

}

?>
