<?php
//5ive definition
$tablename = 'tbl_internalmail_routing';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing email routing.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'email_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'sender_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'recipient_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'folder_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'sent_email' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'read_email' => array(
        'type' => 'integer',
        'length' => 1,
        ),
    'date_read' => array(
        'type' => 'timestamp',
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'email_routing_index';

$indexes = array(
                'fields' => array(
                    'email_id' => array(),
                    'sender_id' => array(),
                    'recipient_id' => array(),
                    'folder_id' => array(),
                ),
        );
?>