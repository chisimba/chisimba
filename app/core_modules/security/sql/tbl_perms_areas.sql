<?php
// Table Name
$tablename = 'tbl_perms_areas';

//Options line for comments, encoding and character set
$options = array('comment' => 'Areas that are defined against applications', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        
        ),
    'application_id' => array(
        'type' => 'text',
        'length' => 32,
    ),

    'area_id' => array(
        'type' => 'integer',
        'length' => 10,
    ),

    'area_define_name' => array(
        'type' => 'text',
        'length' => 50,
    ),
);

//create other indexes here...

$name = 'aid';

$indexes = array(
    'fields' => array(
        'area_id' => array()
    )
);
?>