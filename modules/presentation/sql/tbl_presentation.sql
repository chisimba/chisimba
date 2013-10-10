<?php

/**
*
* Table for holding S5 presentation text for display in the
* presentation module.
*
*/


/*
Set the table name
*/
$tablename = 'tbl_presentation';


/*
Options line for comments, encoding and character set
*/
$options = array('comment' => 'Table for S5 presentations', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'slides' => array(
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