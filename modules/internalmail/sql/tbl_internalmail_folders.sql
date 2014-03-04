<?php
//5ive definition
$tablename = 'tbl_internalmail_folders';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the users email folders.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'user_id' => array(
        'type' => 'text',
        'length' => 32,
        ),
    'folder_name' => array(
        'type' => 'text',
        'length' => 50,
        ),
    'updated' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here...
?>