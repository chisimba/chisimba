<?php
// Table Name
$tablename = 'tbl_perms_grouprights';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of group rights within an area', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'group_id' => array(
        'type' => 'text',
        'length' => 50
        
        ),
    'right_id' => array(
        'type' => 'text',
        'length' => 50
        
        ),
    'right_level' => array(
        'type' => 'text',
        'length' => 50
        
        ),
);

//create other indexes here...

$name = 'rid';

$indexes = array(
    'fields' => array(
        'right_id' => array()
    )
);
?>