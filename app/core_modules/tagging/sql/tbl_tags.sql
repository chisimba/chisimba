<?php
// Table Name
$tablename = 'tbl_tags';

//Options line for comments, encoding and character set
$options = array('comment' => 'tag metadata', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'item_id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'meta_key' => array(
		'type' => 'text',
		'length' => 255,
		),
	'meta_value' => array(
		'type' => 'clob',
		),
	'module' => array(
		'type' => 'text',
		'length' => 255,
		),
	'uri' => array(
		'type' => 'text',
		'length' => 255,
		),
    'context' => array(
		'type' => 'text',
		'length' => 255,
		),
    'searchkey' => array(
		'type' => 'text',
		'length' => 255,
		),
	);

//create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                    'userid' => array(),
                    'meta_value' => array(),
                    'module' => array(),
                    'context' => array(),
                    'searchkey' => array(),
                )
        );
?>