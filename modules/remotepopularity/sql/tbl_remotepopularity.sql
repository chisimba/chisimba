<?php
// Table Name
$tablename = 'tbl_remotepopularity';

//Options line for comments, encoding and character set
$options = array('comment' => 'remote log table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'ip' => array(
		'type' => 'clob',
		),
	'module_name' => array(
		'type' => 'text',
		'length' => 255,
		),
	'downloadedon' => array(
		'type' => 'timestamp',
		),
	);

?>