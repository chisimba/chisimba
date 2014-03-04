<?php

//5ive definition
$tablename = 'tbl_fileshare';

//Options line for comments, encoding and character set
$options = array('comment' => 'Metadata for fileshare', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'fileid' => array(
		'type' => 'text',
		'length' => 32
		),
	'contextcode' => array(
		'type' => 'text',
		'length' => 255
		),
	'workgroupid' => array(
		'type' => 'text',
		'length' => 32
		),
	/*
	'userid' => array(
		'type' => 'text',
		'length' => 25
		),
	*/
	'filename' => array(
		'type' => 'text',
		'length' => 255
		),
	/*
	'filetype' => array(
		'type' => 'text',
		'length' => 32
		),
	'filesize' => array(
		'type' => 'integer'
		),
	'path' => array(
		'type' => 'text',
		'length' => 255
		),
	*/
	'title' => array(
		'type' => 'text',
		'length' => 255
		),
	'description' => array(
		'type' => 'text',
		'length' => 255
		),
	'version' => array(
		'type' => 'text',
		'length' => 255
		),
	'uploadtime' => array(
		'type' => 'integer'
		)
);
?>
