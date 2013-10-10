<?php
// Table Name
$tablename = 'tbl_examiners_faculties';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to store university faculties.', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

// Fields
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => '32',
	),
	'faculty_name' => array(
	   'type' => 'text',
	   'length' => '255',
    ),
	'deleted' => array(
		'type' => 'integer',
		'length' => '1',
	),
	'updated' => array(
		'type' => 'timestamp',
	),
);
?>