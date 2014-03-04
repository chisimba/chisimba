<?php
// Table Name
$tablename = 'tbl_photogallery_images';

//Options line for comments, encoding and character set
$options = array('comment' => 'Photo Gallery Images', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),	
	'album_id' => array(
		'type' => 'text',
		'length' => 32
		),	
	'file_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'description' => array(
		'type' => 'clob',		
		),	
	'no_views' => array(
		'type' => 'integer',
		'length' => 20,
		),
	'is_shared' => array(
		'type' => 'integer',
		'length' => 11,
		),	
	'title' => array(
		'type' => 'clob',
		),
	'position' => array(
		'type' => 'integer',
		'length' => 11,
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