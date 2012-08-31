<?php
// Table Name
$tablename = 'tbl_module_patches';

//Options line for comments, encoding and character set
$options = array('comment' => 'table of patches applied to modules','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'moduleid' => array(
        'type' => 'text',
        'length' => 50,
        'notnull' => TRUE,
        'default' => '0'
        ),
    'version' => array(
        'type' => 'text',
        'length' => 32
        ),
    'tablename' => array(
        'type' => 'text',
        'length' => 32
        ),
    'patchdata' => array(
        'type' => 'text',
        'length' => 255
        ),
    'applied' => array(
        'type' => 'date'
        )
    );

//create other indexes here...

?>