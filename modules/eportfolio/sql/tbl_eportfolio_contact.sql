<?php
// Table Name
$tablename = 'tbl_eportfolio_contact';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner contact information', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'contact_type' => array(
		'type' => 'text',
		'length' => 255,
		),
	'country_code' => array(
		'type' => 'text',
		'length' => 32,
		),
	'area_code' => array(
		'type' => 'text',
		'length' => 32,
		),
	'id_number' => array(
		'type' => 'text',
		'length' => 50,
		)
	);
?>
