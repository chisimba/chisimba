<?php

//5ive definition
$tablename = 'tbl_workgroup_users';

//Options line for comments, encoding and character set
$options = array('comment' => 'Users in workgroups', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'workgroupid' => array(
		'type' => 'text',
		'length' => 32
		),
	'contextcode' => array(
		'type' => 'text',
		'length' => 255
		),
	'userid' => array(
		'type' => 'text',
		'length' => 25
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'date'
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datemodified' => array(
		'type' => 'date'
	)
);

?>