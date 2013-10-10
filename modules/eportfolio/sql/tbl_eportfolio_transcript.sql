<?php
// Table Name
$tablename = 'tbl_eportfolio_transcript';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner transcripts', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'shortdescription' => array(
		'type' => 'text',
		),
	'longdescription' => array(
		'type' => 'text',
		)
	);
?>
