<?php

class dbattachments extends dbtable {

    function init() {
        parent::init("tbl_gift_attachments");
    }

    function addAttachment($giftid, $name) {
        $name = str_replace("'", "\'", $name);
        $data = array(
            'giftid' => $giftid,
            "name" => $name
        );
        $this->insert($data);
    }

    function getAttachments($giftid) {
        $sql =
                "select * from tbl_gift_attachments where giftid = '$giftid' and (deleted='N' or deleted is null)";
        return $this->getArray($sql);
    }

    function getAttachment($id) {
        return $this->getRow("id", $id);
    }

    function getAttachmentName($id) {
        $row = $this->getRow("id", $id);
        return $row['name'];
    }

    function deleteAttachment($id) {
        $data = array("deleted" => "Y");
        return $this->update("id", $id, $data);
    }

}

?>
