<?php
/*
Set the table name
*/
$tablename = 'tbl_travel_countrycodes';


/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for country codes', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'code' => array(
		'type' => 'text',
		'length' => 2
		), 
	'name' => array(
		'type' => 'text',
		'length' => 64
		)
	);
	
$name = "tbl_countrycodes_idx";

?>