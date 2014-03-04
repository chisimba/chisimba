<?php
// Table Name
$tablename = 'tbl_blog_postmeta';

//Options line for comments, encoding and character set
$options = array('comment' => 'blog post metadata', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'post_id' => array(
		'type' => 'text',
		'length' => 32,
		),
	'meta_key' => array(
		'type' => 'text',
		'length' => 255,
		),
	'meta_value' => array(
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
