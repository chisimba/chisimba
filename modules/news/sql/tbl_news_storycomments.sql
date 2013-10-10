<?php
// Table Name
$tablename = 'tbl_news_storycomments';

//Options line for comments, encoding and character set
$options = array('comment' => 'The table contains the comments by users on the stories', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
    ),
	'storyid' => array (
		'type' => 'text',
		'length' =>32,
		'notnull' => 1
	),
	'fullname' => array (
		'type' => 'text',
		'length' => 255
	),
	'emailaddress' => array (
		'type' => 'text',
		'length' => 255
	),
	'comment' => array (
		'type' => 'text'
	),
	'commentdate' => array (
		'type' => 'date',
	),
	'ipaddress' => array (
		'type' => 'text',
		'length' => 25,
	),
	'commentmoderated' => array (
		'type' => 'text',
		'length' => 1,
		'default' => 'Y'
	),
	'creatorid' => array (
		'type' => 'text',
		'length' => 25,
	),
	'datecreated' => array (
		'type' => 'timestamp',
		'notnull' => 1
	),
);
//create other indexes here...
//create other indexes here...
$name = 'tbl_news_storycomments_idx';

$indexes = array(
                'fields' => array(
                	'storyid' => array(),
                	'creatorid' => array(),
                	'emailaddress' => array(),
                	'commentmoderated' => array(),
                	'fullname' => array(),
                	'datecreated' => array(),
                )
        );
		



?>