<?php
// Table Name
$tablename = 'tbl_blog_lists';

//Options line for comments, encoding and character set
$options = array('comment' => 'blog lists', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'list_identifier' => array(
		'type' => 'text',
		'length' => 255,
		),
	'listuser' => array(
		'type' => 'text',
		'length' => 32,
		),
	);

//create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                	'userid' => array(),
                )
        );
?>