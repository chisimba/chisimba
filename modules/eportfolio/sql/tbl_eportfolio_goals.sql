<?php
// Table Name
$tablename = 'tbl_eportfolio_goals';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner goals', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'parentid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'type' => array(
		'type' => 'text',
		'length' => 50,
		),
	'start' => array(
		'type' => 'timestamp',
		),
	'priority' => array(
		'type' => 'text',
		'length' => 50,
		),
	'status' => array(
		'type' => 'text',
		'length' => 50,
		),
	'status_date' => array(
		'type' => 'timestamp',
		),
	'shortdescription' => array(
		'type' => 'text',
		'length' => 255,
		),
	'longdescription' => array(
		'type' => 'text',
		)
	);
?>
