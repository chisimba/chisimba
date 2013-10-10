<?php
// Table Name
$tablename = 'tbl_eportfolio_email';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner email information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'email' => array(
		'type' => 'text',
		'length' => 100,
		)
	);
?>
