<?php
// Table Name
$tablename = 'tbl_perms_right_implied';

//Options line for comments, encoding and character set
$options = array('comment' => 'Implied rights of users', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'right_id' => array(
        'type' => 'text',
        'length' => 50
        
        ),
    'implied_right_id' => array(
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