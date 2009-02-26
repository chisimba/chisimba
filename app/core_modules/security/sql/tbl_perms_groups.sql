<?php
// Table Name
$tablename = 'tbl_perms_groups';

//Options line for comments, encoding and character set
$options = array('comment' => 'Groups', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'group_id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'group_type' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'group_define_name' => array(
        'type' => 'text',
        'length' => 255
        
        ),
);

//create other indexes here...

$name = 'grpid';

$indexes = array(
    'fields' => array(
        'group_id' => array()
    )
);
?>
