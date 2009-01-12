<?php
// Table Name
$tablename = 'tbl_perms_perm_users';

//Options line for comments, encoding and character set
$options = array('comment' => 'User management wrt permissions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'perm_user_id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'auth_user_id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'auth_container_name' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'perm_type' => array(
        'type' => 'text',
        'length' => 32
        
        ),
);

//create other indexes here...

$name = 'pud';

$indexes = array(
    'fields' => array(
        'perm_user_id' => array()
    )
);
?>