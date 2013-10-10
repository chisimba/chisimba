<?php
// Table Name
$tablename = 'tbl_podcaster_category';

//Options line for comments, encoding and character set
$options = array('comment' => 'table for event categories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'category' => array(
		'type' => 'text',
		'length' => 100,
		),
	'description' => array(
		'type' => 'text',
		'length' => 250,
		)
	);
?>
