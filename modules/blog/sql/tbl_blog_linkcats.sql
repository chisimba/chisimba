<?php
// Table Name
$tablename = 'tbl_blog_linkcats';

//Options line for comments, encoding and character set
$options = array('comment' => 'blog link categories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'catname' => array(
		'type' => 'text',
		'length' => 255,
		),
	'autotoggle' => array(
		'type' => 'text',
		'length' => 10,
		),
	'show_images' => array(
		'type' => 'text',
		'length' => 10,
		),
	'show_description' => array(
		'type' => 'text',
		'length' => 10,
		),
	'show_rating' => array(
		'type' => 'text',
		'length' => 10,
		),
	'show_updated' => array(
		'type' => 'text',
		'length' => 10,
		),
	'sort_order' => array(
		'type' => 'text',
		'length' => 64,
		),
	'sort_desc' => array(
		'type' => 'text',
		'length' => 10,
		),
	'list_limit' => array(
		'type' => 'text',
		'length' => 10,
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