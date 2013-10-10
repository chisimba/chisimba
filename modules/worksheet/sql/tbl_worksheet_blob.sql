<?php
//Table Name
$tablename = 'tbl_worksheet_blob';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the segments of uploaded images', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'fileid' => array(
		'type' => 'text',
		'length' => 100
		),
	'segment' => array(
		'type' => 'integer'
		),
	'filedata' => array(
		'type' => 'blob'
		),
	'updated' => array(
		'type' => 'timestamp',
		'length' => 14,
		'notnull' => 1
		)
	);
?>