<?php
// Table Name
$tablename = 'tbl_eportfolio_qcl';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio owner qualification', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'qcl_type' => array(
		'type' => 'text',
		'length' => 255,
		),
	'qcl_title' => array(
		'type' => 'text',
		'length' => 255,
		),
	'organisation' => array(
		'type' => 'text',
		'length' => 255,
		),
	'qcl_level' => array(
		'type' => 'text',
		'length' => 255,
		),
	'award_date' => array(
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
