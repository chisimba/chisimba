<?php
/*
Set the table name
*/
$tablename = "tbl_travel_hotel_descriptions";


/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for hotel descriptions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'integer',
		'length' => 10,
		'notnull' => 1
		), 
	'marketinglevel' => array(
		'type' => 'integer',
		'length' => 5
		),
	'description' => array(
		'type' => 'clob'
		),
	'gdschaincode' => array(
		'type' => 'text',
		'length' => 5,
		), 
	'gdschaincodename' => array(
		'type' => 'text',
		'length' => 64
		), 
	'created' => array(
		'type' => 'timestamp',
		'notnull' => 1,
		),
	'modified' => array(
		'type' => 'timestamp'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32
		)
);

$name = "tbl_travel_hotel_descriptions_idx";

?>