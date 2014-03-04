<?php
// Table Name
$tablename = 'tbl_tutorials_answers';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold tutorial answers', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'question_id' => array(
        'type' => 'text',
        'length' => '32',
    ),
	'student_id' => array(
		'type' => 'text',
		'length' => '32',
	),
    'answer' => array(
        'type' => 'clob',
    ),
    'moderation_reason' => array(
        'type' => 'clob',
    ),
    'moderation_complete' => array(
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