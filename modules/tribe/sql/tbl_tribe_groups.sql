<?php
// Table Name
$tablename = 'tbl_tribe_groups';

//Options line for comments, encoding and character set
$options = array('comment' => 'tribe groups', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
    'groupname' => array(
        'type' => 'text',
        'length' => 100
    ),
    'createdat' => array(
        'type' => 'timestamp',
    ),
    'status' => array(
        'type' => 'text',
        'length' => 1,
    ),
    'privacy' => array(
        'type' => 'text',
        'length' => 30,
    ),

);

?>