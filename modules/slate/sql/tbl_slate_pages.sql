<?php
/**
*
* Create pages for the slate module
*
*/
// Table Name
$tablename = 'tbl_slate_pages';

//Options line for comments, encoding and character set
$options = array('comment' => 'A way of tracking and indexing pages used in the slage module.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'page' => array(
		'type' => 'text',
		'length' => 250,
		),
	'title' => array(
		'type' => 'text',
		'length' => 250,
		),
	'description' => array(
		'type' => 'clob',
		),
	);

//create other indexes here...

$name = 'tbl_slate_pages_idx';

$indexes = array(
    'fields' => array(
         'title' => array(),
    )
);
?>