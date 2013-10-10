<?php
// name of the table
$tablename = 'tbl_podcaster_tags';

// Options line for comments, encoding and character set
$options = array('comment' => 'list of file uploaded', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
    'id' => array(
        'type' => 'text',
        'length' => 32,
		'notnull' => TRUE,
        ),
	'fileid' => array(
        'type' => 'text',
        'length' => 32,
		'notnull' => TRUE,
        ),
	'tag' => array(
        'type' => 'text',
		'notnull' => TRUE,
        ),
    );

// create other indexes here

$name = 'podcaster_tags_index';

$indexes = array(
    'fields' => array(
        'fileid' => array(),
        ),
    );
?>
