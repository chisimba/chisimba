<?php
//5ive definition
$tablename = 'tbl_internalmail';

//Options line for comments, encoding and character set
$options = array('comment' => 'Main email table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'sender_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'recipient_list' => array(
        'type' => 'clob'
        ),
    'subject' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'message' => array(
        'type' => 'clob',
        ),
    'date_sent' => array(
        'type' => 'timestamp',
        ),
    'attachments' => array(
        'type' => 'integer',
        'length' => 2,
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'email_index';

$indexes = array(
                'fields' => array(
                    'sender_id' => array(),
                ),
        );
?>