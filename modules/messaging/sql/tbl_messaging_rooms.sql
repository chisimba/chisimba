<?php
//5ive definition
$tablename = 'tbl_messaging_rooms';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table to hold chat room details', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'room_type' => array( // public, private, context, workgroup or instant messaging
        'type' => 'integer',
        'length' => 1,
        ),
    'room_name' => array( // context chat room, workgroup chat room or private chat room
        'type' => 'text',
        'length' => 255,
        ),
    'room_desc' => array(
        'type' => 'clob',
        ),
    'text_only' => array( // text only allowed in chat messages
        'type' => 'integer',
        'length' => 1,
        ),
    'disabled' => array(
        'type' => 'integer',
        'length' => 1,
        ),   
    'owner_id' => array( // context id, workgroup creator id or user id
        'type' => 'text',
        'length' => 255,
        ),
    'date_created' => array(
        'type' => 'timestamp',
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'tbl_messaging_rooms_index';

$indexes = array(
                'fields' => array(
                    'owner_id' => array(),
                )
        );
?>