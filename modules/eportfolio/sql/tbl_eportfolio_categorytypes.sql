<?php
// Table Name
$tablename = 'tbl_eportfolio_categorytypes';

//Options line for comments, encoding and character set
$options = array('comment' => 'eportfolio category types', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'categoryid' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'type' => array(
		'type' => 'text',
		'length' => 100,
		)
	);
?>
