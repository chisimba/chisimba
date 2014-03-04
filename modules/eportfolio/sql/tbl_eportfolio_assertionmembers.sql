<?php
// Table Name
$tablename = 'tbl_eportfolio_assertionmembers';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner assertion members', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'assertionid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'memberid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'date' => array(
		'type' => 'timestamp',
		)
	);
?>
