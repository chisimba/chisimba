<?php
// Table Name
$tablename = 'tbl_joomlastatus';

//Options line for comments, encoding and character set
$options = array('comment' => 'Karma Points', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'pname' => array(
		'type' => 'text',
		'length' => 50,
		),
	'pvalue' => array(
		'type' => 'text',
		'length' => 50,
		)
	);
?>