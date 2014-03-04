<?php
// Table Name
$tablename = 'tbl_blogcomments';

//Options line for comments, encoding and character set
$options = array('comment' => 'blog style commenting system', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
		'length' => 150,
		),
	'comment_author_email' => array(
		'type' => 'text',
		'length' => 100,
		),
	'comment_author_url' => array(
		'type' => 'text',
		'length' => 255,
		),
	'comment_author_ip' => array(
		'type' => 'text',
		'length' => 100,
		),
	'comment_date' => array(
		'type' => 'integer',
		'length' => 8,
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
	'comment_parentid' => array(
		'type' => 'text',
		'length' => 255,
		),
	'comment_parentmod' => array(
		'type' => 'text',
		'length' => 255,
		),
	'comment_parenttbl' => array(
		'type' => 'text',
		'length' => 255,
		),
	);

//create other indexes here...

$name = 'userid';

$indexes = array(
                'fields' => array(
                	'userid' => array(),
                	'comment_parenttbl' => array(),
                	'comment_parentid' => array(),
                	'id' => array('primary' => true),
                )
        );
?>