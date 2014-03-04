<?php
// Table Name
$tablename = 'tbl_wall_comments';

//Options line for comments, encoding and character set
$options = array('comment' => 'Comments on wall posts', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'parentid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'posterid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'wallcomment' => array(
		'type' => 'text',
		'length' => 255,
		),
	);

//create other indexes here...

$name = 'parentid';

$indexes = array(
    'fields' => array(
         'parentid' => array(),
    )
);
?>