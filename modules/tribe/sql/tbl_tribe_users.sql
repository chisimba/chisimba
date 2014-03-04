<?php
// Table Name
$tablename = 'tbl_tribe_users';

//Options line for comments, encoding and character set
$options = array('comment' => 'table to map jids to userids', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
    ),
    'userid' => array(
        'type' => 'text',
        'length' => 32
    ),
    'jid' => array(
        'type' => 'text',
        'length' => 255,
    ),
    'status' => array(
        'type' => 'text',
        'length' => 1,
    ),
);

?>