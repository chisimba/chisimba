<?php
// name of the table
$tablename = 'tbl_webpresent_files';

// Options line for comments, encoding and character set
$options = array('comment' => 'list of file uploaded', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32, 
        ),
	'processstage' => array(
        'type' => 'text',
        'length' => 32,
        ),
	'inprocess' => array(
        'type' => 'text',
        'length' => 1,
		'default' => 'N'
        ),
	'filename' => array(
        'type' => 'text',
        'length' => 255,
        ),
	'mimetype' => array(
        'type' => 'text',
        'length' => 255,
        ),
    'title' => array(
        'type' => 'text',
        ),
    'description' => array(
        'type' => 'text',
        ),
	'filetype' => array(
        'type' => 'text',
        'length' => 32, 
        ),
	'cclicense' => array(
        'type' => 'text',
        'length' => 32, 
        ),
    'creatorid' => array(
        'type' => 'text',
        'length' => 25,
        ),
    'dateuploaded' => array(
        'type' => 'timestamp',
        ),
    );

// create other indexes here

$name = 'webpresent_files_index';

$indexes = array(
    'fields' => array(
        'id' => array(),
        'creatorid' => array(),
        ),
    );
?>
