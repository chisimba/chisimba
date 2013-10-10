<?php
//5ive definition
$tablename = 'tbl_request';

//Options line for comments, encoding and character set
$options = array('comment' => 'IconRequest', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => TRUE,
		'default' => '',
		),
	'reqid' => array(
		'type' => 'text',
		'length' => 32,
		'default' => '',
		),
	'modname' => array(
		'type' => 'text',
		'length' => 25,
		'default' => '',
		),
	'priority' => array(
		'type' => 'text',
		'length' => 1,
		'default' => '',
		),
	'type' => array(
		'type' => 'text',
		'length' => 1,
		'default' => '',
		),
	'phptype'  => array(
		'type' => 'text',
		'length' => 1,
		'default' => '',
		),
	'iconname' => array(
		'type' => 'text',
		'length' => 25,
		'default' => '',
		),
	'description' => array(
		'type' => 'text',
		'length' => 255,
		'default' => '',
		),
	'uri1' => array(
		'type' => 'text',
		'length' => 32,
		'default' => '',
		),
	'uri2' => array(
		'type' => 'text',
		'length' => 32,
		'default' => '',
		),
	'complete' => array(
		'type' => 'integer',
		'length' => 11,
		'default' => '0',
		),
	'uploaded' => array(
		'type' => 'text',
		'length' => 32,
		'default' => '',
		),
	'time' => array(
		'type' => 'timestamp',
		)
	);
?>
