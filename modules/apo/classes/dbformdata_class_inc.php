<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class dbformdata extends dbtable {

    var $tablename = "tbl_apo_overview";
    var $userid;

    public function init() {
        parent::init($this->tablename);
    }

    public function saveData($docid, $formname, $formdata) {
        $formdata = unserialize($formdata);
        $tablename = "tbl_apo_" . $formname;
        $this->objUser = $this->getObject('user', 'security');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->userutils = $this->getObject('userutils');
        $data = array();
        $data["id"] = $docid;
        $formdatanew = array_merge($data, $formdata);

        if ($this->exists($docid, $formname)) {
            print_r("exists");
            $existingdata = $this->getAll("where docid='$docid'");

            if (count($existingdata) > 0) {
                $this->update('docid', $existingdata[0]['docid'], $formdata, $tablename);
            }
        }
        else {
            $this->insert($formdatanew, $tablename);
        }
    }

    function exists($docid, $formname) {
        $sql = "select * from tbl_apo_" . $formname . " where docid='$docid'";
        $rows = $this->getArray($sql);
        if (count($rows) > 0) {
            return TRUE;
        }
        else
            return FALSE;
    }

    public function getFormData($formname, $docid) {
        $sql = "select * from tbl_apo_".$formname." where docid='$docid'";
        $data = $this->getArray($sql);
        $formdata = "";
        if(!empty($data)) {
            $formdata = $data[0];
        }
        
        return $formdata;
    }
}
?>