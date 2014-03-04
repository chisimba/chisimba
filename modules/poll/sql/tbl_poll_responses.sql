<?php

//5ive definition
$tablename = 'tbl_poll_responses';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table containing the user responses to poll', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'question_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'answer_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'user_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'date_created' => array(
		'type' => 'timestamp'
		),
	'date_modified' => array(
		'type' => 'timestamp'
		),
	);

// create other indexes here...

$name = 'poll_reponses_index';

$indexes = array(
                'fields' => array(
                	'answer_id' => array()
                )
        );
?>
