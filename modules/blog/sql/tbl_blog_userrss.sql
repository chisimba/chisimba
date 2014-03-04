<?php
// Table Name
$tablename = 'tbl_blog_userrss';

//Options line for comments, encoding and character set
$options = array('comment' => 'blog rss feeds', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'url' => array(
		'type' => 'text',
		'length' => 255,
		),
	'name' => array(
		'type' => 'text',
		'length' => 255,
		),
	'description' => array(
		'type' => 'clob',
		),
	'rsscache' => array(
		'type' => 'clob',
		),
	'rsstime' => array(
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