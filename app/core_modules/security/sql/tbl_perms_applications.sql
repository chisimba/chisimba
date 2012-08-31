<?php
// Table Name
$tablename = 'tbl_perms_applications';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of applications (modules) that may have access control', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'application_id' => array(
        'type' => 'text',
        'length' => 50
        
        ),
    'application_define_name' => array(
        'type' => 'text',
        'length' => 100,
    ),
);

//create other indexes here...

$name = 'appid';

$indexes = array(
    'fields' => array(
        'application_id' => array()
    )
);
?>