<?php
// Table Name
$tablename = 'tbl_photogallery_comments';

//Options line for comments, encoding and character set
$options = array('comment' => 'Photo Gallery Comments', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		),	
	'file_id' => array(
		'type' => 'text',
		'length' => 32,
		),		
	'user_id' => array(
		'type' => 'text',
		'length' => 50,
		),
	'name' => array(
		'type' => 'clob',
		),
	'website' => array(
		'type' => 'clob',
		),
	'email' => array(
		'type' => 'clob',
		),
	'commentdate' => array(
		'type' => 'text',
		'length' => 100,
		),
	'comment' => array(
		'type' => 'clob',
		),
	);

//create other indexes here...

$name = 'file_id';

$indexes = array(
                'fields' => array(
                	'file_id' => array(),
                )
        );
?>