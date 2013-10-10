<?php
// Table Name
$tablename = 'tbl_wall_posts';

//Options line for comments, encoding and character set
$options = array('comment' => 'Wall posts', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'posterid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'walltype' => array(
		'type' => 'text',
		'length' => 50,
		),
	'identifier' => array(
		'type' => 'text',
		'length' => 50,
		),
	'ownerid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'wallpost' => array(
		'type' => 'clob',
		),
	);

//create other indexes here...

$name = 'ownerid';

$indexes = array(
    'fields' => array(
         'ownerid' => array(),
    )
);
?>