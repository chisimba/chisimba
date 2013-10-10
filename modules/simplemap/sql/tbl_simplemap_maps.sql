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
$tablename = 'tbl_simplemap_maps';


/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for tbl_simplemap_maps', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'glat' => array(
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
		), 
	'glong' => array(
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
		), 
	'magnify' => array(
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
		),
	'maptype' => array(
		'type' => 'text',
		'length' => 25
		),
	'width' => array(
		'type' => 'text',
		'length' => 10
		),
	'height' => array(
		'type' => 'text',
		'length' => 10
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