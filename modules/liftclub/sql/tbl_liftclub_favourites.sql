<?php
// Table Name
$tablename = 'tbl_liftclub_favourites';

//Options line for comments, encoding and character set
$options = array('comment' => 'lift club favourites', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32,
		),
	'favoureduserid' => array(
		'type' => 'text',
		'length' => 32,
		),
	'datefavoured' => array(
		'type' => 'date',
		)
	);
?>
