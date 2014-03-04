<?php
// Table Name
$tablename = 'tbl_eportfolio_affiliation';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner affiliation', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'type' => array(
		'type' => 'text',
		'length' => 255,
		),
	'classification' => array(
		'type' => 'text',
		'length' => 255,
		),
	'role' => array(
		'type' => 'text',
		'length' => 255,
		),
	'organisation' => array(
		'type' => 'text',
		'length' => 255,
		),
	'start' => array(
		'type' => 'date',
		),
	'finish' => array(
		'type' => 'date',
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
