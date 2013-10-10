<?php
// Table Name
$tablename = 'tbl_eportfolio_reflection';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner reflection', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'language' => array(
		'type' => 'text',
		'length' => 100,
		),
	'rationale' => array(
		'type' => 'text',
		'length' => 255,
		),
	'creation_date' => array(
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
