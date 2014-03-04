<?php
//5ive definition
$tablename = 'tbl_messaging_messages';

//Options line for comments, encoding and character set
$options = array('comment' => 'The main messaging table, holds all message details', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'sender_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'message_type' => array( //chat message or instant message
        'type' => 'integer',
        'length' => 1,
        ),
    'message' => array(
        'type' => 'clob',
        ),
    'recipient_id' => array( //chat room id or message recipient id (user id)
        'type' => 'text',
        'length' => 32,
        ),
    'delivery_state' => array(
        'type' => 'integer',
        'length' => 1,
        ),    
    'message_counter' => array(
        'type' => 'integer',
        'length' => 7,
        ),    
    'date_created' => array(
        'type' => 'timestamp',
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'tbl_messaging_messages_index';

$indexes = array(
                'fields' => array(
                    'sender_id' => array(),
                    'recipient_id' => array(),
                )
        );
?>