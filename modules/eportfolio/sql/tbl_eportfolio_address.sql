<?php
// Table Name
$tablename = 'tbl_eportfolio_address';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner address', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'street_no' => array(
		'type' => 'text',
		'length' => 255,
		),
	'street_name' => array(
		'type' => 'text',
		'length' => 255,
		),
	'locality' => array(
		'type' => 'text',
		'length' => 255,
		),
	'city' => array(
		'type' => 'text',
		'length' => 255,
		),
	'postcode' => array(
		'type' => 'text',
		'length' => 255,
		),
	'postal_address' => array(
		'type' => 'text',
		'length' => 255,
		)
	);
?>
