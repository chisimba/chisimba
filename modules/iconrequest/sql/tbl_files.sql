<?php
//5ive definition
$tablename = 'tbl_files';

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
		'notnull' => TRUE,
		'default' => '',
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => TRUE,
		'default' => '',
		),
	'filename' => array(
		'type' => 'text',
		'length' => 120,
		'notnull' => TRUE,
		'default' => '',
		),
	'size' => array(
		'type' => 'integer',
		'length' => 11,
		'notnull' => TRUE,
		'default' => '',
		),
	'updated' => array(
		'type' => 'timestamp',
		'notnull' => TRUE,
		'default' => '0000-00-00 00:00:00'
		)
	);
?>
