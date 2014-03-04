<?php
// Table Name
$tablename = 'tbl_demodata_info';

//Options line for comments, encoding and character set
$options = array('comment' => 'Info for the demodata module', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'creationdate' => array(
		'type' => 'timestamp'
		),
	'creatorid' => array(
		'type' => 'text',
		'length' => 50,
		),
	'infobytes' => array(
		'type' => 'clob'
		),
	);

?>