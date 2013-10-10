<?php
// Table Name
$tablename = 'tbl_tutorials_instructions';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold instructions for tutorials', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'contextcode' => array(
		'type' => 'text',
		'length' => 255,
	),
	'instructions' => array(
        'type' => 'clob',
    ),
	'deleted' => array( // active, deleted
		'type' => 'integer', 
		'length' => 1,
	),
	'updated' => array(
		'type' => 'timestamp',
	),
);
?>