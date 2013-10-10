<?php
//Table Name
$tablename = 'tbl_worksheet_answers';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of students answers to questions in a worksheet', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'question_id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'student_id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),		
	'dateanswered' => array(
		'type' => 'timestamp',
		'notnull' => 1
		),
	'answer' => array(
		'type' => 'blob'		
		),
	'mark' => array(
		'type' => 'integer',
		'length' => 11
		),
	'comments' => array(
		'type' => 'clob'
		), 
	'lecturer_id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1,
		),
	'datemarked' => array(
		'type' => 'timestamp'
		),
	'updated' => array(
		'type' => 'timestamp',
		'length' => 14
		)
	);
// Other indicies
$name = 'question_idx';
$indexes = array(
    'fields' => array(
        'question_id' => array()
    )
);
?>