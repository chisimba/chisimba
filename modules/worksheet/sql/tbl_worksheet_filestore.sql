<?php
//Table Name
$tablename = 'tbl_worksheet_filestore';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the details of uploaded images', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'context_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32
		),
	'fileid' => array(
		'type' => 'text',
		'length' => 100		
		),
	'filename' => array(
		'type' => 'text',
		'length' => 120
		),
	'filetype' => array(
		'type' => 'text',
		'length' => 32
		),
	'size' => array(
		'type' => 'integer'
		),
	'uploadtime' => array(
		'type' => 'integer'
		),
	'updated' => array(
		'type' => 'timestamp',
		'length' => 14,
		'notnull' => 1
		)
	);
// Other indicies
$name = 'filestore_index';
$indexes = array(
    'fields' => array(
        'context_id' => array(),
        'fileid' => array()
    )
);
?>