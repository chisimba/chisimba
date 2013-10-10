<?php
//Table Name
$tablename = 'tbl_worksheet_questions';

//Options line for comments, encoding and character set
$options = array('comment' => 'List of questions in a worksheet', 'collate' => 'utf8_general_ci', 'character_set' => 'utf8');

//
$fields = array(
	'id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull'=> 1
		),
	'worksheet_id' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1
		),
	'question' => array(
		'type' => 'clob',
		'notnull' => 1
		),
	'model_answer' => array(
		'type' => 'clob'
		),
	'question_worth' => array(
		'type' => 'integer',
		'length' => 11,
		'notnull' => 1
		),
	'question_order' => array(
		'type' => 'integer',
		'length' => 11
		),
	'imageid' => array(
		'type' => 'text',
		'length' => 100
		),
	'imagename' => array(
		'type' => 'text',
		'length' => 120
		),
	'userid' => array(
		'type' => 'text',
		'length' => 32,
		'notnull' => 1	
		),
	'datelastupdated' => array(
		'type' => 'timestamp',
		'notnull' => 1
		),
	'updated' => array(
		'type' => 'timestamp'
		)
	);
// Other indicies
$name = 'worksheet_idx';
$indexes = array(
    'fields' => array(
        'worksheet_id' => array()
    )
);
?>