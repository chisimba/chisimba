<?php
// Table Name
$tablename = 'tbl_tutorials_results';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold students marks', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'has_submitted' => array(
		'type' => 'integer',
		'length' => '1',
	),
    'mark_obtained' => array(
        'type' => 'float',
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