<?php
//5ive definition
$tablename = 'tbl_messaging_users';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of users in a private chat room', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'room_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'user_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'creator_id' => array(
        'type' => 'text',
        'length' => 32,
        ),  
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'tbl_messaging_users_index';

$indexes = array(
                'fields' => array(
                    'room_id' => array(),
                    'user_id' => array(),
                    'creator_id' => array(),
                )
        );
?>