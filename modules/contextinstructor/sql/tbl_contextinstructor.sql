<?php
// Table Name
$tablename = 'tbl_contextinstructor';

//Options line for comments, encoding and character set
$options = array('comment' => 'holds context instructor info', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32,
		),
	'contextcode' => array(
		'type' => 'text',
		'length' => 32,
		)
	);
?>