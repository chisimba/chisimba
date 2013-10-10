<?php
// Table Name
$tablename = 'tbl_eportfolio_names';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner names', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'suffix' => array(
		'type' => 'text',
		'length' => 255,
		),
	'surname' => array(
		'type' => 'text',
		'length' => 255,
		),
	'othernames' => array(
		'type' => 'text',
		'length' => 255,
		),
	);
?>
