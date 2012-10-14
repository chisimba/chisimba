<?php
// Table Name
$tablename = 'tbl_perms_area_admin_areas';

//Options line for comments, encoding and character set
$options = array('comment' => 'Area admin control', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'area_id' => array(
        'type' => 'integer',
        'default' => 0,
    ),

    'perm_user_id' => array(
        'type' => 'integer',
        'default' => 0,
    ),
);

//create other indexes here...

$name = 'aid';

$indexes = array(
    'fields' => array(
        'area_id' => array('sorting' => 'ascending'),
        'perm_user_id' => array('sorting' => 'ascending'),
    )
);
?>