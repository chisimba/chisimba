<?php
// Table Name
$tablename = 'tbl_eportfolio_demographics';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner Demographic information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
		'length' => 50,
		),
	'birth' => array(
		'type' => 'date',
		),
	'nationality' => array(
		'type' => 'text',
		'length' => 60,
		)
	);
?>
