<?php

// Table Name
$tablename = 'tbl_blog_trackbacks';

//Options line for comments, encoding and character set
$options = array('comment' => 'blog trackbacks', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'postid' => array(
		'type' => 'text',
		'length' => 100,
		),
	'remhost' => array(
		'type' => 'text',
		'length' => 255,
		),
	'title' => array(
		'type' => 'text',
		'length' => 255
		),
	'excerpt' => array(
		'type' => 'clob',
		),
	'tburl' => array(
		'type' => 'text',
		'length' => 255
		),
	'blog_name' => array(
		'type' => 'text',
		'length' => 255,
		),
	);
//create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                	'id' => array(),
                )
        );
?>