<?php
// Table Name
$tablename = 'tbl_liftclub_messages';

//Options line for comments, encoding and character set
$options = array('comment' => 'lift club messages', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields 
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'userid' => array(
		'type' => 'text',
		'length' => 50
		),
	'recipentuserid' => array(
		'type' => 'text',
		'length' => 50
		),
	'timesent' => array(
		'type' => 'timestamp'
		),
	'markasread' => array(
		'type' => 'boolean'
		),
	'markasdeleted' => array(
		'type' => 'boolean'
		),	
	'messagetitle' => array(
		'type' => 'text',
		'length' => 150
		),
	'messagebody' => array(
		'type' => 'text'
		)
	);
?>
