<?php
// Table Name
$tablename = 'tbl_eportfolio_product';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner product', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50
		),
	'type' => array(
		'type' => 'text',
		'length' => 255
		),
	'comment' => array(
		'type' => 'text',
		'length' => 255
		),
	'referential_source' => array(
		'type' => 'text',
		'length' => 255
		),
	'referential_id' => array(
		'type' => 'text',
		'length' => 255
		),
	'assertion_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'assignment_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'essay_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'creation_date' => array(
		'type' => 'timestamp'
		),
	'shortdescription' => array(
		'type' => 'text',
		'length' => 255
		),
	'longdescription' => array(
		'type' => 'text'		
		)
	);
?>
