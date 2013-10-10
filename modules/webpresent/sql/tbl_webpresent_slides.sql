<?php
// name of the table
$tablename = 'tbl_webpresent_slides';

// Options line for comments, encoding and character set
$options = array('comment' => 'list of slides within a presentation', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'slidetitle' => array(
        'type' => 'text',
        'length' => 255,
		'notnull' => TRUE,
        ),
	'slideorder' => array(
        'type' => 'integer',
		'notnull' => TRUE,
        ),
	'slidecontent' => array(
        'type' => 'text'
        ),
    );

// create other indexes here

$name = 'webpresent_slides_index';

$indexes = array(
    'fields' => array(
        'fileid' => array(),
        'slidetitle' => array(),
        'slideorder' => array(),
        ),
    );
?>
