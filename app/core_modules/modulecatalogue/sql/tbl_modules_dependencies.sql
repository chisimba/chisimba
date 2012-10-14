<?php
// Table Name
$tablename = 'tbl_modules_dependencies';

//Options line for comments, encoding and character set
$options = array('comment' => 'module dependencies','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32
        ),
    'module_id' => array(
        'type' => 'text',
        'length' => 50
        ),
    'dependency' => array(
        'type' => 'text',
        'length' => 50
        )
    );

//create other indexes here...

$name = 'modules_dependencies';

$indexes = array(
                'fields' => array(
                    'dependency' => array()
                )
        );
?>