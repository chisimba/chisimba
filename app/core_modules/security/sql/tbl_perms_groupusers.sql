<?php
// Table Name
$tablename = 'tbl_perms_groupusers';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of users within a group', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'perm_user_id' => array(
        'type' => 'text',
        'length' => 50
        
        ),
    'group_id' => array(
        'type' => 'text',
        'length' => 50
        
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