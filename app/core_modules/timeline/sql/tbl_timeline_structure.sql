<?php

/**
*
* Timeline structure table, for holding timeline structures for 
* display.
*
*/


/*
Set the table name
*/
$tablename = 'tbl_timeline_structure';


/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for tbl_timeline_structure', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'title' => array(
		'type' => 'clob',
		), 
	'description' => array(
		'type' => 'clob'
		),
	'url' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
		),
	'focusdate' => array(
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
		), 
	'intervalpixels' => array(
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
		), 
	'intervalunit' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'tlheight' => array(
		'type' => 'text',
		'length' => 25,
		'notnull' => 1,
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
?>