<?php
//5ive definition
$tablename = 'tbl_bookmarks_folders';

//Options line for comments, encoding and character set
$options = array('comment' => 'Bookmarks', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32
		),
	'fname' => array(
		'type' => 'text',
		'length' => 100
		),
	'parentid' => array(
		'type' => 'text',
		'length' => 32
		)
	);
?>
