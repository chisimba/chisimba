<?php

//5ive definition
$tablename = 'tbl_hivaids_suggestions';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table holding suggestions from users / students', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'user_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'suggestion' => array(
		'type' => 'text',
		'length' => 100
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'hivaids_suggestions_index';

$indexes = array(
                'fields' => array(
                    'suggestion' => array(),
                	'updated' => array()
                )
        );
?>