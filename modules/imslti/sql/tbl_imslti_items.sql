<?php
//Table Name
$tablename = 'tbl_imslti_items';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table for holding IMS LTI items', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1,
		'default' => '',
		),
	'name' => array(
		'type' => 'text',
		'length' => 150,
		),
	'description' => array(
		'type' => 'clob',
		),
	'context' => array(
		'type' => 'text',
		'length' => 255
		),
	'resturl' => array(
		'type' => 'text',
		'length' => 255
		),
	'secret' => array(
		'type' => 'text',
		'length' => 255
		),
	'updated' => array(
		'type' => 'timestamp',
		'length' => 14,
		'notnull' => 1,
		)
	);
?>