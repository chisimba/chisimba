<?php

class dblivechat extends dbtable {

    function init() {
        parent::init('tbl_livechat_messages');
    }

    function addMessage($message, $from, $to) {
        $fields =
                array(
                    "user_from" => $from,
                    "user_to" => $to,
                    "message" => $message,
                    "message_time" => strftime('%Y-%m-%d %H:%M:%S', mktime())
        );
        return $this->insert($fields);
    }

}

?>
