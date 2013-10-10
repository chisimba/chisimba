<?php
// Table Name
$tablename = 'tbl_tutorials_late';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold students allowed late submissions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'tutorial_id' => array(
		'type' => 'text',
		'length' => 32,
	),
	'student_id' => array(
		'type' => 'text',
		'length' => 32,
    ),
	'answer_open' => array(
		'type' => 'timestamp',
	),
	'answer_close' => array(
		'type' => 'timestamp',
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