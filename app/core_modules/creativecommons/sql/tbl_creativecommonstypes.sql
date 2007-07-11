<?php

// Table Name
$tablename = 'tbl_creativecommonstypes';

//Options line for comments, encoding and character set
$options = array('comment' => 'Used to hold list of Creative Commons plus other licenses', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'code' => array(
		'type' => 'text',
		'length' => 32,
        'notnull' => TRUE
		),
	'title' => array(
		'type' => 'text',
        'notnull' => TRUE
		),
	'description' => array(
		'type' => 'text',
        'notnull' => TRUE
		),
	'images' => array(
		'type' => 'text'
		),
	'url' => array(
		'type' => 'text',
		'length' => 255
		),
	'updated' => array(
		'type' => 'timestamp'
		)

    );

//create other indexes here...

$name = 'tbl_creativecommonstypes_idx';

$indexes = array(
                'fields' => array(
                	'code' => array()
                )
        );


?>