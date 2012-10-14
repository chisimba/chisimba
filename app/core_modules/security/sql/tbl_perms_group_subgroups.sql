<?php
// Table Name
$tablename = 'tbl_perms_group_subgroups';

//Options line for comments, encoding and character set
$options = array('comment' => 'subgroups control', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'group_id' => array(
        'type' => 'integer',
        'default' => 0,
    ),
    'subgroup_id' => array(
        'type' => 'integer',
        'default' => 0,
    ),
);

//create other indexes here...

$name = 'aid';

$indexes = array(
    'fields' => array(
        'group_id' => array('sorting' => 'ascending'),
        'subgroup_id' => array('sorting' => 'ascending'),
    )
);
?>