<?php

$tablename = 'tbl_edit_lock';

/*
Options line for comments, encoding and character set
*/

$options = array('comment' => 'Table for dbedit_lock class', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'rowid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'lockownerid' => array(
		'type' => 'text',
		'length' => 25,
		'notnull' => 1
		),
	'datelocked' => array(
		'type' => 'timestamp'
		)
	);
?>
