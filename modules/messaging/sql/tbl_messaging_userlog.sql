<?php
//5ive definition
$tablename = 'tbl_messaging_userlog';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of users in a chat room', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'room_id' => array( // the room the user is in
        'type' => 'text',
        'length' => 32,
        ),
    'user_id' => array( // the user id
        'type' => 'text',
        'length' => 32,
        ),
    );

// create other indexes here...
$name = 'tbl_messaging_userlog_index';

$indexes = array(
                'fields' => array(
                    'room_id' => array(),
                    'user_id' => array(),
                )
        );
?>