<?php
// Table Name
$tablename = 'tbl_tutorials_marker';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold the students the user has marked', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
        'length' => '32',
    ),
	'marker_id' => array(
		'type' => 'text',
		'length' => '32',
	),
    'is_completed' => array(
        'type' => 'integer',
        'length' => '1',
    ),
    'is_lecturer' => array(
        'type' => 'integer',
        'length' => '1',
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