<?php
// Table Name
$tablename = 'tbl_modules_owned_tables';

//Options line for comments, encoding and character set
$options = array('comment' => 'table of owned modules','collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'kng_module' => array(
		'type' => 'text',
		'length' => 50,
        'notnull' => TRUE,
        'default' => '0'
		),
    'tablename' => array(
		'type' => 'text',
        'length' => 150,

		)
    );

//create other indexes here...

$name = 'module_tables';

$indexes = array(
                'fields' => array(
                	'kng_module' => array()
                )
        );
?>