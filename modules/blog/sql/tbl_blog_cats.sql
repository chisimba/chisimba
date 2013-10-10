<?php
// Table Name
$tablename = 'tbl_blog_cats';

//Options line for comments, encoding and character set
$options = array('comment' => 'blog categories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'cat_name' => array(
		'type' => 'text',
		'length' => 255,
		),
	'cat_nicename' => array(
		'type' => 'text',
		'length' => 255,
		),
	'cat_desc' => array(
		'type' => 'clob',
		),
	'cat_parent' => array(
		'type' => 'text',
		'length' => 255,
		),
	'cat_count' => array(
		'type' => 'text',
		'length' => 255,
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