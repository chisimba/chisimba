<?php
// Table Name
$tablename = 'tbl_tutorials_questions';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table to hold tutorial questions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

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
	'question' => array(
        'type' => 'clob',
    ),
	'model_answer' => array(
		'type' => 'clob',
	),
    'question_value' => array(
        'type' => 'integer',
        'length' => '3',
    ),
    'question_order' => array(
        'type' => 'integer',
        'length' => '3',
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