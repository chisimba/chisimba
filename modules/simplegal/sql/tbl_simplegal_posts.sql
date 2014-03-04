<?php
// Table Name
$tablename = 'tbl_simplegal_posts';

//Options line for comments, encoding and character set
$options = array('comment' => 'simplegal metaweblog post table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'post_date' => array(
		'type' => 'timestamp',
		),
	'post_content' => array(
		'type' => 'clob',
		),
	'post_title' => array(
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
