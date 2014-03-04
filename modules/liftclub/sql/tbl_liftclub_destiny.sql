<?php
// Table Name
$tablename = 'tbl_liftclub_destiny';

//Options line for comments, encoding and character set
$options = array('comment' => 'lift club destiny', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'institution' => array(
		'type' => 'text',
		'length' => 255,
		),
	'street' => array(
		'type' => 'text',
		'length' => 255,
		),
	'suburb' => array(
		'type' => 'text',
		'length' => 255,
		),
	'city' => array(
		'type' => 'text',
		'length' => 255,
		),
	'province' => array(
		'type' => 'text',
		'length' => 255,
		),
	'neighbour' => array(
		'type' => 'text',
		'length' => 255,
		)
	);
?>
