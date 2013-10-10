<?php
//5ive definition
$tablename = 'tbl_developer';

//Options line for comments, encoding and character set
$options = array('comment' => 'IconRequest', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => TRUE,
		'default' => '',
		),
	'name' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => TRUE,
		'default' => '',
		),
	'email' => array(
		'type' => 'text',
		'length' => 64,
		'notnull' => TRUE,
		'default' => ''
		)
	);
?>
