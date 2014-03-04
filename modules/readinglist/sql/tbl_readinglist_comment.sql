<?php

$tablename = 'tbl_readinglist_comment';

/*
Options line for comments, encoding and character set
*/
$options = array('comment' => '', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

/*Fields
*/
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'itemid' => array(
		'type' => 'text',
		'length' => 32
		),	
	'comment' => array(
		'type' => 'clob',
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32
		),	
	'updated' => array(
		'type' => 'timestamp',
		),
	);
?>