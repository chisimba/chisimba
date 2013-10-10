<?php

//5ive definition
$tablename = 'tbl_poll_questions';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the poll questions', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'poll_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'question' => array(
		'type' => 'clob'
		),
	'question_type' => array(
		'type' => 'text',
		'length' => 25
		),
	'is_visible' => array(
		'type' => 'integer',
		'length' => 2
		),
	'has_responses' => array(
		'type' => 'integer',
		'length' => 2
		),
	'order_num' => array(
		'type' => 'integer',
		'length' => 2
		),
	'start_date' => array(
		'type' => 'timestamp'
		),
	'end_date' => array(
		'type' => 'timestamp'
		),
	'creator_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'modifier_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'date_created' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		),
	);

// create other indexes here...

$name = 'poll_questions_index';

$indexes = array(
                'fields' => array(
                	'poll_id' => array()
                )
        );
?>