<?php
// Table Name
$tablename = 'tbl_tribe_groupmembers';

//Options line for comments, encoding and character set
$options = array('comment' => 'tribe group members', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'groupid' => array(
        'type' => 'text',
        'length' => 100
    ),
);

?>