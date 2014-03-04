<?php
// Table Name
$tablename = 'tbl_photogallery_albums';

//Options line for comments, encoding and character set
$options = array('comment' => 'Photo Gallery Albums', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'user_id' => array(
		'type' => 'text',
		'length' => 50,
		),
	'no_pics' => array(
		'type' => 'integer',
		'length' => 20,
		),
	'no_views' => array(
		'type' => 'integer',
		'length' => 20,
		),
	'is_shared' => array(
		'type' => 'integer',
		'length' => 11,
		),
	'contextcode' => array(
		'type' => 'text',
		'length' => 100,
		),
	'thumbnail' => array(
		'type' => 'text',
		'length' => 255,
		),
	'created_date' => array(
		'type' => 'date',
		),
	'title' => array(
		'type' => 'clob',
		),
	'description' => array(
		'type' => 'clob',
		),
	'position' => array(
		'type' => 'integer',
		'length' => 11,
		),
	);

//create other indexes here...

$name = 'id';

$indexes = array(
                'fields' => array(
                	'id' => array(),
                )
        );
?>