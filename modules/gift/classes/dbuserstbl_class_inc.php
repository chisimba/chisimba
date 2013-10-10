<?php

class dbuserstbl extends dbtable {

    /**
     * Assign the table name in dbtable to be the table specified below
     */
    public function init() {
        parent::init("tbl_gift_users");
        $this->objUser = $this->getObject("user", "security");
    }

    public function addUser($data) {
        if (!$this->valueExists('userid', $data['userid'])) {
            return $this->insert($data);
        }
        return FALSE;
    }

    function acceptPolicy() {
        $udata = array('userid' => $this->objUser->userId(), 'time' => strftime('%Y-%m-%d %H:%M:%S', mktime()));
        $this->addUser($udata);
        $data = array('accepted' => 'Y');
        return $this->update('userid', $this->objUser->userid(), $data);
    }

    function policyAccepted() {
        $data = $this->getRow('userid', $this->objUser->userid());
        return $data['accepted'];
    }

    function userExists($userid) {
        return $this->valueExists('userid', $userid);
    }

}

?>
