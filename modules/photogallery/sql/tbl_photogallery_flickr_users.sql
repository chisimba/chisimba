<?php
// Table Name
$tablename = 'tbl_photogallery_flickr_users';

//Options line for comments, encoding and character set
$options = array('comment' => 'Photo Gallery Flickr Users', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),	
	'userid' => array(
		'type' => 'text',
		'length' => 32
		),	
	'flickr_username' => array(
		'type' => 'text',
		'length' => 55
		),	
	'flickr_password' => array(
		'type' => 'text',
		'length' => 20,
		),
	'isreal' => array(
		'type' => 'integer',
		'length' => 11,
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