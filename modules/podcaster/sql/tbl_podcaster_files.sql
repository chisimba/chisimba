<?php
// name of the table
$tablename = 'tbl_podcaster_files';

// Options line for comments, encoding and character set
$options = array('comment' => 'records details of the uploaded file', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32, 
        ),
	'processstage' => array(
        'type' => 'text',
        'length' => 32,
        'default' => 'uploadedraw'
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

$name = 'podcaster_files_index';

$indexes = array(
    'fields' => array(
        'id' => array(),
        'creatorid' => array(),
        ),
    );
?>
