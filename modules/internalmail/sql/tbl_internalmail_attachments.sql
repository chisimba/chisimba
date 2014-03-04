<?php
//5ive definition
$tablename = 'tbl_internalmail_attachments';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing email attachments.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'email_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'file_name' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'stored_name' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'file_type' => array(
        'type' => 'text',
        'length' => 100,
        ),
    'file_size' => array(
        'type' => 'integer',
        'length' => 11,
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'email_attachments_index';

$indexes = array(
                'fields' => array(
                    'email_id' => array(),
                ),
        );
?>