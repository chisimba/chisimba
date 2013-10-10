<?php

//5ive definition
$tablename = 'tbl_hivaids_links';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table for storing pages of links', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'reference' => array(
		'type' => 'text',
		'length' => 32
		),
	'linkspage' => array(
		'type' => 'clob'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 32
		),
	'modifierid' => array(
		'type' => 'text',
		'length' => 32
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

?>