<?php

$tablename = 'tbl_liftclub_provinces';

// Options line for comments, encoding and character set
$options = array('comment' => 'tbl_liftclub_provinces', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'province' => array(
		'type' => 'text',
		'length' => 150,
		)
	);
?>
