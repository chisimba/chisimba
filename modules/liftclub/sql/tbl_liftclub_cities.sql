<?php
// Table Name
$tablename = 'tbl_liftclub_cities';

//Options line for comments, encoding and character set
$options = array('comment' => 'lift club cities', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'city' => array(
		'type' => 'text',
		'length' => 150,
		)
	);
?>
