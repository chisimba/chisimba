<?php
//5ive definition
$tablename = 'tbl_internalmail_addressbook_entries';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing addressbook entries', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'addressbook_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'recipient_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
$name = 'email_addressbook_entries_index';

$indexes = array(
                'fields' => array(
                    'addressbook_id' => array(),
                    'recipient_id' => array(),
                ),
        );
?>