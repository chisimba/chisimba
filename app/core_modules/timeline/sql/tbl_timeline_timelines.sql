<?php

/**
*
* Timeline timelines table, for holding timeline timelines for 
* display.
*
*/


/*
Set the table name
*/
$tablename = 'tbl_timeline_timelines';


/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for tbl_timeline_timelines', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'timelineid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'start' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'end' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'image' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
		),
	'url' => array(
		'type' => 'text',
		'length' => 255,
		'notnull' => 1
		),
	'title' => array(
		'type' => 'clob',
		), 
	'timelinetext' => array(
		'type' => 'clob'
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