<?php
// Table Name
$tablename = 'tbl_blog_pages';

//Options line for comments, encoding and character set
$options = array('comment' => 'blog pages', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'page_name' => array(
		'type' => 'text',
		'length' => 255,
		),
	'page_content' => array(
		'type' => 'clob',
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