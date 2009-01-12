<?php
// Table Name
$tablename = 'tbl_perms_rights';

//Options line for comments, encoding and character set
$options = array('comment' => 'User rights within application and areas', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
    ),
    'area_id' => array(
        'type' => 'integer',
        'length' => 10,
    ),
    'right_id' => array(
        'type' => 'text',
        'length' => 50,
        
    ),
    'right_define_name' => array(
        'type' => 'text',
        'length' => 32,
    ),
    'has_implied' => array(
        'type' => 'text',
        'length' => 50,
    ),
);

//create other indexes here...

$name = 'rightid';

$indexes = array(
    'fields' => array(
        'right_id' => array()
    )
);
?>