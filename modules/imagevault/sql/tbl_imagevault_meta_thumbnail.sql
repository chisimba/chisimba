<?php
// Table Name
$tablename = 'tbl_imagevault_meta_thumbnail';

//Options line for comments, encoding and character set
$options = array('comment' => 'Imagevault metadata thumbnail section', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'imageid' => array(
		'type' => 'text',
		'length' => 32
		),	
    'userid' => array(
		'type' => 'text',
		'length' => 50,
		),
    'compression' => array(
		'type' => 'text',
		'length' => 100,
		),
    'xresolution' => array(
		'type' => 'text',
		'length' => 100,
		),
	'yresolution' => array(
		'type' => 'text',
		'length' => 100,
		),
	'resolutionunit' => array(
		'type' => 'text',
		'length' => 100,
		),
	'jpeginterchangeformat' => array(
		'type' => 'text',
		'length' => 100,
		),
	'jpeginterchangeformatlength' => array(
		'type' => 'text',
		'length' => 100,
		),
    'ycbcrpositioning' => array(
		'type' => 'text',
		'length' => 100,
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
