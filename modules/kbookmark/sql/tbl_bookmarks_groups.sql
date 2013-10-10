<?php
//5ive definition
$tablename = 'tbl_bookmarks_groups';

//Options line for comments, encoding and character set
$options = array('comment' => 'Bookmarks Groups', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'title' => array(
		'type' => 'text',
		'length' => 100
		),
	'description' => array(
		'type' => 'text',
		'length' => 255
		),
	'isprivate' => array(
		'type' => 'text',
		'length' => 1
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'datemodified' => array(
		'type' => 'timestamp'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32
		),
	'isdefault' => array(
		'type' => 'text',
		'length' => 1
		)
	);
?>