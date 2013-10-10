<?php
// Table Name
$tablename = 'tbl_subs';

//Options line for comments, encoding and character set
$options = array('comment' => 'subscriptions module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'subsid' => array(
		'type' => 'text',
		'length' => 255,
		),
	'bloggerid' => array(
		'type' => 'text',
		'length' => 255,
		),
	'postid' => array(
		'type' => 'text',
		'length' => 255,
		),
	'commentid' => array(
		'type' => 'text',
		'length' => 25,
		),
	'link_category' => array(
		'type' => 'text',
		'length' => 32,
		),
	'link_description' => array(
		'type' => 'text',
		'length' => 255,
		),
	'link_visible' => array(
		'type' => 'text',
		'length' => 5,
		),
	'link_owner' => array(
		'type' => 'text',
		'length' => 25,
		),
	'link_rating' => array(
		'type' => 'integer',
		'length' => 5,
		),
	'link_updated' => array(
		'type' => 'text',
		'length' => 50,
		),
	'link_rel' => array(
		'type' => 'text',
		'length' => 255,
		),
	'link_notes' => array(
		'type' => 'clob',
		),
	'link_type' => array(
		'type' => 'text',
		'length' => 255,
		),
	'link_rss' => array(
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