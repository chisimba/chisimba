<?php

//5ive definition
$tablename = 'tbl_poll_answers';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the possible answers to the poll', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'question_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'answer' => array(
		'type' => 'clob'
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

$name = 'poll_answers_index';

$indexes = array(
                'fields' => array(
                	'question_id' => array()
                )
        );
?>