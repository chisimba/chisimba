<?php
//5ive definition
$tablename = 'tbl_generator_testdata';

//Options line for comments, encoding and character set
$options = array('comment' => 'A test table for use with generator to test if its working.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
	),
	'creatorId' => array(
		'type' => 'text',
		'length' => 25
	),
	'dateCreated' => array(
		'type' => 'timestamp'
	),
	'modifierId' => array(
		'type' => 'text',
		'length' => 26
	),	
	'dateModified' => array(
		'type' =>  'timestamp'
	),	
	'issmart' => array(
		'type' => 'text',
		'length' => 1
	),
	'stupidstuff' => array(
		'type' => 'text',
		'length' => 250
	),
	'description' => array(
		'type' => 'text',
		'length' => 250
	),
	'updated' => array(
		'type' => 'timestamp'
	)
);
?>