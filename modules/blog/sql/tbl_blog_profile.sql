<?php
// Table Name
$tablename = 'tbl_blog_profile';

//Options line for comments, encoding and character set
$options = array('comment' => 'blog profile and name', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'blog_name' => array(
		'type' => 'text',
		'length' => 255,
		),
	'blog_descrip' => array(
		'type' => 'clob',
		),
	'blogger_profile' => array(
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