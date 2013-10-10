<?php
// Table Name
$tablename = 'tbl_eportfolio_activity';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner activity information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'contextid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'type' => array(
		'type' => 'text',
		'length' => 50,
		),
	'start' => array(
		'type' => 'date',
		),
	'finish' => array(
		'type' => 'date',
		),
	'shortdescription' => array(
		'type' => 'text',
		),
	'longdescription' => array(
		'type' => 'text',
		)
	);
?>
