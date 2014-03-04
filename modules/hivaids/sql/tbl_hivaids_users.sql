<?php

//5ive definition
$tablename = 'tbl_hivaids_users';

//Options line for comments, encoding and character set
$options = array('comment' => 'Table extending the tbl_users table', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32
		),
	'user_id' => array(
		'type' => 'text',
		'length' => 32
		),
	'staff_student' => array(
		'type' => 'text',
		'length' => 100
		),
	'course' => array(
		'type' => 'text',
		'length' => 255
		),
	'study_year' => array(
		'type' => 'text',
		'length' => 10
		),
	'language' => array(
		'type' => 'text',
		'length' => 255
		),
	'modifierid' => array(
		'type' => 'timestamp'
		),
	'datecreated' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);

// create other indexes here...

$name = 'hivaids_users_index';

$indexes = array(
                'fields' => array(
                	'user_id' => array()
                )
        );
?>