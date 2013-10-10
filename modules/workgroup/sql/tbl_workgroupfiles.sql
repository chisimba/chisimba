<?php

//5ive definition
$tablename = 'tbl_workgroupfiles';

//Options line for comments, encoding and character set
$options = array('comment' => 'Workgroups files', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'workgroupid' => array(
		'type' => 'text',
		'length' => 32
		),
	'fileid' => array(
		'type' => 'text',
		'length' => 32
		),
	'description' => array(
		'type' => 'text',
		'length' => 255
		),
	'title' => array(
		'type' => 'text',
		'length' => 255
		),
	'datecreated' => array(
		'type' => 'date'
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32
		),
	'version' => array(
		'type' => 'text',
		'length' => 32
		),
	'datemodified' => array(
		'type' => 'date'
	)
);

?>