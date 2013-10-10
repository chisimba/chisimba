<?php
// Table Name
$tablename = 'tbl_blog_comments';

//Options line for comments, encoding and character set
$options = array('comment' => 'blog comments', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'commentid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'comment_author' => array(
		'type' => 'text',
		'length' => 50,
		),
	'comment_author_email' => array(
		'type' => 'text',
		'length' => 100,
		),
	'comment_author_url' => array(
		'type' => 'text',
		'length' => 100,
		),
	'comment_author_ip' => array(
		'type' => 'text',
		'length' => 100,
		),
	'comment_date' => array(
		'type' => 'date',
		),
	'comment_date_gmt' => array(
		'type' => 'date',
		),
	'comment_content' => array(
		'type' => 'clob',
		),
	'comment_karma' => array(
		'type' => 'text',
		'length' => 11,
		),
	'comment_approved' => array(
		'type' => 'text',
		'length' => 5,
		),
	'comment_agent' => array(
		'type' => 'text',
		'length' => 255,
		),
	'comment_type' => array(
		'type' => 'text',
		'length' => 255,
		),
	'comment_parent' => array(
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